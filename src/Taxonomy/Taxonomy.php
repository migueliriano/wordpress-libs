<?php
declare( strict_types=1 );

namespace Lipe\Lib\Taxonomy;

use Lipe\Lib\Taxonomy\Taxonomy\Register_Taxonomy;
use Lipe\Lib\Traits\Memoize;
use Lipe\Lib\Util\Actions;

/**
 * Register taxonomy with WordPress.
 *
 * Follows many of the standard `register_taxonomy` arguments with some
 * customizations and additional features.
 *
 * @link   https://developer.wordpress.org/reference/functions/register_taxonomy/
 *
 * @notice Must be constructed before the `init` hook runs
 *
 * @phpstan-import-type REWRITE from Register_Taxonomy
 * @phpstan-import-type DEFAULT from Register_Taxonomy
 */
class Taxonomy {
	use Memoize;

	protected const REGISTRY_OPTION = 'lipe/lib/schema/taxonomy_registry';

	/**
	 * Track the register taxonomies for later use.
	 *
	 * @var Taxonomy[]
	 */
	protected static array $registry = [];

	/**
	 * Array of arguments to automatically use inside `wp_get_object_terms()` for this taxonomy.
	 *
	 * @var Get_Terms
	 */
	public Get_Terms $args;

	/**
	 * Array of post types to attach this taxonomy to.
	 *
	 * @var string[]
	 */
	public readonly array $post_types;

	/**
	 * Whether a taxonomy is intended for use publicly either via the
	 * admin interface or by front-end users.
	 * The default settings of `$publicly_queryable`, `$show_ui`,
	 * and `$show_in_nav_menus` are inherited from `$public`.
	 *
	 * @default true
	 *
	 * @var bool
	 */
	public bool $public = true;

	/**
	 * Whether the taxonomy is publicly queryable.
	 *
	 * @default $this->public
	 *
	 * @var bool
	 */
	public bool $publicly_queryable;

	/**
	 * Whether to generate a default UI for managing this taxonomy.
	 *
	 * @notice `$this->show_in_rest` must be true to show in Gutenberg.
	 *
	 * @default $this->public
	 *
	 * @var bool
	 */
	public bool $show_ui;

	/**
	 * Slug to use for this taxonomy rewrite.
	 *
	 * @var string
	 */
	public string $slug;

	/**
	 * Menu configurations for the taxonomy.
	 *
	 * @since 5.0.0
	 *
	 * @var array{
	 *     parent?: string,
	 *     priority?: int,
	 * }
	 */
	protected array $menu_configuration = [];

	/**
	 * True makes this taxonomy available for selection in navigation menus.
	 *
	 * @default $this->public
	 *
	 * @var bool
	 */
	public bool $show_in_nav_menus;

	/**
	 * REST API Controller class name.
	 *
	 * @default 'WP_REST_Terms_Controller'
	 *
	 * @phpstan-var class-string<\WP_REST_Controller>
	 * @var string
	 */
	public string $rest_controller_class;

	/**
	 * Whether to allow the Tag Cloud widget to use this taxonomy.
	 *
	 * @default $this->show_ui
	 *
	 * @var bool
	 */
	public bool $show_tagcloud;

	/**
	 * Whether to show the taxonomy in the quick/bulk edit panel
	 *
	 * @default $this->show_ui
	 *
	 * @var bool
	 */
	public bool $show_in_quick_edit;

	/***
	 * Include a description of the taxonomy.
	 *
	 * @var string
	 */
	public string $description = '';

	/**
	 * Is this taxonomy hierarchical (have descendants) like categories
	 * or not hierarchical like tags.
	 *
	 * @var bool
	 */
	public bool $hierarchical = false;

