<?php

if ( check_ajax_referer( 'ml-reg', 'n', false ) ) {
	$email              = Mobiloud::get_option( 'ml_contact_link_email', get_bloginfo( 'admin_email' ) );
	$errors             = [
		'username'   => __( 'Please provide an email address.', 'mobiloud' ),
		'password'   => __( 'Please provide an password.', 'mobiloud' ),
		'terms'      => __( 'Please accept the terms of agreement.', 'mobiloud' ),
		'receipt_id' => sprintf( __( 'There was a problem processing your subscription, please contact %s', 'mobiloud' ), esc_html( $email ) ),
	];
	$data['username']   = isset( $_POST['u'] ) ? sanitize_text_field( wp_unslash( $_POST['u'] ) ) : '';
	$data['password']   = isset( $_POST['p'] ) ? sanitize_text_field( wp_unslash( $_POST['p'] ) ) : '';
	$data['terms']      = isset( $_POST['t'] ) ? sanitize_text_field( wp_unslash( $_POST['t'] ) ) : '';
	$data['receipt_id'] = isset( $_POST['r'] ) ? base64_decode( sanitize_text_field( wp_unslash( $_POST['r'] ) ) ) : '';

	$errors = array_filter(
		$errors, function( $key ) use ( $data ) {
			return '' === $data[ $key ];
		}, ARRAY_FILTER_USE_KEY
	);

	if ( empty( $errors ) ) {
		$result  = MLAPI::ml_register_wordpress_user( $data['username'], $data['password'], $data['receipt_id'] );
		$cookies = [];
		foreach ( headers_list() as $header ) {
			if ( preg_match( '/^set-cookie: (.*)$/i', $header, $m ) ) {
				$cookies[] = $m[1];
			}
		}
		if ( ! is_wp_error( $result ) ) {
			$content = Mobiloud::get_option( 'ml_app_registration_block_success', 'You account has been created, check your email for more details.' );
			$message = wp_kses( str_replace( '%LOGOURL%', Mobiloud::get_option( 'ml_preview_upload_image', '' ), $content ), Mobiloud::expanded_alowed_tags() );
			$token   = MLAPI::get_user_token( get_current_user_id() );

			if ( '' !== $token ) {
				header( 'X-ML-VALIDATION: ' . $token );
				wp_send_json_success(
					[
						'cookies' => $cookies,
						'message' => $message,
						'token'   => $token, // todo: send there + header or only header?
					]
				);
			} else {
				$errors[] = 'Failed to create token for user.';
			}
		} else {
			/** @var WP_Error result */
			$errors = $result->get_error_messages();
		}
	}
	wp_send_json_error( [ 'errors' => array_values( $errors ) ] );
} else {
	wp_send_json_error( [ 'errors' => [ 'Securuty check failed. Please reload a page and try again.' ] ] );

}

