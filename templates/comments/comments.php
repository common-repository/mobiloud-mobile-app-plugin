<?php
/**
 * This is a comments template: comments.php.
 *
 * @package MobiLoud.
 * @subpackage MobiLoud/templates/comments
 * @version 4.2.0
 */

/**
 * Render comments tree
 *
 * @param WP_Comment $comment
 */
function ml_render_iphone_comment( $comment, $reply = false, $reply_on_screen = false, $first_reply_shown = false ) {
	// Do not show anonymous and very short comments.
	if ( empty( $comment->comment_author ) && empty( $comment->comment_author_email ) && ( 3 >= strlen( $comment->comment_content ) ) ) {
		return;
	}
	$show_avatars = get_option( 'show_avatars' );
	?>
	<ons-list-item class="comment ml_comment comment_id_<?php echo esc_attr( $comment->comment_ID ); ?>">
		<?php
		if ( $show_avatars ) {
			$uid_or_email = $comment->user_id != 0 ? $comment->user_id : $comment->comment_author_email;
			$link         = Mobiloud::ml_get_avatar_url( $uid_or_email, 50 );

			echo '<img src="' . esc_attr( $link ) . '" class="avatar avatar-50 photo">';
		}
		?>
		<div class="comment_body">
			<?php echo '<strong>' . esc_html( $comment->comment_author ) . '</strong> <p>' . nl2br( convert_smilies( $comment->comment_content ) ) . '</p>'; ?>
			<div
				class="comment_meta"><?php echo esc_html( human_time_diff( strtotime( $comment->comment_date_gmt ), time() ) ); ?></div>
		</div>
		<?php
		$children = $comment->get_children(
			array(
				'status'       => 'approve',
				'order'        => 'ASC',
				'hierarchical' => 'threaded',
			)
		);

		$count_text = '';
		if ( $children ) {
			$count_text = sprintf( _n( '%s Comment', '%s Comments', count( $children ) ), number_format_i18n( count( $children ) ) );
		}
		$linkText = $count_text . ' ' . __( 'Reply' );

		$replyUrl = trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/comments?post_id=' . $comment->comment_post_ID . '&comment=' . $comment->comment_ID;
		$onclick  = "nativeFunctions.handleLink( '" . $replyUrl . "', '" . esc_js( __( 'Reply to this comment' ) ) . "', 'native' )";
		if ( ! $reply ) {
			?>
		<a class="ml-reply-link" onclick="<?php echo esc_attr( $onclick ); ?>"><?php echo esc_html( $linkText ); ?></a>
			<?php

			if ( ! $first_reply_shown ) {
				$child = array_values( $children );
				if ( count( $child ) ) {
					ml_render_iphone_comment( $child[0], false, false, true );
				}
				if ( $reply_on_screen ) {
					echo '<a class="ml-reply-link" onclick=\'' . esc_js( 'replyNow(' . $child[0]->comment_ID . ')' ) . '\'>' . esc_html__( 'Reply' ) . '</a>';
				}
			}
		} else {
			echo '<a class="ml-reply-link" onclick=\'' . esc_js( 'replyNow(' . $comment->comment_ID . ')' ) . '\'>' . esc_html__( 'Reply' ) . '</a>';
			foreach ( $children as $child ) {
				ml_render_iphone_comment( $child, true, true );
			}
		}

		?>
	</ons-list-item>
	<?php
}

function ml_render_comment( $comment, $platform = 'iphone', $reply = false ) {
	if ( $platform == 'iphone' ) {
		ml_render_iphone_comment( $comment, $reply );
	}
}

function ml_render_comments( $post_id, $platform = 'iphone', $offset = 0 ) {
	$parameters = array(
		'post_id'      => $post_id,
		// 'number' => 10,
		'offset'       => $offset,
		'status'       => 'approve',
		'order'        => 'ASC',
		'hierarchical' => 'threaded',
	);

	$comments = get_comments( $parameters );

	if ( count( $comments ) == 0 ) {
		?>
		<h4 style='text-align: center;'><?php esc_html_e( 'No Comments' ); ?></h4>
											<?php
	} else {
		foreach ( $comments as $comment ) {
			ml_render_comment( $comment, $platform );
		}
	}
}

function ml_render_comment_replies( $comment_id, $platform = 'iphone', $offset = 0 ) {
	$comment = get_comment( $comment_id );
	ml_render_comment( $comment, $platform, true );
}

