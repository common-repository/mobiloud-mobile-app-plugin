<?php
$iconShade = 'ml-icon-dark';
if ( Mobiloud_App_Preview::get_color_brightness( get_option( 'ml_preview_theme_color' ) ) < 190 ) {
	$iconShade = 'ml-icon-white';
}
?>

<div class='ml-preview <?php echo esc_attr( strlen( get_option( 'ml_preview_os' ) ) ? get_option( 'ml_preview_os' ) : 'ios' ); ?>'>
	<div class='ml-preview-body'>
		<div class="ml-preview-top-bar <?php echo esc_attr( $iconShade ); ?>"
			style='background-color: <?php echo esc_attr( get_option( 'ml_preview_theme_color' ) ); ?>;'></div>
		<div class='ml-preview-menu-bar'
			style='background-color: <?php echo esc_attr( get_option( 'ml_preview_theme_color' ) ); ?>;'>
			<a href='javascript:void(0);' class='ml-icon ml-icon-menu <?php echo esc_attr( $iconShade ); ?>'></a>
			<a href='javascript:void(0);' class='ml-preview-logo-holder'>
				<?php
				if ( strlen( trim( get_option( 'ml_preview_upload_image' ) ) ) > 0 ) {
					$logoPath = get_option( 'ml_preview_upload_image' );
				} else {
					$logoPath = MOBILOUD_PLUGIN_URL . '/assets/img/ml_preview_nologo.png';
				}
				?>
				<img class='ml-preview-logo' src='<?php echo esc_url( $logoPath ); ?>'/>
			</a>
			<a href='javascript:void(0);' class='ml-icon ml-icon-search <?php echo esc_attr( $iconShade ); ?>'></a>
		</div>
		<div class='ml-preview-article-list'>
			<div class='scroller'>
				<div id='ml-page-placeholder'>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