	/**
	 * Works much like a hook, in that it will be called when the count is updated.
	 *
	 * Defaults:
	 * - `_update_post_term_count()` for taxonomies attached to post types, which confirms
	 *  that the objects are published before counting them.
	 * - `_update_generic_term_count()` for taxonomies attached to other object types, such as users.
	 *
	 * @phpstan-var callable(int[],\WP_Taxonomy): void
	 *
	 * @var callable
	 */
	public $update_count_callback;

	/**
	 * False to disable the query_var, set as string to use
	 * custom query_var instead of default.
	 *
	 * True is not seen as a valid entry and will result in 404 issues.
	 *
	 * @default $this->taxonomy
	 *
	 * @var false|string
	 */
	public string|false $query_var;

	/**
	 * Triggers the handling of rewrites for this taxonomy.
	 *
	 * Default true, using `$taxonomy` as slug.
	 *
	 * - To prevent a rewrite, set to false.
	 * - To specify rewrite rules, an array can be passed with any of these keys:
	 *
	 * @phpstan-var bool|REWRITE
	 *
	 * @var bool|array<string,mixed>
	 */
	public array|bool $rewrite;

	/**
	 * Array of capabilities for this taxonomy.
	 *
	 * @var Capabilities
	 */
	public readonly Capabilities $capabilities;

	/**
	 * Whether terms in this taxonomy should be sorted in the
	 * order they are provided to `wp_set_object_terms()`
	 *
	 * Default false.
	 *
	 * @var bool
	 */
	public bool $sort = false;

	/**
	 * Override any generated labels.
	 *
	 * Usually calling `Taxonomy::set_label` covers any required changes.
	 * Updating this property will fine tune existing or set special not included ones.
	 *
	 * @see      get_taxonomy_labels
	 * @see      Taxonomy::taxonomy_labels()
	 *
	 * @var Labels
	 */
	public readonly Labels $labels;

	/**
	 * The taxonomy slug.
	 *
	 * @var string
	 */
	public readonly string $taxonomy;

	/**
	 * The arguments to pass to `register_taxonomy()`.
	 *
	 * @see Taxonomy::taxonomy_args()
	 *
	 * @var Register_Taxonomy
	 */
	public readonly Register_Taxonomy $register_args;

	/**
	 * Terms to be automatically added to a taxonomy when
	 * it's registered.
	 *
	 * @var array<string|int, string>
	 */
	protected array $initial_terms = [];

	/**
	 * Auto generate a post list filter.
	 *
	 * @var bool
	 */
	protected bool $post_list_filter = false;


	/**
	 * Add hooks to register taxonomy.
	 *
	 * @param string   $taxonomy   - Taxonomy Slug (will convert a title to a slug as well).
	 * @param string[] $post_types - Post types to attach this taxonomy to.
	 */
	public function __construct( string $taxonomy, array $post_types ) {
		$this->post_types = $post_types;
		$this->taxonomy = \strtolower( \str_replace( ' ', '_', $taxonomy ) );
		$this->slug = \strtolower( \str_replace( ' ', '-', $this->taxonomy ) );
		$this->labels = new Labels( $this );
		$this->capabilities = new Capabilities( $this );
		$this->register_args = new Register_Taxonomy();

		$this->set_label();
		$this->hook();
	}


	/**
	 * Hook the taxonomy into WordPress
	 *
	 * @return void
	 */
	protected function hook(): void {
		// So we can add and edit stuff on init hook.
		add_action( 'wp_loaded', function() {
			$this->register();
		}, 8, 0 );
		add_action( 'admin_menu', function() {
			$this->add_as_submenu();
		} );

		add_action( 'restrict_manage_posts', function() {
			$this->post_list_filters();
		} );
		if ( is_admin() ) {
			// If some taxonomies are not registered on the front end.
			add_action( 'wp_loaded', function() {
				$this->static_once( fn() => $this->check_rewrite_rules(), 'check_rewrite_rules' );
			}, 1_000 );
		}
	}


	/**
	 * Set capabilities for the taxonomy using the methods
	 * of the Capabilities class.
	 *
	 * @since 3.15.0
	 *
	 * @return Capabilities
	 */
	public function capabilities(): Capabilities {
		return $this->capabilities;
	}


