<?php
/**
 * Mobiloud Paywall Memberpress class.
 *
 * @since 4.2.0
 */
class Mobiloud_Paywall_Memberpress extends Mobiloud_Paywall_Base {

	/**
	 * Memberpress plugin is active.
	 *
	 * @since 4.2.0
	 *
	 * @return bool
	 */
	private function is_memberpress_active() {
		return ( function_exists( 'mepr_plugin_info' ) && class_exists( 'MeprRule' ) );
	}

	/**
	 * Return error message on activation, when something required is not exists.
	 *
	 * @since 4.2.0
	 *
	 * @return string|null Error messages if any or null.
	 */
	public function activate_error_message() {
		if ( ! function_exists( 'mepr_plugin_info' ) ) {
			return 'Memberpress in not detected (1).';
		}
		if ( ! class_exists( 'MeprRule' ) ) {
			return 'Memberpress in not detected (2).';
		}
		return null;
	}

	/**
	 * Is content restricted, using Memberpress' rules.
	 *
	 * @since 4.2.0
	 *
	 * @return bool true - blocked, false - allowed.
	 */
	protected function ml_is_content_restricted() {
		$restricted = false;
		// if MemberPress is active - turn on Paywall only for content, blocked by it's rules. Ignore our own checkbox option at the posts & categories.
		if ( $this->is_memberpress_active() ) {
			$url        = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			$restricted = $this->check_merp( false, $url ); // allowed by default.
		}
		return $restricted;
	}

	/**
	 * Check and maybe show Paywall screen.
	 *
	 * @return bool true - if content already blocked, false if content is not blocked.
	 */
	protected function ml_paywall_validate_user() {
		$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; // phpcs:ignore
		// Check subscription headers.
		$token = MLAPI::get_token_value();
		if ( '' !== $token ) {
			MLAPI::set_user_from_token( $token );

			if ( $this->is_memberpress_active() ) {
				$restricted = $this->check_merp( false, $url ); // allowed by default.
			} else {
				$post_id = isset( $GLOBALS['post'] ) ? $GLOBALS['post']->ID : null;
				/**
				* Allow access or show Paywall screen.
				* Note: you can detect currently active Paywall class using get_class( ml_get_paywall() ) or is_a( ml_get_paywall(), '...' ).
				*
				* @since 4.2.0
				*
				* @param bool $allow_access Is access alowed.
				* @param string $url Current url.
				* @param int|null $post_id Post ID if detected, otherwise null.
				*/
				$restricted = apply_filters( 'mobiloud_paywall_loggedin_access_check', true, $url, $post_id ); // allowed by default, can use chaining.
			}

			if ( ! is_user_logged_in() || $restricted ) {
				$this->ml_paywall_block();
				return true;
			}
		} elseif ( isset( $_SERVER['HTTP_X_ML_IS_USER_SUBSCRIBED'] ) && $_SERVER['HTTP_X_ML_IS_USER_SUBSCRIBED'] === 'true' && isset( $_SERVER['HTTP_X_ML_SUBSCRIPTION_ID'] ) ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedElseif
			// user is subscribed, do nothing.
		} elseif ( $this->ml_is_content_restricted() ) {
			// trigger paywall block.
			$this->ml_paywall_block();
			return true;
		}
		return false;
	}

	/**
	 * Filter: check access to current or given post if Membership plugin is active.
	 *
	 * @since 4.2.0
	 *
	 * @global WP_Post $post
	 * @param bool         $result
	 * @param string       $url
	 * @param WP_Post|null $current_post Post, if is set checked only for this post.
	 * @return bool true - content blocked, false - not blocked.
	 */
	private function check_merp( $result, $url, $current_post = null ) {
		// if post ID is set.
		if ( is_a( $current_post, 'WP_Post' ) ) {
			$post_url = get_permalink( $current_post->ID );
			return MeprRule::is_locked( $current_post ) || MeprRule::is_uri_locked( $post_url );
		}
		if ( ( strpos( $url, '/post?' ) || strpos( $url, '/posts?' ) ) && isset( $_GET['post_id'] ) ) {
			/**
			* Curent post.
			* Defined at mobiloud-mobile-app-plugin/views/post.php
			*
			* @var WP_Post
			*/
			global $post;
			$current_post = null;
			if ( isset( $post ) && is_a( $post, 'WP_Post' ) ) {
				$current_post = $post;
			} elseif ( isset( $_GET['post_id'] ) ) {
				$current_post = get_post( absint( $_GET['post_id'] ) );
			}
			if ( ! is_null( $current_post ) && is_a( $current_post, 'WP_Post' ) ) {
				$post_url = get_permalink( $current_post );
				$result   = MeprRule::is_locked( $current_post ) || MeprRule::is_uri_locked( $post_url );
			}
		}
		// check using current endpoint url.
		if ( MeprRule::is_uri_locked( $url ) ) {
			$result = true;
		}
		return $result;
	}

}
