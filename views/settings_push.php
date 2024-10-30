<?php wp_nonce_field( 'load_ajax', 'ml_nonce_load_ajax' ); ?>
<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php echo esc_html( Mobiloud_Admin::$settings_tabs[ $active_tab ]['title'] ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
	<?php
		// 0 - PushBots, 1 - OneSignal
		$service = Mobiloud::get_option( 'ml_push_service', '1' );
		?>
		<h4>Automatic push notifications</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Automatically send push notifications when a new post is published</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_push_notification_enabled" name="ml_push_notification_enabled"
						value="true" <?php echo Mobiloud::get_option( 'ml_push_notification_enabled' ) ? 'checked' : ''; ?>/>
					<label for="ml_push_notification_enabled">Send notifications automatically</label>
				</div>
				<p>Select which categories will generate a push notification (empty for all)
					<?php Mobiloud_Admin::load_ajax_insert( 'push_cat_tax' ); ?>
				</p>

				<?php $ml_push_intelligent_delivery = Mobiloud::get_option( 'ml_push_intelligent_delivery', 'off' ); ?>

				<div class="ml-form-row ml-checkbox-wrap">
					<label><?php esc_html_e( 'Intelligent Delivery', 'mobiloud' ); ?></label>
				</div>

				<div class="ml-form-row ml-radio-wrap no-margin">
					<input type="radio" name="ml_push_intelligent_delivery" value="on" <?php echo checked( 'on', $ml_push_intelligent_delivery ) ?> />
					<label><?php esc_html_e( 'On', 'mobiloud' ); ?></label>
				</div>

				<div class="ml-form-row ml-radio-wrap no-margin">
					<input type="radio" name="ml_push_intelligent_delivery" value="off" <?php echo checked( 'off', $ml_push_intelligent_delivery ) ?> />
					<label><?php esc_html_e( 'Off', 'mobiloud' ); ?></label>
				</div>
			</div>
		</div>
		<h4>Push Post Types</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Select which post types should be pushed.</p>
			</div>
			<div class='ml-col-half'>
				<?php
				$posttypes         = get_post_types( '', 'names' );
				$includedPostTypes = explode( ',', Mobiloud::get_option( 'ml_push_post_types', 'post' ) );
				foreach ( $posttypes as $v ) {
					if ( $v != 'attachment' && $v != 'revision' && $v != 'nav_menu_item' ) {
						?>
						<div class="ml-form-row ml-checkbox-wrap no-margin">
							<input type="checkbox" id='postypes_<?php echo esc_attr( $v ); ?>' name="postypes[]"
								value="<?php echo esc_attr( $v ); ?>"<?php echo ( in_array( $v, $includedPostTypes ) ) ? ' checked' : ''; ?>/>
							<label for="postypes_<?php echo esc_attr( $v ); ?>"><?php echo esc_html( $v ); ?></label>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<!-- <h4>Security settings (advanced)</h4>
		<div class='ml-col-row'>
		<div class='ml-col-half'>
		<p>Choose whether to use SSL to communicate with our push service.</p>
		</div>
		<div class="ml-form-row ml-checkbox-wrap no-margin">
		<input type="checkbox" id="ml_pb_use_ssl" name="ml_pb_use_ssl"
		value="true" <?php echo Mobiloud::get_option( 'ml_pb_use_ssl' ) ? 'checked' : ''; ?>/>
		<label for="ml_pb_use_ssl">Enable SSL for push notifications</label>
		</div>
		</div> -->
		<h4 class="ml_system_0"
		<?php
		if ( 1 == $service ) {
			echo ' style="display:none;"';}
		?>
		>Push notification delivery settings</h4>
		<div class='ml-col-row ml_system_0'
		<?php
		if ( 1 == $service ) {
			echo ' style="display:none;"';}
		?>
		>
			<div class='ml-col-half'>
				<p>Push notifications can be sent in chunks in order to minimize the load on your server. You can change the size of each chunk of devices and the delay between each send. The default is 2000 devices every 60 seconds.</p>
			</div>
			<div class="ml-form-row ml-col-half ml-checkbox-wrap no-margin">
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_pb_together" name="ml_pb_together"
						value="true" <?php echo Mobiloud::get_option( 'ml_pb_together', false ) ? 'checked' : ''; ?>/>
					<label for="ml_pb_together">Send notifications at the same time for all devices</label>
				</div>

				<div id="ml_pb_not_together_block"<?php echo ( Mobiloud::get_option( 'ml_pb_together', false ) ) ? ' style="display:none;"' : ''; ?>>
					<label for="ml_pb_chunk">Chunk size: </label>
					<input type="number" id="ml_pb_chunk" name="ml_pb_chunk" value="<?php echo esc_attr( Mobiloud::get_option( 'ml_pb_chunk', 2000 ) ); ?>" min="100" step="100"/>
					<p>This is the number of devices reached at once by the push server.</p>

					<label for="ml_pb_rate">Rate: </label>
					<input type="number" id="ml_pb_rate" name="ml_pb_rate" value="<?php echo esc_attr( Mobiloud::get_option( 'ml_pb_rate', 60 ) ); ?>" min="1"/>
					<p>Rate is expressed in seconds.</p>
				</div>
			</div>
		</div>

		<h4>Notification tags</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>If checked, all notifications will be sent to all devices, irrespective of the user's choices for different
					push categories. This speeds up sending, which can be desirable for breaking news.</p>
			</div>
			<div class="ml-form-row ml-col-half ml-checkbox-wrap no-margin">
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_pb_no_tags" name="ml_pb_no_tags"
						value="true" <?php echo Mobiloud::get_option( 'ml_pb_no_tags', false ) ? 'checked' : ''; ?>/>
					<label for="ml_pb_no_tags">Send notifications without tags</label>
				</div>
			</div>
		</div>

		<h4>Include featured image in push notifications</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>This option allows you to include and show a featured image with new post notifications, or manual notifications linking to a post with a featured image.<br>
				Removing images from notifications can reduce server load when notifications are received simultaneously by a large number of users.<br>
				Alternatively, we advise using an external cache for featured images.</p>
			</div>
			<div class="ml-form-row ml-col-half ml-checkbox-wrap no-margin">
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_push_include_image" name="ml_push_include_image"
						value="true" <?php echo Mobiloud::get_option( 'ml_push_include_image', true ) ? 'checked' : ''; ?>/>
					<label for="ml_push_include_image">Include featured image</label>
				</div>
			</div>
		</div>

		<h4>Wakes your app from background</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Wakes app from background, iOS only. Not applicable if the app was swiped away.</p>
			</div>
			<div class="ml-form-row ml-col-half ml-checkbox-wrap no-margin">
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_push_wakes_app" name="ml_push_wakes_app"
						value="true" <?php checked( Mobiloud::get_option( 'ml_push_wakes_app', true ) ); ?>/>
					<label for="ml_push_wakes_app">Wakes app from background</label>
				</div>
			</div>
		</div>

		<h4><?php esc_html_e( 'Limit push notifications', 'mobiloud' ) ?></h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p><?php esc_html_e( 'Limit the amount of notifications that can be sent in a 15 minutes interval, preventing your OneSignal app from getting disabled.' ); ?></p>
			</div>
			<div class="ml-form-row ml-col-half ml-checkbox-wrap no-margin">
				<div class="ml-form-row ml-checkbox-wrap">
					<div>
						<input type="checkbox" id="ml_pb_rate_limit" name="ml_pb_rate_limit"
							value="true" <?php echo Mobiloud::get_option( 'ml_pb_rate_limit', true ) ? 'checked' : ''; ?>/>
						<label for="ml_pb_rate_limit"><?php esc_html_e( 'Enabled/Disabled' ); ?></label>
					</div>
				</div>
			</div>
		</div>

		<h4>Enable logging for debugging</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>When you enable this, we'll store a log of the requests and responses received from the push server,
					in the order for us to debug any issues with push notifications. Logs will be saved to a file on your server.</p>
			</div>
			<div class="ml-form-row ml-col-half ml-checkbox-wrap no-margin">
				<div class="ml-form-row ml-checkbox-wrap">
					<div>
						<input type="checkbox" id="ml_pb_log_enabled" name="ml_pb_log_enabled"
							value="true" <?php echo Mobiloud::get_option( 'ml_pb_log_enabled', false ) ? 'checked' : ''; ?>/>
						<label for="ml_pb_log_enabled">Enable push logging</label>
					</div>
					<div id="ml_push_log_name_block"<?php echo Mobiloud::get_option( 'ml_pb_log_enabled', false ) ? '' : ' style="display:none;"'; ?>>
						<input type="text" value="<?php echo esc_attr( Mobiloud_Admin::get_pb_log_name( true ) ); ?>" readonly="readonly" class="ml-input-full">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Push Keys', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
	<div class='ml-col-row'>
			<p>Once your app has been published, enter here your Onesignal API keys. These settings will be ineffective until you’ve purchased the MobiLoud service and your app has been built.</p>

			<div class="ml-col-row ml_system_1">
				<div class='ml-col-half'>
					Push App ID
				</div>
				<div class="ml-form-row ml-col-half no-margin">
					<input size="36" type="text" id="ml_onesignal_app_id" name="ml_onesignal_app_id"
						placeholder="OneSignal App ID" class="ml_migrate_req ml-input-full"
						value='<?php echo esc_attr( Mobiloud::get_option( 'ml_onesignal_app_id' ) ); ?>'>
				</div>
			</div>
			<div class="ml-col-row ml_system_1">
				<div class='ml-col-half'>
					Secret Key
				</div>
				<div class="ml-form-row ml-col-half">
					<input size="36" type="text" id="ml_onesignal_secret_key" name="ml_onesignal_secret_key"
						placeholder="REST API Key" class="ml_migrate_req ml-input-full"
						value='<?php echo esc_attr( Mobiloud::get_option( 'ml_onesignal_secret_key' ) ); ?>'>
				</div>
			</div>
		</div>
	</div>
</div>