	/**
	 * The name of the custom meta box to use on the post editing screen for this taxonomy.
	 *
	 * Three custom meta boxes are provided:
	 *
	 *  - 'radio' for a meta box with radio inputs
	 *  - 'simple' for a meta box with a simplified list of checkboxes
	 *  - 'dropdown' for a meta box with a dropdown menu
	 *
	 * @phpstan-param Meta_Box::TYPE_* $type
	 *
	 * @param string                   $type          - The type of UI to render.
	 * @param bool                     $checked_ontop - Move checked items to top.
	 *
	 * @return void
	 */
	public function meta_box( string $type, bool $checked_ontop = false ): void {
		$box = new Meta_Box( $this->taxonomy, $type, $checked_ontop );
		$this->register_args->meta_box_sanitize_cb = [ $box, 'translate_string_term_ids_to_int' ];
	}


	/**
	 * Provide a callback function for the meta box display.
	 *
	 * @see      Taxonomy::meta_box()
	 *
	 * @since    5.0.0
	 *
	 * @formatter:off
	 * @phpstan-param false|callable(\WP_Post,array{args: array{taxonomy: string}, id: string, title: string}): void $callback
	 * @phpstan-param callable(string,mixed): (int|string)[]                                                         $sanitize
	 *
	 * @param callable|false $callback - Callback function for the meta box display.
	 * @param callable       $sanitize - Callback function for sanitizing taxonomy data saved from a meta box.
	 * @formatter:on
	 *
	 * @return void
	 */
	public function custom_meta_box( callable|false $callback, callable $sanitize ): void {
		$this->register_args->meta_box_cb = $callback;
		$this->register_args->meta_box_sanitize_cb = $sanitize;
	}


	/**
	 * Creates the drop-downs to filter the post list by this taxonomy
	 *
	 * @action restrict_manage_posts 10 0
	 *
	 * @return void
	 */
	protected function post_list_filters(): void {
		global $typenow, $wp_query;
		if ( ! $this->post_list_filter || ! \in_array( $typenow, $this->post_types, true ) ) {
			return;
		}

		$args = new Wp_Dropdown_Categories();
		$args->orderby = 'name';
		$args->value_field = 'slug';
		$args->hierarchical = true;
		$args->show_count = true;
		$args->hide_empty = true;
		/* translators: Plural label of taxonomy. */
		$args->show_option_all = \sprintf( __( 'All %s' ), $this->labels->get_label( 'name' ) ?? $this->labels->get_label( 'singular_name' ) ?? '' ); //phpcs:ignore WordPress.WP.I18n -- Using global translation namespace.
		$args->taxonomy = $this->taxonomy;
		$args->name = $this->taxonomy;

		if ( isset( $wp_query->query[ $this->taxonomy ] ) && '' !== $wp_query->query[ $this->taxonomy ] ) {
			$args->selected = (string) $wp_query->query[ $this->taxonomy ];
			add_action( 'manage_posts_extra_tablenav', function() {
				$this->static_once( fn() => $this->clear_filters_button(), 'clear_filters_button' );
			}, 1_000 );
		}

		wp_dropdown_categories( $args->get_args() );
	}


	/**
	 * Specify terms to be added automatically when a taxonomy is created.
	 *
	 * @param array<int|string, string> $terms = array( <slug> => <name> ) || array( <name> ).
	 *
	 * @return void
	 */
	public function add_initial_terms( array $terms = [] ): void {
		$this->initial_terms = $terms;
	}


	/**
	 * Removes a column from the terms list in the admin.
	 *
	 * Default WP columns are
	 * 1. 'description'
	 * 2. 'slug'
	 * 3. 'posts'.
	 *
	 * @param string $column - Column slug.
	 */
	public function remove_column( string $column ): void {
		add_filter( "manage_edit-{$this->taxonomy}_columns", function( $columns ) use ( $column ) {
			unset( $columns[ $column ] );
			return $columns;
		} );
	}


