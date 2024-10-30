<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MLPostsController
 */
class MLPostsController {
	/**
	 * Generate posts array
	 *
	 * @param WP_Post[] $posts
	 * @param $offset
	 * @param $taxonomy
	 * @param int $post_count
	 * @param bool $cache_on Unused parameter.
	 * @param mixed $image_format
	 *
	 * @return mixed
	 */
	public function get_final_posts( $posts, $offset, $taxonomy, $post_count, $cache_on, $image_format = 1 ) {
		$final_posts = array(
			'posts'      => array(),
			'post-count' => $post_count,
		);

		$media = new MLMediaController();
		$media->set_image_format( $image_format );
		foreach ( $posts as $post ) {
			$format       = get_post_format( $post );
			$post_id      = $post->ID;
			if ( $offset > 0 && is_sticky( $post_id ) ) {
				continue;
			}
			$final_post = $this->final_post( $taxonomy, $post_id, $post, $media, $format );
			$final_posts['posts'][] = $final_post;
		}

		return $final_posts;
	}


	/**
	 * @param $taxonomy
	 * @param int $post_id
	 * @param WP_Post $post
	 * @param MLMediaController $media
	 * @param $format
	 *
	 * @return array|mixed
	 */
	private function final_post( $taxonomy, $post_id, $post, $media, $format ) {
		$final_post = $this->new_post( $post_id, $post );
		$final_post = $this->add_comments( $post_id, $final_post );
		$final_post = $this->add_permalink( $post_id, $final_post, $post );
		$final_post = $this->add_categories( $taxonomy, $post_id, $final_post );
		$final_post = $this->add_date( $post, $final_post );
		$final_post = $this->add_content( $final_post, $post );
		$final_post = $this->add_custom_field( $post, $final_post );
		$final_post = $this->add_excerpt( $post, $final_post );
		$final_post = $this->set_sticky( $post, $final_post );
		$final_post = $media->add_media( $post, $final_post );
		$final_post = $this->add_swipe( $post, $final_post );
		$final_post = $this->update_all_images( $post, $final_post );
		if ( $format === 'status' ) {
			$final_post = $this->set_status_format( $post, $final_post );
		}

		return $final_post;
	}


	/**
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function get_post_excerpt( $post_id ) {
		global $post;
		$save_post = $post;
		$post      = get_post( $post_id ); // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		add_filter( 'excerpt_length', array( 'MLPostsController', 'set_excerpt_length' ) );
		add_filter( 'excerpt_more', array( 'MLPostsController', 'set_excerpt_more' ) );
		$output = get_the_excerpt();
		remove_filter( 'excerpt_more', array( 'MLPostsController', 'set_excerpt_more' ) );
		remove_filter( 'excerpt_length', array( 'MLPostsController', 'set_excerpt_length' ) );
		$post = $save_post; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited

		return $output;
	}

	public static function set_excerpt_length( $length ) {
		return Mobiloud::get_option( 'ml_excerpt_length', 100 );
	}

	public static function set_excerpt_more( $more ) {
		return apply_filters( 'ml_excerpt_more', ' …' );
	}

	/**
	 * EscapeJson
	 *
	 * @param $data
	 */
	public function escape_json( $value ) {
		$escapers     = array( '\\', '/', '"', "\n", "\r", "\t", "\x08", "\x0c" );
		$replacements = array( '\\\\', '\\/', '\\"', "\\n", "\\r", "\\t", "\\f", "\\b" );
		$result       = str_replace( $escapers, $replacements, $value );

		return $result;
	}


	/**
	 * @param $final_post
	 *
	 * @return mixed
	 */
	public function add_content( $final_post, $post ) {
		global $wp;
		ob_start();

		$template = get_option( 'ml-templates', 'legacy' );
		$endpoint = $wp->query_vars['__ml-api'];

		if ( 'default' === $template && 'posts' === $endpoint ) {
			header( 'Content-Type: application/json' );
			Mobiloud::require_default_template_wrapper();
		} else {
			// we will use this variable in templates.
			$ml_post_type = $post->post_type;
			$template     = Mobiloud::use_template( 'views', [ $ml_post_type, 'post' ], false );
			include $template;
		}

		$html_content = ob_get_clean();

		// replace relative URLs with absolute.
		$html_content = preg_replace(
			"#(<\s*a\s+[^>]*href\s*=\s*[\"'])(?!http|/)([^\"'>]+)([\"'>]+)#",
			'$1' . ( ! empty( $final_post['permalink'] ) ? $final_post['permalink'] : '' ) . '/$2$3',
			$html_content
		);

		$final_post['content'] = $html_content;

		return $final_post;
	}

