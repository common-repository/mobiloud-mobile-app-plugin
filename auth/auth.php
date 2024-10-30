<?php
header( 'Content-type: application/json' );

$action = isset( $_POST['action'] ) ? sanitize_text_field( $_POST['action'] ) : ( isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '' );

$user = isset( $_POST['username'] ) ? sanitize_text_field( $_POST['username'] ) : '';
$pass = isset( $_POST['password'] ) ? sanitize_text_field( $_POST['password'] ) : '';

do_action(
	'mobiloud_auth_custom_endpoint', array(
		'post'   => $_POST,
		'action' => $action,
	)
);

function ml_set_memberium_actions() {
	if ( function_exists( 'memberium_app' ) ) {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}
		// successful admin login.
		$_POST['redirect_to'] = '?action=echo';

		// failed login.
		if ( ! isset( $_POST['woocommerce-login-nonce'] ) ) {
			$_POST['woocommerce-login-nonce'] = true;
		}
	}

}

switch ( $action ) {
	case 'echo':
		include_once MOBILOUD_PLUGIN_DIR . 'subscriptions/functions.php';
		ml_set_memberium_actions();
		$ml_user = wp_get_current_user();

		if ( 0 === $ml_user->ID ) {
			$response = array(
				'status'  => 'failed',
				'message' => 'Login failed.',
			);
			echo wp_json_encode( $response );
			die();
		}
		/** @var array */
		$errors  = null;
		$message = null;
		if ( is_wp_error( $ml_user ) ) {
		} else {
			/**
			* Filters whether to disallow login on auth endpoint.
			* Could be used for additional credentials checking (together with mobiloud_token_set_user filter).
			*
			* @since 4.2.0
			*
			* @param string  $message Error message, if non empty will disallow login.
			* @param WP_User $ml_user Signed in user.
			*/
			$message = apply_filters( 'mobiloud_auth_disallow_login', '', $ml_user );
			if ( '' !== $message ) {
				$errors = array( 'custom_error' => $message );
			}
		}

		if ( is_null( $errors ) ) {
			$token = MLAPI::get_user_token( get_current_user_id() );
			if ( '' === $token ) {
				http_response_code( 401 );
				$response = array(
					'status'  => 'failed',
					'message' => 'Failed to create token for user.',
				);
				echo wp_json_encode( $response );
				die();
			}

			// Send the validation header back.
			header( 'X-ML-VALIDATION: ' . $token );
			$response = array(
				'status'  => 'ok',
				'message' => 'Login successful!',
			);

		} else {
			wp_set_current_user( 0 ); // log out any current user.
			wp_clear_auth_cookie();

			http_response_code( 401 );
			$response = array(
				'status'  => 'failed',
				'message' => is_null( $message ) ? 'Invalid username, email address or incorrect password' : $message,
				'errors'  => $errors,
			);
		}
		echo wp_json_encode( $response );
		break;

	case 'login':
	default:
		include_once MOBILOUD_PLUGIN_DIR . 'subscriptions/functions.php';

		ml_set_memberium_actions();
		$ml_user = MLAPI::ml_login_wordpress( $user, $pass );

		/** @var array */
		$errors  = null;
		$message = null;
		if ( is_wp_error( $ml_user ) ) {
			$errors = $ml_user->errors;
		} else {
			/**
			* Filters whether to disallow login on auth endpoint.
			* Could be used for additional credentials checking (together with mobiloud_token_set_user filter).
			*
			* @since 4.2.0
			*
			* @param string  $message Error message, if non empty will disallow login.
			* @param WP_User $ml_user Signed in user.
			*/
			$message = apply_filters( 'mobiloud_auth_disallow_login', '', $ml_user );
			if ( '' !== $message ) {
				$errors = array( 'custom_error' => $message );
			}
		}

		if ( is_null( $errors ) ) {
			$userID   = get_current_user_id();
			$ml_token = get_user_meta( $userID, 'ml_auth_token', true );
			$ml_time  = get_user_meta( $userID, 'ml_auth_time', true );
			// Check if user already has a token.
			if ( empty( $ml_token ) ) {
				// generate the token.
				$ml_token = wp_hash( $userID );
				$created  = update_user_meta( $userID, 'ml_auth_token', $ml_token );

				if ( ! $created ) {
					http_response_code( 401 );
					$response = array(
						'status'  => 'failed',
						'message' => 'Failed to create token for user.',
					);
					echo wp_json_encode( $response );
					die();
				}
			}
			update_user_meta( $userID, 'ml_auth_time', time() );
			// Send the validation header back.
			header( 'X-ML-VALIDATION: ' . $ml_token . '|' . time() );
			$response = array(
				'status'  => 'ok',
				'message' => 'Login successful!',
			);

		} else {
			wp_set_current_user( 0 ); // log out any current user.
			wp_clear_auth_cookie();

			http_response_code( 401 );
			$response = array(
				'status'  => 'failed',
				'message' => is_null( $message ) ? 'Invalid username, email address or incorrect password' : $message,
				'errors'  => $errors,
			);
		}
		echo wp_json_encode( $response );
		break;
}
die;
