<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MLAPI {

	const ENDPOINTS_VERSION = 2;

	public static function plugins_loaded() {

		add_filter( 'query_vars', array( __CLASS__, 'add_query_vars' ), 10 );
		add_action( 'parse_request', array( __CLASS__, 'check_requests' ), 10 );
		if ( self::ENDPOINTS_VERSION != Mobiloud::get_option( 'ml_api_endpoints_version' ) ) {
			add_action( 'init', array( __CLASS__, 'activate_endpoints' ), 10 );
		}
	}

	/**
	 * Add public query vars
	 *
	 * @return array $vars
	 */
	public static function add_query_vars( $vars ) {
		$vars[] = '__ml-api';

		return $vars;
	}


	public static function activate_endpoints() {
		self::add_endpoints( true );
	}


	/**
	 * Add Endpoints
	 *
	 * @return void
	 */
	public static function add_endpoints( $force_save = false ) {
		foreach ( Mobiloud::get_rules() as $rule => $variable ) {
			add_rewrite_rule( $rule, 'index.php?__ml-api=' . $variable, 'top' );
		}
		if ( $force_save ) {
			flush_rewrite_rules();
			Mobiloud::set_option( 'ml_api_endpoints_version', self::ENDPOINTS_VERSION );
		}
	}

	/**
	 * Check Requests, handle endpoints
	 */
	public static function check_requests() {
		global $wp, $wp_rewrite;

		// self::handle_user_token(); // move it later.
		// self::well_known();
		// Plain permalinks structure? Check for one of endpoints manually.
		if ( ! $wp_rewrite->using_permalinks() || apply_filters( 'mobiloud_detect_endpoints', false ) ) {
			$pathinfo         = isset( $_SERVER['PATH_INFO'] ) ? $_SERVER['PATH_INFO'] : '';
			list( $pathinfo ) = explode( '?', $pathinfo );
			$pathinfo         = str_replace( '%', '%25', $pathinfo );

			if ( isset( $_SERVER['REQUEST_URI'] ) ) {
				list( $req_uri, $params ) = explode( '?', $_SERVER['REQUEST_URI'] . ( false === strpos( $_SERVER['REQUEST_URI'], '?' ) ? '?' : '' ) );

				$home_path       = trim( wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
				$home_path_regex = sprintf( '|^%s|i', preg_quote( $home_path, '|' ) );

				// Trim path info from the end and the leading home path from the front.
				// For path info requests, this leaves us with the requesting filename, if any.
				$req_uri = str_replace( $pathinfo, '', $req_uri );
				$req_uri = trim( $req_uri, '/' );
				$req_uri = preg_replace( $home_path_regex, '', $req_uri );
				$req_uri = trim( $req_uri, '/' );

				// Try to match with one of endpoints.
				$list = Mobiloud::get_rules();
				foreach ( $list as $rule => $variable ) {
					if ( preg_match( "!$rule!", $req_uri ) ) {
						$wp->set_query_var( '__ml-api', $variable );
						break;
					}
				}
			}
		}
		if ( ! self::ignore_request() ) {
			if ( isset( $wp->query_vars['__ml-api'] ) ) {
				Mobiloud::do_post_to_get_redirect();

				self::disable_new_relic();
				$api_endpoint_url = $wp->query_vars['__ml-api'];
				self::request( $api_endpoint_url );
				exit;
			}
		}
	}

	/**
	 * Handle Requests
	 *
	 * @param string $api_endpoint
	 * @return void
	 */
	protected static function request( $api_endpoint ) {
		global $wp;

		/**
		* @since 4.2.0 Current Moliloud endpoint.
		*
		* @see MLAPI::get_current_endpoint() Please use this method instead of constant. Because extension plugin may replace native MLAPI::request() with their code.
		*/
		define( 'MOBILOUD_API_REQUEST', $api_endpoint );
		if ( Mobiloud_Cache::is_api_enabled() ) {
			Mobiloud_Cache::add_header();
		}
		self::handle_user_token();

		/**
		* Action to override existing endpoint before the standard version called.
		*
		* @since 4.2.0
		*
		* @param string $api_endpoint Endpoint.
		*/
		do_action( 'mobiloud_pre_endpoint_call', $api_endpoint );
		self::cache_headers( $api_endpoint );

		$template = get_option( 'ml-templates', 'legacy' );

		switch ( $api_endpoint ) {
			case 'config':
				self::php_notices();
				self::add_headers( false );
				include_once MOBILOUD_PLUGIN_DIR . 'config.php';
				break;
			case 'menu':
				self::php_notices();
				self::add_headers( false );
				include_once MOBILOUD_PLUGIN_DIR . 'get_categories.php';
				break;
			case 'comments':
				Mobiloud::use_template( 'comments', 'comments' );
				break;
			case 'sections':
				Mobiloud::use_template( 'sections', 'sections' );
				break;
			case 'disqus':
				include_once MOBILOUD_PLUGIN_DIR . '/comments/disqus.php';
				break;
			case 'page':
				include_once MOBILOUD_PLUGIN_DIR . 'get_page.php';
				break;
			case 'post':
				self::add_headers( ml_is_paywall_enabled(), false );

				if ( 'legacy' === $template ) {
					include_once MOBILOUD_PLUGIN_DIR . 'post/post.php';
				} else if ( 'default' === $template ) {
					require_once Mobiloud::get_default_template( $wp->query_vars['__ml-api'] );
				}

				break;
			case 'list':
				include_once MOBILOUD_PLUGIN_DIR . '/api/controllers/MLApiController.php';
				self::add_headers( false, false );

				if ( 'legacy' === $template ) {
					Mobiloud::use_template( 'list', 'loop' );
				} else if ( 'default' === $template ) {
					Mobiloud::require_default_template_wrapper();
				}

				break;
			case 'auth':
				include_once MOBILOUD_PLUGIN_DIR . 'auth/auth.php';
				break;
			case 'subscription':
				include_once MOBILOUD_PLUGIN_DIR . 'subscriptions/endpoint.php';
				break;
			case 'reg_data':
				include_once MOBILOUD_PLUGIN_DIR . 'auth/register.php';
				break;
			case 'registration':
				self::add_headers( true, false );
				Mobiloud::use_template( 'registration', 'form' );
				break;
			case 'version':
				self::php_notices();
				self::add_headers( false );
				include_once MOBILOUD_PLUGIN_DIR . 'version.php';
				break;
			case 'login':
				self::php_notices();
				include_once MOBILOUD_PLUGIN_DIR . '/subscriptions/login.php';
				self::add_headers();
				break;
			case 'posts':
				$is_default_list = 'default' === get_option( 'ml-templates', 'legacy' );
				include_once MOBILOUD_PLUGIN_DIR . '/api/controllers/MLApiController.php';
				$debug = true;

				// do_action( 'mobiloud_before_content_requests' );
				remove_all_actions( 'wp_login_failed' );
				remove_all_actions( 'authenticate' );

				$api = new MLApiController();
				$api->set_error_handlers( $debug );
				self::php_notices();

				self::add_headers( true, ! $is_default_list );

				$custom_response = apply_filters( 'mobiloud_custom_posts_results', null );

				if ( ! empty( $custom_response ) ) {
					$response = $custom_response;
				} else {
					$response = $api->handle_request();
				}

				$api->send_response( $response );

				break;
			default:
				/**
				* Action to execute non standard endpoint.
				*
				* @since 4.2.0
				*
				* @param string $api_endpoint Endpoint.
				*/
				do_action( 'mobiloud_endpoint_call', $api_endpoint );
				echo 'Mobiloud API v1.';
		}
	}

	/**
	 * Return name of current endpoint.
	 *
	 * @since 4.2.0
	 *
	 * @return string Endpoint name (ex: 'list', 'posts') or empty string.
	 */
	public static function get_current_endpoint() {
		if ( defined( 'MOBILOUD_API_REQUEST' ) ) {
			return MOBILOUD_API_REQUEST;
		}
		if ( isset( $wp->query_vars['__ml-api'] ) ) {
			return $wp->query_vars['__ml-api'];
		}
		return '';
	}

	private static function disable_new_relic() {
		if ( extension_loaded( 'newrelic' ) && function_exists( 'newrelic_disable_autorum' ) ) {
			newrelic_disable_autorum();
		}
	}

	/**
	 * Auth user using X-ML-VALIDATION header.
	 * Called for all endpoints.
	 */
	public static function handle_user_token() {
		if ( isset( $_SERVER['HTTP_X_ML_VALIDATION'] ) && ! empty( $_SERVER['HTTP_X_ML_VALIDATION'] ) && strlen( $_SERVER['HTTP_X_ML_VALIDATION'] ) > 18 ) {
			// get auth header string
			$ml_token = explode( '|', sanitize_text_field( $_SERVER['HTTP_X_ML_VALIDATION'] ) );
			self::set_user_from_token( $ml_token[0] );
			if ( ! is_user_logged_in() ) {
				do_action( 'mobiloud_user_token_invalid' );
			} else {
				header( 'X-ML-VALIDATION: ' . $ml_token[0] . '|' . time() );
				return true;
			}
		} else {
			return false;
		}
	}

	/**
	 * Set user using token
	 *
	 * @since 4.2.0
	 *
	 * @param string $ml_token_raw Current token.
	 * @return string|null Token or null value.
	 */
	public static function set_user_from_token( $token ) {
		$user_id = 0;
		// find user.
		$ml_users = get_users(
			array(
				'blog_id'    => 0,
				'meta_key'   => 'ml_auth_token',
				'meta_value' => $token,
			)
		);
		if ( count( $ml_users ) > 0 ) {
			/**
			* Additional filter for set user ID after it was found using token.
			* Called for any endpoint.
			* Could be used for additional credentials checking (together with mobiloud_auth_disallow_login filter).
			*
			* @since 4.2.0
			*
			* @param int         $user_id  Current user ID, return 0 to log out.
			* @param string      $token    Token.
			* @param string|null $endpoint Current endpoint code or null.
			*/
			$user_id = apply_filters( 'mobiloud_token_set_user', intval( $ml_users[0]->ID ), $token, self::get_current_endpoint() );
		}

		if ( 0 !== $user_id ) {
			wp_set_current_user( $user_id );
			wp_set_auth_cookie( $user_id, true );
			return $token;
		} else {
			wp_set_current_user( 0 ); // log out any current user.
			wp_clear_auth_cookie();
			self::notify_about_invalid_token( $token, 'token_not_found', true );
		}
		return null;
	}

	/**
	 * Try to log user in using parameters
	 *
	 * @param string $username
	 * @param string $password
	 * @return {WP_Error|WP_User} WP_User if login successful, WP_Error if error
	 */
	public static function ml_login_wordpress( $username, $password ) {
		$creds                  = array();
		$creds['user_login']    = $username;
		$creds['user_password'] = $password;
		$creds['remember']      = true;
		$user                   = wp_signon( $creds, false );

		if ( get_class( $user ) === 'WP_User' ) {
			wp_set_current_user( $user->ID );
		}

		return $user;
	}

	/**
	 * Register user using username and password. Save Receipt ID value.
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $receipt_id
	 * @return WP_Error|int int User id if success.
	 */
	public static function ml_register_wordpress_user( $username, $password, $receipt_id ) {
		// similar checks as function register_new_user() has.
		$errors = new WP_Error();

		$sanitized_user_login = sanitize_user( $username );
		/**
		* Filters the email address of a user being registered.
		*
		* @since 2.1.0
		*
		* @param string $user_email The email address of the new user.
		*/
		$user_email = apply_filters( 'user_registration_email', $username );

		// Check the email address
		if ( $user_email == '' ) {
			$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your email address.' ) );
		} elseif ( ! is_email( $user_email ) ) {
			$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' ) );
			$user_email = '';
		} elseif ( email_exists( $user_email ) ) {
			$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.' ) );
		} elseif ( ! validate_username( $username ) ) { // Check the username
			$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.' ) );
			$sanitized_user_login = '';
		} elseif ( username_exists( $sanitized_user_login ) ) {
			$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.' ) );

		} else {
			/** This filter is documented in wp-includes/user.php */
			$illegal_user_logins = array_map( 'strtolower', (array) apply_filters( 'illegal_user_logins', array() ) );
			if ( in_array( strtolower( $sanitized_user_login ), $illegal_user_logins ) ) {
				$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: Sorry, that username is not allowed.' ) );
			}
		}

		if ( $errors->get_error_code() ) {
			return $errors;
		}

		$result = wp_create_user( $sanitized_user_login, $password, $user_email );
		if ( ! is_wp_error( $result ) ) {
			update_user_meta( $result, 'ml_receipt_id', "$receipt_id" );
			/** @var int result */
			get_user_by( 'ID', $result )->set_role( 'ml_app_user' );
			wp_set_current_user( $result );
			wp_set_auth_cookie( $result );
		}
		return $result;
	}

	/**
	 * Return token value using X-ML-VALIDATION header.
	 * Check is the token valid using periods of time.
	 * May add http code 401 header.
	 *
	 * @since 4.2.0
	 *
	 * @return string
	 */
	public static function get_token_value() {
		if ( isset( $_SERVER['HTTP_X_ML_VALIDATION'] ) && ! empty( $_SERVER['HTTP_X_ML_VALIDATION'] ) ) {
			$ml_token = ''; // raw token.
			$source   = true;
			$reason   = '';
			if ( strlen( $_SERVER['HTTP_X_ML_VALIDATION'] ) > 18 ) {
				// get auth header string.
				$ml_token = explode( '|', sanitize_text_field( $_SERVER['HTTP_X_ML_VALIDATION'] ) );
				if ( 2 === count( $ml_token ) ) {
					$time = intval( $ml_token[1] );
					return $ml_token[0];
				}
			}
		}
		return '';
	}

	/**
	 * Return user token.
	 *
	 * @param int $user_id
	 * @return string Token or empty string if can not create.
	 */
	public static function get_user_token( $user_id ) {
			$ml_token = get_user_meta( $user_id, 'ml_auth_token', true );
			$ml_time  = get_user_meta( $user_id, 'ml_auth_time', true );
			// Check if user already has a token.
		if ( empty( $ml_token ) ) {
			// generate the token.
			$ml_token = wp_hash( $user_id );
			$created  = update_user_meta( $user_id, 'ml_auth_token', $ml_token );
			if ( ! $created ) {
				return '';
			}
		}
			update_user_meta( $user_id, 'ml_auth_time', time() );
			return $ml_token . '|' . time();
	}

	/**
	 * Notify about invalid token.
	 * Set http code 401 header.
	 *
	 * @since 4.2.0
	 *
	 * @param string $ml_token Current token or empty string if can not be parsed.
	 * @param string $reason   Reason code why token rejected.
	 * @param bool   $source   Source rejected the token: true - built-in code, false - custom filter.
	 * @return void
	 */
	protected static function notify_about_invalid_token( $ml_token, $reason = 'token_not_found', $source = true ) {
		/**
		* Notify about invalid token action.
		*
		* @since 4.2.0
		*
		* @param string $ml_token Current token or empty string if can not be parsed.
		* @param string $reason   Reason code why token rejected.
		* @param bool  $source   Source rejected the token: true - built-in code, false - custom filter.
		*/
		do_action( 'mobiloud_token_rejected', $ml_token, $reason, $source );
		// reset user at the App side.
		http_response_code( 401 );
		unset( $_SERVER['HTTP_X_ML_VALIDATION'] );
		delete_transient( 'ml_token_validation' );

		// todo: do we need try to logout user?
	}


	/*
	public static function well_known( $original_template ) {
	list( $req_uri, $params ) = explode( '?', $_SERVER['REQUEST_URI'] . ( false === strpos( $_SERVER['REQUEST_URI'], '?' ) ? '?' : '' ) );
	if ( '/' . '.well-known/apple-app-site-association' === $req_uri ) {
	}
	return $original_template;
	}
	*/

	private static function add_headers( $is_private = true, $is_json = true ) {
		if ( $is_json ) {
			header( 'Content-Type: application/json' );
		}
		if ( 2 !== (int) Mobiloud::get_option( 'ml_app_version', 2 ) ) {
			$time = absint( Mobiloud::get_option( 'ml_cache_expiration', 30 ) ) * 60;
			header( 'Cache-Control: ' . ( $is_private ? 'private' : 'public' ) . ", max-age=$time, s-max-age=$time", true );
		}
	}

	private static function cache_headers( $endpoint ) {
		if ( 2 === (int) Mobiloud::get_option( 'ml_app_version', 2 ) ) {
			$options = [
				'list'     => [ 'ml_cache_list_age', 'ml_cache_list_is_private' ],
				'posts'    => [ 'ml_cache_list_age', 'ml_cache_list_is_private' ], // same as list.
				'post'     => [ 'ml_cache_post_age', 'ml_cache_post_is_private' ],
				'page'     => [ 'ml_cache_page_age', 'ml_cache_page_is_private' ],
				'config'   => [ 'ml_cache_config_age', 'ml_cache_config_is_private' ],
				'menu'     => [ 'ml_cache_config_age', 'ml_cache_config_is_private' ], // same as config.
				'sections' => [ 'ml_cache_config_age', 'ml_cache_config_is_private' ], // same as config.
				'version'  => [ 'ml_cache_config_age', 'ml_cache_config_is_private' ], // same as config.
			];
			if ( isset( $options[ $endpoint ] ) ) {
				$max_age    = (int) Mobiloud::get_option( $options[ $endpoint ][0], self::cache_default_age( $endpoint ) );
				$is_private = (int) Mobiloud::get_option( $options[ $endpoint ][1], self::cache_default_is_private( $endpoint ) );
				$time       = $max_age * 60;
				header( 'Cache-Control: ' . ( $is_private ? 'private' : 'public' ) . ( $max_age > 0 ? ", max-age=$time, s-max-age=$time" : ', no-cache, max-age=0, s-max-age=0' ), true );
			}
		}
	}

	public static function cache_default_age( $endpoint ) {
		switch ( $endpoint ) {
			case 'list':
				return 30;
			case 'post':
				return 30;
			case 'page':
				return 30;
			case 'config':
				return 30;
		}
		return 30;
	}

	public static function cache_default_is_private( $endpoint ) {
		switch ( $endpoint ) {
			case 'list':
				return true;
			case 'post':
				return true;
			case 'page':
				return false;
			case 'config':
				return false;
		}
		return true;
	}

	private static function php_notices() {
		if ( get_option( 'ml_disable_notices', true ) ) {
			$level = error_reporting();
			error_reporting( $level & ~E_NOTICE & ~E_WARNING & ( defined( 'E_STRICT' ) ? ~E_STRICT : 1 ) & ( defined( 'E_DEPRECATED' ) ? ~E_DEPRECATED : 1 ) );
		}
	}

	private static function ignore_request() {
		if ( isset( $_POST['gform_ajax'] ) && class_exists( 'RGForms' ) ) {
			add_action( 'wp', array( 'RGForms', 'ajax_parse_request' ), 10 );
			return true;
		}
		return false;
	}

	/**
	* Get List Categories from request.
	* Try to load categories list from MLApiController (exists if list or posts endpoints queried).
	* Then try to load categories list from new MLQuery instance:
	*
	*
	* @since 4.2.8
	*
	* @return array
	*/
	public static function get_list_cat() {
		static $result = null;
		if ( is_null( $result ) ) {
			$result = class_exists( 'MLApiController' ) ? MLApiController::$list_cat : null; // try to use existing request first.
		}
		if ( is_null( $result ) ) {
			require_once __DIR__ . '/models/MLQuery.php';
			$ml_query = new MLQuery();
			$result = $ml_query->list_cat;
		}
		return $result;
	}
}
