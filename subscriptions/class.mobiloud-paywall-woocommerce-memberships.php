<?php
/**
 * Mobiloud Paywall WooCommerce Memberships class.
 *
 * @since 4.2.0
 */
class Mobiloud_Paywall_WooCommerce_Memberships extends Mobiloud_Paywall_Base {

	/**
	 * WooCommerce plugin is active.
	 *
	 * @since 4.2.0
	 *
	 * @return bool
	 */
	private function is_active() {
		return class_exists( 'WC_Memberships' );
	}

	/**
	 * Return error message on activation, when something required is not exists.
	 *
	 * @since 4.2.0
	 *
	 * @return string|null Error messages if any or null.
	 */
	public function activate_error_message() {
		if ( ! $this->is_active() ) {
			return 'WooCommerce Memberships is not detected.';
		}
		return null;
	}

	/**
	 * Is content restricted, using WooCommerce Memberships' rules.
	 *
	 * @since 4.2.0
	 *
	 * @return bool true - blocked, false - allowed.
	 */
	protected function ml_is_content_restricted() {
		$restricted = false;
		// if main plugin is active - turn on Paywall only for content, blocked by it's rules.
		if ( $this->is_active() ) {
			$url        = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; // phpcs:ignore
			$restricted = $this->check_woo( false, $url ); // allowed by default.
		}
		return $restricted;
	}

	/**
	 * Check and maybe show Paywall screen.
	 *
	 * @return bool true - if content already blocked, false if content is not blocked.
	 */
	protected function ml_paywall_validate_user() {
		$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		// Check subscription headers.
		$token = MLAPI::get_token_value();
		if ( '' !== $token ) {
			MLAPI::set_user_from_token( $token );

			if ( $this->is_active() ) {
				$restricted = $this->check_woo( false, $url ); // allowed by default.
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
	 * Check access to current or given post if WooCommerce Memberships plugin is active.
	 *
	 * @since 4.2.0
	 *
	 * @global WP_Post $post
	 * @param bool   $result
	 * @param string $url
	 * @return bool true - content blocked, false - not blocked.
	 */
	private function check_woo( $result, $url ) {
		if ( 'list' === MLAPI::get_current_endpoint() && isset( $_GET['taxonomy'] ) && isset( $_GET['term_id'] ) ) {
			$result = $this->woo_categories_restricted( $_GET['term_id'], false, $_GET['taxonomy'] );
		}
		if ( 'list' === MLAPI::get_current_endpoint() && isset( $_GET['categories'] ) ) {
			$result = $this->woo_categories_restricted( $_GET['categories'] );
		}
		if ( ( 'post' === MLAPI::get_current_endpoint() || 'posts' === MLAPI::get_current_endpoint() ) && isset( $_GET['post_id'] ) ) {
			$post_id = intval( $_GET['post_id'] );
			if ( wc_memberships_is_post_content_restricted( $post_id ) && ! wc_memberships_user_can( get_current_user_id(), 'view', array( 'post' => $post_id ) ) ) {
				$result = true;
			} else {
				$taxes = get_taxonomies(
					array(
						'public' => true,
					)
				);

				$cats  = wp_get_post_terms( $post_id, $taxes );
				$terms = array();
				foreach ( $cats as $cat ) {
					$terms[] = $cat->term_id;
				}
				$result = $this->woo_categories_restricted( implode( ',', $terms ), true );
			}
		}

		return $result;
	}

	/**
	 * Check access to category or taxonomy.
	 *
	 * @since 4.2.0
	 *
	 * @param string $cats
	 * @param bool   $single
	 * @param string $tax
	 */
	private static function woo_categories_restricted( $cats, $single = false, $tax = 'category' ) {
		$restricted = false;
		$terms      = explode( ',', $cats );
		$rcount     = 0;

		foreach ( $terms as $term ) {
			if ( ! current_user_can( 'wc_memberships_view_restricted_taxonomy_term', $tax, $term ) ) {
				if ( $single ) {
					$restricted = true;
				}
				// count total restricted categories.
				$rcount ++;
			}
		}

		if ( $rcount === count( $terms ) ) {
			$restricted = true;
		}

		return $restricted;
	}
}
