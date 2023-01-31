<?php

namespace Lipe\Lib\CMB2;

use Lipe\Lib\CMB2\Box\Tabs;
use Lipe\Lib\Meta\Repo;

/**
 * Main meta box class.
 *
 * A fluent interface for CMB2 meta box properties.
 *
 */
class Box {
	use Box_Trait;

	/**
	 * Used as a flag to allow REST fields to be added
	 * to the `meta` response without the `cmb2` REST
	 * endpoint being added.
	 *
	 */
	public const EXCLUDE_CMB2_REST_ENDPOINT = 'lipe/lib/cmb2/box/exclude-cmb2-rest-endpoint';

	/**
	 * The id of metabox
	 *
	 * @link https://github.com/CMB2/CMB2/wiki/Box-Properties#id
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The context within the screen where the boxes should display.
	 * Available contexts vary from screen to screen.
	 *
	 * @see     Box
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#context
	 * @example 'normal', 'side', 'advanced' 'form_top',
	 *          'before_permalink', 'after_title', 'after_editor'
	 *
	 * @var string
	 */
	protected $context;

	/**
	 * Title display in the admin metabox.
	 *
	 * To keep from registering an actual post-screen metabox,
	 * omit the 'title' property from the metabox registration array.
	 * (WordPress will not display metaboxes without titles anyway)
	 * This is a good solution if you want to handle outputting your
	 * metaboxes/fields elsewhere in the post-screen.
	 *
	 * @link https://github.com/CMB2/CMB2/wiki/Box-Properties#title
	 *
	 * @var string|false
	 */
	protected $title = '';

	/**
	 * Priority of the metabox in its context.
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#priority
	 *
	 * @default 'high'
	 *
	 * @var 'high' | 'core' | 'default' | 'low'
	 */
	public $priority = 'high';

	/**
	 * This property allows you to optionally add classes to the CMB2 wrapper.
	 * This property can take a string, or array.
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#classes
	 *
	 * @example 'additional-class'
	 * @example array( 'additional-class', 'another-class' ),
	 *
	 * @var mixed
	 */
	public $classes;

	/**
	 * Like the classes property, allows adding classes to the CMB2 wrapper,
	 * but takes a callback.
	 * That callback should return an array of classes.
	 * The callback gets passed the CMB2 $properties array as the first argument,
	 * and the CMB2 $cmb object as the second argument.
	 *
	 * @link   https://github.com/CMB2/CMB2/wiki/Box-Properties#classes_cb
	 *
	 * @example: 'yourprefix_function_to_add_classes( $properties, $cmb ){ return [] }',
	 *
	 * @var callable
	 */
	public $classes_cb;

	/**
	 * Set to true to default metabox being closed
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#closed
	 *
	 * @example true
	 * @default false
	 *
	 * @var bool
	 */
	public $closed;

	/**
	 * Whether to enqeue CMB2 stylesheet
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#cmb_styles
	 *
	 * @example false
	 * @default true
	 *
	 * @var bool
	 */
	public $cmb_styles;

	/**
	 * Whether to enqeue CMB2 Javascript files.
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#enqueue_js
	 *
	 * @example false
	 * @default true
	 *
	 * @var bool
	 */
	public $enqueue_js;

	/**
	 * Handles hooking CMB2 forms/metaboxes into the post/attachment/user screens,
	 * and handles hooking in and saving those fields.
	 * Set to false if you plan on handling the form/field output/saving
	 * (via something like cmb2_metabox_form()).
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#hookup
	 *
	 * @default true
	 *
	 * @var bool
	 */
	public $hookup;

	/**
	 * Override the rendering of the box on rest api responses.
	 * Used to create an entirely custom response.
	 *
	 * @link  https://gist.github.com/jtsternberg/a70e845aca44356b8fbf05aafff4d0c8
	 *
	 * @todo  Add link to docs once they exist.
	 *
	 * @var callable
	 */
	public $register_rest_field_cb;

	/**
	 * This parameter is for post alternate-context metaboxes only.
	 * To output the fields 'naked' (without a postbox wrapper/style)
	 *
	 * @note    Must set title of box to false
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#context
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#remove_box_wrap
	 *
	 * @see     Box::$context
	 * @see     Box::remove_box_wrap();
	 *
	 * @example true
	 * @default false
	 *
	 * @internal
	 *
	 * @var bool
	 */
	public $remove_box_wrap;

