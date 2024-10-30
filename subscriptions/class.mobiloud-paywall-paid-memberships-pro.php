<?php
/**
 * Mobiloud Paywall Paid Memberships Pro class.
 *
 * @since 4.2.0
 */
class Mobiloud_Paywall_Paid_Memberships_Pro extends Mobiloud_Paywall_Base {

	/**
	 * Paid Memberships pro plugin is active.
	 *
	 * @since 4.2.0
	 *
	 * @return bool
	 */
	private function is_paid_memberships_pro_active() {
		return function_exists( 'pmpro_has_membership_access' );
	}

	/**
	 * Return error message on activation, when something required is not exists.
	 *
	 * @since 4.2.0
	 *
	 * @return string|null Error messages if any or null.
	 */
	public function activate_error_message() {
		if ( ! $this->is_paid_memberships_pro_active() ) {
			return 'Paid Memberships Pro in not detected.';
		}
		return null;
	}

	/**
	 * Is content restricted, using Paid Memberships Pro rules.
	 *
	 * @since 4.2.0
	 *
	 * @return bool true - blocked, false - allowed.
	 */
	protected function ml_is_content_restricted() {
		$restricted = false;
		// if MemberPress is active - turn on Paywall only for content, blocked by it's rules. Ignore our own checkbox option at the posts & categories.
		if ( $this->is_paid_memberships_pro_active() ) {
			$restricted = $this->check_access( false ); // allowed by default.
		}
		return $restricted;
	}

	/**
	 * Check and maybe show Paywall screen.
	 *
	 * @since 4.2.0
	 *
	 * @return bool true - if content already blocked, false if content is not blocked.
	 */
	protected function ml_paywall_validate_user() {
		$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; // phpcs:ignore
		// Check subscription headers.
		$token = MLAPI::get_token_value();
		if ( '' !== $token ) {
			MLAPI::set_user_from_token( $token );

			if ( $this->is_paid_memberships_pro_active() ) {
				$restricted = $this->check_access( false ); // allowed by default.
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
	 * @param bool $result
	 * @return bool true - content blocked, false - not blocked.
	 */
	private function check_access( $result ) {
		/**
		* Curent post.
		* Defined at mobiloud-mobile-app-plugin/views/post.php
		*
		* @var WP_Post
		*/
		global $post;
		if ( ! is_null( $post ) ) {
			$current_post = null;
			if ( isset( $post ) && is_a( $post, 'WP_Post' ) ) {
				$current_post = $post;
			} elseif ( isset( $_GET['post_id'] ) ) {
				$current_post = get_post( absint( $_GET['post_id'] ) );
			}
			if ( ! is_null( $current_post ) && is_a( $current_post, 'WP_Post' ) ) {
				$result = ! pmpro_has_membership_access( intval( $current_post->ID ), null, false );
			}
		}
		return $result;
	}
}