	/**
	 * @param $taxonomy
	 * @param $post_id
	 * @param $final_post
	 *
	 * @return mixed
	 */
	public function add_categories( $taxonomy, $post_id, $final_post ) {
		$categories = get_the_category( $post_id );
		foreach ( $categories as $category ) {
			$final_post['categories'][] = array(
				'cat_id' => "$category->cat_ID",
				'name'   => $category->cat_name,
				'slug'   => $category->category_nicename,
			);
		}

		if ( $taxonomy !== 'category' && ! empty( $taxonomy ) ) {
			$terms = wp_get_post_terms( $post_id, $taxonomy );

			foreach ( $terms as $term ) {
				$final_post['categories'][] = array(
					'cat_id' => "$term->term_id",
					'name'   => $term->name,
					'slug'   => $term->slug,
				);
			}

			return $final_post;
		}

		return $final_post;
	}

	/**
	 * @param $post
	 * @param $final_post
	 *
	 * @return mixed
	 */
	public function add_date( $post, $final_post ) {
		$final_post['date'] = $post->post_date;

		if ( get_option( 'ml_datetype', 'prettydate' ) === 'datetime' ) {
			$final_post['date_display'] = date_i18n(
				get_option( 'ml_dateformat', 'F j, Y' ),
				strtotime( $post->post_date ),
				get_option( 'gmt_offset' )
			);
		} else {
			// Ex: "2 hours ago".
			$final_post['date_ago'] = sprintf( __( '%s ago' ), human_time_diff( strtotime( $post->post_date ) - intval( get_option( 'gmt_offset' ) ) * HOUR_IN_SECONDS ) );
		}

		return $final_post;
	}



	/**
	 * @param $post_id
	 * @param $final_post
	 * @param $post
	 *
	 * @return mixed
	 */
	public function add_permalink( $post_id, $final_post, $post ) {
		$final_post['permalink'] = get_permalink( $post_id );
		if ( empty( $final_post['permalink'] ) ) {
			$final_post['permalink'] = '0';
		}
		if ( strlen( trim( get_option( 'ml_custom_field_url', '' ) ) ) > 0 ) {
			$custom_url_value = get_post_meta( $post->ID, get_option( 'ml_custom_field_url' ), true );
			if ( strlen( trim( $custom_url_value ) ) > 0 ) {
				$final_post['permalink'] = $custom_url_value;

				return $final_post;
			}

			return $final_post;
		}

		return $final_post;
	}

	/**
	 * @param $post_id
	 * @param $post
	 *
	 * @return array
	 */
	public function new_post( $post_id, $post ) {
		$final_post = array();

		$final_post['post_id']   = "$post_id";
		$final_post['post_type'] = $post->post_type;

		$final_post['author']     = array();
		$final_post['categories'] = array();

		$final_post['author']['name']      = html_entity_decode( get_the_author_meta( 'display_name', $post->post_author ) );
		$final_post['author']['author_id'] = $post->post_author;

		$final_post['title'] = wp_strip_all_tags( $post->post_title );
		$final_post['title'] = html_entity_decode( $final_post['title'], ENT_QUOTES );

		$final_post['videos'] = array();
		$final_post['images'] = array();

		$final_post['excerpt'] = '';

		return $final_post;
	}

	/**
	 * @param $post_id
	 * @param $final_post
	 *
	 * @return mixed
	 */
	public function add_comments( $post_id, $final_post ) {
		$comments_count = wp_count_comments( $post_id );

		$final_post['comments-count'] = 0;
		if ( $comments_count ) {
			$final_post['comments-count'] = intval( $comments_count->approved );
		}

		$final_post['comments-count-text'] = sprintf( _n( '%s Comment', '%s Comments', $final_post['comments-count'] ), number_format_i18n( $final_post['comments-count'] ) );

		return $final_post;
	}

	/**
	 * @param $post
	 * @param $final_post
	 *
	 * @return mixed
	 */
	public function add_custom_field( $post, $final_post ) {
		if ( strlen( get_option( 'ml_custom_field_name', '' ) ) > 0 ) {

			if ( get_option( 'ml_custom_field_name', '' ) === 'excerpt' ) {
				$custom_field_val = html_entity_decode( urldecode( wp_strip_all_tags( $this->get_post_excerpt( $post->ID ) ) ) );

				$final_post['custom1'] = $custom_field_val;

				return $final_post;
			} else {
				$custom_field_val      = get_post_meta( $post->ID, get_option( 'ml_custom_field_name', '' ), true );
				$final_post['custom1'] = $custom_field_val;

				return $final_post;
			}
		}

		return $final_post;
	}

	/**
	 * @param $post
	 * @param $final_post
	 *
	 * @return mixed
	 */
	public function add_excerpt( $post, $final_post ) {
		$final_post['excerpt'] = html_entity_decode( urldecode( wp_strip_all_tags( $this->get_post_excerpt( $post->ID ) ) ) );
		$final_post['excerpt'] = str_replace( 'Read More', '', $final_post['excerpt'] );
		// $final_post['excerpt'] = htmlentities( $final_post['excerpt'], ENT_QUOTES, 'utf-8', FALSE);
		return $final_post;
	}

	/**
	 * @param $post
	 * @param $final_post
	 *
	 * @return mixed
	 */
	public function set_status_format( $post, $final_post ) {
		$final_post['title']   = $post->post_content;
		$final_post['content'] = '';
		$final_post['custom1'] = '';
		$final_post['excerpt'] = '';

		return $final_post;
	}

