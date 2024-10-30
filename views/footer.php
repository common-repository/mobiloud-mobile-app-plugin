<?php if ( defined( 'ml_with_form' ) && ml_with_form ) {
	if ( ! defined( 'no_submit_button' ) || ! no_submit_button ) {
		?><p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
		<?php } ?>
	</form>
	<?php
}
?>
<?php
if ( defined( 'ml_with_sidebar' ) && ml_with_sidebar ) {
	?>
	</div>
	<?php
}
?>
</div><!--#wrap-->

<?php
$user_site      = get_site_url();
$paramsArray = array(
	'plugin_url' => $user_site
);
$params = urlencode( json_encode( $paramsArray ) );
?>

<div class="mlconf__live-preview-overlay" style="display: none;"></div>
<div id="mlconf__live-preview-wrapper" style="display: none;">
	<div class="mlconf__live-preview-wrapper--close"></div>
	<div class="mlconf__live-preview-device-tabs">
		<div data-live-preview-tab="ios" class="mlconf__live-preview-device-tab ml-device-tab-selected"><?php esc_html_e( 'iOS', 'mobiloud' ); ?></div>
		<div data-live-preview-tab="android" class="mlconf__live-preview-device-tab"><?php esc_html_e( 'Android', 'mobiloud' ); ?></div>
	</div>
	<div class="mlconf__live-preview-device-wrapper--outer">
		<div class="mlconf__live-preview-device-wrapper">
			<div class="mlconf__live-preview-device mlconf__live-preview-device--ios">
				<iframe id="iframe_ios" class="iframe" src="https://appetize.io/embed/5uqrjy35kf719h2304t4ehpy2r?device=iphone12promax&osVersion=15.0&autoplay=false&orientation=portrait&scale=66&xdocMsg=true&params=<?php echo $params; ?>" width="310px" height="640px" frameborder="0" scrolling="no"></iframe>
			</div>
			<div class="mlconf__live-preview-device mlconf__live-preview-device--android">
				<iframe id="iframe_android" class="iframe" src="https://appetize.io/embed/9pmb2g5gp7mk9e15zq7yf5ygz4?device=pixel4&osVersion=10.0&scale=75&autoplay=false&orientation=portrait&deviceColor=black&xdocMsg=true&params=<?php echo $params; ?>" width="300px" height="640px" frameborder="0" scrolling="no"></iframe>
			</div>
		</div>
	</div>
</div>
