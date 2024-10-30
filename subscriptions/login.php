<?php
ini_set( 'display_errors', 1 );
if ( ! defined( 'MOBILOUD_API_REQUEST' ) ) {
	require_once dirname( dirname( __FILE__ ) ) . '/api/compability.php';
	ml_compability_api_result( 'login', true );
}
require_once 'functions.php';

if ( ! isset( $_POST['username'] ) || ! isset( $_POST['password'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification -- this request is coming from mobile App.
	die();
}

$username = sanitize_text_field( wp_unslash( $_POST['username'] ) );
$password = sanitize_text_field( wp_unslash( $_POST['password'] ) );

$data = array();
$user = MLAPI::ml_login_wordpress( $username, $password );

if ( get_class( $user ) == 'WP_User' ) {

	// Get capabilities from Groups plugin if it's present.
	if ( class_exists( 'Groups_User' ) ) {

		$group_user           = new Groups_User( $user->ID );
		$data['user']         = array();
		$data['user']['name'] = "$user->user_firstname $user->user_lastname";
		$data['groups']       = array();
		$data['capabilities'] = array();

		$groups = $group_user->__get( 'groups' );
		foreach ( $groups as $group ) {
			$g                = array();
			$g['id']          = $group->group_id;
			$g['name']        = $group->name;
			$data['groups'][] = $g;

			// capabilities.
			$capabilities = $group->__get( 'capabilities' );
			if ( $capabilities != null ) {
				foreach ( $capabilities as $capability ) {
					$value = $capability->__get( 'capability' );
					if ( ! is_null( $value ) ) {
						$data['capabilities'][] = $value;
					}
				}
			}
		}
	} else { // Default WP capabilities.
		foreach ( $user->allcaps as $capability => $value ) {
			if ( ( $value == true ) && ! is_null( $capability ) ) {
				$data['capabilities'][] = $capability;
			}
		}
	}
} else { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedElse
	// error, user not found - return default answer.
}

echo wp_json_encode( $data );
