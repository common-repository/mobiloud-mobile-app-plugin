<?php
/**
 * This is a related content template: content.php.
 *
 * @package MobiLoud.
 * @subpackage MobiLoud/templates/related
 * @version 4.2.8
 */

if ( ! function_exists( 'ml_related_posts_json' ) ) {
	function ml_jetpack_image_size_json( $thumbnail_size ) {
		return ( empty( $_GET['thumb_size'] ) ) ? 192 : absint( $_GET['thumb_size'] );
	}
	add_filter( 'jetpack_relatedposts_filter_thumbnail_size', 'ml_jetpack_image_size_json' );

	function ml_jetpack_relatedposts_filter_options_json( $options ) {
		$options['size'] = 3;
		return $options;
	}
	add_action( 'jetpack_relatedposts_filter_options', 'ml_jetpack_relatedposts_filter_options_json' );

	function ml_related_posts_json( $post_id ) {
		$related      = array();
		$use_fallback = true;
		try {
			if ( class_exists( 'Jetpack_RelatedPosts' ) ) {
				$class = Jetpack_RelatedPosts::init();

				if ( method_exists( $class, 'get_for_post_id' ) ) {
					$related      = $class->get_for_post_id( $post_id, array() );
					$use_fallback = false;
				}
			}
			if ( $use_fallback ) {
				$related = ml_related_posts_fallback( $post_id );
			}
		} catch ( Exception $e ) {
		}
		$related = apply_filters( 'ml_related_posts_data', $related, $post_id );
		return $related;
	}

	function ml_related_posts( $post_id ) {
		$related = ml_related_posts_json( $post_id );
		$list_cat = MLAPI::get_list_cat();

		if ( ! empty( $related ) ) {
			$options = apply_filters(
				'ml_related_posts_options',
				array(
					'header'  => trim( Mobiloud::get_option( 'ml_related_header', '' ) ),
					'image'   => Mobiloud::get_option( 'ml_related_image' ),
					'excerpt' => Mobiloud::get_option( 'ml_related_excerpt' ),
					'date'    => Mobiloud::get_option( 'ml_related_date' ),
				),
				$post_id
			);
			if ( ! empty( $options['header'] ) ) {
				?><h3 class="ml-relatedposts-header"><?php echo esc_html( $options['header'] ); ?></h3>
				<?php
			}
			?>
			<div class="ml-relatedposts-list">
				<?php
				foreach ( $related as $item ) {
					$href       = esc_attr( $item['url'] );
					$related_id = $item['id'];
					$ml_href    = esc_attr( get_site_url() . '/ml-api/v2/post/?post_id=' . $related_id );
					$list_cats  = is_array( $list_cat ) ? implode( ',', $list_cat ) : '';
					?>
					<a class="ml-relatedposts-a" onclick="nativeFunctions.handlePost(<?php echo intval( $related_id ); ?><?php echo ( ! empty( $list_cats ) ? ", '$list_cats'" : ''); ?>);">
						<div class="ml-relatedposts-post">
							<?php
							if ( ! empty( $options['image'] ) && ! empty( $item['img'] ) && ! empty( $item['img']['src'] ) ) {
								?>
								<span class="ml-relatedposts-img ml_followlinks" style="background-image: url(<?php echo esc_attr( $item['img']['src'] ); ?>);">
								</span>
								<?php
							}
							?>
							<h4 class="ml-relatedposts-title"><?php echo esc_html( $item['title'] ); ?></h4>
							<?php
							if ( ! empty( $options['excerpt'] ) && ! empty( $item['excerpt'] ) ) {
								?>
								<p class="ml-relatedposts-excerpt"><?php echo esc_html( $item['excerpt'] ); ?></p>
								<?php
							}
							if ( ! empty( $options['date'] ) && ! empty( $item['date'] ) ) {
								?>
								<p class="ml-relatedposts-date"><?php echo esc_html( $item['date'] ); ?></p>
								<?php
							}
							?>
						</div>
					</a>
					<?php
				}
				?>
			</div>
			<?php
		};
	}
}

