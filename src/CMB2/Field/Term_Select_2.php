<?php
declare( strict_types=1 );

namespace Lipe\Lib\CMB2\Field;

use Lipe\Lib\CMB2\Field\Term_Select_2\Register;
use Lipe\Lib\Libs\Scripts;
use Lipe\Lib\Libs\Scripts\ScriptHandles;
use Lipe\Lib\Libs\Scripts\StyleHandles;
use Lipe\Lib\Meta\Repo;
use Lipe\Lib\Taxonomy\Get_Terms;
use Lipe\Lib\Traits\Memoize;
use Lipe\Lib\Traits\Singleton;

/**
 * Select 2 Field for Terms.
 */
class Term_Select_2 {
	use Memoize;
	use Singleton;

	public const NAME = 'lipe/lib/cmb2/field-types/term-select-2';

	public const GET_TERMS        = 'lipe/lib/cmb2/field-types/term-select-2/ajax';
	public const CREATE_NEW_TERMS = 'create_terms';

	/**
	 * Fields that have been registered.
	 *
	 * @var Register[]
	 */
	protected static array $registered = [];


	/**
	 * Called via self::init_once() from various entry points
	 *
	 * @return void
	 */
	protected function hook(): void {
		add_action( 'cmb2_render_' . self::NAME, function( \CMB2_Field $field, array|string $value, int|string $object_id, string $object_type, \CMB2_Types $field_type ) {
			$this->render( $field, $value, $field_type );
		}, 10, 5 );
		add_filter( 'cmb2_sanitize_' . self::NAME, [ $this, 'set_object_terms' ], 10, 4 );
		add_filter( 'cmb2_types_esc_' . self::NAME, [ $this, 'esc_values' ], 10, 3 );
		add_action( 'wp_ajax_' . self::GET_TERMS, [ $this, 'ajax_get_terms' ] );

		// Remove subtle conflict with acf.
		add_filter( 'acf/settings/select2_version', fn() => 4 );
	}


	/**
	 * Get available terms via AJAX.
	 *
	 * Done via AJAX to support taxonomies not available on the REST API
	 * and to allow for the creation of new terms.
	 *
	 * @return void
	 */
	public function ajax_get_terms(): void {
		check_ajax_referer( self::GET_TERMS );
		$field = $this->get_registered( sanitize_text_field( wp_unslash( $_POST['id'] ?? '' ) ) );
		if ( null === $field ) {
			wp_send_json_error( 'Field not found.' );
		}

		if ( isset( $_POST['selected'] ) && \is_array( $_POST['selected'] ) ) {
			$selected = \array_map( 'sanitize_text_field', \wp_unslash( $_POST['selected'] ) );
		}

		$args = new Get_Terms( [] );
		$args->number = 10;
		$args->taxonomy = $field->taxonomy;
		$args->fields = Get_Terms::FIELD_ALL;
		$args->hide_empty = false;
		$args->exclude = \array_map( '\intval', $selected ?? [] );
		$args->search = sanitize_text_field( wp_unslash( $_POST['term'] ?? '' ) );

		$terms = get_terms( $args->get_light_args() );
		if ( is_wp_error( $terms ) ) {
			wp_send_json_error( $terms->get_error_message() );
		}
		if ( \is_string( $terms ) ) {
			wp_send_json_error( 'Failed retrieving terms.' );
		}

		$formatted = \array_map( fn( \WP_Term $term ) => [
			'id'   => $term->term_id,
			'text' => $term->name,
		], \array_filter( $terms, fn( $term ) => $term instanceof \WP_Term ) );
		wp_send_json_success( \array_values( $formatted ) );
	}


	/**
	 * Render the Field.
	 *
	 * @param \CMB2_Field          $field             This field object.
	 * @param array<string>|string $value             The value of this field escaped.
	 * @param \CMB2_Types          $field_type_object The field type object.
	 *
	 * @return void
	 */
	public function render( \CMB2_Field $field, array|string $value, \CMB2_Types $field_type_object ): void {
		$field_type_object->type = new \CMB2_Type_Select( $field_type_object );

		$attrs = \CMB2_Utils::concat_attrs( [
			'multiple'         => false === $field->args( 'multiple' ) ? 'multiple' : $field->args( 'multiple' ),
			'data-js'          => $field->id(),
			'name'             => $field_type_object->_name() . '[]',
			'id'               => $field_type_object->_id(),
			'class'            => 'regular-text',
			'data-placeholder' => $field->args( 'attributes', 'placeholder' ) ?? $field->args( 'description' ),
		] );

		\printf( '<select%s>%s</select>%s',
			$attrs, //phpcs:ignore WordPress.Security.EscapeOutput
			$this->get_multi_select_options( $field_type_object, (array) $value ), //phpcs:ignore WordPress.Security.EscapeOutput
			$field_type_object->_desc( true ) //phpcs:ignore WordPress.Security.EscapeOutput
		);

		$this->load_scripts();
	}


