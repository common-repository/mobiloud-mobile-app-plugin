<!-- step 0 -->
<div class="ml2-block ml2-welcome-block welcome-step-0">
	<div class="ml2-body text-left">
		<form action="#" method="post" class="contact-form">
			<?php wp_nonce_field( 'tab_welcome', 'ml_nonce' ); ?>
			<input type="hidden" name="step" value="0">
			<h1>Important!</h1>
			<p>The MobiLoud News Plugin is only for MobiLoud customers, click here to see our <a href="https://www.mobiloud.com/pricing/?utm_source=news-plugin&utm_medium=welcome-first">pricing plans</a>. If you are already a customer, insert your activation code in the field below:</p>
			<div class="ml-first-code-wrap">
				<input name="ml_first_code" id="ml_first_code" type="text" value="" required="required">
			</div>
			<div class='ml-col-row ml-init-button'>
				<button type="submit" name="submit" id="submit" class="button button-hero button-primary ladda-button" data-style="zoom-out">Submit</button>
			</div>
		</form>

	</div>
</div>
