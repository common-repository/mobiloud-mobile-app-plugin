<?php
/**
 * Class to support the Leaky Paywall plugin..
 *
 * @since 4.2.0
 */
class Mobiloud_Paywall_Leaky extends Mobiloud_Paywall_Base {

	/**
	 * The constructor function.
	 */
	public function __construct() {
		if ( ! isset( $_GET['post_id'] ) ) {
			return;
		}

		$this->show_paywall = false;
		$this->ignore_user_filters = false;
		$this->leaky = new Leaky_Paywall_Restrictions( $_GET['post_id'] );

		add_filter( 'ml_default_post_content_filter', function( $val ) {
			return ! $this->show_paywall;
		} );
	}

	/**
	 * Returns true if content is restrcicted.
	 *
	 * @return boolean
	 */
	public function is_content_restricted() {
		$is_restricted = false;

		if ( $this->content_matches_restriction_rules() ) {
			$is_restricted = true;
		}

		return $is_restricted;
	}

	/**
	 * Returns true if content matches restriction rules.
	 */
	protected function content_matches_restriction_rules() {
		

		if ( $this->leaky->content_matches_restriction_exceptions() ) {
			return false;
		}

		// check if content is set to be open to everyone.
		if ( $this->leaky->visibility_allows_access() ) {
			return false;
		}

		if ( $this->leaky->visibility_restricts_access() ) {
			return true;
		}

		// check if content is restricted based on main restriction settings.
		if ( $this->leaky->content_restricted_by_settings() ) {
			return true;
		}


		return false;
	}

	/**
	 * Validates users and renders paywall for restricted users.
	 */
	protected function ml_paywall_validate_user() {
		if ( ! $this->is_content_restricted() ) {
			return;
		}

		if ( $this->leaky->current_user_can_access() ) {
			return;
		}

		$this->show_paywall = true;
		$this->ml_paywall_block();
	}
}
