<div class="ml2-main-area">
	<?php
		if ( Mobiloud_Admin::no_push_keys() ) {
			printf( '<div style="display: block;" class="notice notice-warning"><p>%s</p></div>', __( 'In order to send push notifications please add your Onesignal keys in the <a href="https://mobifresh.local/wp-admin/admin.php?page=mobiloud&tab=push">Push Settings page</a>.' ) );
		}
	?>
	<div class="ml2-block">
		<div class="ml2-header"><h2>Push notifications</h2></div>
		<div class="ml2-body">
			<?php wp_nonce_field( 'tab_push', 'ml_nonce' ); ?>

			<form method="post" action="<?php echo esc_attr( admin_url( 'admin.php?page=mobiloud_push&tab=notifications' ) ); ?>">
				<?php wp_nonce_field( 'form-push_notifications' ); ?>
				<h3>Send manual message</h3>
				<div id="success-message" class="updated inline" style="display: none;">Your message has been sent!</div>
				<?php ml_push_notification_manual_send(); ?>

				<h3>Notification history</h3>
				<!-- NOTIFICATIONS LIST -->
				<div id="ml_push_notification_history">
					<?php ml_push_notification_history_ajax_load(); ?>
				</div>
			</form>
		</div>
	</div>
</div>
