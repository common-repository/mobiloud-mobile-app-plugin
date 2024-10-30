<?php
/**
 * Mobiloud Paywall class.
 * Add metabox and show Paywall screen if access if restricted for not logged in users.
 * Also a class for third party integrations using filters.
 *
 * @since 4.2.0
 */
class Mobiloud_Paywall extends Mobiloud_Paywall_Base {

	public function __construct() {
		$this->ignore_user_filters = false;
	}

	protected function ml_paywall_categories_restricted( $cats, $single = false ) {
		$restricted = false;
		$terms      = explode( ',', $cats );
		$rcount     = 0;
		foreach ( $terms as $term ) {
			$opt = get_option( 'taxonomy_' . $term );
			if ( $opt['ml_tax_paywall'] === 'true' ) {
				if ( $single ) {
					$restricted = true;
				}
				// count total restricted categories.
				$rcount++;
			}
		}

		if ( $rcount === count( $terms ) ) {
			$restricted = true;
		}

		// todo: add filter.
		return $restricted;
	}

	/**
	 * Is content restricted, using metabox value for post, category or taxonomy.
	 *
	 * @since 4.2.0
	 *
	 * @return bool true - blocked, false - allowed.
	 */
	protected function ml_is_content_restricted() {
		$restricted = false;

		$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		if ( strpos( $url, '/list?' ) && isset( $_GET['taxonomy'] ) && isset( $_GET['term_id'] ) ) {
			$restricted = $this->ml_paywall_categories_restricted( sanitize_text_field( wp_unslash( $_GET['term_id'] ) ) );
		}

		if ( strpos( $url, '/list?' ) && isset( $_GET['categories'] ) ) {
			$restricted = $this->ml_paywall_categories_restricted( sanitize_text_field( wp_unslash( $_GET['categories'] ) ) );
		}

		if ( ( strpos( $url, '/post?' ) || strpos( $url, '/posts?' ) ) && isset( $_GET['post_id'] ) ) {

			if ( get_post_meta( sanitize_text_field( wp_unslash( $_GET['post_id'] ) ), 'ml_paywall_protected', true ) === 'true' ) {
				$restricted = true;
			} else {
				$taxes = get_taxonomies(
					array(
						'public' => true,
					)
				);

				$cats  = wp_get_post_terms( sanitize_text_field( wp_unslash( $_GET['post_id'] ) ), $taxes );
				$terms = array();
				foreach ( $cats as $cat ) {
					$terms[] = $cat->term_id;
				}
				$restricted = $this->ml_paywall_categories_restricted( implode( ',', $terms ), true );
			}
		}

		return $restricted;
	}

	// Admin dashboard stuff.

	/**
	 * Action 'add_meta_boxes' handler
	 */
	public function ml_paywall_meta_box() {

		$ml_post_types = get_post_types();

		foreach ( $ml_post_types as $ml_post_type ) {
			if ( $ml_post_type === 'attachment' || $ml_post_type === 'nav_menu_item' ) {
				continue;
			}
			add_meta_box(
				'mobiloud_app_paywall_metabox', __( 'MobiLoud App Paywall' ), array( $this, 'ml_paywall_metabox_process' ), $ml_post_type, 'side', 'high'
			);
		}
	}

	/**
	 * Handler for metabox
	 *
	 * @param WP_Post $post
	 */
	public function ml_paywall_metabox_process( $post ) {
		// Add a nonce field so we can check for it later.
		wp_nonce_field( 'ml_paywall_nonce', 'ml_paywall_nonce' );
		$value = 'true' === get_post_meta( $post->ID, 'ml_paywall_protected', true );
		?>
		<label>
			<input type="checkbox" name="ml_paywall_protected" <?php checked( $value ); ?> />
			Protect this content with Paywall
		</label>
		<?php
	}

	/**
	 * Handler for 'save_post' action
	 *
	 * @param int $post_id
	 */
	public function ml_save_paywall_meta_box_data( $post_id ) {

		// Check if our nonce is set.
		if ( ! isset( $_POST['ml_paywall_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ml_paywall_nonce'] ) ), 'ml_paywall_nonce' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' === sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) ) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		$option_value = 'false';
		// Make sure that it is set.
		if ( ! isset( $_POST['ml_paywall_protected'] ) ) {
			$option_value = 'false';
		} else {
			$option_value = 'true';
		}

		// Update the meta field in the database.
		update_post_meta( $post_id, 'ml_paywall_protected', $option_value );
	}

