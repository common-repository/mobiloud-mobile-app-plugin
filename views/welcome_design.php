<!-- step 5 -->
<?php
$next_step = add_query_arg( [ 'tab' => 'welcome-close' ], remove_query_arg( [ 'step', 'tab' ] ) );
?>
<div class="mlsw__app-design">
	<div class="mlsw__config-title">
		App Design
	</div>
	<div class="mlsw__config-desc mlsw__config-desc--page-title">
		The first step is for you to configure the branding and design aspects of your app
	</div>
	<hr class="mlsw__config-separator">
	<div>
		<form action="<?php echo esc_attr( $next_step ); ?>" method="post" class="contact-form">
			<?php wp_nonce_field( 'ml-form-design' ); // same nonce as Design tab uses. ?>
			<input type="hidden" name="step" value="5">
			<div class="mlsw__config-section-title">
				Upload your logo
			</div>
			<input id="ml_preview_upload_image" type="text" size="36" name="ml_preview_upload_image"
				value="<?php echo esc_url( get_option( 'ml_preview_upload_image' ) ); ?>"/>
			<input id="ml_preview_upload_image_button--conf" type="button" value="Upload Image" class="browser button"/>
			<?php $logoPath = Mobiloud::get_option( 'ml_preview_upload_image' ); ?>
			<div class="ml-form-row ml-preview-upload-image-row" <?php echo ( strlen( $logoPath ) === 0 ) ? 'style="display:none;"' : ''; ?>>
				<div class='ml-preview-image-holder'>
					<img src='<?php echo esc_url( $logoPath ); ?>'/>
				</div>
				<a href='#' class='ml-preview-image-remove-btn'>Remove logo</a>
				<div class="mlsw__config-desc mlsw__config-desc--image-logo">
					Make sure to use a high quality version of your logo, with a transparent background, in the .PNG format. The logo will be displayed at the top of your app.
				</div>
			</div>
			<hr class="mlsw__config-separator">
			<div class="mlsw__config-section-title">
				Select your brand's main color
			</div>
			<input name="ml_preview_theme_color" id="ml_preview_theme_color" type="text" value="<?php echo esc_attr( get_option( 'ml_preview_theme_color' ) ); ?>"/>
			<div class="mlsw__config-desc">
				Pick a color that matches your branding, this color will be used across the app in several places. This will be your logo's background color too.
			</div>
		</form>
		<div class="mlsw__button-controls">
			<a href="<?php echo admin_url( 'admin.php?page=mobiloud&step=details' ); ?>" type="submit" name="back" class="mlsw__button mlsw__button--gray"><?php esc_html_e( 'Back' ); ?></a>
			<a href="<?php echo admin_url( 'admin.php?page=mobiloud&step=configuration' ); ?>" type="submit" name="finish" class="mlsw__button mlsw__button--blue"><?php esc_html_e( 'Next' ); ?></a>
		</div>
	</div>
</div>