	/**
	 * The following parameter is any additional arguments passed as $callback_args
	 * to add_meta_box, if/when applicable.
	 *
	 * CMB2 does not use these arguments in the add_meta_box callback, however, these args
	 * are parsed for certain special properties, like determining Gutenberg/block-editor
	 * compatibility.
	 *
	 * We have our own Gutenberg/block-editor properties in this class so use those instead
	 * of this property if you are working with Gutenberg
	 *
	 * @see Box::$display_when_gutenberg_active
	 * @see Box::$gutenberg_compatible
	 *
	 * More: https://wordpress.org/gutenberg/handbook/designers-developers/developers/backwards-compatibility/meta-box/
	 *
	 * @var array
	 */
	public $mb_callback_args;

	/**
	 * This flag lets you set whether the meta box works in the block editor or not.
	 * Setting it to true signifies that the you’ve confirmed that the meta box
	 * works in the block editor, setting it to false signifies that it doesn't.
	 *
	 * If set to false, WP will automatically fall back to the classic editor when
	 * this box is loaded.
	 *
	 * @see  Box::get_args()
	 * @see  Box::$display_when_gutenberg_active
	 *
	 * @link https://make.wordpress.org/core/2018/11/07/meta-box-compatibility-flags/
	 *
	 * @uses sets the `__block_editor_compatible_meta_box` meta box flag
	 *
	 * @var bool
	 */
	public $gutenberg_compatible = true;

	/**
	 * Set to false if you have converted this meta box fully to Gutenberg and
	 * you don't want the default meta box to display when gutenberg is active.
	 *
	 * When the classic editor is loaded this meta box will load no matter what
	 * this is set to.
	 *
	 * @see  Box::get_args()
	 * @see  Box::$gutenberg_compatible
	 *
	 * @link https://make.wordpress.org/core/2018/11/07/meta-box-compatibility-flags/
	 *
	 * @uses sets the `__back_compat_meta_box` meta box flag
	 *
	 * @var bool
	 */
	public $display_when_gutenberg_active = true;

	/**
	 * If false, will not save during hookup
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#save_fields
	 *
	 * @see     Box
	 *
	 * @example false
	 * @default true
	 *
	 * @var bool
	 */
	public $save_fields;

	/**
	 * Determines if/how fields/metabox are available in the REST API.
	 *
	 * Only individual fields that are explicitly set to truthy will
	 * be included in default WP response.
	 * If the box is set to true and all fields are in the /cmb2 response.
	 *
	 * @link    https://github.com/WebDevStudios/CMB2/wiki/REST-API
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#show_in_rest
	 *
	 * @example WP_REST_Server::READABLE // Same as `true`
	 * @example WP_REST_Server::ALLMETHODS
	 * @example WP_REST_Server::EDITABLE
	 *
	 * @notice  Boxes must be registered on `cmb2_init` instead of `cmb2_admin_init`
	 *         to use this property. Change in `Meta_Provider` if applicable
	 *
	 * @default false
	 *
	 * @var string|bool
	 */
	public $show_in_rest;

	/**
	 * Whether to show labels for the fields
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#show_names
	 * @default true
	 * @example false
	 *
	 * @var bool
	 */
	public $show_names;

	/**
	 * Post IDs or page templates to display this metabox.
	 * Overrides 'show_on_cb'.
	 * More info:
	 *
	 * @link    https://github.com/WebDevStudios/CMB2/wiki/Adding-your-own-show_on-filters
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#show_on
	 *
	 * @example array( 'key' => 'page-template', 'value' => 'template-contact.php' )
	 * @example array( 'key' => 'id', 'value' => array( 50, 24 ) )
	 *
	 * @var  array{key:string,value:string|array<int>}
	 */
	public $show_on;

	/**
	 * To show or not based on the result
	 * of a function.
	 * Pass a function name here
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#show_on_cb
	 *
	 * @example should_show_meta_box( $cmb ){ return bool; }
	 *
	 * @var callable
	 */
	public $show_on_cb;

	/**
	 * Tabs for this box
	 *
	 * @see     Box::add_tab
	 *
	 * @var array
	 */
	public array $tabs = [];