	/**
	 * If $this->show_in_menu was set to a slug instead
	 * of a boolean, we add the taxonomy as a submenu of
	 * the provided slug.
	 *
	 * The taxonomy will be added at the end of the menu unless
	 * an order is provided by setting $this->show_in_menu to an array.
	 *
	 * @see    Taxonomy::$show_in_menu
	 *
	 * @action admin_menu 10 0
	 *
	 * @return void
	 */
	protected function add_as_submenu(): void {
		global $submenu;
		$edit_tags_file = 'edit-tags.php?taxonomy=%s';
		if ( isset( $this->register_args->show_in_menu ) && false === $this->register_args->show_in_menu ) {
			return;
		}
		if ( ! isset( $this->menu_configuration['parent'] ) ) {
			return;
		}

		$tax = get_taxonomy( $this->taxonomy );
		$parent = $this->menu_configuration['parent'];
		$order = $this->menu_configuration['priority'] ?? 100;

		if ( false !== $tax ) {
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride -- Intentional override.
			$submenu[ $parent ][ $order ] = [
				esc_attr( $tax->labels->menu_name ),
				$tax->cap->manage_terms,
				\sprintf( $edit_tags_file, $tax->name ),
			];
			\ksort( $submenu[ $parent ] );
		}

		// Set the current parent menu for the custom location.
		add_filter( 'parent_file', function( string $menu ) {
			return $this->set_current_menu( $menu );
		} );
	}


	/**
	 * Enable the admin post lists column for this taxonomy.
	 * Optionally set the label for the column. Defaults to the taxonomy label.
	 *
	 * WP core does not support changing the label using `show_admin_column` so
	 * we use a filter to change the label.
	 *
	 * @param string $label - Optional label to use for the column.
	 */
	public function show_admin_column( string $label = '' ): void {
		$this->register_args->show_admin_column = true;
		if ( '' === $label ) {
			return;
		}
		Actions::in()->add_filter_all( array_map( function( $post_type ) {
			return "manage_{$post_type}_posts_columns";
		}, $this->post_types ), function( array $columns ) use ( $label ) {
			$columns[ 'taxonomy-' . $this->taxonomy ] = $label;
			return $columns;
		} );
	}


	/**
	 * Enable/disable a post list filter for this taxonomy.
	 *
	 * @param bool $enabled - Whether to enable the post list filter.
	 *
	 * @return void
	 */
	public function post_list_filter( bool $enabled = true ): void {
		$this->post_list_filter = $enabled;
	}


	/**
	 * Set the default term which will be added to new posts which
	 * use this taxonomy.
	 *
	 * If the term does not exist, it will be created automatically.
	 *
	 * @requires WP 5.5.0+
	 *
	 * @param string $slug        - The slug of the term to use.
	 * @param string $name        - The name of the term to use.
	 * @param string $description - The description of the term to use.
	 *
	 * @return void
	 */
	public function set_default_term( string $slug, string $name, string $description = '' ): void {
		$this->register_args->default_term = [
			'description' => $description,
			'name'        => $name,
			'slug'        => $slug,
		];
	}


	/**
	 * Show the taxonomy in the admin menu under a specific menu and priority.
	 *
	 * @since 5.0.0
	 *
	 * @param string $slug     - The slug of the menu to show the taxonomy under.
	 * @param int    $priority - The priority of the taxonomy in the menu.
	 *
	 * @return void
	 */
	public function show_in_menu( string $slug = '', int $priority = - 1 ): void {
		$this->register_args->show_in_menu = true;
		if ( '' !== $slug ) {
			$this->menu_configuration['parent'] = $slug;
		}
		if ( - 1 !== $priority ) {
			$this->menu_configuration['order'] = $priority;
		}
	}


