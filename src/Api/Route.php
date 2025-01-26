<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare( strict_types=1 );

namespace Lipe\Lib\Api;

use Lipe\Lib\Post_Type\Wp_Insert_Post;
use Lipe\Lib\Query\Get_Posts;
use Lipe\Lib\Traits\Memoize;
use Lipe\Lib\Traits\Singleton;

/**
 * Routes a custom url to a page template
 *
 * @example Route::init();
 * @example Route::add( 'custom-page', array( 'title' => __( 'Custom Page' ), 'template' => get_stylesheet_directory()
 *          . '/content/custom-page.php' ) );
 */
class Route {
	use Singleton;
	use Memoize;

	protected const NAME = 'lipe/lib/util/route';

	protected const POST_TYPE       = 'lipe-lib-util-route';
	protected const QUERY_VAR       = 'lipe/lib/util/route/template';
	protected const PARAM_QUERY_VAR = 'lipe/lib/util/route/param';
	protected const OPTION          = 'lipe/lib/util/route/cache';
	protected const POST_ID_OPTION  = 'lipe/lib/util/route/post-id';

	/**
	 * The id of the placeholder post.
	 *
	 * @var int
	 */
	protected int $post_id = 0;

	/**
	 * Registered routes.
	 *
	 * @var array<string, array{title:string, template:string}>
	 */
	protected array $routes = [];


	/**
	 * Add a custom route and template.
	 *
	 * @param string                               $url  - url appended to the sites home url.
	 * @param array{title:string, template:string} $args {
	 *                                                   title => "Title of page"
	 *                                                   template => "full file path to template"
	 *                                                   }.
	 *
	 * @return void
	 */
	public function add( string $url, array $args ): void {
		$this->routes[ $url ] = $args;
	}


	/**
	 * Set up a special post type to be used in the queries, so we return
	 * an actual post and not a 404.
	 *
	 * A single post of this type is created to be queried
	 * We then filter the post to match our needs
	 *
	 * @return void
	 */
	protected function register_post_type(): void {
		$args = [
			'public'              => false,
			'show_ui'             => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'supports'            => [ 'title' ],
			'has_archive'         => false,
			'rewrite'             => false,
		];
		register_post_type( self::POST_TYPE, $args );
	}


	/**
	 * Actions and filters.
	 */
	protected function hook(): void {
		add_filter( 'query_vars', fn( array $vars ) => $this->add_query_var( $vars ) );
		add_action( 'init', function() {
			$this->setup_endpoints();
		} );

		add_action( 'pre_get_posts', function( \WP_Query $query ) {
			$this->maybe_add_post_hooks( $query );
		} );
		add_action( 'wp_loaded', function() {
			$this->maybe_flush_rules();
		}, PHP_INT_MAX );
		add_action( 'init', function() {
			$this->register_post_type();
		} );
	}


	/**
	 * Add the post filtering hooks only if we are parsing
	 * a custom route.
	 *
	 * @param \WP_Query $query - The query object.
	 *
	 * @action pre_get_posts 10 1
	 *
	 * @return void
	 */
	protected function maybe_add_post_hooks( \WP_Query $query ): void {
		if ( ! isset( $query->query_vars[ self::QUERY_VAR ] ) ) {
			return;
		}
		$query->set( 'post_type', self::POST_TYPE );

		add_filter( 'the_title', [ $this, 'get_title' ], 10, 2 );
		add_filter( 'single_post_title', [ $this, 'get_title' ], 10, 2 );
		add_filter( 'body_class', fn( array $classes ) => $this->adjust_body_class( $classes ), 99 );
		add_filter( 'template_include', fn() => $this->override_template() );
	}


	/**
	 * Adjust body classes when viewing this custom route's page.
	 *
	 * 1. Remove misleading body classes generated by the post type and id.
	 * 2. Add a body class, which matches the current route.
	 *
	 * @param array<string> $classes - Current body classes.
	 *
	 * @filter body_class 99 1
	 *
	 * @return array<string>
	 */
	protected function adjust_body_class( array $classes ): array {
		$post = get_post();
		if ( ! $post instanceof \WP_Post ) {
			return $classes;
		}

		foreach ( $classes as $k => $_class ) {
			if ( \str_contains( $_class, 'postid' ) ) {
				unset( $classes[ $k ] );
			} elseif ( $_class === $post->post_name ) {
				unset( $classes[ $k ] );
			} elseif ( \str_contains( $_class, 'page-template' ) ) {
				unset( $classes[ $k ] );
			}
		}

		$route = $this->get_current_route();
		if ( null === $route ) {
			return $classes;
		}
		$classes[] = sanitize_title_with_dashes( $route['title'] );
		$template = \explode( '/', $route['template'] );
		$classes[] = 'page-template-' . \str_replace( '.', '-', \end( $template ) );

		return $classes;
	}