if ( ! function_exists( 'ml_related_posts_fallback' ) ) {
	/**
	 * This is a fallback when related posts is ON but no JetPack with related posts present.
	 *
	 * @since 4.2.0
	 *
	 * @param mixed $post_id
	 *
	 * @return array Array with found related posts or empty array.
	 * Each item has properties:
	 * url, id, image, img[src], img[width], img[height], excerpt, title, date (as formatted string).
	 */
	function ml_related_posts_fallback( $post_id ) {
		$results = [];
		$size    = 3; // count of related posts.

		$categories = wp_get_post_categories( $post_id, [ 'fields' => 'ids' ] );

		$uncategorized_category = 1; // Uncategorized.
		$position               = array_search( $uncategorized_category, $categories, true );// do not use default category.
		if ( false !== $position ) {
			unset( $categories[ $position ] );
		}

		$args = apply_filters( 'ml_related_posts_fallback_args', [
			'posts_per_page' => $size + 2,
			'order'          => 'DESC',
			'orderby'        => 'date',
			'post_type'      => get_post_type( $post_id ),
			'post_status'    => 'publish',
		] ); // we will exclude current post later.

		$exclude_cat = Mobiloud::get_option( 'ml_article_list_exclude_categories', '' ); // use option from settings.
		if ( ! empty( $exclude_cat ) ) {
			$args['category__not_in'] = $exclude_cat;
		}

		// a list of the latest posts from the same category as the current post.
		if ( ! empty( $categories ) ) {
			$args['cat'] = $categories;
			ml_add_related_posts_using_args( $results, $args, $post_id, $size );
			unset( $args['cat'] );
		}
		// display the latest published posts.
		if ( count( $results ) < $size ) {
			unset( $args['cat'] );
			ml_add_related_posts_using_args( $results, $args, $post_id, $size );
		}
		return $results;
	}

	/**
	 * Fill results with posts found by query with args. Exclude current post.
	 *
	 * @since 4.2.0
	 *
	 * @param array $results
	 * @param array $args
	 * @param int   $current_post_id
	 * @param int   $size
	 */
	function ml_add_related_posts_using_args( &$results, $args, $current_post_id, $size ) {
		$ids = array_map( // already added posts ID.
			function( $item ) {
				return $item['id'];
			},
			$results
		);
		$data = new WP_Query( $args );

		if ( $data->post_count ) {

			/**
			* Change default image size for related posts.
			*
			* @since 4.2.4
			*
			* @param string|array Image size, ex: post-thumbnail, medium, medium_large, large, [ 150, 150 ] - width x height.
			*/
			$image_size = apply_filters( 'ml_related_posts_image_size', 'medium_large' );
			/** @var WP_Post $post  */
			foreach ( $data->posts as $post ) {
				if ( ( $current_post_id !== $post->ID ) && ( count( $results ) < $size ) && ! in_array( $post->ID, $ids ) ) {

					$final_post = [];
					$image_url  = get_the_post_thumbnail_url( $post );
					$img        = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), $image_size );

					if ( get_option( 'ml_datetype', 'prettydate' ) === 'datetime' ) {
						$date = date_i18n(
							get_option( 'ml_dateformat', 'F j, Y' ),
							strtotime( $post->post_date ),
							get_option( 'gmt_offset' )
						);
					} else {
						// Ex: "2 hours ago".
						$date = sprintf( __( '%s ago' ), human_time_diff( strtotime( $post->post_date ) ) );
					}
					$results[] = [
						'id'      => $post->ID,
						'url'     => get_permalink( $post ),
						'image'   => $image_url,
						'img'     => [
							'src'    => $img ? $img[0] : '',
							'width'  => $img ? $img[1] : '',
							'height' => $img ? $img[2] : '',
						],
						'title'   => get_the_title( $post ),
						'excerpt' => function_exists( 'ml_remove_shortcodes' ) ? ml_remove_shortcodes( $post->post_excerpt ) : $post->post_excerpt, // note: can not use get_the_excerpt() here.
						'date'    => $date,
					];
					$ids[] = $post->ID;
				}
			}
		}
	}
}

if ( Mobiloud::get_option( 'ml_related_posts' ) ) {
	if ( isset( $_GET['related_posts'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		header( 'Content-Type: application/json' );
		$time = absint( Mobiloud::get_option( 'ml_cache_expiration', 30 ) ) * 60;
		header( "Cache-Control: public, max-age=$time, s-max-age=$time", true );

		$result = ml_related_posts_json( $post->ID ); // $post variable defined in post/post.php.
		echo wp_json_encode( $result );
	} else {
		ml_related_posts( $post->ID ); // $post variable defined in post/post.php.
	}
}