	/**
	 * Return the list of options, with selected options at the top preserving their order.
	 *
	 * Also handles the removal of selected options which no longer exist in the options array.
	 *
	 * @param \CMB2_Types       $types - The field type object.
	 * @param array<int|string> $value - The selected values.
	 *
	 * @return string
	 */
	protected function get_multi_select_options( \CMB2_Types $types, array $value ): string {
		if ( [] !== $value ) {
			$value = \array_map( '\intval', $value );
			$options = \get_terms( [
				'include'    => $value,
				'fields'     => 'id=>name',
				'taxonomy'   => $types->field->args['taxonomy'] ?? 'category',
				'hide_empty' => false,
			] );
			if ( [] === $options || is_wp_error( $options ) ) {
				return '';
			}
		} else {
			return '';
		}

		$select = new \CMB2_Type_Select( $types );

		$output = '';
		foreach ( $options as $term_id => $option_label ) {
			$option = [
				'value'   => $term_id,
				'label'   => $option_label,
				'checked' => true,
			];
			$output .= $select->select_option( $option );
		}
		return $output;
	}


	/**
	 * Based on the passed optionsAssign terms to the object.
	 *
	 * @param mixed                $filtered   - The filtered value. (probably null).
	 * @param mixed                $meta_value - The value of the field.
	 * @param int|string           $id         - Post id on post screens, field key on settings screens.
	 * @param array<string, mixed> $field_args - The field args.
	 *
	 * @return list<int>|list<List<int>>|null
	 */
	public function set_object_terms( mixed $filtered, mixed $meta_value, int|string $id, array $field_args ): ?array {
		if ( ! \is_array( $meta_value ) ) {
			return $filtered;
		}

		$field = $this->get_registered( $field_args['id'] );
		foreach ( $meta_value as $key => $val ) {
			if ( ! \is_array( $val ) ) {
				$meta_value[ $key ] = (int) ( $val );
				continue;
			}
			$meta_value[ $key ] = \array_map( '\intval', $val );
		}

		if ( null === $field || ! $field->assign_terms ) {
			return $meta_value;
		}

		$meta_type = $field->field->get_box()?->get_object_type() ?? '';
		if ( '' !== $id && 0 !== $id && Repo::in()->supports_taxonomy_relationships( $meta_type, $field->field ) ) {
			if ( $field->is_repeatable() ) {
				$ids = \array_merge( ...$meta_value );
				\wp_set_object_terms( (int) $id, \array_map( '\intval', $ids ), $field->taxonomy );
			} else {
				\wp_set_object_terms( (int) $id, \array_map( '\intval', $meta_value ), $field->taxonomy );
			}
		}

		return $meta_value;
	}


	/**
	 * Handle repeatable data escaping
	 *
	 * @param mixed                                $filtered   - Filtered value (probably null).
	 * @param null|array<string[]>|string[]|string $values     - The value of the field.
	 * @param array<string, mixed>                 $field_args - The field args.
	 *
	 * @return null|array<string[]>|string[]|string
	 */
	public function esc_values( mixed $filtered, array|null|string $values, array $field_args ): null|array|string {
		$field = $this->get_registered( $field_args['id'] );
		if ( ! \is_array( $values ) || null === $field ) {
			return $filtered;
		}

		foreach ( $values as $key => $val ) {
			if ( ! \is_array( $val ) ) {
				$values[ $key ] = \esc_attr( $val );
				continue;
			}
			$values[ $key ] = \array_map( 'esc_attr', $val );
		}

		return $values;
	}


	/**
	 * - Select 2 JS and CSS
	 * - Admin JS
	 * - Meta Box CSS
	 */
	protected function load_scripts(): void {
		//phpcs:ignore WordPress.WP.EnqueuedResourceParameters -- Version handled by the CDN.
		wp_enqueue_style( 'select2', 'https://unpkg.com/select2@4.0.13/dist/css/select2.min.css', [], null );
		//phpcs:ignore WordPress.WP.EnqueuedResourceParameters -- Version handled by the CDN.
		wp_enqueue_script( 'select2', 'https://unpkg.com/select2@4.0.13/dist/js/select2.min.js', [ 'jquery' ], null );

		Scripts::in()->enqueue_script( ScriptHandles::ADMIN );
		Scripts::in()->enqueue_style( StyleHandles::META_BOXES );
	}


	/**
	 * Get a registered field.
	 *
	 * @param string $field_id - The field id.
	 */
	protected function get_registered( string $field_id ): ?Register {
		return static::$registered[ $field_id ] ?? null;
	}


	/**
	 * JS configurations for multiple select2 fields.
	 *
	 * @return array{ajaxUrl: string, action: string, fields: list<Register>}
	 */
	public function js_config(): array {
		$url = html_entity_decode( wp_nonce_url( admin_url( 'admin-ajax.php' ), self::GET_TERMS ) );

		return [
			'ajaxUrl' => add_query_arg( [ 'action' => self::GET_TERMS ], $url ),
			'action'  => self::GET_TERMS,
			'fields'  => \array_values( static::$registered ),
		];
	}


	/**
	 * Register a field with Select 2.
	 *
	 * @param Register $field - The field to register.
	 */
	public function register( Register $field ): void {
		static::$registered[ $field->field->get_id() ] = $field;
		static::init_once();
	}
}