	/**
	 * Get the args like title and template for the current route
	 * Will return false is no route is found.
	 *
	 * @return null|array{title:string, template:string}
	 */
	public function get_current_route(): ?array {
		$route = get_query_var( self::QUERY_VAR );
		if ( ! \is_string( $route ) || ! isset( $this->routes[ $route ] ) ) {
			return null;
		}
		return $this->routes[ $route ];
	}


	/**
	 * Adding rewrite rules requires a flush of all rules
	 * This checks for new ones then flushes as needed.
	 *
	 * @return void
	 */
	protected function maybe_flush_rules(): void {
		$hash = \hash( 'murmur3f', (string) wp_json_encode( $this->routes ) );
		if ( get_option( self::OPTION ) !== $hash ) {
			flush_rewrite_rules();
			update_option( self::OPTION, $hash );
		}
	}


	/**
	 * Add a query var to allow for our custom urls to be specified.
	 *
	 * @param array<string> $vars - Current query vars.
	 *
	 * @filter query_vars 10 1
	 *
	 * @return array<string>
	 */
	protected function add_query_var( array $vars ): array {
		$vars[] = self::QUERY_VAR;
		$vars[] = self::PARAM_QUERY_VAR;

		return $vars;
	}


	/**
	 * Register the rewrite rules to send the appropriate urls to our
	 * custom query var which will tell us what route we are using.
	 *
	 * @action init 10 0
	 *
	 * @return void
	 */
	protected function setup_endpoints(): void {
		foreach ( $this->routes as $_route => $_args ) {
			add_rewrite_rule( $_route . '/([^/]+)/?.?', 'index.php?post_type=' . self::POST_TYPE . '&p=' . $this->get_post_id() . '&' . self::QUERY_VAR . '=' . $_route . '&' . self::PARAM_QUERY_VAR . '=$matches[1]', 'top' );

			add_rewrite_rule( '^' . $_route . '/?$', 'index.php?post_type=' . self::POST_TYPE . '&p=' . $this->get_post_id() . '&' . self::QUERY_VAR . '=' . $_route, 'top' );
		}
	}


	/**
	 * Get the ID of the placeholder post.
	 *
	 * @throws \LogicException -- If we somehow fail to create the post.
	 *
	 * @return int
	 */
	protected function get_post_id(): int {
		if ( $this->post_id > 0 ) {
			return $this->post_id;
		}

		$this->post_id = (int) get_option( self::POST_ID_OPTION, 0 );
		if ( $this->post_id > 0 ) {
			return $this->post_id;
		}

		$args = new Get_Posts( [] );
		$args->fields = 'ids';
		$args->post_status = 'publish';
		$args->post_type = self::POST_TYPE;
		$args->posts_per_page = 1;

		$posts = get_posts( $args->get_light_args() );
		if ( isset( $posts[0] ) && \is_int( $posts[0] ) ) {
			$this->post_id = $posts[0];
		} else {
			$this->post_id = $this->make_post();
		}

		if ( $this->post_id > 0 ) {
			update_option( self::POST_ID_OPTION, $this->post_id );
		} else {
			throw new \LogicException( 'Failed creating post for routing.' );
		}

		return $this->post_id;
	}


	/**
	 * Make a new placeholder post
	 *
	 * @return int The ID of the new post
	 */
	protected function make_post(): int {
		$args = new Wp_Insert_Post( [] );
		$args->post_type = self::POST_TYPE;
		$args->post_status = 'publish';
		$args->post_title = 'Lipe Libs Placeholder Post';

		$id = wp_insert_post( $args->get_args(), true );
		if ( is_wp_error( $id ) ) {
			return 0;
		}

		return $id;
	}


	/**
	 * Use the specified template file based on the current route
	 *
	 * @return string
	 */
	protected function override_template(): string {
		$route = $this->get_current_route();
		if ( null === $route ) {
			return '';
		}
		return $route['template'];
	}


	/**
	 * Are we currently on a specified route page?
	 *
	 * @param string $route - The route slug.
	 *
	 * @return bool
	 */
	public function is_current_route( string $route ): bool {
		return get_query_var( self::QUERY_VAR ) === $route;
	}


	/**
	 * Retrieves the value sent within the url
	 * /%route%/%param%/
	 *
	 * @example If the url is /profile/mat/ this will return 'mat'
	 *
	 * @return string
	 */
	public function get_url_parameter(): string {
		return get_query_var( self::PARAM_QUERY_VAR );
	}


	/**
	 * Get the title for the placeholder page from the route.
	 *
	 * @param string       $title - The current title.
	 * @param int|\WP_Post $post  - The current post.
	 *
	 * @return string
	 */
	public function get_title( string $title, int|\WP_Post $post ): string {
		$_post = get_post( $post );
		if ( null === $_post || $this->get_post_id() === $_post->ID ) {
			$route = $this->get_current_route();
			if ( null === $route ) {
				return $title;
			}
			return $route['title'];
		}

		return $title;
	}
}
