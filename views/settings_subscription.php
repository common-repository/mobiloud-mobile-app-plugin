<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'General Settings', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<div class='ml-col-row'>
			<p>By enabling in-app subscriptions your app will start displaying the subscription buttons in all article pages and under your app main menu.</p>
			<div class="ml-form-row ml-checkbox-wrap">
				<input type="checkbox" id="ml_app_subscription_enabled" name="ml_app_subscription_enabled"
					value="true" <?php echo Mobiloud::get_option( 'ml_app_subscription_enabled' ) ? 'checked' : ''; ?>/>
				<label for="ml_app_subscription_enabled">Enable in-app subscriptions</label>
			</div>
		</div>

		<h4>Apple In-App Purchase ID</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Insert your in-app purchase product identifier, this information can be found under your iTunes Connect account.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input id="ml_app_subscription_ios_in_app_purchase_id" type="text" size="36" name="ml_app_subscription_ios_in_app_purchase_id" class="ml-input-full"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_app_subscription_ios_in_app_purchase_id', '' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Google In-App Purchase ID</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Insert your in-app purchase product identifier, this information can be found under your Google Developer Console.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input id="ml_app_subscription_android_in_app_purchase_id" type="text" size="36" name="ml_app_subscription_android_in_app_purchase_id" class="ml-input-full"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_app_subscription_android_in_app_purchase_id', '' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Subscribe Menu Item Text</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Insert the text for your subscribe link</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input id="ml_app_subscriptions_subscribe_link_text" type="text" size="36" name="ml_app_subscriptions_subscribe_link_text" class="ml-input-full"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_app_subscriptions_subscribe_link_text', '' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Manage Subscription Menu Item Text</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Insert the text for your Manage Subscription link</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input id="ml_app_subscriptions_manage_subscription_link_text" type="text" size="36" name="ml_app_subscriptions_manage_subscription_link_text" class="ml-input-full"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_app_subscriptions_manage_subscription_link_text', '' ) ); ?>"/>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Subscription screen settings', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<h4>HTML Content</h4>
		<div class="ml-form-row">
			<textarea class="ml-editor-area ml-editor-area-html ml-show" name="ml_app_subscription_block_content"><?php echo esc_html( Mobiloud::get_option( 'ml_app_subscription_block_content', '' ) ); ?></textarea>
		</div>

		<h4>CSS rules</h4>
		<div class="ml-form-row">
			<textarea class="ml-editor-area ml-editor-area-css ml-show" name="ml_app_subscription_block_css"><?php echo esc_html( Mobiloud::get_option( 'ml_app_subscription_block_css', '' ) ); ?></textarea>
		</div>
	</div>
</div>
