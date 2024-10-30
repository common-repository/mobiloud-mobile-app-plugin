<?php

define( 'MOBILOUD_ONESIGNAL_URL', 'https://onesignal.com/api/v1/' );

/**
 * Pushbots API notifications class
 */
class Mobiloud_Onesignal_Api extends Mobiloud_Push_Api {
	private $app_id;
	private $secret_key;
	private $endpoint_url;

	protected function load_options() {
		parent::load_options();
		$this->app_id       = get_option( 'ml_onesignal_app_id' );
		$this->secret_key   = get_option( 'ml_onesignal_secret_key' );
		$this->endpoint_url = MOBILOUD_ONESIGNAL_URL;
	}

	public function send_batch_notification( $data, $tagNames = array() ) {
		// convert platforms:
		// https://documentation.onesignal.com/v3.0/reference#section-platform-to-deliver-to
		$fields = array(
			'app_id'         => $this->app_id,
			'ios_badgeType'  => 'Increase',
			'ios_badgeCount' => 1,
		);
		if ( in_array( 0, $data['platform'] ) ) {
			$fields['isIos']             = true;
			$fields['content_available'] = (bool) Mobiloud::get_option( 'ml_push_wakes_app', true );
		}
		if ( in_array( 1, $data['platform'] ) ) {
			$fields['isAndroid'] = true;
		}

		if ( isset( $data['delayed_option'] ) ) {
			$fields['delayed_option'] = $data['delayed_option'];
		}

		// message.
		$fields['contents'] = array( 'en' => stripslashes( $data['msg'] ) );
		// tags.
		if ( ! empty( $data['tags'] ) && ! ( 1 === count( $data['tags'] ) && ( 'all' === $data['tags'][0] ) ) ) {
			$filters = array();
			// Tag "all" to always include when we are sending with tags.
			if ( ! in_array( 'all', $data['tags'] ) ) {
				$data['tags'][] = 'all';
			}
			foreach ( $data['tags'] as $value ) {
				if ( ! empty( $filters ) ) {
					$filters[] = array( 'operator' => 'OR' );
				}
				$filters[] = array(
					'field'    => 'tag',
					'key'      => $value,
					'relation' => '=',
					'value'    => $value,
				);
			}
			$fields['filters'] = $filters;
		} else {
			// Segment "All" to use when we send with no tags.
			$fields['included_segments'] = array( 'All' );
		}
		// payload.
		if ( ! empty( $data['payload'] ) ) {
			$fields['data'] = array();
			if ( isset( $data['payload']['post_id'] ) ) {
				$fields['data']['post_id'] = absint( $data['payload']['post_id'] );
			}
			if ( isset( $data['payload']['thumbnail'] ) ) {
				$fields['large_icon'] = $data['payload']['thumbnail'];
			}
			if ( isset( $data['payload']['featured_image'] ) ) {
				if ( empty( $fields['large_icon'] ) ) {
					$fields['large_icon'] = $data['payload']['featured_image'];
				}
				$fields['ios_attachments'] = array( 'id1' => $data['payload']['featured_image'] );
			}
			if ( isset( $data['payload']['url'] ) ) {
				$fields['data']['url'] = $data['payload']['url'];
				$fields['url']         = $data['payload']['url'];
			}
		}
		$json_data = wp_json_encode( $fields );

		$url        = $this->endpoint_url . 'notifications';
		$parameters = array(
			'timeout'   => 10,
			'headers'   => array(
				'Content-Type'   => 'application/json; charset=uft-8',
				'Authorization'  => 'Basic ' . $this->secret_key,
				'Content-Length' => strlen( $json_data ),
			),
			'sslverify' => false,
			'body'      => $json_data,
		);
		$result     = wp_remote_post( $url, $parameters );

		if ( $this->log_enabled ) {
			// hide secret key value.
			$parameters['headers']['Authorization'] = 'Basic *****';
			$this->save_log( $url, $parameters, $result );
		}
		$error = false;
		if ( ! empty( $result ) && ! is_wp_error( $result ) && ( '' !== wp_remote_retrieve_body( $result ) ) ) {
			$result = json_decode( wp_remote_retrieve_body( $result ), true );
			if ( ! empty( $result['id'] ) ) {
				$this->save_to_db( $data, $tagNames );
				return true;
			} elseif ( ! empty( $result['errors'] ) || ! empty( $result['warnings'] ) ) {
				$messages = ! empty( $result['errors'] ) ? $result['errors'] : array();
				if ( ! empty( $result['warnings'] ) ) {
					$messages = array_merge( $messages, $result['warnings'] );
				}
				foreach ( $messages as $key => $text ) {
					if ( 'All included players are not subscribed' == $text ) {
						$messages[ $key ] = "There's no users meeting the criteria used, this includes the categories in any post attached and/or your selection of platforms.";
					}
				}
				return implode( '<br>', $messages );
			}
		}
		return 'There was an error sending this notification';
	}

	protected function registered_devices() {
		$fields    = array(
			'app_id' => $this->app_id,
			'limit'  => 1,
			'offset' => 0,
		);
		$json_data = wp_json_encode( $fields );

		$url        = $this->endpoint_url . 'players?app_id=' . $this->app_id . '&limit=1&offset=0';
		$parameters = array(
			'timeout'   => 10,
			'headers'   => array(
				'Content-Type'  => 'application/json; charset=uft-8',
				'Authorization' => 'Basic ' . $this->secret_key,
			),
			'sslverify' => false,
		);
		$result     = wp_remote_get(
			$url,
			$parameters
		);

		$count = null;
		if ( ! empty( $result ) && ! is_wp_error( $result ) && ( '' !== wp_remote_retrieve_body( $result ) ) ) {
			$result = json_decode( wp_remote_retrieve_body( $result ), true );
			if ( ! empty( $result['total_count'] ) ) {
				$count = $result['total_count'];
			} elseif ( ! empty( $result['errors'] ) && is_array( $result['errors'] )
			&& ( false !== strpos( $result['errors'][0], 'over 100,000' ) || false !== strpos( $result['errors'][0], 'csv_export' ) ) ) {
				$count = 'over 100,000';
			} else {
				if ( $this->log_enabled ) {
					$this->save_log( $url, $parameters, $result );
				}
			}
		}
		return $count;
	}

	public function registered_devices_count() {
		$total = $this->registered_devices();
		if ( is_null( $total ) ) {
			$androidCount = Mobiloud::get_option( 'ml_count_total', 0 );
		} else {
			Mobiloud::set_option( 'ml_count_total', $total );
		}
		return array(
			'total'   => $total,
			'ios'     => null,
			'android' => null,
		);
	}

}