	/**
	 * Show or hide this post type in the REST API.
	 *
	 * @since 5.0.0
	 *
	 * @see   Custom_Post_Type::rest_controllers()
	 *
	 * @param bool    $show  - Whether to show in REST.
	 * @param ?string $base  - The base to use. Defaults to the taxonomy.
	 * @param string  $space - The namespace to use.
	 *
	 * @return void
	 */
	public function show_in_rest( bool $show = true, ?string $base = null, string $space = 'wp/v2' ): void {
		$this->register_args->show_in_rest = $show;

		if ( $show ) {
			if ( ! isset( $this->register_args->rest_base ) ) {
				$this->register_args->rest_base = $base ?? $this->taxonomy;
			}
			if ( ! isset( $this->register_args->rest_namespace ) ) {
				$this->register_args->rest_namespace = $space;
			}
		} else {
			unset( $this->register_args->rest_base, $this->register_args->rest_namespace, $this->register_args->rest_controller_class );
		}
	}


	/**
	 * Set the current admin menu, so the correct one is highlighted.
	 * Only used when $this->menu_configuration['parent'] is set to a slug of a menu.
	 *
	 * @filter parent_file 10 1
	 *
	 * @see    Taxonomy::show_in_menu()
	 * @see    Taxonomy::add_as_submenu();
	 *
	 * @param string $parent_file - Parent file slug to set as current.
	 *
	 * @return string
	 */
	protected function set_current_menu( string $parent_file ): string {
		$screen = \get_current_screen();
		if ( null === $screen || ! isset( $this->menu_configuration['parent'] ) ) {
			return $parent_file;
		}
		if ( "edit-{$this->taxonomy}" === $screen->id && $this->taxonomy === $screen->taxonomy ) {
			return $this->menu_configuration['parent'];
		}

		return $parent_file;
	}


	/**
	 * Handles any calls, which need to run to register this taxonomy.
	 *
	 * @action wp_loaded 8 0
	 *
	 * @return void
	 */
	protected function register(): void {
		$this->register_taxonomy();
		static::$registry[ $this->taxonomy ] = $this;
		if ( \count( $this->initial_terms ) > 0 ) {
			$this->insert_initial_terms();
		}
	}


	/**
	 * Sets the singular and plural labels automatically.
	 *
	 * @param string $singular - The singular label to use.
	 * @param string $plural   - The plural label to use.
	 *
	 * @return void
	 */
	public function set_label( string $singular = '', string $plural = '' ): void {
		if ( '' === $singular ) {
			$singular = \ucwords( \str_replace( '_', ' ', $this->taxonomy ) );
		}
		if ( '' === $plural ) {
			if ( \str_ends_with( $singular, 'y' ) ) {
				$plural = \substr( $singular, 0, - 1 ) . 'ies';
			} else {
				$plural = $singular . 's';
			}
		}

		$this->labels->singular_name( $singular );
		$this->labels->name( $plural );
	}


	/**
	 * Include a link in the "+ New" menu in the admin bar to
	 * quickly add a new term.
	 *
	 * Similar to how post types automatically have a link to add a new post.
	 *
	 * @since 4.10.0
	 *
	 * @return void
	 */
	public function show_in_admin_bar(): void {
		$cap = $this->capabilities->get_cap( 'edit_terms' ) ?? 'manage_categories';
		if ( ! current_user_can( $cap ) ) {
			return;
		}
		add_action( 'admin_bar_menu', function( \WP_Admin_Bar $wp_admin_bar ) {
			$wp_admin_bar->add_menu( [
				'id'     => 'new-' . $this->taxonomy,
				'title'  => $this->labels->get_label( 'singular_name' ) ?? '',
				'parent' => 'new-content',
				'href'   => admin_url( 'edit-tags.php?taxonomy=' . $this->taxonomy ),
			] );
		}, 100 );
	}


	/**
	 * Set individual labels for the taxonomy.
	 *
	 * @since 5.0.0
	 *
	 * @return Labels
	 */
	public function labels(): Labels {
		return $this->labels;
	}


