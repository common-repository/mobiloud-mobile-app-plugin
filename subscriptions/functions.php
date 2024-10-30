<?php
require_once dirname( __FILE__ ) . '/class.mobiloud-groups.php';

/**
 * Get Membership integrations list
 *
 * @return array Items, associative array.
 *   index is a class name.
 *   value is a title.
 */
function ml_get_memberships_list() {
	/**
	* Filters Membership integrations list.
	*
	* @since 4.2.0
	*
	* @param array $items Items, associative array.
	*   index is a class name.
	*   value is a title.
	*/
	return apply_filters(
		'mobiloud_paywall_memberships',
		array(
			'Mobiloud_Paywall_Memberium'               => 'Memberium',
			'Mobiloud_Paywall_Memberpress'             => 'Memberpress',
			'Mobiloud_Paywall'                         => 'Mobiloud Paywall',
			'Mobiloud_Paywall_Paid_Memberships_Pro'    => 'Paid Memberships Pro',
			'Mobiloud_Paywall_WooCommerce_Memberships' => 'WooCommerce Memberships',
			'Mobiloud_Paywall_WooCommerce_Memberships' => 'WooCommerce Memberships',
			'Mobiloud_Paywall_Leaky'                   => 'Leaky Paywall',
		)
	);
}

/**
 * Return instance of chosen at the option Mobiloud Paywall class
 *
 * @since 4.2.0
 *
 * @param bool $force Force creation of new class, used after it changed at options.
 * @return Mobiloud_Paywall_Base
 */
function ml_get_paywall( $force = false ) {
	static $instance = null;
	// load on demand only.
	require_once dirname( __FILE__ ) . '/class.mobiloud-paywall-base.php';
	require_once dirname( __FILE__ ) . '/class.mobiloud-paywall-memberium.php';
	require_once dirname( __FILE__ ) . '/class.mobiloud-paywall-memberpress.php';
	require_once dirname( __FILE__ ) . '/class.mobiloud-paywall.php';
	require_once dirname( __FILE__ ) . '/class.mobiloud-paywall-paid-memberships-pro.php';
	require_once dirname( __FILE__ ) . '/class.mobiloud-paywall-woocommerce-memberships.php';
	require_once dirname( __FILE__ ) . '/class.mobiloud-paywall-leaky.php';

	if ( is_null( $instance ) ) {
		/**
		* Load custom paywall classes.
		* Class definition should be wrapped with if ( ! class_exists(...)) .
		*
		* @since 4.2.0
		*/
		do_action( 'mobiloud_paywall_initialize' );
	}
	if ( is_null( $instance ) || $force ) {
		$class = get_option( 'ml_membership_class' );
		if ( empty( $class ) || ! class_exists( $class ) ) {
			$class = 'Mobiloud_Paywall_Base';
		}
		$instance = new $class();
		if ( ! is_a( $instance, 'Mobiloud_Paywall_Base' ) ) {
			$instance = new Mobiloud_Paywall_Base();
		}
	}
	return $instance;
}

/**
 * Is Paywall enabled.
 *
 * @since 4.2.0
 *
 * @return bool Is Paywall feature turned on at the plugin options.
 */
function ml_is_paywall_enabled() {
	$selected_option = Mobiloud::get_option( 'ml_membership_class', '' );
	return ! empty( $selected_option );
}
