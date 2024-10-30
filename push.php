<?php
require_once dirname( __FILE__ ) . '/notification_categories.php';

// function that sets the last notified post.
function ml_set_post_id_as_notified( $postID ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'mobiloud_notifications';
	$wpdb->insert(
		$table_name,
		array(
			'time'    => current_time( 'timestamp' ),
			'post_id' => absint( $postID ),
		)
	);
}

function ml_is_notified( $post_id ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'mobiloud_notifications';
	$num        = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE post_id = %d", absint( $post_id ) ) );

	return $num > 0;
}

/**
 * Checks whether the rate limit for push notifications is reached.
 *
 * @see https://github.com/50pixels/mobiloud-mobile-app-plugin/issues/243
 */
function ml_has_rate_limit_exceeded() {
	$is_rate_limit_enabled = Mobiloud::get_option( 'ml_pb_rate_limit', false );

	if ( empty( $is_rate_limit_enabled ) ) {
		return false;
	}

	$record = get_option( 'ml_rate_limit_record', array() );

	if ( empty( $record ) ) {
		update_option( 'ml_rate_limit_record', array(
			'last_pushed_at' => gmdate( 'U' ),
			'push_count'     => 1,
		) );
		return false;
	}

	$current_time     = gmdate( 'U' );
	$last_pushed_time = $record['last_pushed_at'];
	$difference       = ( $current_time - $last_pushed_time )/60;

	if ( $difference > 15 ) {
		update_option( 'ml_rate_limit_record', array(
			'last_pushed_at' => gmdate( 'U' ),
			'push_count'     => 0,
		) );

		return false;
	}

	if ( $record['push_count'] < 10 ) {
		update_option( 'ml_rate_limit_record', array(
			'last_pushed_at' => $record['last_pushed_at'],
			'push_count'     => (int)$record['push_count'] + 1,
		) );
		return false;
	} else {
		return true;
	}
}

function ml_pb_post_published_notification_future( $post ) {
	ml_pb_post_published_notification( 'publish', 'future', $post, true );
}

// Do auto push notification.
function ml_pb_post_published_notification( $new_status, $old_status, $post ) {

	if ( 'publish' === $new_status && 'publish' === $old_status ) {
		return;
	}

	$published_date    = strtotime( $post->post_date );
	$current_timestamp = current_time( 'timestamp' );

	if ( $published_date < $current_timestamp ) {
		return;
	}

	if ( ml_is_notified( $post->ID ) || ! ml_check_post_notification_required( $post->ID ) ) {
		return;
	}

	$push_types = Mobiloud::get_option( 'ml_push_post_types', 'post' );
	if ( strlen( $push_types ) > 0 ) {
		$push_types = explode( ',', $push_types );

		if ( $new_status == 'publish' && $old_status != 'publish' && in_array( $post->post_type, $push_types ) ) {  // only send push if it's a new publish
			
			if ( ml_has_rate_limit_exceeded() ) {
				return;
			}
			
			$payload = array(
				'post_id' => strval( $post->ID ),
			);

			if ( Mobiloud::get_option( 'ml_push_include_image' ) ) {
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium_large' );
				if ( is_array( $image ) ) {
					$payload['featured_image'] = $image[0];
				}
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
				if ( is_array( $image ) ) {
					$payload['thumbnail'] = $image[0];
				}
			}
			$tags       = ml_get_post_tag_ids( $post->ID );
			$tags[]     = 'all';
			$tagNames   = ml_get_post_tags( $post->ID );
			$tagNames[] = 'all';
			$data       = array(
				'platform' => array( 0, 1 ),
				'msg'      => strip_tags( trim( $post->post_title ) ),
				'sound'    => 'default',
				'badge'    => '+1',
				'payload'  => $payload,
			);
			$value      = get_post_meta( $post->ID, 'ml_notification_notags', true );
			if ( ! Mobiloud::get_option( 'ml_pb_no_tags', false ) && empty( $value ) ) {
				$data['notags'] = true;
				$data['tags']   = $tags;
			} else {
				$tagNames = array();
				update_post_meta( $post->ID, 'ml_notification_notags', 1 );
			}
			require_once dirname( __FILE__ ) . '/push_notifications/class.mobiloud_notifications.php';
			$push_api = Mobiloud_Notifications::get();

			$result = $push_api->send_notifications( $data, $tagNames );
			if ( true === $result ) {
				if ( ! ml_is_notified( $post->ID ) ) {
					ml_set_post_id_as_notified( $post->ID );
				}
			}
		}
	}
}

