<!-- step 1 -->
<?php
global $current_user;
?>
<?php wp_nonce_field( 'tab_welcome', 'ml_nonce' ); ?>
<div class="mlsw-root">

	<!-- Start initial details block -->
	<div class="mlsw__pane mlsw__pane--left">
		<div class="mlsw__card-wrap">
			<form action="https://www.mobiloud.com/demo-success/" method="post" target="_blank" class="contact-form">
				<?php wp_nonce_field( 'ml-form-welcome' ); ?>
				<input type="hidden" name="step" value="1">

				<div class="mlsw__ip-control-wrapper">
					We would like to hear a bit more from you! Our team will then be able to properly guide you through the process of getting your apps up and running.
				</div>
				<div class="mlsw__ip-control-wrapper">
					<label for="pname">Name:</label>
					<input type="text" class="form-control" id="pname" value="<?php echo esc_attr( Mobiloud::get_option( 'ml_user_name', '' ) ); ?>"
						name="name" minlength="2" required="" aria-required="true" maxlength="100">
				</div>

				<div class="mlsw__ip-control-wrapper">
					<label for="pemail">Email:</label>
					<input type="email" class="form-control" id="pemail" value="<?php echo esc_attr( Mobiloud::get_option( 'ml_user_email', $current_user->user_email ) ); ?>"
						name="email" required="" aria-required="true" maxlength="254">
				</div>

				<div class="mlsw__ip-control-wrapper">
					<label for="pphone">Phone number (incl. country code)</label>
					<input type="text" class="form-control" id="pphone" value="<?php echo esc_attr( Mobiloud::get_option( 'ml_user_phone', '' ) ); ?>"
						name="phone" required="" aria-required="true" maxlength="100">
				</div>

				<div class="mlsw__ip-control-wrapper">
					<label for="ml_sitetype">Which category best describes your website?&nbsp;<span class="red">*</span></label>
					<select name="ml_sitetype" id="ml_sitetype" class="form-control">
						<option value="content">Content site, blog, or news site</option>
						<option value="learning">Learning website</option>
						<option value="ecommerce">Ecommerce</option>
						<option value="directory">Directory site</option>
						<option value="other">Something else</option>
					</select>
				</div>


				<label>
					<input type="checkbox" name="accept" id="accept" value="1" required="" aria-required="true">
					<span class="checkbox_content">I accept MobiLoud's <a href="https://www.mobiloud.com/terms/?utm_source=news-plugin&utm_medium=welcome-screen" target="_blank">Terms of Service</a>
						and <a href="https://www.mobiloud.com/privacy/?utm_source=news-plugin&utm_medium=welcome-screen" target="_blank">Privacy Policy</a><span class="red">*</span></span>
				</label>
				<br>
				<br>
				<div class='ml-col-row ml-init-button'>
					<button type="submit" name="submit" id="submit" class="mlsw-get-started-button">Get Started</button>
					<div class="mlsw-spinner"></div>
				</div>
			</form>
		</div>
	</div>
	<!-- Learn more block -->
	<div class="mlsw__pane mlsw__pane--right">
		<div class="mlsw__card-wrap">
			<div class="mlsw__pane-title">
				<strong>Launch mobile apps for your WordPress site</strong>
			</div>
			<div class="mlsw__ip-control-wrapper">
				MobiLoud is a complete service to have a native mobile app built for your WordPress website. We take care of everything for you, from configuring to publishing your app and maintaining it over time. Offering an unmatched level of service is our obsession. Any questions? Send us an email or read more on our website.
			</div>
			<div class="mlsw__panel-pill">
				<a target="_blank" href="https://www.mobiloud.com/pricing">
					<div class="mlsw__panel-pill-desc">LEARN MORE ABOUT</div>
					<div class="mlsw__panel-pill-title">Pricing</div>
					<div class="mlsw__caret-right">
						<img src="<?php echo esc_url( MOBILOUD_PLUGIN_URL . 'assets/icons/caret-right.svg' ); ?>" />
					</div>
				</a>
			</div>
			<div class="mlsw__panel-pill">
				<a target="_blank" href="https://mobiloud.com/demo/?utm_source=news-plugin&utm_medium=wizard">
					<div class="mlsw__panel-pill-desc">ASK QUESTIONS</div>
					<div class="mlsw__panel-pill-title">Schedule a call</div>
					<div class="mlsw__caret-right">
						<img src="<?php echo esc_url( MOBILOUD_PLUGIN_URL . 'assets/icons/caret-right.svg' ); ?>" />
					</div>
				</a>
			</div>
			<div class="mlsw__panel-pill">
				<a target="_blank" href="https://www.mobiloud.com/help">
					<div class="mlsw__panel-pill-desc">LEARN IN DETAILS</div>
					<div class="mlsw__panel-pill-title">Documentation</div>
					<div class="mlsw__caret-right">
						<img src="<?php echo esc_url( MOBILOUD_PLUGIN_URL . 'assets/icons/caret-right.svg' ); ?>" />
					</div>
				</a>
			</div>
		</div>
	</div>
	<div class="mlsw__paid-service-message">
		<h2 class="mlsw__paid-service-message__title">
			<?php esc_html_e( 'Important! Read before proceeding' ); ?>
		</h2>
		<div class="mlsw__paid-service-message__content">
			<?php esc_html_e( 'MobiLoud News is a paid service, and this plugin is for MobiLoud customers. You will still be able to play with the plugin settings and preview your apps if you are not a customer yet, but in order to have the apps built you will need to be subscribed to one of our paid plans.' ); ?>
		</div>
		<div class="mlsw__paid-service-message__button-control">
			<button class="mlsw__paid-service-message__button-control--agree" type="button"><?php esc_html_e( 'I understand' ) ?></button>
			<a href="<?php echo esc_url( admin_url() . 'plugins.php' ); ?>" class="mlsw__paid-service-message__button-control--disagree" type="button"><?php esc_html_e( "No, it won't work for me" ) ?></a>
		</div>
	</div>
	<div class="mlsw__paid-service-message__overlay"></div>
</div>
<script>
	( function( $ ) {
		$( '.mlsw__paid-service-message__button-control--agree' ).click( function() {
			$( '.mlsw__paid-service-message__overlay' ).hide();
			$( '.mlsw__paid-service-message' ).hide();
		} );
	} )( jQuery )
</script>