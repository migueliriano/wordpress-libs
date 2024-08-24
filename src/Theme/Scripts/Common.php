<?php
declare( strict_types=1 );

namespace Lipe\Lib\Theme\Scripts;

use Lipe\Lib\Theme\Resources;
use Lipe\Lib\Traits\Memoize;

/**
 * Common resource loading and configuration shared cross site.
 *
 * Scripts may be conditionally excluded, and their dependencies
 * using the `ResourceHandles` enum.
 *
 * @since    5.1.0
 *
 * @see      Handles - For configuring which scripts load.
 */
class Common {
	use Memoize;

	public const CSS_ENUM_HANDLE = 'lipe/project/theme/css-enums';


	/**
	 * Instantiate the Common class with the required dependencies.
	 *
	 * @param ResourceHandles[] $handles - Array of resource handles.
	 * @param Config            $scripts - Scripts class which supports a `js_config` method.
	 */
	final protected function __construct(
		protected readonly array $handles,
		protected readonly Config $scripts,
	) {
	}


	/**
	 * Add the actions and filters for the class.
	 *
	 * @return void
	 */
	public function init_once(): void {
		$this->static_once( function() {
			add_action( 'setup_theme', function() {
				$this->load_css_enums();
				$this->support_block_inline_styles();
			} );
			add_action( 'init', function() {
				$this->include_styles_in_editor();
			} );
			add_action( 'admin_enqueue_scripts', function() {
				$this->admin_scripts();
			}, 11 );
			add_action( 'enqueue_block_assets', function() {
				$this->block_scripts();
			}, 11 );
			add_action( 'wp_enqueue_scripts', function() {
				$this->theme_scripts();
			}, 11 );
			add_filter( 'wp_headers', fn( $a ) => $this->revision_header( $a ) );
			add_action( 'wp_head', function() {
				$this->remove_scripts();
			}, - 1 );
		}, __METHOD__ );
	}


	/**
	 * Remove superfluous scripts added by WP core.
	 *
	 * @action wp_head -1 0
	 *
	 * @return void
	 */
	public function remove_scripts(): void {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		// WordPress 6.3 <=.
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		// WordPress 6.4+.
		remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );

		// Remove jquery-migrate.
		$jquery = wp_scripts()->query( 'jquery' );
		if ( ! $jquery instanceof \_WP_Dependency ) {
			return;
		}
		$jquery->deps = \array_diff( $jquery->deps, [ 'jquery-migrate' ] );
	}


	/**
	 * Include the front-end styles in Gutenberg.
	 * Styles are converted to `<style>` tags wrapped in `.editor-styles-wrapper`.
	 *
	 * @notice Must refresh browser to see changes.
	 *
	 * @action init 10 0
	 *
	 * @return void
	 */
	public function include_styles_in_editor(): void {
		foreach ( $this->handles as $resource ) {
			if ( ! $resource->in_editor() ) {
				continue;
			}
			add_theme_support( 'editor-styles' );
			$enum = Enqueue::factory( $resource );
			add_editor_style( $enum->get_file() );

			/**
			 * Use regular expression to strip out the sourcemap, otherwise the
			 * sources point to random files.
			 */
			add_filter( 'block_editor_settings_all', function( $settings ) use ( $enum ) {
				$settings['styles'] = \array_map( function( $style ) use ( $enum ) {
					if ( \array_key_exists( 'baseURL', $style ) && $enum->get_url() === $style['baseURL'] ) {
						$style['css'] = \preg_replace( '/\/\*# sourceMap.*?\*\//', '', $style['css'] );
					}
					return $style;
				}, $settings['styles'] );
				return $settings;
			} );
		}
	}


	/**
	 * Use on demand block stylesheet loading.
	 *
	 * @link https://make.wordpress.org/core/2021/07/01/block-styles-loading-enhancements-in-wordpress-5-8/
	 *
	 * @return void
	 */
	public function support_block_inline_styles(): void {
		add_filter( 'should_load_separate_core_block_assets', '__return_true' );

		/**
		 * Use stylesheets instead of inline styles for WP core blocks when
		 * SCRIPT_DEBUG is true.
		 */
		if ( SCRIPT_DEBUG ) {
			add_filter( 'styles_inline_size_limit', '__return_zero' );
		}
	}


	/**
	 * @action admin_enqueue_scripts 11 0
	 */
	public function admin_scripts(): void {
		foreach ( $this->handles as $resource ) {
			if ( ! $resource->in_admin() ) {
				continue;
			}

			Enqueue::factory( $resource )->enqueue();

			if ( $resource->with_js_config() ) {
				add_action( 'admin_print_footer_scripts', function() use ( $resource ) {
					wp_localize_script( $resource->handle(), 'CORE_CONFIG', $this->scripts->js_config() );
				}, 1 );
			}
		}
	}


	/**
	 * Using the enqueue_block_assets hook assures styles are loaded:
	 * 1. In block editors.
	 * 2. In iframe block editors.
	 * 3. On the front-end.
	 * We skip #3 because we want the block styles to load on the front-end
	 * after the front-end.css file is loaded.
	 *
	 * @link   https://make.wordpress.org/core/2023/07/18/miscellaneous-editor-changes-in-wordpress-6-3/#post-editor-iframed
	 *
	 * @action enqueue_block_assets 11 0
	 */
	public function block_scripts(): void {
		if ( ! is_admin() ) {
			return;
		}
		foreach ( $this->handles as $resource ) {
			if ( ! $resource->is_block_asset() ) {
				continue;
			}
			Enqueue::factory( $resource )->enqueue();
		}
	}


	/**
	 * @action wp_enqueue_scripts 11 0
	 */
	public function theme_scripts(): void {
		Resources::in()->use_cdn_for_resources( [ 'react', 'react-dom', 'jquery', 'lodash' ] );

		foreach ( $this->handles as $resource ) {
			if ( ! $resource->in_front_end() ) {
				continue;
			}

			Enqueue::factory( $resource )->enqueue();

			if ( $resource->with_js_config() ) {
				add_action( 'wp_print_footer_scripts', function() use ( $resource ) {
					wp_localize_script( $resource->handle(), 'CORE_CONFIG', $this->scripts->js_config() );
				}, 1 );
			}
		}
	}


	/**
	 * Add a "Revision" header to the response, which matches
	 * the latest `.revision` file's Git version.
	 *
	 * @filter wp_headers 10 1
	 *
	 * @param array<string, string> $headers - Included headers.
	 *
	 * @return array<string, string>
	 */
	public function revision_header( array $headers ): array {
		$revision = Resources::in()->get_revision();
		if ( null !== $revision ) {
			$headers['Revision'] = $revision;
		}
		return $headers;
	}


	/**
	 * Load the CSS enums available in postcss-boilerplate version 4.9.0+.
	 *
	 * @action init 10 0
	 *
	 * @return void
	 */
	public function load_css_enums(): void {
		$enum = $this->handles[0]::from( self::CSS_ENUM_HANDLE );
		if ( SCRIPT_DEBUG ) {
			require get_stylesheet_directory() . '/css/' . $enum->file();
		} else {
			require $enum->dist_path() . $enum->file();
		}
	}


	/**
	 * Instantiate the Common class with the required dependencies.
	 *
	 * @param ResourceHandles[] $handles - Array of resource handles.
	 * @param Config            $scripts - Scripts class which supports a `js_config` method.
	 */
	public static function factory( array $handles, Config $scripts ): static {
		return new static( $handles, $scripts );
	}
}