function ml_notifications( $limit = null ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'mobiloud_notifications';
	$sql        = "SELECT * FROM $table_name ORDER BY time DESC";
	if ( $limit != null ) {
		$sql .= ' LIMIT ' . absint( $limit );
	}

	return $wpdb->get_results( $sql );
}

function ml_get_notification_by( $filter = array() ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'mobiloud_notifications';
	$sql        = $wpdb->prepare(
		'SELECT * FROM ' . $table_name . '
		WHERE
		msg = %s', $filter['msg']
	);
	if ( $filter['post_id'] != null ) {
		$sql .= $wpdb->prepare( ' AND post_id = %d', absint( $filter['post_id'] ) );
	}
	if ( $filter['url'] != null ) {
		$sql .= $wpdb->prepare( ' AND url = %s', $filter['url'] );
	}
	$sql .= $wpdb->prepare( ' AND android = %s AND ios = %s', $filter['android'], $filter['ios'] ); // 'Y' or 'N'.

	return $wpdb->get_results( $sql );
}

function ml_get_post_tags( $postId ) {
	$post_categories = wp_get_post_categories( $postId );
	$tags            = array();

	foreach ( $post_categories as $c ) {
		$cat     = get_category( $c );
		$tags[]  = $cat->slug;
		$parents = get_ancestors( $cat->term_id, 'category' );
		if ( count( $parents ) ) { // include all parent categories slugs.
			foreach ( $parents as $parent ) {
				$cat    = get_category( $parent );
				$tags[] = $cat->slug;
			}
		}
	}

	return array_unique( $tags );
}

function ml_get_post_tag_ids( $postId ) {
	$post_categories = wp_get_post_categories( $postId );
	$tags            = array();
	foreach ( $post_categories as $c ) {
		$tags[]  = $c;
		$parents = get_ancestors( $c, 'category' );
		$tags    = array_merge( $tags, $parents ); // include all parent categories.
	}

	return array_values( array_unique( $tags ) );
}

function ml_check_post_notification_required( $postId ) {
	$notification_categories = ml_get_push_notification_categories();
	$notification_taxonomies = ml_get_push_notification_taxonomies();

	if ( empty( $notification_categories ) && empty( $notification_taxonomies ) ) {
		return true;
	}

	if ( is_array( $notification_categories ) && count( $notification_categories ) > 0 ) {
		$categories = $post_categories = wp_get_post_categories( $postId );
		foreach ( $categories as $cat ) {
			// Send notifications for sub-categories when any parent category is enabled.
			$post_categories = array_merge( $post_categories, get_ancestors( $cat, 'category' ) );
		}
		$post_categories = array_unique( $post_categories );

		$found = false;
		if ( is_array( $post_categories ) && count( $post_categories ) > 0 ) {
			foreach ( $post_categories as $post_category_id ) {
				foreach ( $notification_categories as $notification_category ) {
					if ( $notification_category->cat_ID == $post_category_id ) {
						return true;
					}
				}
			}
		}
	}

	if ( is_array( $notification_taxonomies ) && count( $notification_taxonomies ) > 0 ) {
		$taxonomies = get_taxonomies( array( '_builtin' => false ), 'objects' );
		$tax_list   = array();
		foreach ( $taxonomies as $tax ) {
			if ( $tax->query_var ) {
				$tax_list[] = $tax->query_var;
			}
		}

		$post_tax = wp_get_object_terms( $postId, $tax_list );
		if ( ! is_wp_error( $post_tax ) && is_array( $post_tax ) && count( $post_tax ) > 0 ) {
			foreach ( $post_tax as $tax ) {
				if ( in_array( $tax->term_id, $notification_taxonomies ) ) {
					return true;
				}
			}
		}
	}

	return false;
}