	/**
	 * Inserts any specified terms for a new taxonomy.
	 * Will run only once when term is first registered.
	 * Will only run on fresh taxonomies with no existing terms.
	 *
	 * @return void
	 */
	protected function insert_initial_terms(): void {
		$already_defaulted = get_option( 'lipe/lib/taxonomy/defaults-registry', [] );

		if ( ! isset( $already_defaulted[ $this->slug ] ) ) {
			// Don't do anything if the taxonomy already has terms.
			$existing = get_terms( [
				'taxonomy' => $this->taxonomy,
				'fields'   => 'count',
			] );
			if ( \is_numeric( $existing ) && 0 === (int) $existing ) {
				foreach ( $this->initial_terms as $slug => $term ) {
					$args = [];
					if ( ! \is_numeric( $slug ) ) {
						$args['slug'] = $slug;
					}
					wp_insert_term( $term, $this->taxonomy, $args );
				}
			}
			$already_defaulted[ $this->slug ] = 1;
			update_option( 'lipe/lib/taxonomy/defaults-registry', $already_defaulted, true );
		}
	}


	/**
	 * Register this taxonomy with WordPress
	 *
	 * Allow using a different process for registering taxonomies via
	 * child classes.
	 */
	protected function register_taxonomy(): void {
		register_taxonomy( $this->taxonomy, $this->post_types, $this->taxonomy_args() );
	}


	/**
	 * Build the args array for the taxonomy definition
	 *
	 * @return array<string, mixed>
	 */
	protected function taxonomy_args(): array {
		$args = $this->register_args;
		$args->labels = $this->taxonomy_labels();
		$args->public = $this->public;
		$args->publicly_queryable = $this->publicly_queryable ?? $this->public;
		$args->show_ui = $this->show_ui ?? $this->public;
		$args->show_in_nav_menus = $this->show_in_nav_menus ?? $this->public;
		$args->rewrite = $this->rewrites();
		$args->capabilities = $this->capabilities->get_capabilities();
		$args->sort = $this->sort;
		$args->description = $this->description;
		$args->hierarchical = $this->hierarchical;

		if ( isset( $this->rest_base ) ) {
			$args->rest_base = $this->rest_base;
		}
		if ( isset( $this->rest_namespace ) ) {
			$args->rest_namespace = $this->rest_namespace;
		}
		if ( isset( $this->rest_controller_class ) ) {
			$args->rest_controller_class = $this->rest_controller_class;
		}
		if ( isset( $this->show_tagcloud ) ) {
			$args->show_tagcloud = $this->show_tagcloud;
		}
		if ( isset( $this->args ) ) {
			$args->args = $this->args->get_args();
		}
		if ( isset( $this->show_in_quick_edit ) ) {
			$args->show_in_quick_edit = $this->show_in_quick_edit;
		}
		if ( null !== $this->update_count_callback ) {
			$args->update_count_callback = $this->update_count_callback;
		}
		if ( isset( $this->query_var ) ) {
			$args->query_var = $this->query_var;
		}
		if ( isset( $this->default_term ) ) {
			$args->default_term = $this->default_term;
		}

		$args = apply_filters( 'lipe/lib/taxonomy/args', $args->get_args(), $this->taxonomy );
		return apply_filters( "lipe/lib/taxonomy/args_{$this->taxonomy}", $args );
	}