	/**
	 * Tabs to display either vertical or horizontal
	 *
	 * @see     Box::tabs_style()
	 *
	 * @var string
	 */
	protected string $tab_style = 'vertical';

	/**
	 * @var \CMB2
	 */
	public \CMB2 $cmb;


	/**
	 * Register a new meta box.
	 *
	 * @template Location of 'normal'|'side'|'advanced'|'form_top'|'before_permalink'| 'after_title'|'after_editor'
	 *
	 * @param string      $id           - ID of this box.
	 * @param array       $object_types - [post type slugs], or 'user', 'term',
	 *                                  'comment', or 'options-page'.
	 * @param string|null $title        - Title of this box.
	 * @param Location    $context      - Location the meta box will display.
	 */
	public function __construct( string $id, array $object_types, ?string $title = null, $context = 'normal' ) {
		$this->id = $id;
		$this->object_types = $object_types;
		$this->title = $title;
		$this->context = $context;
	}


	/**
	 * Display a description at the top of a meta box, or an option's page
	 *
	 * @param string $description
	 *
	 * @return void
	 */
	public function description( string $description ) : void {
		$types[] = 'post';
		foreach ( $this->get_object_types() as $_type ) {
			add_action( "cmb2_before_{$_type}_form_{$this->id}", function () use ( $description ) {
				?>
				<div class="cmb-row">
					<p>
						<span class="description">
							<?= wp_kses_post( $description ) ?>
						</span>
					</p>
				</div>
				<?php
			} );
		}
	}


	/**
	 * If the box's `show_in_rest` is false, and a non `false` parameter
	 * is passed, the box's `show_in_rest` will be set to true and all
	 * fields which do not have a `show_in_rest` specified will be set false.
	 *
	 * Only individual fields that are explicitly set to truthy will
	 * be included in default WP response even if the box is set to true
	 * and all fields are in the /cmb2 response.
	 *
	 *
	 * @param string|bool $methods
	 *
	 * @see     Box_Trait::selectively_show_in_rest()
	 *
	 * @example WP_REST_Server::READABLE // Same as `true`
	 * @example WP_REST_Server::ALLMETHODS
	 * @example WP_REST_Server::EDITABLE
	 * @example false // Will allow fields to show
	 *          up under the `meta` key without also showing in `cmb2`.
	 *
	 *
	 * @return void
	 */
	public function show_in_rest( $methods = \WP_REST_Server::READABLE ) : void {
		if ( false === $methods ) {
			$this->show_in_rest = static::EXCLUDE_CMB2_REST_ENDPOINT;
		} else {
			$this->show_in_rest = $methods;
		}
	}


	/**
	 * Add a tab to this box which can later be assigned to fields via
	 * Field::tab( $id );
	 *
	 * @param string $id
	 * @param string $label
	 *
	 * @see     \Lipe\Lib\CMB2\Field::tab();
	 *
	 *
	 * @return void
	 */
	public function add_tab( string $id, string $label ) : void {
		$this->tabs[ $id ] = $label;
		Tabs::init_once();
	}


	/**
	 * Remove meta box wrap for alternate-context meta-boxes
	 *
	 * Takes care of setting the title to false which is required
	 * by the native `remove_box_wrap` property
	 *
	 * @link  https://github.com/CMB2/CMB2/wiki/Box-Properties#context
	 *
	 *
	 * @return void
	 */
	public function remove_box_wrap() : void {
		$this->title = false;
		$this->remove_box_wrap = true;
	}


	/**
	 * Should the tabs display vertical or horizontal?
	 * Default is vertical when not calling this.
	 *
	 * @param string $layout - vertical, horizontal.
	 *
	 *
	 * @return void
	 */
	public function tabs_style( string $layout = 'horizontal' ) : void {
		$this->tab_style = $layout;
	}


	/**
	 * Id of the CMB2 meta box, also stored as the id of this class.
	 *
	 * @link    https://github.com/CMB2/CMB2/wiki/Box-Properties#id
	 *
	 * @example 'lipe/project/meta/category-fields',
	 *
	 * @return string
	 */
	public function get_id() : string {
		return $this->id;
	}


