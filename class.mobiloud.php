<?php

class Mobiloud {
	/**
	* Capability for plugin configuration
	*/
	const capability_for_configuration = 'activate_plugins';

	/**
	* Capability for using push notifications
	*/
	const capability_for_use = 'publish_posts';

	private static $option_key = 'ml_options';

	private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
			self::set_default_options();
			Mobiloud_Cache::init();
		}
	}

	/**
	 * Return all endpoints and corresponding __ml_api values
	 */
	public static function get_rules() {
		/**
		* Register custom endpoints.
		*
		* @since 4.2.0
		*
		* @param array $endpoints Associative array, [rewrite rule => endpoint name].
		*/
		$custom    = apply_filters( 'mobiloud_register_endpoints', [] );
		$endpoints = array(
			'^ml-api/v1/posts/?'             => 'posts',
			'^ml-api/v1/config/?'            => 'config',
			'^ml-api/v1/menu/?'              => 'menu',
			'^ml-api/v1/login/?'             => 'login',
			'^ml-api/v1/page/?'              => 'page',
			'^ml-api/v1/post/?'              => 'post',
			'^ml-api/v1/version/?'           => 'version',
			'^ml-api/v1/comments/disqus/?'   => 'disqus',
			'^ml-api/v1/comments/?'          => 'comments',

			'^ml-api/v2/posts/?'             => 'posts',
			'^ml-api/v2/config/?'            => 'config',
			'^ml-api/v2/post/?'              => 'post',
			'^ml-api/v2/version/?'           => 'version',
			'^ml-api/v2/page/?'              => 'page',

			'^ml-api/v2/comments/disqus/?'   => 'disqus',
			'^ml-api/v2/comments/?'          => 'comments',
			'^ml-api/v2/list/?$'             => 'list',
			// Note: '^ml-api/v2/list/([0-9]+)/?' initialized at blocks code and not served by endpoint code @see mobiloud_list_builder_rewrite().
			'^ml-api/v2/auth/?'              => 'auth',
			'^ml-api/v2/sections/?'          => 'sections',
			'^ml-api/v2/subscription/?'      => 'subscription',
			'^ml-api/v2/registration/data/?' => 'reg_data',
			'^ml-api/v2/registration/?'      => 'registration',
		);
		return array_merge( $custom, $endpoints );
	}

	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;

		if ( get_option( 'ml_push_notification_enabled' ) ) {
			add_action( 'transition_post_status', 'ml_pb_post_published_notification', 10, 3 );
			// add_action('transition_post_status','ml_pb_post_published_notification');
			// add_action('publish_future_post','ml_pb_post_published_notification_future');
		}

		// Allow anonymous commenting via REST API if enabled in settings.
		if ( 1 == get_option( 'ml_comments_rest_api_enabled' ) ) {
			add_filter( 'rest_allow_anonymous_comments', '__return_true' );
		}

		add_action( 'wp_head', array( 'Mobiloud', 'on_head' ) );

		if ( ml_is_paywall_enabled() ) {
			add_action( 'mobiloud_before_content_requests', array( ml_get_paywall(), 'ml_validate_requests' ), 1 );
		}

		MLAPI::add_endpoints( false );

		add_action( 'wp_ajax_nopriv_process_comments', array( 'Mobiloud', 'ajax_process_comments' ) );
		add_action( 'wp_ajax_process_comments', array( 'Mobiloud', 'ajax_process_comments' ) );

		add_shortcode( 'ml-ios-only', [ __CLASS__, 'shortcode' ], 10, 3 );
		add_shortcode( 'ml-android-only', [ __CLASS__, 'shortcode' ], 10, 3 );

		if ( Mobiloud::get_option( 'ml_exclude_posts_enabled' ) ) { // exclude posts from lists enabled.
			self::register_taxonomy();
		}
	}

	public static function mobiloud_activate() {
		set_transient( 'ml_activation_redirect', 1, 60 );

		self::set_default_options( true );
		self::run_db_install();
		self::set_default_values();
		self::create_sample_list_builder_post();
		self::create_sample_app_pages();
		self::add_defaults_to_hamburger_menu();
		self::add_defaults_to_sections_menu();
		self::add_defaults_to_push_notifications_menu();
		self::add_defaults_to_category_menu();
		self::add_defaults_to_settings_menu();
		self::set_template_type();
		MLAPI::add_endpoints( true );
	}

	/**
	 * Plugin uninstall
	 */
	public static function uninstall() {

	}

	/**
	 * delete app builder data from wordpress
	 */
	public static function delete_app_builder_records() {
		delete_option('ml_news_app_builder_experience');
		delete_option('ml_news_app_builder_experience_privacy_agreement');
	}

	public static function set_default_options( $force_update = false ) {
		if ( $force_update ) {
			delete_option( 'ml_schedule_dismiss' );
		}
		if ( $force_update || ( self::get_option( 'ml_version' ) !== MOBILOUD_PLUGIN_VERSION ) ) {
			if ( self::get_option( 'ml_article_list_include_post_types', 'none' ) === 'none' ) {
				self::set_option( 'ml_article_list_include_post_types', 'post' );
			}
			if ( self::get_option( 'ml_custom_featured_image', 'none' ) === 'none' ) {
				self::set_option( 'ml_custom_featured_image', '' );
			}
			if ( self::get_option( 'ml_menu_show_favorites', 'none' ) === 'none' ) {
				self::set_option( 'ml_menu_show_favorites', true );
			}
			if ( self::get_option( 'ml_show_android_cat_tabs', 'none' ) === 'none' ) {
				self::set_option( 'ml_show_android_cat_tabs', true );
			}
			if ( self::get_option( 'ml_allow_landscape', 'none' ) === 'none' ) {
				self::set_option( 'ml_allow_landscape', true );
			}
			if ( self::get_option( 'ml_article_list_enable_dates', 'none' ) === 'none' ) {
				self::set_option( 'ml_article_list_enable_dates', true );
			}

			if ( self::get_option( 'ml_original_size_featured_image', 'none' ) === 'none' ) {
				self::set_option( 'ml_original_size_featured_image', true );
			}

			if ( self::get_option( 'ml_show_article_featuredimage', 'none' ) === 'none' ) {
				self::set_option( 'ml_show_article_featuredimage', true );
			}
			if ( self::get_option( 'ml_post_author_enabled', 'none' ) === 'none' ) {
				self::set_option( 'ml_post_author_enabled', true );
			}
			if ( self::get_option( 'ml_page_author_enabled', 'none' ) === 'none' ) {
				self::set_option( 'ml_page_author_enabled', false );
			}
			if ( self::get_option( 'ml_followimagelinks', 'none' ) === 'none' ) {
				self::set_option( 'ml_followimagelinks', 0 );
			}
			if ( self::get_option( 'ml_post_date_enabled', 'none' ) === 'none' ) {
				self::set_option( 'ml_post_date_enabled', true );
			}
			if ( self::get_option( 'ml_page_date_enabled', 'none' ) === 'none' ) {
				self::set_option( 'ml_page_date_enabled', false );
			}
			if ( self::get_option( 'ml_post_title_enabled', 'none' ) === 'none' ) {
				self::set_option( 'ml_post_title_enabled', true );
			}
			if ( self::get_option( 'ml_page_title_enabled', 'none' ) === 'none' ) {
				self::set_option( 'ml_page_title_enabled', true );
			}

			$lang = get_bloginfo( 'language' );
			if ( self::get_option( 'ml_rtl_text_enable', 'none' ) === 'none' && ( is_rtl() || $lang === 'ar' || $lang === 'he-IL' ) ) {
				self::set_option( 'ml_rtl_text_enable', true );
			}

			if ( self::get_option( 'ml_internal_links', 'none' ) === 'none' ) {
				self::set_option( 'ml_internal_links', true );
			}

			if ( self::get_option( 'ml_article_list_view_type', 'none' ) === 'none' ) {
				self::set_option( 'ml_article_list_view_type', 'compact' );
			}

			if ( self::get_option( 'ml_datetype', 'none' ) === 'none' ) {
				self::set_option( 'ml_datetype', 'prettydate' );
			}

			if ( self::get_option( 'ml_dateformat', 'none' ) === 'none' ) {
				self::set_option( 'ml_dateformat', 'F j, Y' );
			}

			if ( self::get_option( 'ml_show_email_contact_link', 'none' ) === 'none' ) {
				self::set_option( 'ml_show_email_contact_link', true );
			}
			if ( self::get_option( 'ml_contact_link_email', 'none' ) === 'none' ) {
				self::set_option( 'ml_contact_link_email', get_bloginfo( 'admin_email' ) );
			}
			if ( self::get_option( 'ml_copyright_string', 'none' ) === 'none' ) {
				self::set_option( 'ml_copyright_string', '&copy; ' . date( 'Y' ) . ' ' . get_bloginfo( 'name' ) );
			}
			if ( self::get_option( 'ml_comments_system', 'none' ) === 'none' || self::get_option( 'ml_comments_system', 'none' ) === '' ) {
				self::set_option( 'ml_comments_system', 'wordpress' ); // phpcs:ignore WordPress.WP.CapitalPDangit.Misspelled
			}

			if ( self::get_option( 'ml_related_header', 'none' ) === 'none' ) {
				add_option( 'ml_related_header', 'Related Posts' );
			}
			if ( self::get_option( 'ml_related_image', 'none' ) === 'none' ) {
				self::set_option( 'ml_related_image', true );
			}
			// value "1" removed from list.
			if ( 1 == self::get_option( 'ml_ios_native_ad_interval' ) ) {
				self::set_option( 'ml_ios_native_ad_interval', 2 );
			}
			if ( 1 == self::get_option( 'ml_android_native_ad_interval' ) ) {
				self::set_option( 'ml_android_native_ad_interval', 2 );
			}
			// 4.2.0+
			if ( false === self::get_option( 'ml_membership_class', false ) ) {
				$ml_paywall_settings_option = Mobiloud::get_option( 'ml_paywall_settings', array() );
				if ( count( $ml_paywall_settings_option ) > 0 && is_array( $ml_paywall_settings_option ) && isset( $ml_paywall_settings_option['ml_enable_paywall'] ) ) {
					if ( '1' == $ml_paywall_settings_option['ml_enable_paywall'] ) {
						self::set_option( 'ml_membership_class', 'Mobiloud_Paywall' );
					}
					unset( $ml_paywall_settings_option['ml_enable_paywall'] );
					self::set_option( 'ml_paywall_settings', $ml_paywall_settings_option );
				}
			}
			$data = self::get_option( 'ml_paywall_settings', false );
			if ( is_array( $data ) ) {
				if ( isset( $data['sblock_content'] ) ) {
					self::set_option( 'ml_app_subscription_block_content', stripslashes( $data['sblock_content'] ) );
				}
				if ( isset( $data['sblock_css'] ) ) {
					self::set_option( 'ml_app_subscription_block_css', stripslashes( $data['sblock_css'] ) );
				}
				if ( isset( $data['pblock_content'] ) ) {
					self::set_option( 'ml_paywall_pblock_content', stripslashes( $data['pblock_content'] ) );
				}
				if ( isset( $data['pblock_css'] ) ) {
					self::set_option( 'ml_paywall_pblock_css', stripslashes( $data['pblock_css'] ) );
				}
				delete_option( 'ml_paywall_settings' );
			}
			if ( false === self::get_option( 'ml_tabbed_navigation_enabled', false ) ) {
				self::set_option( 'ml_tabbed_navigation_enabled', '1' );
			}
			$site_url = trailingslashit( get_bloginfo( 'url' ) );
			if ( false === self::get_option( 'ml_tabbed_navigation', false ) ) {
				$tn_data = array(
					'active_icon_color'   => '#222222',
					'inactive_icon_color' => '#666666',
					'background_color'    => '#FFFFFF',
					'tabs'                => array(
						array(
							'enabled'                  => '1',
							'label'                    => 'Home',
							'icon_url'                 => MOBILOUD_PLUGIN_URL . 'assets/icons/home.png',
							'type'                     => 'homescreen',
							'url'                      => '',
							'endpoint_url'             => $site_url . 'ml-api/v2/loop',
							'horizontal_navigation'    => 'top',
							'first_item_label'         => 'Home',
							'webview_background_color' => '#FFFFFF',
						),
						array(
							'enabled'                  => '1',
							'label'                    => 'Sections',
							'icon_url'                 => MOBILOUD_PLUGIN_URL . 'assets/icons/format_list_bulleted.png',
							'type'                     => 'sections',
							'url'                      => '',
							'endpoint_url'             => $site_url . 'ml-api/v2/sections',
							'horizontal_navigation'    => array(),
							'first_item_label'         => '',
							'webview_background_color' => '#FFFFFF',
						),
						array(
							'enabled'                  => '1',
							'label'                    => 'Favorites',
							'icon_url'                 => MOBILOUD_PLUGIN_URL . 'assets/icons/bookmark.png',
							'type'                     => 'favorites',
							'url'                      => '',
							'horizontal_navigation'    => array(),
							'first_item_label'         => '',
							'webview_background_color' => '#FFFFFF',
						),
						array(
							'enabled'                  => '1',
							'label'                    => 'Settings',
							'icon_url'                 => MOBILOUD_PLUGIN_URL . 'assets/icons/settings.png',
							'type'                     => 'settings',
							'url'                      => '',
							'horizontal_navigation'    => array(),
							'first_item_label'         => '',
							'webview_background_color' => '#FFFFFF',
						),
						array(
							'enabled'                  => '0',
							'label'                    => 'Disabled',
							'icon_url'                 => '',
							'type'                     => '',
							'url'                      => '',
							'horizontal_navigation'    => array(),
							'first_item_label'         => '',
							'webview_background_color' => '#FFFFFF',
						),
					),
				);
				self::set_option( 'ml_tabbed_navigation', $tn_data );
			}
			if ( false === self::get_option( 'ml_app_subscription_block_content', false ) ) {
				self::set_option(
					'ml_app_subscription_block_content',
					implode(
						"\n", array_map(
							'trim', explode(
								"\n",
								'<img src="%LOGOURL%" width="250" height="auto" />

								<p class="description">Subscribe today to gain full access to the content</p>
								<a class="ml-paywall__button" onclick="nativeFunctions.handleButton( \'in_app_purchase\', \'in.app.purchase.id\', null )">
								Monthly
								<span>Free for 1 month, then $10.99 per month</span>
								</a>

								<a class="ml-paywall__button" onclick="nativeFunctions.handleButton( \'in_app_purchase\', \'in.app.purchase.id\', null )">
								Annual
								<span>Free for 1 month, then $99.99 per year</span>
								</a>

								<p>
								$10.99 per month or $99.99 per year.<br />You can cancel anytime<br/><br />
								Already a member? <a onclick="nativeFunctions.handleButton(\'login\', null, null)">Sign-in</a> or <a onclick="nativeFunctions.handleButton(\'restore_purchase\', null, null)">restore purchase</a>
								</p>

								<div class="separator"></div>

								<div class="terms-conditions">
								<h3>Terms &amp; Conditions</h3>
								<p>Payments will be charged to your Account at confirmation purchase.</p>
								<p>The subscription automatically renews unless auto-renew is turned off at least 24-hours before the end of the current period.</p>
								<p>Your account will be charged for renewal within 24 hours prior to the end of the current period.</p>
								<p>Subscriptions may be managed by the user and auto-renewal may be turned off by going to the user\'s Account Settings after purchase.</p>
								<p>Any unused portion of the free trial period, if offered, will be forfeited when the user purchases a subscription, where applicable.</p>
								<p>Learn more about our <a onclick="nativeFunctions.handleLink(\'' . esc_js( $site_url . 'terms' ) . '\', \'Terms of Service\', \'internal\')">Terms of use</a> and <a onclick="nativeFunctions.handleLink(\'' . esc_js( $site_url . 'privacy' ) . '\', \'Privacy Policy\', \'internal\')">Privacy Policy</a></p>
								</div>'
							)
						)
					)
				);
			}
			if ( false === self::get_option( 'ml_app_subscription_block_css', false ) ) {
				self::set_option(
					'ml_app_subscription_block_css',
					implode(
						"\n", array_map(
							'trim', explode(
								"\n",
								'@import url(\'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Roboto:wght@100;300;400;500;700&display=swap\');
								body.ml-subscription {
								background:#000;
								color: #FFF;
								font-size: 16px;
								font-family: \'Roboto\', sans-serif;
								}
								.ml-subscription .wrapper {
								max-width: 800px;
								margin: 0 auto;
								padding: 40px 20px 0 20px;
								text-align: center;
								word-break: break-word;
								}
								.ml-subscription h2 { font-family:"Open Sans", serif; color: #FFF; font-size: 28px; font-weight:600; margin-bottom:30px; }
								.ml-subscription h1 { font-family:"Open Sans", serif; color: #FFF; font-size: 26px; font-weight:bold; }
								.ml-subscription img {
								margin: 0 auto 25px auto;
								max-width:100%;
								}
								.ml-subscription p.description {
								font-size: 18px;
								line-height: 1.4em;
								}
								.ml-subscription p a {
								color: #F6E54B;
								font-weight: bold;
								text-decoration: none;
								}
								.ml-paywall__button {
								color: #000;
								background: #4cba6f;
								display:block;
								padding: 20px 30px;
								margin: 0 0 20px 0;
								text-decoration: none;
								font-size: 18px;
								font-weight: bold;
								border-radius: 6px;
								}
								.ml-paywall__button span {
								font-size:14px;
								font-weight:normal;
								display:block;
								}
								.terms-conditions {
								padding: 15px 0;
								text-align: left;
								}
								.terms-conditions h3 {
								color: #FFF;
								font-size: 18px;
								}
								#ml-subscription-close {
								position: absolute;
								top: 0;
								left: 0;
								font-size: 36px;
								padding: 10px;
								display: block;
								border-radius: 50%;
								transform: rotate(45deg);
								font-weight: 200;
								line-height: 30px;
								width: 30px;
								}
								.separator {
								display:block;
								border-bottom:1px solid #ddd;
								padding-top:20px;
								margin-bottom:30px;
								}'
							)
						)
					)
				);
			}
			$value = self::get_option( 'ml_app_registration_block_content', false );
			if ( empty( $value ) || 'ecf0b3121f95bf10ab53ca0379ec82f1' === md5( $value ) ) { // no value or update old default value.
				self::set_option(
					'ml_app_registration_block_content',
					implode(
						"\n", array_map(
							'trim', explode(
								"\n",
								'<img src="%LOGOURL%" width="250" height="auto" />
								<p class="description">
								Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
								</p>
								<div class="registration-errors" id="reg_errors"></div>
								<form id="reg_form" action="" method="post">
								<p>
								<label for="reg_user">Email<br>
								<input type="text" id="reg_user" class="reg-input border-radius border-gray" value="" size="20">
								</label>
								</p>
								<p>
								<label for="reg_pass">Password<br>
								<input type="password" id="reg_pass" class="reg-input border-radius border-gray" value="" size="20">
								</label>
								</p>
								<p class="terms"><label for="reg_terms"><input type="checkbox" id="reg_terms" value="1" class="border-radius border-gray reg-checkbox"> Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore</label></p>
								<p class="submit">
								<input type="submit" id="wp-submit" class="reg-button border-radius border-gray" value="Register">
								</p>
								</form>'
							)
						)
					)
				);
			}
			if ( false === self::get_option( 'ml_app_registration_block_css', false ) ) {
				self::set_option(
					'ml_app_registration_block_css',
					implode(
						"\n", array_map(
							'trim', explode(
								"\n",
								'@import url("https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&amp;family=Roboto:wght@100;300;400;500;700&amp;display=swap");
								body.ml-registration {
								background:#ffffff;
								color: #000000;
								font-size: 16px;
								font-family: "Roboto", sans-serif;
								}
								.ml-registration .wrapper {
								max-width: 480px;
								margin: 0 auto;
								padding: 40px 20px 0 20px;
								text-align: left;
								word-break: break-word;
								}
								.ml-registration img {
								margin: 0 auto 25px auto;
								max-width:100%;
								display: block;
								}
								.ml-registration p {
								margin: 0 0 25px 0;
								font-size: 18px;
								line-height: 1.4em;
								}
								.ml-registration p a {
								color: #0000FF;
								font-weight: bold;
								text-decoration: none;
								}
								.registration-errors {
								display: none;
								font-size: 18px;
								line-height: 1.4em;
								background-color: #f9bbbb;
								font-weight: bold;
								text-decoration: none;
								border-radius: 5px;
								border-color: #f9bbbb;
								overflow: hidden;
								margin: 0 0 25px 0;
								padding: 20px;
								opacity: 1;
								}
								.registration-errors p {
								margin-bottom: 10px;
								}
								.registration-errors p:last-child {
								margin-bottom: 0;
								}
								.ml-registration .reg-input  {
								box-sizing: border-box;
								margin: 0;
								font-size: 18px;
								line-height: 1.4em;
								width: 100%;
								padding: 10px;
								}
								.ml-registration .border-radius {
								border-radius: 5px;
								}
								.ml-registration .border-gray {
								border-color: #ddd;
								border-style: solid;
								border-width: 2px;
								}

								p.terms {
								position: relative;
								padding-left: 40px;
								}
								.reg-checkbox {
								position: absolute;
								left: 0px;
								width: 1.5em;
								height: 1.5em;
								vertical-align: bottom;
								}
								.reg-button {
								box-sizing: border-box;
								margin: 0;
								font-size: 18px;
								line-height: 1.4em;
								width: 100%;
								padding: 10px;
								background-color: #aaa;
								color: white;
								cursor: pointer;
								border-bottom-color: #888;
								border-right-color: #888;
								}
								.reg-button:hover {
								background-color: #999;
								}

								.ml-loader {
								display: none;
								z-index: 9999;
								position: fixed;
								top: 0;
								left: 0;
								bottom: 0;
								right: 0;
								opacity: 0.7;
								background-color: #000;
								background-size: 64px 64px;
								background-image: url(' . esc_attr( MOBILOUD_PLUGIN_URL . 'assets/img/android-spinner.svg' ) . ');
								background-repeat: no-repeat;
								background-position: center;
								}
								.is-ios .ml-loader {
								background-image: url(' . esc_attr( MOBILOUD_PLUGIN_URL . 'assets/img/ios-spinner.svg' ) . ');
								}
								.ml-close {
								position: absolute;
								top: 0;
								left: 0;
								font-size: 36px;
								padding: 10px;
								display: block;
								border-radius: 50%;
								transform: rotate(45deg);
								font-weight: 200;
								line-height: 30px;
								width: 30px;
								}
								body.is-loading .ml-loader {
								display: block;
								}'
							)
						)
					)
				);
			}
			if ( false === self::get_option( 'ml_paywall_pblock_content', false ) ) {
				self::set_option(
					'ml_paywall_pblock_content',
					implode(
						"\n", array_map(
							'trim', explode(
								"\n",
								'<h2>Login to continue reading</h2>
								<p>This content is for premium members only, in order to continue reading you must have a subscription.</p>
								<a class="ml-paywall__button" onclick="nativeFunctions.handleButton( \'subscription_screen\', null, \'' . esc_js( $site_url . 'ml-api/v2/subscription' ) . '\' )">Subscribe now</a>'
							)
						)
					)
				);
			}
			if ( false === self::get_option( 'ml_paywall_pblock_css', false ) ) {
				self::set_option(
					'ml_paywall_pblock_css',
					implode(
						"\n", array_map(
							'trim', explode(
								"\n",
								'html, body {
								pointer-events: none;
								overflow: hidden;
								word-break: break-word;
								margin: 0;
								height: 100%;
								}
								.ml-paywall {
								display: block;
								position: fixed;
								top: 0;
								bottom: 0;
								left: 0;
								right: 0;
								z-index: 99999;
								background: rgba( 255,255,255,0.4 );
								pointer-events: all;
								}
								.ml-paywall__wrap {
								position: absolute;
								left: 0;
								right: 0;
								bottom: 0;
								background-image: linear-gradient( to top, rgba(255,255,255,1) 85%, rgba(255,255,255,0) );
								padding: 50px 20px 20px;
								text-align: center;
								}
								.ml-paywall__wrap h2 {
								font-size: 24px;
								font-weight: bold;
								margin: 15px 0;
								}
								.ml-paywall__wrap p {
								font-size: 16px;
								line-height: 1.4;
								margin-bottom:15px;
								}
								.ml-paywall__button {
								color: #000;
								background: #4cba6f;
								display: inline-block;
								padding: 20px 30px;
								margin: 0 0 20px 0;
								text-decoration: none;
								font-size: 18px;
								font-weight: bold;
								border-radius: 6px;
								}
								.mb_body_single .ml-paywall__wrap {
								position:absolute !important;
								top:320px !important;
								background-image: linear-gradient( to top, rgba(255,255,255,1) 98%, rgba(255,255,255,0) ) !important;
								}'
							)
						)
					)
				);
			}
			// set menu v1/v2 option.
			if ( false === self::get_option( 'ml_app_version', false ) ) {
				$version = self::get_option( 'ml_version', '' );
				if ( '' !== $version && version_compare( $version, '4.1.0', '<=' ) && '' !== self::get_option( 'ml_pb_app_id' ) . self::get_option( 'ml_pb_secret_key' ) . self::get_option( 'ml_onesignal_app_id' ) . self::get_option( 'ml_onesignal_secret_key' ) ) {
					self::set_option( 'ml_app_version', 1 ); // previous plugin version used and has push keys.
				} else {
					self::set_option( 'ml_app_version', 2 );
					if ( '' === $version ) {
						self::set_option( 'ml_list_type', 'web' );
					}
				}
			}

			$menu = Mobiloud::get_option( 'ml_sections_menu', false );
			if ( $menu instanceof WP_Term ) {
				Mobiloud::set_option( 'ml_sections_menu', $menu->slug );
			}
			$current_tabs = Mobiloud::get_option( 'ml_tabbed_navigation', [] );
			if ( $current_tabs && isset( $current_tabs['tabs'] ) ) {
				foreach ( $current_tabs['tabs'] as $_key => $_tab ) {
					if ( 'sections' === $_tab['type'] ) {
						$menu = $current_tabs['tabs'][ $_key ]['horizontal_navigation'];
						if ( $menu instanceof WP_Term ) {
							$current_tabs['tabs'][ $_key ]['horizontal_navigation'] = $menu->slug;
							Mobiloud::set_option( 'ml_tabbed_navigation', $current_tabs );
						}
					}
				}
			}

			if ( is_null( get_role( 'ml_app_user' ) ) ) {
				$role         = get_role( 'subscriber' );
				$capabilities = ! is_null( $role ) ? $role->capabilities : [
					'read'    => true,
					'level_0' => true,
				];
				add_role( 'ml_app_user', 'App User', $capabilities );
			}

			self::set_option( 'ml_version', MOBILOUD_PLUGIN_VERSION );

			if (!self::get_option('ml_news_app_builder_experience')) {
				self::set_option('ml_news_app_builder_experience', 'no'); // insert default value on plugin activate
			}
		}
	}

	/**
	 * Pre-fill menu pages, categories, links from "primary" or just a first existing menu
	 */
	private static function configure_items_from_menu() {
		// find main location.
		$location_name = false;
		$locations     = get_registered_nav_menus();
		if ( ! empty( $locations ) ) {
			if ( isset( $locations['primary'] ) ) {
				$location_name = 'primary';
			} else {
				foreach ( $locations as $key => $value ) {
					if ( false !== strpos( $key, 'main' ) ) {
						$location_name = $key;
						break;
					}
				}
			}
		}
		$theme_locations = get_nav_menu_locations();
		$menu            = false;

		// find menu.
		if ( ! empty( $theme_locations ) && ! empty( $theme_locations[ $location_name ] ) ) {
			$menu = wp_get_nav_menu_object( $theme_locations[ $location_name ] );
		}

		if ( empty( $menu ) ) {
			return 0;
		};
		// get menu items.
		$items = wp_get_nav_menu_items(
			$menu->term_id,
			array(
				'order'      => 'ASC',
				'orderby'    => 'menu_order',
				'output'     => ARRAY_A,
				'output_key' => 'menu_order',
			)
		);

		// get pages, categories, links.
		$pages      = array();
		$cats       = array();
		$menu_links = array();

		if ( ! empty( $items ) && is_array( $items ) ) {
			foreach ( $items as $item ) {
				if ( 'page' == $item->object ) {
					$pages[] = $item->object_id;
				} elseif ( 'category' == $item->object ) {
					$cats[] = $item->object_id;
				} elseif ( 'custom' == $item->object && ! empty( $item->url ) ) {
					$menu_links[] = array(
						'urlTitle' => $item->title,
						'url'      => $item->url,
					);
				}
			}
		}

		if ( ! empty( $pages ) ) {
			include_once MOBILOUD_PLUGIN_DIR . 'pages.php';
			ml_remove_all_pages();
			foreach ( $pages as $page_id ) {
				ml_add_page( $page_id );
			}
		}

		if ( ! empty( $menu_links ) ) {
			self::set_option( 'ml_menu_urls', $menu_links );
		}
		if ( ! empty( $cats ) ) {
			include_once MOBILOUD_PLUGIN_DIR . 'categories.php';
			ml_remove_all_categories();
			foreach ( $cats as $cat_id ) {
				ml_add_category( $cat_id );
			}
		} elseif ( count( $pages ) + count( $menu_links ) > 0 ) {
			// if no categories found, but pages or links found.
			include_once MOBILOUD_PLUGIN_DIR . 'categories.php';
			// prefill menu config with top 5 categories by count of posts.
			$cats = get_categories(
				array(
					'orderby'    => 'count',
					'order'      => 'DESC',
					'number'     => 5,
					'hide_empty' => 1,
				)
			);
			foreach ( $cats as $cat ) {
				ml_add_category( $cat->cat_ID );
				$cats[] = $cat->cat_ID;
			}
		}

		return count( $pages ) + count( $cats ) + count( $menu_links );
	}

	/**
	 * Creates a sample list under the list builder post type
	 * on plugin activation.
	 */
	private static function create_sample_list_builder_post() {
		$post_exists = post_exists( 'Home screen list', '', '', 'list-builder' );

		mobiloud_list_builder_init();
		flush_rewrite_rules();

		if ( $post_exists > 0 ) {
			return;
		}

		$categories_by_count = get_categories(
			array(
				'taxonomy' => 'category',
				'orderby'  => 'count',
				'order'    => 'DESC',
				'number'   => 9,
			)
		);

		$unserialized_attributes = [];

		if ( ! empty( $categories_by_count ) ) {
			$unserialized_attributes['selectedTerms'] = array(
				'category' => array(),
			);
			foreach ( $categories_by_count as $category ) {
				$unserialized_attributes['selectedTerms']['category'][] = array(
					'label' => $category->name,
					'value' => $category->term_id,
				);
			}
		}

		$serialized_attributes = ! empty( $unserialized_attributes ) ? serialize_block_attributes( $unserialized_attributes ) : '';
		$serialized_attributes = empty( $serialized_attributes ) ? '' : ',' . substr( $serialized_attributes, 1, -1 );

		wp_insert_post(
			array(
				'post_title'   => 'Home screen list',
				'post_type'    => 'list-builder',
				'post_status'  => 'publish',
				'post_content' => '<!-- wp:mobiloud/heading {"fontFamily":"Merriweather","fontSize":2.7,"titleText":"List of Posts"} /-->

				<!-- wp:mobiloud/posts {"highlightFirstPost":true,"infiniteScroll":true,"showTaxonomies":{"category":true,"post_tag":true}' . $serialized_attributes . '} /-->',
			)
		);
	}

	/**
	 * Creates the following App pages on plugin activation:
	 * - About
	 * - Contact
	 * - Privacy Policy
	 */
	private static function create_sample_app_pages() {
		$about_us       = post_exists( 'About Us', '', '', 'app-pages' );
		$contact        = post_exists( 'Contact', '', '', 'app-pages' );
		$privacy_policy = post_exists( 'Privacy Policy', '', '', 'app-pages' );

		mobiloud_list_builder_init();
		flush_rewrite_rules();

		/**
		 * Add About Us page.
		 */
		if ( 0 === $about_us ) {
			wp_insert_post(
				array(
					'post_title'   => 'About Us',
					'post_type'    => 'app-pages',
					'post_status'  => 'publish',
					'post_content' => '',
				)
			);
		}

		/**
		 * Add Contact page.
		 */
		if ( 0 === $contact ) {
			wp_insert_post(
				array(
					'post_title'   => 'Contact',
					'post_type'    => 'app-pages',
					'post_status'  => 'publish',
					'post_content' => '',
				)
			);
		}

		/**
		 * Add Privay Policy page.
		 */
		if ( 0 === $privacy_policy ) {
			wp_insert_post(
				array(
					'post_title'   => 'Privacy Policy',
					'post_type'    => 'app-pages',
					'post_status'  => 'publish',
					'post_content' => '',
				)
			);
		}
	}

	/**
	 * Adds defaults to the hamburger menu.
	 */
	private static function add_defaults_to_hamburger_menu() {
		$nav_menu = wp_get_nav_menu_object( 'Mobile App - Hamburger menu' );

		if ( false !== $nav_menu ) {
			return;
		}

		$items_for_hamburger_menu = [];
		$pages_to_get = [
			'About Us',
			'Contact',
			'Privacy Policy'
		];

		foreach ( $pages_to_get as $page_title ) {
			$page = get_page_by_title( $page_title, OBJECT, 'app-pages' );

			if ( is_null( $page ) ) {
				continue;
			}

			$items_for_hamburger_menu[] = $page->ID;
		}

		$menu_slug = Mobiloud_Admin::update_menu_with_items( 'Mobile App - Hamburger menu', [], $items_for_hamburger_menu );
		Mobiloud::set_option( 'ml_hamburger_nav', $menu_slug );
	}

	/**
	 * Adds defaults to the section menu.
	 */
	private static function add_defaults_to_sections_menu() {
		$menu_name = 'Mobile App - Sections menu';
		$nav_menu  = wp_get_nav_menu_object( $menu_name );

		if ( false !== $nav_menu ) {
			return;
		}

		wp_create_nav_menu( $menu_name );

		$categories = get_categories( array(
			'taxonomy'   => 'category',
			'hide_empty' => false,
		) );

		$menu = get_term_by( 'name', $menu_name, 'nav_menu' );

		if ( empty( $categories ) ) {
			return;
		}

		foreach ( $categories as $category ) {
			wp_update_nav_menu_item(
				$menu->term_id,
				0,
				array(
					'menu-item-title'     => $category->name,
					'menu-item-object-id' => $category->term_id,
					'menu-item-db-id'     => 0,
					'menu-item-object'    => 'category',
					'menu-item-parent-id' => $category->category_parent,
					'menu-item-depth'     => 1,
					'menu-item-type'      => 'taxonomy',
					'menu-item-url'       => get_category_link( $category->term_id ),
					'menu-item-status'    => 'publish',
				)
			);
		}

		$nav_menu  = wp_get_nav_menu_object( $menu_name );
		Mobiloud::set_option( 'ml_sections_menu', $nav_menu->slug );
	}

	/**
	 * Adds defaults to the Push Motifications Categories menu
	 * ordered by categories count.
	 */
	private static function add_defaults_to_push_notifications_menu() {
		$menu_name = 'Mobile App - Push Notifications Categories';
		$nav_menu  = wp_get_nav_menu_object( $menu_name );

		if ( false !== $nav_menu ) {
			return;
		}

		wp_create_nav_menu( $menu_name );

		$categories_by_count = get_categories(
			array(
				'taxonomy' => 'category',
				'orderby'  => 'count',
				'order'    => 'DESC',
				'number'   => 9,
			)
		);

		$menu = get_term_by( 'name', $menu_name, 'nav_menu' );

		if ( empty( $categories_by_count ) ) {
			return;
		}

		foreach ( $categories_by_count as $category ) {
			wp_update_nav_menu_item(
				$menu->term_id,
				0,
				array(
					'menu-item-title'     => $category->name,
					'menu-item-object-id' => $category->term_id,
					'menu-item-db-id'     => 0,
					'menu-item-object'    => 'category',
					'menu-item-parent-id' => $category->category_parent,
					'menu-item-depth'     => 1,
					'menu-item-type'      => 'taxonomy',
					'menu-item-url'       => get_category_link( $category->term_id ),
					'menu-item-status'    => 'publish',
				)
			);
		}

		$nav_menu  = wp_get_nav_menu_object( $menu_name );
		Mobiloud::set_option( 'ml_push_notification_menu', $nav_menu->slug );
	}

	/**
	 * Adds defaults to the Category menu
	 * ordered by categories count.
	 */
	private static function add_defaults_to_category_menu() {
		$menu_name = 'Mobile App - Categories';
		$nav_menu  = wp_get_nav_menu_object( $menu_name );

		if ( false !== $nav_menu ) {
			return;
		}

		wp_create_nav_menu( $menu_name );

		$categories_by_count = get_categories(
			array(
				'taxonomy' => 'category',
				'orderby'  => 'count',
				'order'    => 'DESC',
				'number'   => 9,
			)
		);

		$menu = get_term_by( 'name', $menu_name, 'nav_menu' );

		if ( empty( $categories_by_count ) ) {
			return;
		}

		foreach ( $categories_by_count as $category ) {
			wp_update_nav_menu_item(
				$menu->term_id,
				0,
				array(
					'menu-item-title'     => $category->name,
					'menu-item-object-id' => $category->term_id,
					'menu-item-db-id'     => 0,
					'menu-item-object'    => 'category',
					'menu-item-parent-id' => $category->category_parent,
					'menu-item-depth'     => 1,
					'menu-item-type'      => 'taxonomy',
					'menu-item-url'       => get_category_link( $category->term_id ),
					'menu-item-status'    => 'publish',
				)
			);
		}
	}

	/**
	 * Adds defaults to the Settings menu.
	 */
	private static function add_defaults_to_settings_menu() {
		$menu_name = 'Mobile App - Settings menu';
		$nav_menu  = wp_get_nav_menu_object( $menu_name );

		if ( false !== $nav_menu ) {
			return;
		}

		$items_for_hamburger_menu = [];
		$pages_to_get = [
			'Privacy Policy'
		];

		foreach ( $pages_to_get as $page_title ) {
			$page = get_page_by_title( $page_title, OBJECT, 'app-pages' );

			if ( is_null( $page ) ) {
				continue;
			}

			$items_for_hamburger_menu[] = $page->ID;
		}

		$menu_slug = Mobiloud_Admin::update_menu_with_items( $menu_name, [], $items_for_hamburger_menu );
		Mobiloud::set_option( 'ml_general_settings_menu', $menu_slug );
	}

	/**
	 * Sets the default template type on plugin activation.
	 */
	private static function set_template_type() {
		$template = get_option( 'ml-templates', false );

		if ( false === $template ) {
			update_option( 'ml-templates', 'default' );
		}
	}

	/**
	 * Pre-fill configuration
	 */
	private static function set_default_values() {
		$default_timeout = ini_get( 'default_socket_timeout' );
		ini_set( 'default_socket_timeout', 5 ); // wait 5 sec.

		include_once MOBILOUD_PLUGIN_DIR . '/categories.php';
		include_once MOBILOUD_PLUGIN_DIR . '/pages.php';

		$current_cat = ml_categories();
		$menu_links  = self::get_option( 'ml_menu_urls' );
		$menu_tags   = self::get_option( 'ml_menu_tags' );
		if ( empty( $current_cat ) && empty( $menu_links ) && empty( $menu_tags ) && ! count( ml_pages() ) ) {
			if ( ! self::configure_items_from_menu() ) {

				// Prefill menu config with top 5 categories by count of posts.
				$cats = get_categories(
					array(
						'orderby'    => 'count',
						'order'      => 'DESC',
						'number'     => 5,
						'hide_empty' => 1,
					)
				);
				foreach ( $cats as $cat ) {
					ml_add_category( $cat->cat_ID );
				}

				// Prefill menu config with a page with name about*.
				global $wpdb;
				$sql   = $wpdb->prepare(
					"
					SELECT ID
					FROM $wpdb->posts
					WHERE post_title LIKE %s
					AND post_type = 'page'
					AND post_status = 'publish'
					ORDER BY post_date ASC
					LIMIT 1",
					'about%'
				); // only published pages (not posts).
				$pages = $wpdb->get_col( $sql );
				if ( is_array( $pages ) && count( $pages ) ) {
					foreach ( $pages as $id ) {
						ml_add_page( $id );
					}
				}
			}
		}

		// Configure logo image.
		$logo_url = get_option( 'ml_preview_upload_image' );
		if ( empty( $logo_url ) ) {
			if ( function_exists( 'gridlove_get_option' ) ) {
				$logo_url = gridlove_get_option( 'logo_retina' );
				if ( empty( $logo_url ) ) {
					$logo_url = gridlove_get_option( 'logo' );
				}
				self::set_option( 'ml_preview_upload_image', $logo_url );
			}
		}
		if ( empty( $logo_url ) ) {
			if ( function_exists( 'get_site_icon_url' ) ) {
				$logo_url = get_site_icon_url( 192 );
			} else {
				$site_icon_id = get_option( 'site_icon' );
				if ( $site_icon_id ) {
					$size_data = array( 192, 192 );
					$logo_url  = wp_get_attachment_image_url( $site_icon_id, $size_data );
				}
			}
			if ( ! empty( $logo_url ) && filter_var( $logo_url, FILTER_VALIDATE_URL ) !== false ) {
				self::set_option( 'ml_preview_upload_image', $logo_url );
			}
		}
		if ( empty( $logo_url ) ) {
			$logo_url = get_site_icon_url( 128 ); // set desired width of the logo image to 128px.
			if ( empty( $logo_url ) ) { // or use external API to retrieve the logo.
				$logo_url = 'http://logo.clearbit.com/' . rawurlencode( wp_parse_url( get_site_url(), PHP_URL_HOST ) );
				$data     = wp_remote_get( $logo_url ); // check.
				if ( empty( $data ) || is_wp_error( $data ) || false !== strpos( wp_remote_retrieve_body( $data ), '<html>' ) ) { // image not found.
					$logo_url = '';
				}
			}
			if ( ! empty( $logo_url ) && filter_var( $logo_url, FILTER_VALIDATE_URL ) !== false ) {
				self::set_option( 'ml_preview_upload_image', $logo_url );
			}
		}

		// Configure bar background color.
		$color = get_option( 'ml_preview_theme_color' );
		if ( empty( $color ) || ( '#1e73be' === $color ) ) { // did not set or has default value (class.mobiloud-admin.php: function menu_get_started()).

			if ( function_exists( 'gridlove_get_option' ) ) {
				$color = gridlove_get_option( 'color_header_main_bg' );
			}

			if ( empty( $color ) ) {
				$color = get_theme_mod( 'header_background_color', '' );
			}
			if ( empty( $color ) && function_exists( 'get_background_color' ) ) {
				$color = get_background_color();
			}
			if ( empty( $color ) ) {
				$url  = 'https://www.colorfyit.com/api/swatches/list.json?url=' . rawurlencode( get_site_url() );
				$data = wp_remote_get( $url, array( 'sslverify' => false ) );
				if ( ! is_wp_error( $data ) ) {
					$json_data = json_decode( wp_remote_retrieve_body( $data ), true );
					if ( is_array( $json_data ) && isset( $json_data['colors'] ) && is_array( $json_data['colors'] ) ) {
						$color = ''; // Ex: {"colors":[{"Hex":"#003388","Rgb":{"r":0,"g":51,"b":136}...
						foreach ( $json_data['colors'] as $item ) {
							if ( ! empty( $item['Hex'] ) && is_string( $item['Hex'] ) ) {
								$color = $item['Hex'];
							}
						}
					}
				}
			}
			if ( ! empty( $color ) ) {
				self::set_option( 'ml_preview_theme_color', sanitize_text_field( false === strpos( $color, '#' ) ? '#' . $color : $color ) );
			}
		}
		ini_set( 'default_socket_timeout', $default_timeout ); // restore.
	}

	public static function run_db_update_notifications() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name = $wpdb->prefix . 'mobiloud_notifications';

		// check if there is the column 'url'.
		$results = $wpdb->get_results( 'SHOW FULL COLUMNS FROM `' . $table_name . "` LIKE 'url'", ARRAY_A );
		if ( $results == null || count( $results ) == 0 ) {
			// update the table.
			$sql = 'ALTER TABLE `' . $table_name . '` ADD `url` VARCHAR(255) NULL DEFAULT NULL AFTER `post_id`';
			$wpdb->query( $sql );
		}
	}

	private static function run_db_install() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name = $wpdb->prefix . 'mobiloud_notifications';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			$sql = 'CREATE TABLE ' . $table_name . " (
			id bigint(11) NOT NULL AUTO_INCREMENT,
			time bigint(11) DEFAULT '0' NOT NULL,
			post_id bigint(11),
			msg blob,
			android varchar(1) NOT NULL,
			ios varchar(1) NOT NULL,
			tags blob,
			UNIQUE KEY id (id)
			);";

			dbDelta( $sql );
		}

		self::run_db_update_notifications();

		$table_name = $wpdb->prefix . 'mobiloud_notification_categories';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			$sql = 'CREATE TABLE ' . $table_name . ' (
			id bigint(11) NOT NULL AUTO_INCREMENT,
			cat_ID bigint(11) NOT NULL,
			UNIQUE KEY id (id)
			);';

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}

		$table_name = $wpdb->prefix . 'mobiloud_categories';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			// install della tabella.
			$sql = 'CREATE TABLE ' . $table_name . " (
			id bigint(11) NOT NULL AUTO_INCREMENT,
			time bigint(11) DEFAULT '0' NOT NULL,
			cat_ID bigint(11) NOT NULL,
			UNIQUE KEY id (id)
			);";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}

		$table_name = $wpdb->prefix . 'mobiloud_pages';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			// install della tabella.
			$sql = 'CREATE TABLE ' . $table_name . " (
			id bigint(11) NOT NULL AUTO_INCREMENT,
			time bigint(11) DEFAULT '0' NOT NULL,
			page_ID bigint(11) NOT NULL,
			UNIQUE KEY id (id)
			);";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}

		// check if there is the column 'ml_render'.
		$results = $wpdb->get_results( 'SHOW FULL COLUMNS FROM `' . $table_name . "` LIKE 'ml_render'", ARRAY_A );
		if ( $results == null || count( $results ) == 0 ) {
			// update the table.
			$sql = "ALTER TABLE $table_name ADD ml_render TINYINT(1) NOT NULL DEFAULT 1;";
			$wpdb->query( $sql );
		}
	}

	public static function set_generic_option( $name, $value ) {
		if ( ! update_option( $name, $value ) ) {
			add_option( $name, $value );
		}
	}

	/**
	 * Get ML option value
	 *
	 * @param string $name
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public static function get_option( $name, $default = null ) {
		return get_option( $name, $default );
	}

	/**
	 * Set ML option value
	 *
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return boolean
	 */
	public static function set_option( $name, $value ) {
		return update_option( $name, $value );
	}

	public static function trim_string( $string, $length = 30 ) {
		if ( strlen( $string ) <= $length ) {
			return $string;
		} else {
			return substr( $string, 0, $length ) . '...';
		}
	}

	public static function get_plugin_url() {
		return MOBILOUD_PLUGIN_URL;
	}

	private static function is_mobiloud_app() {
		$ua               = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$req_app          = isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : '';
		$android_app_name = '';
		$platform         = isset( $_SERVER['HTTP_X_ML_PLATFORM'] ) ? $_SERVER['HTTP_X_ML_PLATFORM'] : '';
		return false !== stripos( $ua, 'mobiloud' ) || ( ! empty( $android_app_name ) ) && ( $android_app_name === $req_app ) || 'iOS' === $platform || 'Android' === $platform;
	}

	/**
	 * Add custom CSS for embedded pages
	 */
	public static function on_head() {
		if ( isset( $_SERVER['HTTP_X_ML_PLATFORM'] ) && ( $_SERVER['HTTP_X_ML_PLATFORM'] === "iOS" || $_SERVER['HTTP_X_ML_PLATFORM'] === "Android" ) || ( isset( $_SERVER['HTTP_USER_AGENT'] ) && strlen( strstr( $_SERVER['HTTP_USER_AGENT'], "Mobiloud" ) ) ) > 0 ) {
			$css = array();
			$custom_css = stripslashes( self::get_option( 'ml_embedded_page_css', '' ) );
			if ( ! empty( $custom_css ) ) {
				$css[] = $custom_css;
			}
			if ( ! empty( $css ) ) {
				?>
				<style type="text/css" media="screen"><?php echo strip_tags( implode( "\n", $css ) ); ?></style>
				<?php
			}
		}
	}

	public static function do_post_to_get_redirect() {
		if ( ( 'POST' == $_SERVER['REQUEST_METHOD'] ) && apply_filters( 'ml_do_post_to_get_redirect', false ) ) {
			$url = $_SERVER['REQUEST_URI'];
			if ( false !== strpos( $url, '?' ) ) {
				$url = explode( '?', $url, 2 );
				$url = $url[0];
			}
			$params = file_get_contents( 'php://input' );
			if ( '' !== $params ) {
				$url .= '?' . $params;
			}
			if ( strlen( $url ) < 2000 ) {
				header( 'Location: ' . $url, true, 302 );
				die();
			}
		}
	}

	/**
	 * Is ajax action allowed
	 *
	 * @param string $slug nonce action
	 * @param string $configuration true - plugin configuration, false - plugin use
	 */
	public static function is_action_allowed_ajax( $slug, $configuration = true ) {
		$result = current_user_can( $configuration ? self::capability_for_configuration : self::capability_for_use );
		if ( $result && '' !== $slug ) {
			return check_ajax_referer( $slug, 'ml_nonce' );
		}
		return $result;
	}

	public static function ml_get_avatar_url( $uid_or_email, $size ) {
		preg_match( "/[sS][rR][cC]\s*=\s*['\"]([^'\"]+)['\"]/i", get_avatar( $uid_or_email, $size, get_option( 'avatar_default', 'mystery' ) ), $matches );

		return $matches[1];
	}

	public static function ajax_process_comments() {
		$do           = isset( $_POST['do'] ) ? sanitize_text_field( $_POST['do'] ) : '';
		$toggle_nonce = Mobiloud::get_option( 'ml_commenting_toggle_nonce', 'yes' );
		/**
		 * Allow to bypass nonce check for comment action.
		 *
		 * @since 4.2.6
		 *
		 * @param bool   $check_nonce
		 * @param string $do_action
		 */
		if ( 'yes' === $toggle_nonce ) {
			if ( apply_filters( 'ml_post_comment_check_nonce', true, $do ) && ! check_ajax_referer( 'ml_post_comment', 'nonce' ) ) {
				return;
			}
		}
		$do = sanitize_text_field( $_POST['do'] );

		switch ( $do ) {
			case 'avatar':
				$email  = sanitize_email( $_POST['email'] );
				$avatar = Mobiloud::ml_get_avatar_url( $email, 60 );
				echo $avatar;
				break;

			case 'insert':
				if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) {
					echo wp_json_encode( array( 'message' => 'notloggedin' ) );
				} else {
					$commentdata = array(
						'comment_post_ID'      => isset( $_POST['post'] ) ? sanitize_text_field( $_POST['post'] ) : '', // to which post the comment will show up.
						'comment_author'       => isset( $_POST['author_name'] ) ? sanitize_text_field( $_POST['author_name'] ) : '',
						'comment_author_email' => isset( $_POST['author_email'] ) ? sanitize_email( $_POST['author_email'] ) : '',
						'comment_content'      => isset( $_POST['content'] ) ? esc_html( sanitize_textarea_field( $_POST['content'] ) ) : '',
						'comment_parent'       => isset( $_POST['parent'] ) ? esc_html( sanitize_text_field( $_POST['parent'] ) ) : '',
					);
					if ( is_user_logged_in() ) {
						$commentdata['user_id'] = get_current_user_id();
					}
					$response = wp_new_comment( $commentdata, true );
					if ( is_int( $response ) ) {
						$status = wp_get_comment_status( $response );
						$data   = array(
							'id'          => $response,
							'status'      => $status,
							'avatar'      => Mobiloud::ml_get_avatar_url( $commentdata['comment_author_email'], 60 ),
							'content'     => $commentdata['comment_content'],
							'author_name' => $commentdata['comment_author'],
						);

						echo wp_json_encode( $data );
					} else {
						$data = array(
							'message' => $response->get_error_message(),
						);
						echo wp_json_encode( $data );
					}
				}

				break;
		}
		die();
	}

	/**
	 * Create unique class using path to template and prefix.
	 * Add class name based on prefix and user logged in ou out.
	 *
	 * @since 4.2.0
	 *
	 * @param string $template_file_pathname
	 * @param string $prefix
	 * @return string Class name based on prefix, template basename and "-custom" suffix it is custom (not from MobiLoud plugin dir).
	 *
	 * @example:
	 * for /templates/list/regular.php file in MobiLoud dir and prefix "ml-list-" it will return class: "ml-list-regular-php-template".
	 * for /templates/list/regular.php file in MobiLoud extension plugin dir and prefix "ml-list-" it will return classes": "ml-list-regular-php-template ml-list-regular-php-template-custom".
	 */
	public static function get_template_class( $template_file_pathname, $prefix = 'ml-' ) {
		// class name based on current template.
		$class   = $prefix . ( sanitize_title( basename( $template_file_pathname ) ) ) . '-template';
		$classes = [
			// class name based on user logged in or out.
			$prefix . ( is_user_logged_in() ? 'user-logged-in' : 'user-logged-out' ),
			// class name based on devide type.
			$prefix . ( self::is_ios() ? 'is-ios' : 'not-ios' ),
			$class,
		];
		// if template is not from MobiLoud plugin.
		if ( 0 !== strpos( $template_file_pathname, rtrim( MOBILOUD_PLUGIN_DIR, '/\\' ) ) ) {
			// class name with "-custom" suffix.
			$classes[] = "$class {$class}-custom";
		}

		return implode( ' ', $classes );
	}

	/**
	 * Resolve template to pathname and include or just return it.
	 *
	 * @since 4.2.0
	 *
	 * @param  string       $template_type  'sections', 'paywall', 'list', 'regular', ''... Usually a directory in templates.
	 * @param  string|array $template_names Single file name or array with file names. Without '.php'.
	 * @param  bool         $load           Load template using require or require_once if found.
	 * @param  bool         $require_once   What to use: true - require_once, false - require.
	 * @return string                       The template file name with path if it was located or empty string.
	 */
	public static function use_template( $template_type, $template_names, $load = true, $require_once = true ) {
		if ( ! is_array( $template_names ) ) {
			$template_names = array( $template_names );
		}
		// apply existing filter first.
		$default_name = '' !== $template_type ? ( 'list' !== $template_type ? $template_type : 'loop' ) . "/{$template_names[0]}.php" : "{$template_names[0]}.php";

		/**
		* Template path filter.
		* Please note: this is not a recommended way to permanent replace the whole template,
		* but a great place where you can replace it using some conditions.
		*
		* @since 4.2.0
		*
		* @param string $template_pathname Full template pathname.
		* @param string $name              Just a template name.
		*/
		$located = apply_filters( 'mobiloud_template_filter', '', $default_name );
		if ( '' === $located ) {
			$default_path = MOBILOUD_PLUGIN_DIR . 'templates';
			// without trailing slash.
			/**
			* Allow to register custom subdirectory(ies) as custom directory for search templates.
			*
			* @param array $paths_array Array with directories, without trailing slash.
			*/
			$paths  = apply_filters( 'mobiloud_templates_paths', array( $default_path ) );
			$subdir = '' !== $template_type ? DIRECTORY_SEPARATOR . $template_type : '';
			// all this types of templates used same subdirectory.
			if ( in_array( $template_type, array( 'list', 'regular', 'search', 'favorites', 'special' ) ) ) {
				$subdir = DIRECTORY_SEPARATOR . 'list';
			}

			$prev_name = '';
			foreach ( $template_names as $template_name ) {
				foreach ( $paths as $path ) {
					$name = trailingslashit( $path . $subdir ) . $template_name . '.php';
					if ( $name !== $prev_name ) { // no need to check same file again.
						if ( file_exists( $name ) ) {
							$located = $name;
							break;
						}
						$prev_name = $name;
					}
				}
				if ( '' !== $located ) {
					break;
				}
			}
		}
		if ( $load && '' !== $located ) {
			if ( $require_once ) {
				require_once $located;
			} else {
				require $located;
			}
		}

		return $located;
	}

	/**
	 * Is request coming from iOS device.
	 *
	 * @since 4.2.0
	 *
	 * @return bool True for iOS device.
	 */
	public static function is_ios() {
		static $result = null;
		if ( is_null( $result ) ) {
			require_once MOBILOUD_PLUGIN_DIR . 'libs/mobile-detect/Mobile_Detect.php';
			$detect = new ML_Mobile_Detect();
			$result = $detect->isiOS();
		}
		return $result;
	}

	public static function shortcode( $atts, $content, $tag ) {
		$is_ios = self::is_ios();
		if ( $is_ios && 'ml-ios-only' === $tag || ! $is_ios && 'ml-android-only' ) {
			return $content;
		}
		return '';
	}

	/**
	 * Return list of allowed tags for using in kses() function
	 *
	 * @return array
	 */
	public static function expanded_alowed_tags() {
		$my_allowed = wp_kses_allowed_html( 'post' );
		// for Paywall screens.
		$my_allowed['a']['onclick']      = true;
		$my_allowed['button']['onclick'] = true;
		// iframe.
		$my_allowed['iframe'] = array(
			'src'             => array(),
			'height'          => array(),
			'width'           => array(),
			'frameborder'     => array(),
			'allowfullscreen' => array(),
		);
		// script.
		$my_allowed['script'] = array(
			'type' => array(),
			'src'  => array(),
		);
		// form fields - input.
		$my_allowed['input'] = array(
			'class' => array(),
			'id'    => array(),
			'name'  => array(),
			'value' => array(),
			'type'  => array(),
		);
		// select.
		$my_allowed['select'] = array(
			'class' => array(),
			'id'    => array(),
			'name'  => array(),
			'value' => array(),
			'type'  => array(),
		);
		// select options.
		$my_allowed['option'] = array(
			'selected' => array(),
		);
		// style.
		$my_allowed['style'] = array(
			'types' => array(),
		);

		// script.
		$my_allowed['noscript'] = array();
		// forms.
		if ( ! isset( $my_allowed['form'] ) ) {
			$my_allowed['form'] = [];
		}
		$my_allowed['form']['id']     = [];
		$my_allowed['form']['action'] = [];
		$my_allowed['form']['method'] = [];
		$my_allowed['form']['class']  = [];

		return $my_allowed;
	}

	public static function reinitialize_shortcodes() {
		$ignore_list = self::get_option( 'ml_ignore_shortcodes', [] );
		if ( ! empty( $ignore_list ) ) {
			foreach ( $ignore_list as $shortcode ) {
				if ( shortcode_exists( $shortcode ) ) {
					remove_shortcode( $shortcode );
				}
				add_shortcode( $shortcode, '__return_empty_string' );
			}
		}
	}

	/**
	 * Post excluded from list.
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public static function is_post_excluded_from_list( $post_id ) {
		$exclude_post_from_list = get_post_meta( $post_id, 'ml_exclude_post', true );
		return ! empty( $exclude_post_from_list );
	}

	public static function set_post_excluded_from_list( $post_id, $is_excluded ) {
		update_post_meta( $post_id, 'ml_exclude_post', empty( $is_excluded ) ? 0 : 1 );
		if ( $is_excluded ) {
			wp_set_post_terms( $post_id, 'excluded', 'mobile_app_exclude', false ); // used for Post swipe feature.
		} elseif ( has_term( 'excluded', 'mobile_app_exclude', $post_id ) ) {
			wp_remove_object_terms( $post_id, 'excluded', 'mobile_app_exclude' );
		}
	}

	/**
	* Register taxonomy, required for "Exclude post from list" feature.
	*
	* @since 4.2.8
	*/
	public static function register_taxonomy() {
		$post_types = explode( ',', (string)get_option( 'ml_article_list_include_post_types' ) );
		if ( count( $post_types ) ) {
			register_taxonomy(
				'mobile_app_exclude',
				$post_types,
				[
					'label' => 'Mobile App Excluded Items',
					'public' => false,
					'show_ui' => false,
					'rewrite' => false,
					'query_var' => false,
					'meta_box_cb' => false,
					'show_in_rest' => false,
				]
			);
			if ( ! term_exists( 'excluded', 'mobile_app_exclude' ) ) { // first run with the feature turned on.
				$term_id = wp_insert_term( 'excluded', 'mobile_app_exclude' );
				if ( is_array( $term_id ) ) {
					// update existing posts with post meta ml_exclude_post = 1
					$posts = get_posts(
						array(
							'post_type' => $post_types,
							'fields' => 'ids',
							'meta_key' => 'ml_exclude_post',
							'meta_value' => '1',
							'numberposts' => 1000,
						)
					);
					if ( is_array( $posts ) && count( $posts ) ) {
						array_walk( $posts, function( $post_id ) {
							wp_set_post_terms( (int)$post_id, 'excluded', 'mobile_app_exclude', false );
						} );
					}
				}
			}
		}
	}

	public static function require_default_template_wrapper() {
		$theme_dir = get_stylesheet_directory();

		if ( file_exists( $theme_dir . '/mobiloud-news-templates/' . 'list.php' ) ) {
			require_once $theme_dir . '/mobiloud-news-templates/' . 'list.php';
		} else {
			require_once MOBILOUD_PLUGIN_DIR . 'mobiloud-news-templates/list.php';
		}
	}

	/**
	 * Returns the template path as per the endpoint.
	 */
	public static function get_default_template( $endpoint = 'list' ) {
		global $wp;

		$theme_dir     = get_stylesheet_directory();
		$template_path = '';

		/**
		 * MobiLoud has a posts endpoint that returns JSON.
		 * We need to handle that as well.
		 */
		$ml_api = isset( $wp->query_vars['__ml-api'] ) ? $wp->query_vars['__ml-api'] : false;

		if ( 'posts' === $ml_api ) {
			$endpoint = 'post';
		}

		$is_archive = self::is_archive_page();

		if ( false !== $is_archive && is_array( $is_archive ) ) {
			foreach ( $is_archive as $path ) {
				if ( file_exists( $theme_dir . '/mobiloud-news-templates/' . $path . '-content.php' ) ) {
					return $theme_dir . '/mobiloud-news-templates/' . $path . '-content.php';
				}
			}
		}

		if ( file_exists( $theme_dir . '/mobiloud-news-templates/' . $endpoint . '-content.php' ) ) {
			$template_path = $theme_dir . '/mobiloud-news-templates/' . $endpoint . '-content.php';
		} else {
			$template_path = MOBILOUD_PLUGIN_DIR . 'mobiloud-news-templates/' . $endpoint . '-content.php';
		}

		return apply_filters( "mobiloud_default_path", $template_path, $endpoint, $_GET );
	}

	/**
	 * Returns array of template paths for archive pages, else returns false.
	 *
	 * @return array|boolean
	 */
	public static function is_archive_page() {
		$known_keys = array(
			'author',
			'post_type',
			'taxonomy',
		);

		$_get = $_GET;

		foreach ( $_get as $key => $value ) {
			if ( ! in_array( $key, $known_keys ) && 'term_id' !== $key ) {
				unset( $_get[ $key ] );
			}
		}

		if ( 0 === count( $_get ) ) {
			return false;
		}

		$get_keys = array_keys( $_get );

		if ( 1 === count( $_get ) ) {
			$single_key = $get_keys[0];

			if ( in_array( $single_key, $known_keys ) ) {
				return array(
					$single_key . '-' . $_get[ $single_key ],
					$single_key,
				);
			}
		}

		if ( 2 === count( $_get ) && isset( $_get['taxonomy'] ) && isset( $_get['term_id'] ) ) {
			return array(
				'taxonomy' . '-' . $_get['taxonomy'] . '-' . $_get['term_id'],
			);
		}
	}

	/**
	 * Loads header for default templates.
	 */
	public static function get_default_template_header() {
		require_once MOBILOUD_PLUGIN_DIR . '/mobiloud-news-templates/header.php';
	}

	/**
	 * Loads footer for default templates.
	 */
	public static function get_default_template_footer() {
		require_once MOBILOUD_PLUGIN_DIR . '/mobiloud-news-templates/footer.php';
	}

	/**
	 * Returns posts as per GET parameters.
	 *
	 * @return array
	 */
	public static function get_default_template_list_data() {
		$api      = new MLApiController();
		$ml_query = new MLQuery();
		$ml_posts = new MLPostsModel();

		$api->build_query( $ml_query, $ml_posts );
		$posts = $ml_posts->get_posts( $ml_query );

		$ml_posts_ctrl = new MLPostsController();

		$user_offset  = $ml_query->user_offset;
		$taxonomy     = $ml_query->taxonomy;
		$post_count   = $ml_query->post_count;
		$image_format = $ml_query->image_format;

		$final_posts = $ml_posts_ctrl->get_final_posts( $posts, $user_offset, $taxonomy, $post_count, false, $image_format );

		if ( $ml_query->permalink_is_taxonomy ) {
			$final_posts['taxonomy'] = $ml_query->taxonomy;
		}

		$current_user = wp_get_current_user();
		$final_posts  = apply_filters( 'ml_posts', $final_posts, $current_user );

		foreach ( $final_posts['posts'] as $k => $p ) {
			$or_method = $final_posts['posts'][ $k ];

			if ( ! empty( $or_method ) && is_string( $or_method ) ) {
				$opening_method = $or_method;
			} else {
				$opening_method = 'native';
			}

			if ( get_post_meta( $p['post_id'], 'open_externally', true ) == 'true' ) {
				$opening_method = 'internal';
			}

			$final_posts['posts'][ $k ]['opening_method'] = $opening_method;
		}

		return $final_posts;
	}

	/**
	 * Returns true action filter status if $post_id > 0.
	 *
	 * @param integer $post_id Post ID.
	 * @return boolean
	 */
	public static function get_action_filter_status( $post_id = 0 ) {
		$status = false;

		if ( 0 === $post_id ) {
			return $status;
		}

		$local_status = get_post_meta( $post_id, '_mobiloud_action_filters_status', true );

		return $local_status;
	}

	/**
	 * Returns handlePost for post type that supports comments, and handleLink otherwise.
	 * @param string     $post_type The Post Type.
	 * @param string|int $post_id   The Post ID.
	 *
	 * @return string
	 */
	public static function get_native_link( $post_type = 'post', $post_id = 0 ) {
		if ( 0 === $post_id ) {
			return '';
		}

		$does_support_comment = post_type_supports( $post_type, 'comments' );

		if ( $does_support_comment ) {
			return sprintf( "nativeFunctions.handlePost( {$post_id} )" );
		}

		$permalink = get_permalink( $post_id );
		$title = get_the_title( $post_id );

		return sprintf(
			"nativeFunctions.handleLink( '%s', '%s', 'native' )",
			$permalink,
			$title
		);
	}
}
