<?php

namespace Lipe\Lipe\Api;

use Lipe\Lib\Util\Cache;

/**
 * Youtube API
 *
 * Creates an object from a Youtube video url.
 * If an api key is not provided this will fall back to oembed with works but
 * does not contain things like high resolution thumbnails or descriptions.
 *
 * An api key may be obtained by registering a project with Google APIs and giving it YouTube access.
 *
 * # Go to the developer console https://console.developers.google.com/project
 * # Click "Create Project" if some already exist otherwise, use the "Select a Project" drop-down and click "Create a
 * Project"
 * # Enter a project name like "Steelcase.com"
 * # Click "Create"
 * # Click on the project to enter it
 * # Using the hamburger on the top left go to "Apis & Services"
 * # Under the list of "Popular APIs" click "View All"
 * # Search For "YouTube Data API v3" and click it
 * # Click "Enable"
 * # Click "Create credentials"
 * # Under "Where will you be calling the API from?" select "Web Browser"
 * # Under "What data will you be accessing?" check "Public data"
 * # Click "What credentials do I need?"
 * # Under "Get your credentials" copy the API key
 * # Click "Done"
 *
 * E.G AIzaSyCwuMNgkjhfDWZc_FDrcq8TexW3OMT3I1Q
 *
 */
class YouTube implements \JsonSerializable {
	public const API_URL = 'https://www.googleapis.com/youtube/v3/videos?id={{id}}&key={{api_key}}&part=snippet';

	public const OEMBED_URL = 'http://www.youtube.com/oembed?url={{url}}&maxwidth={{width}}&maxheight={{height}}';

	public $height = 400;

	public $width = 700;

	private $api_key = false;

	private $url;


	public function __construct( $url, $api_key ) {
		$this->url     = $url;
		$this->api_key = $api_key;
	}


	public function set_width( $width ) : void {
		$this->width = $width;
	}


	public function set_height( $height ) : void {
		$this->height = $height;
	}


	/**
	 * Calls the methods to match the structure of a oembed object
	 *
	 * So $video->thumbnail_url becomes $this->get_thumbnail_url()
	 *
	 * @param $field
	 *
	 * @return bool
	 */
	public function __get( $field ) {
		if ( method_exists( $this, "get_$field" ) ) {
			return $this->{"get_$field"}();
		}

		return false;
	}


	public function jsonSerialize() {
		return [
			'id'            => $this->get_id(),
			'url'           => $this->url,
			'title'         => $this->get_title(),
			'video'         => $this->get_html(),
			'thumbnail_url' => $this->get_thumbnail_url(),
			'description'   => $this->get_description(),
			'html'          => $this->get_html(),
		];
	}


	public function get_id() {
		$object = $this->get_object();

		return $object->id ?? '';

	}


	public function get_object() {
		if ( isset( $this->object ) ) {
			return $this->object;
		}
		if ( empty( $this->api_key ) ) {
			$this->object = $this->get_oembed();
		} else {
			$this->object = $this->request_from_api();
		}

		return $this->object;
	}


	/**
	 * Get the Oembed object for this video
	 * Does not include things like description and thumbnail size
	 * but does include an html player
	 * Also, does not require an api key
	 *
	 * @notice if you have an api key this method is pretty much redundant
	 *
	 * @return object
	 */
	public function get_oembed() {
		$cache_key = [
			__CLASS__,
			__METHOD__,
			'url' => $this->url,
		];

		$object = Cache::get( $cache_key );
		if ( false === $object ) {
			$url = str_replace(
				[
					'{{url}}',
					'{{height}}',
					'{{width}}',
				],
				[
					$this->height,
					$this->width,
					urlencode( $this->url ),
				],
				self::OEMBED_URL );

			$response = wp_remote_get( $url );
			$object   = json_decode( wp_remote_retrieve_body( $response ) );
			Cache::set( $cache_key, $object );
		}

		return $object;
	}


	/**
	 * Get the video object from the api
	 * Contains description and image size which the
	 * oembed does not.
	 * Does not include and html player
	 *
	 * @notice If you don't have an api key use $this->get_oembed()
	 *
	 * @return mixed
	 */
	private function request_from_api() {
		if ( empty( $this->api_key ) ) {
			return false;
		}

		$cache_key = [
			__CLASS__,
			__METHOD__,
			'url' => $this->url,
		];

		$object = Cache::get( $cache_key );
		if ( false === $object ) {
			$id = $this->get_id_from_url();
			if ( ! empty( $id ) ) {
				$url = str_replace( [ '{{id}}', '{{api_key}}' ], [ $id, $this->api_key ], self::API_URL );

				$response = wp_remote_get( $url );
				$object   = json_decode( wp_remote_retrieve_body( $response ) );
				if ( ! empty( $object ) ) {
					$video      = array_shift( $object->items );
					$object     = $video->snippet;
					$object->id = $video->id;
				}
				Cache::set( $cache_key, $object );
			}
		}

		return $object;
	}


	private function get_id_from_url() : string {
		$id = false;
		parse_str( parse_url( $this->url, PHP_URL_QUERY ), $_args );
		if ( ! empty( $_args['v'] ) ) {
			$id = $_args['v'];
		} elseif ( strpos( $this->url, 'youtu.be' ) ) {
			$id = trim( parse_url( $this->url, PHP_URL_PATH ), '/' );
		}

		return $id;
	}


	public function get_title() : string {
		$object = $this->get_object();

		return $object->title ?? '';

	}


	public function get_html() : string {
		$object = $this->get_object();
		$frame  = '';
		if ( ! empty( $object->id ) ) {
			$frame = '<iframe 
						width="' . $this->width . '" 
						height="' . $this->height . '"
						src="https://www.youtube.com/embed/' . $object->id . '">
				</iframe>';
		}

		return $frame;
	}


	public function get_thumbnail_url() : string {
		$object    = $this->get_object();
		$thumbnail = '';
		if ( ! empty( $object->thumbnails ) ) {
			if ( isset( $object->thumbnails->maxres ) ) {
				$thumbnail = $object->thumbnails->maxres->url;
			} elseif ( isset( $object->thumbnails->high ) ) {
				$thumbnail = $object->thumbnails->high->url;
			} elseif ( isset( $object->thumbnails->medium ) ) {
				$thumbnail = $object->thumbnails->medium->url;
			}
			//fallback to oembed url
		} elseif ( ! empty( $object->thumbnail_url ) ) {
			$thumbnail = $object->thumbnail_url;
		}

		return $thumbnail;
	}


	public function get_description() {
		$object = $this->get_object();

		return $object->description ?? '';

	}
}