if ( is_user_logged_in() ) {
	$c_user = wp_get_current_user();

	wp_set_current_user( $c_user->ID, $c_user->user_login );
	wp_set_auth_cookie( $c_user->ID, true );
	do_action( 'wp_login', $c_user->user_login, $c_user );
}


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1, user-scalable=no">

		<?php
		function ml_comments_stylesheets() {
			wp_enqueue_style( 'mobiloud-typeplate', MOBILOUD_PLUGIN_URL . 'comments/css/_typeplate.css' );
			wp_enqueue_style( 'onsenui', MOBILOUD_PLUGIN_URL . 'libs/onsen/css/onsenui.min.css' );
			wp_enqueue_style( 'onsen-components', MOBILOUD_PLUGIN_URL . 'libs/onsen/css/onsen-css-components.min.css' );
			wp_enqueue_style( 'mobiloud-comments', MOBILOUD_PLUGIN_URL . 'comments/css/styles.css' );
		}

		function ml_comments_scripts() {
			wp_enqueue_script( 'onsenui', MOBILOUD_PLUGIN_URL . 'libs/onsen/js/onsenui.min.js', array(), false, true );

			wp_enqueue_script( 'mobiloud-list', MOBILOUD_PLUGIN_URL . 'comments/js/comments.js', array( 'onsenui' ), false, true );
		}

		remove_all_actions( 'wp_head' );
		remove_all_actions( 'wp_footer' );
		remove_all_actions( 'wp_print_styles' );
		remove_all_actions( 'wp_enqueue_scripts' );
		remove_all_actions( 'locale_stylesheet' );
		remove_all_actions( 'wp_print_head_scripts' );
		remove_all_actions( 'wp_print_footer_scripts' );
		remove_all_actions( 'wp_shortlink_wp_head' );

		add_action( 'wp_print_styles', 'ml_comments_stylesheets' );
		add_action( 'wp_print_footer_scripts', 'ml_comments_scripts', 30 );
		add_action( 'wp_print_footer_scripts', '_wp_footer_scripts', 30 );

		add_action( 'wp_head', 'wp_print_styles' );
		add_action( 'wp_footer', 'wp_print_footer_scripts', 20 );

		wp_head();

		$custom_css = stripslashes( get_option( 'ml_post_custom_css' ) );
		echo $custom_css ? '<style type="text/css" media="screen">' . $custom_css . '</style>' : ''; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		?>
		<script>
			window.ML_SAVE_USER_DETAILS_THROUGH_FORM_SUBMIT = false;
			window.ML_SAVE_USER_DETAILS_THROUGH_FORM_SUBMIT_MESSAGE = "<?php esc_html_e( 'Name and email saved.' ); ?>";

			function saveUserDetailsWrapper() {
				window.ML_SAVE_USER_DETAILS_THROUGH_FORM_SUBMIT = true;
				saveUserDetails();
			}
		</script>
	</head>
	<?php
	$show_avatars = get_option( 'show_avatars' );
	$body_classes = array( 'comments', Mobiloud::get_template_class( __FILE__, 'ml-comments-' ) );
	if ( ! $show_avatars ) {
		$body_classes[] = 'no-avatars';
	}
	/**
	* Modify body classes list for the page.
	*
	* @since 4.2.0
	*
	* @param string[] $body_classes  Array with class names.
	* @param string   $template_type Template type where it called: 'list', 'comments', etc.
	*/
	$body_classes = apply_filters( 'mobiloud_body_class', $body_classes, 'comments' );
	$post_id      = isset( $_GET['post_id'] ) ? sanitize_text_field( wp_unslash( $_GET['post_id'] ) ) : null; // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected
	$comment_id   = isset( $_GET['comment'] ) ? sanitize_text_field( wp_unslash( $_GET['comment'] ) ) : null; // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected

	?>
	<body class="<?php echo esc_attr( implode( ' ', $body_classes ) ); ?>">
		<ons-list id="comment-list" class="comment-list" data-post-id="<?php echo esc_attr( $post_id ); ?>">
			<?php
			if ( ! is_null( $post_id ) && ! is_null( $comment_id ) ) {
				ml_render_comment_replies( $comment_id );
			} elseif ( ! is_null( $post_id ) ) {
				ml_render_comments( $post_id );
			} else {
				// todo: should we show all comments together?
				ml_render_comments( null );
			}
			?>
		</ons-list>
		<div id="ml-comment-form" class="comment-form
		<?php
		if ( is_user_logged_in() ) {
			echo ' logged-in'; }
		?>
		">

			<?php if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) : ?>
				<div class="ml-comment-login"><a class="ml_button" onclick="nativeFunctions.handleButton('login', null, null);"><?php esc_html_e( 'Log in to leave a Comment' ); ?></a></div>
			<?php else : ?>

				<?php
				// Check if we have the email and name header set.
				$have_info = ( isset( $_SERVER['HTTP_X_ML_COMMENTER'] ) );
				$commenter = isset( $_SERVER['HTTP_X_ML_COMMENTER'] ) ? explode( '|', wp_unslash( $_SERVER['HTTP_X_ML_COMMENTER'] ) ) : [ '', '' ];

				if ( is_user_logged_in() ) {
					$src       = $show_avatars ? Mobiloud::ml_get_avatar_url( get_current_user_id(), 60 ) : '';
					$have_info = true;
				} else {
					$src = $show_avatars ? Mobiloud::ml_get_avatar_url( $commenter[1], 60 ) : '';
				}
				if ( $show_avatars ) : ?>
					<img id="form-avatar" src="<?php echo esc_attr( $src ); ?>" />
					<ons-progress-circular id="form-avatar-processing" indeterminate></ons-progress-circular>
				<?php endif; ?>

			<input type="hidden" name="ml_commenter_name" id="ml-commenter-name" value="<?php echo esc_attr( $commenter[0] ); ?>" />
			<input type="hidden" name="ml_commenter_email" id="ml-commenter-email" value="<?php echo esc_attr( $commenter[1] ); ?>" />

			<textarea id="comment-form-text" class="textarea <?php echo $have_info ? 'has-details' : ''; ?>" rows="1" placeholder="<?php esc_attr_e( 'Leave a Comment' ); ?>"></textarea>
			<ons-icon onclick="submitCommentForm()" id="comment-submit" icon="md-mail-send"></ons-icon>
			<ons-progress-circular id="comment-processing" indeterminate></ons-progress-circular>
			<?php endif; ?>
		</div>

		<!-- Dummy elements so that JS doesn't break. -->
		<span id="form-avatar" style="width: 0;"></span>
		<span id="form-avatar-processing" style="width: 0;"></span>

		<?php
		$nonce = wp_create_nonce( 'ml_post_comment' );
		?>
		<input type="hidden" id="restNonce" value="<?php echo esc_attr( $nonce ); ?>" />
		<?php

		$userEmail = '';
		$userName  = '';
		if ( is_user_logged_in() ) {
			?>
			<input type="hidden" id="mlValidationHeader" value="<?php echo isset( $_SERVER['HTTP_X_ML_VALIDATION'] ) ? esc_attr( wp_unslash( $_SERVER['HTTP_X_ML_VALIDATION'] ) ) : ''; ?>" />
			<?php
			$userEmail = $c_user->user_email;
			$userName  = $c_user->display_name;
		}

		$background_color = get_option( 'ml_commenting_bg_ui_color' );
		$foreground_color = get_option( 'ml_commenting_fg_ui_color' );
		$toggle_nonce     = Mobiloud::get_option( 'ml_commenting_toggle_nonce', 'yes' );
		?>

		<ons-modal id="infoModal" direction="up">
			<div style="text-align: center; margin-top: 30px;">

				<p>
					<ons-input id="commenter-name" value="<?php echo esc_attr( $userName ); ?>"  modifier="underbar" placeholder="<?php esc_attr_e( 'Name' ); ?>" float></ons-input>
				</p>
				<p>
					<ons-input id="commenter-email" value="<?php echo esc_attr( $userEmail ); ?>" modifier="underbar" placeholder="<?php esc_attr_e( 'Email' ); ?>" float></ons-input>
				</p>
				<p style="margin-top: 30px;">
					<ons-button style="background: <?php echo esc_html( $background_color ); ?>; color: <?php echo esc_html( $foreground_color ); ?>;" onclick="saveUserDetailsWrapper()"><?php esc_html_e( 'Save' ); ?></ons-button>
					<ons-button style="background: <?php echo esc_html( $background_color ); ?>; color: <?php echo esc_html( $foreground_color ); ?>;" onclick="closeModal()"><?php esc_html_e( 'Cancel' ); ?></ons-button>
				</p>
			</div>

		</ons-modal>

		<ons-toast id="errorToast" animation="ascend" style="display:none;">
			<span id="err-message">
			</span>
			<button onclick="errorToast.hide()"><?php esc_html_e( 'OK' ); ?></button>
		</ons-toast>

		<script data-cfasync="false">
			// Cloudflare
			var __cfRLUnblockHandlers = 1 ;

			var pluginUrl = '<?php echo esc_js( MOBILOUD_PLUGIN_URL . 'comments/process.php' ); ?>';
			var commentsEndpoint = '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';
			var commentReplyTo = '<?php echo esc_js( $comment_id ); ?>';
			var ml_comments = <?php // @phpcs:ignore
			echo wp_json_encode(
				array(
					'awaiting'      => __( 'Your comment is awaiting moderation.' ),
					'spam'          => __( 'This comment is currently marked as spam.' ),
					'just_now'      => human_time_diff( time(), time() ),
					'invalid_email' => __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' ),
					'user_login'    => __( '<strong>ERROR</strong>: Please enter a username.' ),
					'forbidden'     => __( 'Sorry, you are not allowed to do that.' ),
				)
			);
			// @phpcs:ignore ?>;
		</script>

		<script>
			window.ML_SAVE_USER_DETAILS_THROUGH_FORM_SUBMIT = Boolean( '<?php echo $commenter && 2 === count( $commenter ) && strlen( $commenter[0] ) > 0 && strlen( $commenter[1] ) > 0; ?>' );
		</script>

		<?php wp_footer(); ?>

	</body>
</html>