	/**
	 * Build the labels array for the post type definition
	 *
	 * @param string|null $single - The singular label to use.
	 * @param string|null $plural - The plural label to use.
	 *
	 * @return array<Labels::*, string>
	 */
	protected function taxonomy_labels( ?string $single = null, ?string $plural = null ): array {
		$single = $single ?? $this->labels->get_label( 'singular_name' );
		$plural = (string) ( $plural ?? $this->labels->get_label( 'name' ) );
		$menu = $this->labels->get_label( 'menu_name' ) ?? $this->labels->get_label( 'name' );

		// phpcs:disable WordPress.WP.I18n -- Allow core translations to work.
		$labels = [
			'name'                       => $plural,
			'singular_name'              => $single,
			'search_items'               => sprintf( __( 'Search %s' ), $plural ),
			'popular_items'              => sprintf( __( 'Popular %s' ), $plural ),
			'all_items'                  => sprintf( __( 'All %s' ), $plural ),
			'parent_item'                => sprintf( __( 'Parent %s' ), $single ),
			'parent_item_colon'          => sprintf( __( 'Parent %s:' ), $single ),
			'edit_item'                  => sprintf( __( 'Edit %s' ), $single ),
			'view_item'                  => sprintf( __( 'View %s' ), $single ),
			'update_item'                => sprintf( __( 'Update %s' ), $single ),
			'add_new_item'               => sprintf( __( 'Add New %s' ), $single ),
			'new_item_name'              => sprintf( __( 'New %s Name' ), $single ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas' ), $plural ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s' ), $plural ),
			'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s' ), $plural ),
			'not_found'                  => sprintf( __( 'No %s found' ), $plural ),
			'no_terms'                   => sprintf( __( 'No %s' ), $plural ),
			'no_item'                    => sprintf( __( 'No %s' ), \strtolower( $plural ) ), // For extended taxos.
			'items_list_navigation'      => sprintf( __( '%s list navigation' ), $plural ),
			'items_list'                 => sprintf( __( '%s list' ), $plural ),
			'most_used'                  => __( 'Most Used' ),
			'back_to_items'              => sprintf( __( '&larr; Back to %s' ), $plural ),
			'menu_name'                  => $menu,
		];
		// phpcs:enable WordPress.WP.I18n
		$labels = wp_parse_args( $this->labels->get_labels(), $labels );

		$labels = apply_filters( 'lipe/lib/taxonomy/labels', $labels, $this->taxonomy );
		return apply_filters( "lipe/lib/taxonomy/labels_{$this->taxonomy}", $labels );
	}


	/**
	 * Build rewrite args or pass the class var if set.
	 *
	 * @phpstan-return REWRITE|bool
	 * @return array|bool
	 */
	protected function rewrites(): array|bool {
		return $this->rewrite ?? [
			'slug'         => $this->slug,
			'with_front'   => false,
			'hierarchical' => $this->hierarchical,
		];
	}


	/**
	 * If the taxonomies registered through this API have changed,
	 * rewrite rules need to be flushed.
	 *
	 * @action wp_loaded 1_000 0
	 */
	protected function check_rewrite_rules(): void {
		$slugs = \array_keys( static::$registry );
		if ( get_option( static::REGISTRY_OPTION ) !== $slugs ) {
			flush_rewrite_rules();
			update_option( static::REGISTRY_OPTION, $slugs );
		}
	}


	/**
	 * Render a button to clear the filters on the post list page.
	 *
	 * @action manage_posts_extra_tablenav 1000
	 */
	protected function clear_filters_button(): void {
		// phpcs:ignore WordPress.Security.NonceVerification -- Nonce not required.
		$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : '';
		if ( '' !== $post_type ) {
			$base_url = admin_url( 'edit.php?post_type=' . $post_type );
		} else {
			$base_url = admin_url( 'edit.php' );
		}
		?>
		<a
			href="<?= esc_url( $base_url ) ?>"
			class="button lipe-libs-taxonomy-clear-filters"
		>
			<?php esc_html_e( 'Clear Filters', 'lipe' ); ?>
		</a>
		<?php
	}


	/**
	 * Get a registered taxonomy object.
	 *
	 * @param string $taxonomy - Taxonomy slug.
	 *
	 * @return ?Taxonomy
	 */
	public static function get_taxonomy( string $taxonomy ): ?Taxonomy {
		return static::$registry[ $taxonomy ] ?? null;
	}
}
