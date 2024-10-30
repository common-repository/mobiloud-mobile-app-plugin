<?php

class Mobiloud_Cache {
	/**
	* Capability for plugin configuration
	*/
	const capability_for_configuration = 'activate_plugins';

	/**
	* Capability for using push notifications
	*/
	const capability_for_use = 'publish_posts';

	private static $is_api_enabled;
	private static $is_images_enabled;
	private static $images_url;
	private static $api_url;
	private static $cdn_key;
	private static $url_self;
	private static $url_length;
	private static $url_base;
	private static $initialized = false;

	public static function init() {
		if ( ! self::$initialized ) {
			self::$is_api_enabled    = Mobiloud::get_option( 'ml_caching_enabled', 0 );
			self::$is_images_enabled = Mobiloud::get_option( 'ml_caching_images_enabled', 0 );
			self::$images_url        = (string) Mobiloud::get_option( 'ml_images_cdn_url', '' );
			self::$api_url           = (string) Mobiloud::get_option( 'ml_api_cdn_url', '' );
			self::$cdn_key           = (string) Mobiloud::get_option( 'ml_cdn_key', '' );
			if ( self::$is_images_enabled ) {
				add_filter( 'mobiloud_image_url', [ __CLASS__, 'update_image_url' ] );
			}
			if ( self::$is_api_enabled ) {
				add_filter( 'mobiloud_api_url', [ __CLASS__, 'update_api_url' ] );
				add_action( 'transition_post_status', [ __CLASS__, 'post_status_update' ], 10, 3 );
				add_action( 'activated_plugin', [ __CLASS__, 'flush_cdn' ], 10, 0 );
				add_action( 'deactivated_plugin', [ __CLASS__, 'flush_cdn' ], 10, 0 );
				add_action( 'upgrader_process_complete', [ __CLASS__, 'flush_cdn' ], 10, 0 );
				add_action( 'ml-options-tab-updated', [ __CLASS__, 'flush_cdn' ], 10, 0 );

				add_action( 'edit_post', [ __CLASS__, 'flush_api' ], 100, 2 );
				add_action( 'delete_attachment', [ __CLASS__, 'flush_images' ], 100, 2 );
				// todo: add_action( 'post_updated', ..., 100, 2 );
			}

			self::$url_self   = strtolower( trailingslashit( get_bloginfo( 'url' ) ) );
			$scheme           = parse_url( self::$url_self, PHP_URL_SCHEME );
			$host             = parse_url( self::$url_self, PHP_URL_HOST );
			self::$url_self   = "{$scheme}://{$host}";
			self::$url_base   = "{$scheme}/{$host}";
			self::$url_length = strlen( self::$url_self );

			// todo: Make sure cache is cleared whenever a post, page, media, setting, plugin is updated
			self::$initialized = true;
		}
	}

	public static function is_api_enabled() {
		if ( ! self::$initialized ) {
			self::init();
		}
		return self::$is_api_enabled;
	}

	public static function add_header() {
		$scheme = parse_url( self::$api_url, PHP_URL_SCHEME );
		$host   = parse_url( self::$api_url, PHP_URL_HOST );
		header( "Access-Control-Allow-Origin: {$scheme}://{$host}" );
	}

	/**
	 * Flush everything at the site's CDN.
	 *
	 * @return string|null Error message or null on success.
	 */
	public static function flush_cdn() {
		if ( ! self::$is_api_enabled && ! self::$is_images_enabled ) {
			return 'CDN is not active';
		}
		if ( '' === self::$cdn_key ) {
			return 'CDN key is empty';
		}
		$result = self::flush_api();
		if ( is_null( $result ) ) {
			$result = self::flush_images();
		}
		return $result;
	}

	/**
	 * Flush api cache.
	 *
	 * @return string|null Error message or null on success.
	 */
	public static function flush_api() {
		if ( self::$is_api_enabled ) {
			$error = self::flush_path( self::$api_url . self::$url_base );
			if ( ! is_null( $error ) ) {
				return $error;
			}
		}
		return null; // no errors.
	}

	/**
	 * Flush images cache.
	 *
	 * @return string|null Error message or null on success.
	 */
	public static function flush_images() {
		if ( self::$is_images_enabled ) {
			$error = self::flush_path( self::$images_url . self::$url_base );
			if ( ! is_null( $error ) ) {
				return $error;
			}
		}
		return null; // no errors.
	}

	/**
	 * Flush using path.
	 *
	 * @param string $path
	 *
	 * @return string|null Null on success, string with message on error.
	 */
	private static function flush_path( $path ) {
		return 'Not available.';
	}

	/**
	 * Update url for using as image.
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public static function update_image_url( $url ) {
		if ( self::$is_images_enabled ) {
			$url = self::update_url_raw( $url, self::$images_url );
		}
		return $url;
	}

	/**
	 * Update url for using as API.
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public static function update_api_url( $url ) {
		if ( self::$is_api_enabled ) {
			$url = self::update_url_raw( $url, self::$api_url );
		}
		return $url;
	}

	/**
	 * Return CDN version of url with prefix or unchanged url if domain is different.
	 *
	 * @param string $url Source URL.
	 * @param string $prefix Base path, for images or api.
	 *
	 * @return string
	 */
	private static function update_url_raw( $url, $prefix ) {
		if ( self::is_same_domain_url( $url ) ) {
			return $prefix . self::$url_base . substr( $url, self::$url_length );
		}
		return $url;
	}

	/**
	 * @param string $url
	 *
	 * @return bool
	 */
	private static function is_same_domain_url( $url ) {
		return 0 === stripos( $url, self::$url_self );
	}

	/**
	 * Filter, on post transition update.
	 *
	 * @param string  $new_status
	 * @param string  $old_status
	 * @param WP_Post $post
	 */
	public static function post_status_update( $new_status, $old_status, $post ) {
		if ( ( 'publish' === $new_status || 'publish' === $old_status ) && $new_status !== $old_status ) {
			if ( false !== wp_is_post_autosave( $post->ID ) || false !== wp_is_post_revision( $post->ID ) ) {
				return;
			}

			$post_types = explode( ',', get_option( 'ml_article_list_include_post_types' ) ); // is it one of the post types, which selected at the options?
			if ( in_array( $post->post_type, $post_types, true ) ) {
				self::flush_api();
			}
		}
	}
}
