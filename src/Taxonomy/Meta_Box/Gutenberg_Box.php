<?php
declare( strict_types=1 );

namespace Lipe\Lib\Taxonomy\Meta_Box;

use Lipe\Lib\Libs\Scripts;
use Lipe\Lib\Libs\Scripts\ScriptHandles;
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
	 * @var array<Gutenberg_Box>
	 */
	protected static array $boxes = [];


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

		add_action( 'enqueue_block_assets', [ $this, 'load_script' ], 25 );
	}


	/**
	 * Register the meta box with the JS config.
	 *
	 * @return void
	 */
	protected function add_to_js_config(): void {
		$i = Arrays::in()->find_index( static::$boxes, fn( Gutenberg_Box $box ) => $box->taxonomy === $this->taxonomy );
		if ( null !== $i ) {
			static::$boxes[ $i ] = $this;
		} else {
			static::$boxes[] = $this;
		}
	}


	/**
	 * Load the script for the meta boxes and localize it one time.
	 *
	 * We only localize this once because multiple calls will append the
	 * config multiple times.
	 *
	 * @note `enqueue_block_assets` gets called 2x per each page, once for the
	 * editor iframe and one for the admin so this must be allowed to run multiple times.
	 *
	 * @action enqueue_block_assets 25 0
	 *
	 * @return void
	 */
	public function load_script(): void {
		Scripts::in()->enqueue_script( ScriptHandles::META_BOXES );

		if ( false === wp_scripts()->get_data( ScriptHandles::META_BOXES->value, 'data' ) ) {
			wp_localize_script( ScriptHandles::META_BOXES->value, 'LIPE_LIBS_META_BOXES', static::$boxes );
		}
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
