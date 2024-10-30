<!-- step 2 -->
<?php
$next_step = add_query_arg( 'step', Mobiloud_Admin::$welcome_steps[2], remove_query_arg( [ 'step', 'tab' ] ) );
$type      = Mobiloud::get_option( 'ml_user_sitetype', '' );
if ( ! in_array( $type, [ 'content', 'learning', 'ecommerce', 'directory', 'other' ], true ) ) {
	$type = 'content';
}
$url = add_query_arg(
	[
		'url'          => rawurlencode( Mobiloud::get_option( 'ml_user_site' ) ),
		'email'        => rawurlencode( Mobiloud::get_option( 'ml_user_email' ) ),
		'name'         => rawurlencode( Mobiloud::get_option( 'ml_user_name' ) ),
		'company_name' => rawurlencode( Mobiloud::get_option( 'ml_user_company' ) ),
		'phone'        => rawurlencode( Mobiloud::get_option( 'ml_user_phone' ) ),
		'questions'    => rawurlencode( 'I would like to learn more about Canvas and speak to an expert.' ),
	],
	'https://mobiloud.com/demo/?utm_source=news-plugin&utm_medium=wizard'
);
?>
<div class="ml2-block ml2-welcome-block welcome-step-2">
	<div class="ml2-body text-left">
		<form action="<?php echo esc_attr( $next_step ); ?>" method="post" class="contact-form" data-url="<?php echo esc_url( $url ); ?>">
			<?php wp_nonce_field( 'ml-form-welcome' ); ?>
			<input type="hidden" name="step" value="2">
			<h3 class="title_big">Which category best describes your website?</h3>
			<br>
			<p>We'll recommend the best settings based on your choice.</p>
			<br>
			<p class="ml-choice ml-choice-wrap"><input type="radio" name="ml_sitetype" value="content" id="content" <?php checked( $type, 'content' ); ?>><label for="content">Content site, blog, or news site</label></p>
			<p class="ml-choice ml-choice-wrap"><input type="radio" name="ml_sitetype" value="learning" id="learning" <?php checked( $type, 'learning' ); ?>><label for="learning">Learning website</label></p>
			<p class="ml-choice ml-choice-wrap"><input type="radio" name="ml_sitetype" value="ecommerce" id="ecommerce" <?php checked( $type, 'ecommerce' ); ?>><label for="ecommerce">Ecommerce</label></p>
			<p class="ml-choice ml-choice-wrap"><input type="radio" name="ml_sitetype" value="directory" id="directory" <?php checked( $type, 'directory' ); ?>><label for="directory">Directory site</label></p>
			<p class="ml-choice ml-choice-wrap"><input type="radio" name="ml_sitetype" value="other" id="other" <?php checked( $type, 'other' ); ?>><label for="other">Something else</label></p>
			<br>
			<div class='ml-col-row ml-init-button'>
				<button type="submit" name="submit" id="submit" class="button button-hero button-primary ladda-button" data-style="zoom-out">Save and Continue</button>
			</div>
		</form>

	</div>
</div>