	/**
	 * @param $post
	 * @param $final_post
	 *
	 * @return mixed
	 */
	public function set_sticky( $post, $final_post ) {
		$final_post['sticky'] = is_sticky( $post->ID ) || $post->sticky;

		return $final_post;
	}

	/**
	 * Update img src=... and img srcset=... images urls in html code with CDN version.
	 *
	 * @param string $html
	 * @param mixed  $is_raw_srcset Is it content of scrset attribute?
	 *
	 * @return string
	 */
	private function update_html( $html, $is_raw_srcset = false ) {
		if ( ! $is_raw_srcset ) {
			$html = preg_replace_callback(
				'!(<img[^>]+src=[\'"])([^\'"]*?)([\'"])!i', function( $m ) {
					return $m[1] . apply_filters( 'mobiloud_image_url', $m[2] ) . $m[3];
				}, $html
			);
			$html = preg_replace_callback(
				'!(<img[^>]+srcset=[\'"])([^\'"]*?)([\'"])!mi', function( $m ) {
					$urls = explode( ',', $m[2] );
					foreach ( $urls as &$value ) {
						$parts = preg_split( '!\s+!m', trim( $value ), 2 );
						if ( 2 === count( $parts ) ) {
							$parts[0] = apply_filters( 'mobiloud_image_url', $parts[0] );
							$value    = implode( ' ', $parts );
						}
					}
					return $m[1] . implode( ', ', $urls ) . $m[3];
				}, $html
			);
		} else {
			$urls = explode( ',', $html );
			foreach ( $urls as &$value ) {
				$parts = preg_split( '!\s+!m', trim( $value ), 2 );
				if ( 2 === count( $parts ) ) {
					$parts[0] = apply_filters( 'mobiloud_image_url', $parts[0] );
					$value    = implode( ' ', $parts );
				}
			}
			$html = implode( ', ', $urls );

		}
		return $html;
	}

	/**
	 * Update all images.
	 *
	 * @param $post_id
	 * @param $final_post
	 *
	 * @return mixed
	 */
	private function update_all_images( $post, $final_post ) {
		$final_post['content'] = $this->update_html( $final_post['content'] );
		if ( isset( $final_post['featured_image_resp'] ) && ! empty( $final_post['featured_image_resp']['html'] ) ) {
			$final_post['featured_image_resp']['html'] = $this->update_html( $final_post['featured_image_resp']['html'] );
		}
		if ( isset( $final_post['featured_image_resp'] ) && ! empty( $final_post['featured_image_resp']['srcset'] ) ) {
			$final_post['featured_image_resp']['srcset'] = $this->update_html( $final_post['featured_image_resp']['srcset'], true );
		}
		return $final_post;
	}

	/**
	 * Callback for substitute custom categories list
	 *
	 * @since 4.2.8
	 *
	 * @param array $terms
	 * @return int[] Categories list.
	 */
	public function wp_get_object_terms_filter( $terms ) {
		$list_cat = MLApi::get_list_cat();
		return is_array( $list_cat ) ? array_map( 'intval', $list_cat ) : [];
	}

	/**
	 * Add infinite swipe info.
	 * Load list cat info from MLApi::get_list_cat().
	 *
	 * @since 4.2.8
	 *
	 * @param WP_Post $post
	 * @param array $final_post
	 * @return array
	 */
	private function add_swipe( $post, $final_post ) {
		$list_cat = MLApi::get_list_cat();

		$post_save = $GLOBALS['post'];
		$GLOBALS['post'] = $post;
		$replace_list_cat = ! empty( $list_cat ); // do not replace and ignore categories if list is empty, i.e. for homepage.
		try {
			// replace categories list.
			if ( $replace_list_cat ) {
				add_filter( 'wp_get_object_terms', [ $this, 'wp_get_object_terms_filter' ], 1000 );
			}
			$excluded_terms = get_terms( [ 'taxonomy' => 'mobile_app_exclude', 'fields' => 'ids' ]); // exclude posts, turned off by "Exclude post from mobile app" feature.
			if ( ! is_array( $excluded_terms ) ) {
				$excluded_terms = [];
			}

			$prev = get_adjacent_post( $replace_list_cat, $excluded_terms, true );
			$next = get_adjacent_post( $replace_list_cat, $excluded_terms, false );
		} finally {
			if ( $replace_list_cat ) {
				remove_filter( 'wp_get_object_terms', [ $this, 'wp_get_object_terms_filter' ], 1000 );
			}
			$GLOBALS['post'] = $post_save;
		}

		$final_post['swipe'] = [
			'prevPostId' => $prev instanceof WP_Post ? $prev->ID : null,
			'nextPostId' => $next instanceof WP_Post ? $next->ID : null,
			'listCat' => is_array( $list_cat ) ? implode( ',', $list_cat ) : null,
		];
		return $final_post;
	}
}
