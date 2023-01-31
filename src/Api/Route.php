<?php

namespace Lipe\Lib\Api;

use Lipe\Lib\Traits\Singleton;

/**
 * Routes a custom url to a page template
 *
 * @example Route::init();
 * @example Route::add( 'custom-page', array( 'title' => __( 'Custom Page' ), 'template' => get_stylesheet_directory()
 *          . '/content/custom-page.php' ) );
 *
 */
class Route {
	use Singleton;

	protected const NAME = 'lipe/lib/util/route';

	protected const QUERY_VAR       = 'lipe/lib/util/route_template';
	protected const PARAM_QUERY_VAR = 'lipe/lib/util/route_param';
	protected const OPTION          = 'lipe/lib/util/route_cache';
	protected const POST_ID_OPTION  = 'lipe/lib/util/route_post_id';

	/**
	 * The id of the placeholder post.
	 *
	 * @static
	 * @var int
	 */
	protected static $post_id = 0;

	/**
	 * @var array
	 */
	private static $routes = [];


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
	public static function add( string $url, array $args ) : void {
		self::$routes[ $url ] = $args;
	}


	/**
	 * Setup a special post type to be used in the queries, so we return
	 * an actual post and not a 404.
	 *
	 * A single post of this type is created to be queried
	 * We then filter the post to match our needs
	 *
	 * @static
	 *
	 * @return void
	 */
	public static function register_post_type() : void {
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
		register_post_type( self::NAME, $args );
	}


	public function hook() : void {
		add_filter( 'query_vars', [ $this, 'add_query_var' ] );
		add_action( 'init', [ $this, 'setup_endpoints' ] );
		add_action( 'init', [ __CLASS__, 'register_post_type' ] );
		add_action( 'pre_get_posts', [ $this, 'maybe_add_post_hooks' ] );

		add_action( 'wp_loaded', [ $this, 'maybe_flush_rules' ], 999999999999 );
	}


	/**
	 * Maybe Add Post Hooks
	 *
	 * Add the post filtering hooks only if we are parsing
	 * a custom route
	 *
	 * @param \WP_Query $query
	 *
	 * @action pre_get_posts 10 1
	 *
	 * @return void
	 */
	public function maybe_add_post_hooks( \WP_Query $query ) : void {
		if ( isset( $query->query_vars[ self::QUERY_VAR ] ) ) {
			$this->add_post_hooks();
		}
	}


	/**
	 * Add Post Hooks
	 *
	 * Hooks we only run if we are retrieving a custom route
	 *
	 * @see $this->maybe_add_post_hooks
	 *
	 * @return void
	 */
	protected function add_post_hooks() : void {
		add_filter( 'the_title', [ $this, 'get_title' ], 10, 2 );
		add_filter( 'single_post_title', [ $this, 'get_title' ], 10, 2 );
		add_filter( 'body_class', [ $this, 'adjust_body_class' ], 99 );
		add_filter( 'template_include', [ $this, 'override_template' ], 10, 1 );
	}


	/**
	 * Adjust body classes when viewing this custom route's page.
	 *
	 * 1. Remove misleading body classes generated by the post type and id.
	 * 2. Add a body class, which matches the current route.
	 *
	 * @param array $classes
	 *
	 * @filter body_class 99 1
	 *
	 * @return array
	 */
	public function adjust_body_class( array $classes ) : array {
		$post = get_post();

		foreach ( $classes as $k => $_class ) {
			if ( false !== strpos( $_class, 'postid' ) ) {
				unset( $classes[ $k ] );
			} elseif ( $_class === $post->post_name ) {
				unset( $classes[ $k ] );
			} elseif ( false !== strpos( $_class, 'page-template' ) ) {
				unset( $classes[ $k ] );
			}
		}

		$route = $this->get_current_route();
		$classes[] = sanitize_title_with_dashes( $route['title'] );
		$template = explode( '/', $route['template'] );
		$classes[] = 'page-template-' . str_replace( '.', '-', end( $template ) );

		return $classes;
	}


