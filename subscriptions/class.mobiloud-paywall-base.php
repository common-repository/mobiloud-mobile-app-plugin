<?php

/**
 * Mobiloud Paywall base class.
 * The basic parent class for all working integrations.
 *
 * @since 4.2.0
 */
class Mobiloud_Paywall_Base {

	protected $ignore_user_filters;

	public function __construct() {
		$this->ignore_user_filters = true;
	}

	/**
	 * Return error message on activation, when something required is not exists.
	 *
	 * @since 4.2.0
	 *
	 * @return string|null Error messages if any or null.
	 */
	public function activate_error_message() {
		$class = get_option( 'ml_membership_class' );
		if ( ! empty( $class ) && ! class_exists( $class ) ) {
			$options = ml_get_memberships_list();
			return isset( $options[ $class ] ) ? "Extension class for {$options[ $class ]} not found." : "Extension class {$class} not found.";
		}
		return null;
	}

	/**
	 * Action 'mobiloud_before_content_requests' handler
	 */
	public function ml_validate_requests() {
		// Check if content is restricted and maybe add paywall screen.
		$this->ml_paywall_validate_user();
	}

	/**
	 * Check and maybe show Paywall screen.
	 *
	 * @since 4.2.0
	 *
	 * @return bool true - if content already blocked, false if content is not blocked.
	 */
	protected function ml_is_content_restricted() {
		return false;
	}

	/**
	 * Check and maybe show Paywall screen.
	 * Called for for post, category or taxonomy.
	 *
	 * @return bool true - if content already blocked, false if content is not blocked.
	 */
	protected function ml_paywall_validate_user() {
		// allow full access if paywall disabled.
		if ( ! ml_is_paywall_enabled() ) {
			return false;
		}

		$post_id = isset( $GLOBALS['post'] ) && ( is_a( $GLOBALS['post'], 'WP_Post' ) ) ? $GLOBALS['post']->ID : null;

		if ( is_null( $post_id ) ) {
			if ( isset( $_GET['post_id'] ) ) {
				$post_id = (int)filter_input( INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT );
			}
		}

		$terms = wp_get_post_terms( $post_id, 'category' );
		$paywall_per_post = get_post_meta( $post_id, 'ml_paywall_protected', true );
		$is_marked_for_paywall = false;

		foreach ( $terms as $term ) {
			$opt = get_option( 'taxonomy_' . $term->term_id );

			if ( isset( $opt['ml_tax_paywall'] ) && 'true' === $opt['ml_tax_paywall'] ) {
				$is_marked_for_paywall = true;
			}
		}

		if ( ! ( $is_marked_for_paywall || 'true' === $paywall_per_post ) ) {
			return false;
		}

		$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; // phpcs:ignore
		// Check subscription header 'HTTP_X_ML_VALIDATION' and return the token.
		$token = MLAPI::get_token_value();
		if ( '' !== $token ) {
			if ( is_null( MLAPI::set_user_from_token( $token ) ) ) {
				if ( $this->ml_is_content_restricted() ) {
					// trigger paywall block.
					$this->ml_paywall_block();
					return true;
				} else {
					return false;
				}
			}
			if ( ! $this->ignore_user_filters ) {
				// Filter to check content access for current user.
				if ( has_filter( 'mobiloud_paywall_loggedin_access' ) ) { // does already have some legacy filters?
					$restricted = apply_filters( 'mobiloud_paywall_loggedin_access', $url ); // allowed by default.
				} else {
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
					$restricted = apply_filters( 'mobiloud_paywall_loggedin_access_check', false, $url, $post_id ); // allowed by default, can use chaining.
				}
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
	 * Show Paywall screen.
	 */
	public function ml_paywall_block() {
		Mobiloud::use_template( 'paywall', 'paywall', true, false );
	}

	// Admin dashboard stuff.

	/**
	 * Show Paywall metaboxes when feature is on.
	 *
	 * @since 4.2.0
	 *
	 * @return void
	 */
	public function maybe_add_metaboxes() {
		// nothing to add, should be redifined in child classes.
	}

}
