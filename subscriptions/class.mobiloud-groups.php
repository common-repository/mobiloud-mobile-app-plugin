<?php

/**
 * Integration with Groups plugin.
 */
class Mobiloud_Groups {

	/**
	 * Has active Groups plugin active
	 *
	 * @return bool
	 */
	private static function ml_has_groups_library() {
		return ( class_exists( 'Groups_Post_Access' ) && class_exists( 'Groups_User' ) );
	}

	/**
	 * Is subscriptions feature active?
	 * Activated at the settings and has Groups plugin active.
	 *
	 * @return bool
	 */
	public static function ml_subscriptions_enable() {
		return ( self::ml_has_groups_library() && get_option( 'ml_subscriptions_enable' ) !== 'false' );
	}

	/**
	 * Filter posts by capabilities for the user_id using Groups plugin
	 *
	 * @param array    $posts
	 * @param int|null $user_id null for current user.
	 * @return array Of filtered posts.
	 */
	public static function ml_subscriptions_filter_posts( $posts, $user_id ) {
		$filtered_posts = array();
		foreach ( $posts as $post ) {
			if ( Groups_Post_Access::user_can_read_post( $post->ID, $user_id ) ) {
				$filtered_posts[] = $post;
			}
		}
		return $filtered_posts;
	}

	protected static function ml_subscriptions_post_capabilities( $post ) {
		// todo: unused.
		$capabilities = array();
		foreach ( Groups_Post_Access::get_read_post_capabilities( $post->ID ) as $capability ) {
			if ( $capability !== null ) {
				$capabilities[] = $capability;
			}
		}
		return $capabilities;
	}

}
