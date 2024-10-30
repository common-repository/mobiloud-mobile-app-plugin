<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MLPostsModel {


	/**
	 * @param $includedPostTypes
	 * @param $category
	 * @param $term_arr
	 * @param $user_category_filter
	 *
	 * @return int
	 */
	public function count_posts_by_filter( $ml_query ) {

		$includedPostTypes    = $ml_query->post_types;
		$category             = $ml_query->category;
		$term_arr             = $ml_query->term_arr;
		$user_category_filter = $ml_query->user_category_filter;

		remove_all_filters( 'pre_get_posts' );
		remove_all_filters( 'posts_request' );
		remove_all_filters( 'the_posts' );

		$published_post_count = 0;
		foreach ( $includedPostTypes as $incPostType ) {
			$published_post_count += wp_count_posts( $incPostType )->publish;
		}

		$ml_category = new MLCategoryController();

		if ( $category ) {
			$published_post_count  = $this->get_post_count( array( $category->term_id ) );
			$published_post_count += $ml_category->ml_get_category_child_post_count( $category->term_id, $term_arr['tax'] );
		}

		if ( $user_category_filter ) {
			$arrayFilter      = array();
			$arrayFilterItems = explode( ',', $user_category_filter );
			foreach ( $arrayFilterItems as $afi ) {
				$tcat = get_category_by_slug( $afi );
				if ( ! $tcat ) {
					$tcat = get_category( $afi );
				}
				if ( $tcat ) {
					array_push( $arrayFilter, $tcat->cat_ID );
				}
			}
			$published_post_count = $this->get_post_count( $arrayFilter );

			return $published_post_count;
		}

		return $published_post_count;
	}


	/**
	 * Get posts from database
	 *
	 * @param $query_array
	 * @param $user_category
	 * @param $real_offset
	 *
	 * @return array
	 */
	public function get_posts( MLQuery $ml_query ) {
		$query_array   = $ml_query->query_array;
		$user_category = $ml_query->user_category;
		$real_offset   = $ml_query->real_offset;

		if ( Mobiloud_Groups::ml_subscriptions_enable() ) {
			// user login using $this->ml_request['username'] and $this->ml_request['password']
			$user = MLAPI::ml_login_wordpress( $ml_query->ml_request['username'], $ml_query->ml_request['password'] );
		}
		if ( ! isset( $ml_query->ml_request['post_id'] ) ) {
			wp_reset_postdata();

			if ( isset( $ml_query->ml_request['taxonomy'] ) ) {
				$query_array['tax_query'] = array(
					array(
						'taxonomy' => $ml_query->ml_request['taxonomy'],
						'field'    => 'term_id',
						'terms'    => array_map( 'intval', explode( ',', $ml_query->ml_request['term_id'] ) ),
					),
				);

				if ( isset( $ml_query->ml_request['order'] ) ) {
					$query_array['order'] = $ml_query->ml_request['order'];
				}

				if ( isset( $ml_query->ml_request['orderby'] ) ) {
					$tn_post_order = $ml_query->ml_request['orderby'];

					if ( $tn_post_order === 'name' ) {
						$tn_post_order = 'title';
					}
					$query_array['orderby'] = $tn_post_order;
				}
			}

			if ( isset( $ml_query->ml_request['author'] ) ) {
				$query_array['author'] = $ml_query->ml_request['author'];
			}

			if ( isset( $ml_query->ml_request['post_ids'] ) && ! empty( $ml_query->ml_request['post_ids'] ) ) {
				$query_array['post__in'] = explode( ',', sanitize_text_field( $ml_query->ml_request['post_ids'] ) );
			}

			if ( isset( $ml_query->ml_request['post_type'] ) ) {
				$query_array['post_type']   = isset( $_REQUEST['post_type'] ) ? sanitize_text_field( $_REQUEST['post_type'] ) : '';
				$query_array['post_parent'] = 0;
			}

			$query_array['no_found_rows'] = true;

			$query_array = apply_filters('ml_query_array', $query_array);

			$posts = get_posts( $query_array );

			if ( isset( $ml_query->ml_request['taxonomy'] ) || isset( $ml_query->ml_request['post_type'] ) ) {
				wp_reset_postdata();
				return $posts;
			}

			wp_reset_postdata();

			if ( ! isset( $ml_query->ml_request['search'] ) && ( empty( $query_array['tax_query'] ) || ! empty( $query_array['tax_query'] ) && empty( $query_array['tax_query']['terms'] ) ) ) {
				$sticky_category_1 = null;
				$sticky_category_2 = null;
				if ( empty( $user_category ) && ! isset( $ml_query->ml_request['post_ids'] ) ) {
					$sticky_category_1 = get_option( 'sticky_category_1' );
					$sticky_category_2 = get_option( 'sticky_category_2' );
				}

				if ( ! is_null( $sticky_category_1 ) || ! is_null( $sticky_category_2 ) ) {

					// must be the second, first because the first will be prepended
					if ( $sticky_category_2 && ( $real_offset === null || $real_offset === 0 ) ) {
						// loading second 3 posts of the sticky category
						$cat = get_category( $sticky_category_2 );
						if ( $cat ) {
							$query_array                   = array();
							$query_array['posts_per_page'] = get_option( 'ml_sticky_category_2_posts', 3 );
							$query_array['category_name']  = null;
							$query_array['category']       = null;
							$query_array['cat']            = $cat->cat_ID;
							$cat_2_posts                   = get_posts( $query_array );
							foreach ( $cat_2_posts as $p ) {
								$p->sticky = true;
								foreach ( $posts as $i => $v ) {
									if ( $v->ID === $p->ID ) {
										array_splice( $posts, $i, 1 );
									}
								}
							}
							$posts = array_merge( $cat_2_posts, $posts );
						}
					}

					if ( $sticky_category_1 && ( $real_offset === null || $real_offset === 0 ) ) {
						// loading first 3 posts of the sticky category
						$cat = get_category( $sticky_category_1 );
						if ( $cat ) {
							$query_array                   = array();
							$query_array['posts_per_page'] = get_option( 'ml_sticky_category_1_posts', 3 );

							$query_array['category_name'] = null;
							$query_array['category']      = null;
							$query_array['cat']           = $cat->cat_ID;
							$cat_1_posts                  = get_posts( $query_array );
							foreach ( $cat_1_posts as $p ) {
								$p->sticky = true;
								foreach ( $posts as $i => $v ) {
									if ( $v->ID === $p->ID ) {
										array_splice( $posts, $i, 1 );
									}
								}
							}
							$posts = array_merge( $cat_1_posts, $posts );
						}
					}
				}
			}
		} else {
			$single_post_id = $ml_query->ml_request['post_id'];
			$posts          = array();
			$posts[0]       = get_post( $single_post_id );
		}

		// subscriptions system enabled?
		if ( Mobiloud_Groups::ml_subscriptions_enable() ) {

			if ( get_class( $user ) === 'WP_User' ) {
				// loggedin
				// subscriptions: filter posts using capabilities
				$posts = Mobiloud_Groups::ml_subscriptions_filter_posts( $posts, $user->ID );
			} else {
				$posts = Mobiloud_Groups::ml_subscriptions_filter_posts( $posts, null );
			}
		}

		return $posts;
	}

	/**
	 * @return array
	 */
	public function post_types() {
		$post_types = explode( ',', get_option( 'ml_article_list_include_post_types', 'post' ) );

		return $post_types;
	}


	/**
	 * @param array $categories
	 *
	 * @return int
	 */
	public function get_post_count( $categories ) {
		global $wpdb;
		$post_count = 0;

		foreach ( $categories as $cat ) :
			$querystr    = $wpdb->prepare(
				"SELECT count
				FROM $wpdb->term_taxonomy, $wpdb->posts, $wpdb->term_relationships
				WHERE $wpdb->posts.ID = $wpdb->term_relationships.object_id
				AND $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
				AND $wpdb->term_taxonomy.term_id = %d
				AND $wpdb->posts.post_status = 'publish'
				",
				absint( $cat )
			);
			$result      = $wpdb->get_var( $querystr );
			$post_count += $result;
		endforeach;

		return $post_count;
	}
}