	/**
	 * Handler for (tax)_add_form_fields and (tax)_edit_form_fields actions
	 *
	 * @param mixed $term
	 */
	public function ml_taxonomy_paywall_protected( $term = null ) {

		// check if on Edit screen.
		$term_protected = array(
			'ml_tax_paywall' => 'false',
		);
		if ( ! empty( $term ) && is_object( $term ) ) {
			$t_id           = $term->term_id;
			$term_protected = get_option( "taxonomy_$t_id" );
		}
		?>

		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_meta[ml_tax_paywall]">MobiLoud App Paywall</label></th>
			<td>
				<label>
					<input type="checkbox" name="term_meta[ml_tax_paywall]" id="term_meta[ml_tax_paywall]" <?php echo ( $term_protected['ml_tax_paywall'] === 'true' ) ? 'checked' : ''; ?> />
					Protect this content with Paywall
				</label>
				<br/>
			</td>
		</tr>
		<?php
	}

	/**
	 * Handler for edited_(tax) and create_(tax) actions
	 *
	 * @param int $term_id
	 */
	public function ml_taxonomy_paywall_save( $term_id ) {
		$t_id      = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );

		if ( isset( $_POST['term_meta']['ml_tax_paywall'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification
			$term_meta['ml_tax_paywall'] = 'true';
		} else {
			$term_meta['ml_tax_paywall'] = 'false';
		}

		// Save the option array.
		update_option( "taxonomy_$t_id", $term_meta );
	}

	/**
	 * Filter: check access to current or given post if Membership plugin is active.
	 *
	 * @since 4.2.0
	 *
	 * @global WP_Post $post
	 * @param bool         $result
	 * @param string       $url
	 * @param WP_Post|null $current_post Post, if is set checked only for this post.
	 * @return bool true - content blocked, false - not blocked.
	 */
	private function check_merp( $result, $url, $current_post = null ) {
		static $blocked = null;
		// if post ID is set.
		if ( ! is_a( $current_post, 'WP_Post' ) ) {

			$post_url = get_permalink( $current_post->ID );
			return MeprRule::is_locked( $current_post ) || MeprRule::is_uri_locked( $post_url );
		}
		if ( is_null( $blocked ) ) {

			if ( ( strpos( $url, '/post?' ) || strpos( $url, '/posts?' ) ) && isset( $_GET['post_id'] ) ) {
				/**
				* Curent post.
				* Defined at mobiloud-mobile-app-plugin/views/post.php
				*
				* @var WP_Post
				*/
				global $post;
				$current_post = null;
				if ( isset( $post ) && is_a( $post, 'WP_Post' ) ) {
					$current_post = $post;
				} elseif ( isset( $_GET['post_id'] ) ) {
					$current_post = get_post( absint( $_GET['post_id'] ) );
				}
				if ( ! is_null( $current_post ) && is_a( $current_post, 'WP_Post' ) ) {
					$post_url = get_permalink( $current_post );
					$result   = MeprRule::is_locked( $current_post ) || MeprRule::is_uri_locked( $post_url ) || MeprRule::is_uri_locked( $post_url );
				}
				// check against current endpoint url.
				if ( MeprRule::is_uri_locked( $url ) ) {
					$result = true;
				}
			}
			$blocked = $result;
		}

		return $blocked;
	}

	/**
	 * Show Paywall metaboxes when feature is on and Memberpress plugin is not active.
	 *
	 * @since 4.2.0
	 *
	 * @return void
	 */
	public function maybe_add_metaboxes() {
		if ( ml_is_paywall_enabled() ) {
			add_action( 'add_meta_boxes', array( $this, 'ml_paywall_meta_box' ) );
			add_action( 'save_post', array( $this, 'ml_save_paywall_meta_box_data' ) );

			$taxes = get_taxonomies(
				array(
					'public' => true,
				)
			);

			foreach ( $taxes as $tax ) {
				// Add the paywall field to the Add New {tax} page.
				add_action( $tax . '_add_form_fields', array( $this, 'ml_taxonomy_paywall_protected' ), 10, 2 );

				// Add the paywall field to the Edit {tax} page.
				add_action( $tax . '_edit_form_fields', array( $this, 'ml_taxonomy_paywall_protected' ), 10, 2 );

				// Save tax paywall.
				add_action( 'edited_' . $tax, array( $this, 'ml_taxonomy_paywall_save' ), 10, 2 );
				add_action( 'create_' . $tax, array( $this, 'ml_taxonomy_paywall_save' ), 10, 2 );
			}
		}
	}
}
