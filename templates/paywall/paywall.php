<?php
/**
 * This is a paywall block template: paywall.php.
 *
 * @package MobiLoud.
 * @subpackage MobiLoud/templates/paywall
 * @version 4.2.0
 */

$subscription_endpoint = trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/subscription';
$ml_paywall_settings   = Mobiloud::get_option( 'ml_paywall_settings' );
?>
<style type="text/css">
	<?php echo Mobiloud::get_option( 'ml_paywall_pblock_css' ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
</style>

<div id="ml-paywall" class="ml-paywall">
	<div class="ml-paywall__wrap">
		<?php echo wp_kses( Mobiloud::get_option( 'ml_paywall_pblock_content' ), Mobiloud::expanded_alowed_tags() ); ?>
	</div>
</div>