	/**
	 * Get teh CMB2 version of this box.
	 *
	 * @return \CMB2
	 */
	public function get_box() : \CMB2 {
		if ( null !== $this->cmb ) {
			return $this->cmb;
		}

		$args = $this->get_args();
		$this->cmb = new_cmb2_box( $args );

		return $this->cmb;
	}


	protected function get_args() : array {
		$args = [];
		foreach ( get_object_vars( $this ) as $_var => $_value ) {
			if ( 'cmb' === $_var || ! isset( $this->{$_var} ) || 'fields' === $_var ) {
				continue;
			}
			$args[ $_var ] = $this->{$_var};
		}

		if ( isset( $args['show_in_rest'] ) && static::EXCLUDE_CMB2_REST_ENDPOINT === $args['show_in_rest'] ) {
			$args['show_in_rest'] = false;
		}

		$args['mb_callback_args'] = $this->get_meta_box_callback_args();

		return $args;
	}


	/**
	 * Handle any massaging of callback arguments and return them
	 *
	 * Take care of the Gutenberg properties
	 *
	 * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/backwards-compatibility/meta-box/
	 *
	 * @return array
	 */
	protected function get_meta_box_callback_args() : array {
		if ( ! isset( $this->mb_callback_args['__block_editor_compatible_meta_box'] ) ) {
			$this->mb_callback_args['__block_editor_compatible_meta_box'] = $this->gutenberg_compatible;
		}

		if ( ! isset( $this->mb_callback_args['__back_compat_meta_box'] ) ) {
			// Notice we use the opposite here.
			$this->mb_callback_args['__back_compat_meta_box'] = ! $this->display_when_gutenberg_active;
		}

		return $this->mb_callback_args;
	}


	/**
	 * 1. Add a field to this CMB2 box.
	 * 2. Assign the box_id to the field.
	 * 3. Register the field with the Meta\Repo.
	 *
	 * @param Field $field
	 *
	 * @return void
	 */
	protected function add_field_to_box( Field $field ) : void {
		$box = $this->get_box();
		$box->add_field( $field->get_field_args(), $field->position );
		$field->box_id = $this->id;

		Repo::in()->register_field( $field );
	}


	/**
	 * Register the meta on all types or subtypes of this
	 * box's object type.
	 *
	 * 1. Adds the default value if provided.
	 * 2. Translates long /namespaced names to API friendly.
	 *    If you need the long names due to conflicts, they will still
	 *    be available via /cmb2 values.
	 * 3. Registers the provided 'show_in_rest' configuration.
	 *
	 * Support a default value for any `get_metadata()` calls.
	 * Will add the values of all subfields.
	 *
	 * @param Field $field
	 * @param array $config
	 *
	 */
	public function register_meta_on_all_types( Field $field, array $config ) : void {
		if ( null !== $field->default ) {
			$config['default'] = $field->default;
		}

		if ( isset( $config['show_in_rest'] ) ) {
			$config = $this->translate_rest_keys( $field, $config );
		}

		if ( $field->sanitize_callback ) {
			$config['sanitize_callback'] = function ( $value ) use ( $field ) {
				return \call_user_func( $field->sanitize_callback, $value, $field->get_field_args(), $field->get_cmb2_field() );
			};
		}

		// Nothing to register.
		if ( 3 > \count( $config ) ) {
			return;
		}

		$type = $this->get_object_type();
		$sub_types = $this->object_types;
		if ( 'term' === $this->get_object_type() ) {
			$type = 'term';
			if ( isset( $this->taxonomies ) ) {
				$sub_types = $this->taxonomies;
			}
		} elseif ( \in_array( $this->get_object_type(), [ 'user', 'comment' ], true ) ) {
			$sub_types = [ false ];
		}

		foreach ( $sub_types as $_type ) {
			$config['object_subtype'] = $_type;
			register_meta( $type, $field->get_id(), $config );

			// A secondary field for file ids.
			if ( Repo::FILE === $field->data_type ) {
				if ( ! empty( $config['show_in_rest']['name'] ) ) {
					$config['show_in_rest']['name'] .= '_id';
					unset( $config['show_in_rest']['prepare_callback'] );
				}
				register_meta( $type, $field->get_id() . '_id', $config );
			}
		}
	}

}