	/**
	 * Get the args like title and template for the current route
	 * Will return false is no route is found
	 *
	 * @return null|array{title:string, template:string}
	 */
	public function get_current_route() : ?array {
		$route = get_query_var( self::QUERY_VAR );
		if ( empty( $route ) || empty( self::$routes[ $route ] ) ) {
			return null;
		}

		return self::$routes[ $route ];
	}


	/**
	 * Adding rewrite rules requires a flush of all rules
	 * This checks for new ones then flushes as needed.
	 *
	 * @return void
	 */
	public function maybe_flush_rules() : void {
		if ( get_option( self::OPTION ) !== md5( wp_json_encode( self::$routes ) ) ) {
			flush_rewrite_rules();
			update_option( self::OPTION, md5( wp_json_encode( self::$routes ) ) );
		}
	}


	/**
	 * Add a query var to allow for our custom urls to be specified.
	 *
	 * @param array $vars
	 *
	 * @filter query_vars 10 1
	 *
	 * @return array
	 */
	public function add_query_var( array $vars ) : array {
		$vars[] = self::QUERY_VAR;
		$vars[] = self::PARAM_QUERY_VAR;

		return $vars;
	}


	/**
	 * Register the rewrite rules to send the appropriate urls to our
	 * custom query var which will tell us what route we are using
	 *
	 * @return void
	 */
	public function setup_endpoints() : void {
		foreach ( self::$routes as $_route => $_args ) {
			add_rewrite_rule( $_route . '/([^/]+)/?.?', 'index.php?post_type=' . self::NAME . '&p=' . self::get_post_id() . '&' . self::QUERY_VAR . '=' . $_route . '&' . self::PARAM_QUERY_VAR . '=$matches[1]', 'top' );

			add_rewrite_rule( $_route, 'index.php?post_type=' . self::NAME . '&p=' . self::get_post_id() . '&' . self::QUERY_VAR . '=' . $_route, 'top' );
		}
	}


	/**
	 * Get the ID of the placeholder post
	 *
	 * @return int
	 */
	protected static function get_post_id() : int {
		if ( self::$post_id ) {
			return self::$post_id;
		}

		self::$post_id = (int) get_option( self::POST_ID_OPTION, false );
		if ( self::$post_id ) {
			return self::$post_id;
		}

		$posts = get_posts( [
			'post_type'      => self::NAME,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		] );
		if ( $posts ) {
			self::$post_id = $posts[0];
		} else {
			self::$post_id = self::make_post();
		}

		if ( self::$post_id ) {
			update_option( self::POST_ID_OPTION, (int) self::$post_id );
		}

		return self::$post_id;
	}


	/**
	 * Make a new placeholder post
	 *
	 * @return int The ID of the new post
	 */
	private static function make_post() : int {
		$post = [
			'post_title'  => 'Lipe Libs Placeholder Post',
			'post_status' => 'publish',
			'post_type'   => self::NAME,
		];
		$id = wp_insert_post( $post );
		if ( is_wp_error( $id ) ) { // @phpstan-ignore-line
			return 0;
		}

		return $id;
	}


	/**
	 * Use the specified template file based on the current route
	 *
	 * @return string
	 */
	public function override_template() : string {
		return $this->get_current_route()['template'];
	}


	/**
	 * Are we currently on a specified route page?
	 *
	 * @param string $route - the route slug.
	 *
	 * @return bool
	 */
	public function is_current_route( string $route ) : bool {
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
	public function get_url_parameter() : string {
		return get_query_var( self::PARAM_QUERY_VAR );
	}


	/**
	 * Get the title for the placeholder page from the route.
	 *
	 * @param string       $title
	 * @param int|\WP_Post $post
	 *
	 * @return string
	 */
	public function get_title( string $title, $post ) : string {
		$_post = get_post( $post );
		if ( self::get_post_id() === $_post->ID ) {
			$route = $this->get_current_route();

			return $route['title'];
		}

		return $title;
	}

}
