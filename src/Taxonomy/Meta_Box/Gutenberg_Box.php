<?php
declare( strict_types=1 );

namespace Lipe\Lib\Taxonomy\Meta_Box;

use Lipe\Lib\Taxonomy\Meta_Box;
use Lipe\Lib\Util\Arrays;

/**
 * Hold a JS configuration for a taxonomy meta box.
 *
 * @author Mat Lipe
 * @since  4.10.0
 */
class Gutenberg_Box implements \JsonSerializable {
	public const TYPE_RADIO    = 'radio';
	public const TYPE_DROPDOWN = 'dropdown';
	public const TYPE_SIMPLE   = 'simple';


	/**
	 * Construct and pass the configuration to the JS.
	 *
	 * @phpstan-param self::TYPE_* $type
	 *
	 * @param string               $type          The type of meta box to display.
	 *                                            - radio: A radio button list.
	 *                                            - dropdown: A dropdown select.
	 *                                            - simple: A simple list of checkboxes.
	 * @param string               $taxonomy      The taxonomy to attach the meta box to.
	 * @param bool                 $checked_ontop Should the checked items be on top.
	 */
	final protected function __construct(
		protected string $type,
		protected string $taxonomy,
		protected bool $checked_ontop
	) {
		$this->add_to_js_config();
	}


	/**
	 * Register the meta box with the JS config.
	 *
	 * @return void
	 */
	protected function add_to_js_config(): void {
		add_filter( 'lipe/lib/libs/scripts/js-config', function( array $config ): array {
			if ( isset( $config['taxonomyMetaBoxes'] ) ) {
				$i = Arrays::in()->find_index( $config['taxonomyMetaBoxes'], fn( Gutenberg_Box $box ) => $box->taxonomy === $this->taxonomy );
				if ( null !== $i ) {
					$config['taxonomyMetaBoxes'][ $i ] = $this;
				} else {
					$config['taxonomyMetaBoxes'][] = $this;
				}
			} else {
				$config['taxonomyMetaBoxes'] = [ $this ];
			}

			return $config;
		} );
	}


	/**
	 * @return array{
	 *     type: self::TYPE_*,
	 *     taxonomy: string,
	 *     checkedOnTop: bool
	 * }
	 */
	public function jsonSerialize(): array {
		return [
			'type'         => $this->type,
			'taxonomy'     => $this->taxonomy,
			'checkedOnTop' => $this->checked_ontop,
		];
	}


	/**
	 * Public access to constructing a new instance.
	 *
	 * @see Taxonomy::meta_box()
	 *
	 * @param Meta_Box $box Taxonomy Meta_Box to convert to a Gutenberg_Box.
	 */
	public static function factory( Meta_Box $box ): Gutenberg_Box {
		return new static( $box->type, $box->taxonomy, $box->checked_ontop );
	}
}
