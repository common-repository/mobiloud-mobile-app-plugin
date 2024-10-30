<h4>Preview the results</h4>
<p>Select a post or page to preview the results of your edits.</p>
<select id="preview_popup_post_select">
	<?php
	$ids = [];

	$posts_query         = array(
		'posts_per_page' => 10,
		'orderby'        => 'post_date',
		'order'          => 'DESC',
		'post_type',
	);
	$included_post_types = explode( ',', Mobiloud::get_option( 'ml_article_list_include_post_types', array() ) );
	foreach ( $included_post_types as $post_type ) {
		$posts_query['post_type'] = $post_type;
		$posts                    = get_posts( $posts_query );
		if ( count( $posts ) > 0 ) {
			?>
			<optgroup label="<?php echo esc_attr( ucfirst( $post_type ) ); ?>">
				<?php
				foreach ( $posts as $post ) {
					$ids[] = $post->ID;
					?>

					<option
						value="<?php echo esc_attr( site_url( '/ml-api/v2/post/?post_id=' . $post->ID ) ); ?>">
						<?php if ( strlen( $post->post_title ) > 40 ) { ?>

							<?php echo esc_html( substr( $post->post_title, 0, 40 ) ); ?>

							..
						<?php } else { ?>

							<?php echo esc_html( $post->post_title ); ?>

						<?php } ?>
				</option><?php } ?>
			</optgroup>
			<?php
		}
	}


	?>
	<?php
	$pages = get_pages(
		array(
			'sort_order'  => 'ASC',
			'sort_column' => 'post_title',
			'post_type'   => 'page',
			'post_status' => 'publish',
		)
	);
	?>
	<optgroup label="Pages">
		<?php foreach ( $pages as $page ) { ?>

			<option value="<?php echo esc_attr( site_url( '/ml-api/v2/post/?post_id=' . $page->ID ) ); ?>">
				<?php if ( strlen( $page->post_title ) > 40 ) { ?>

					<?php echo esc_html( substr( $page->post_title, 0, 40 ) ); ?>

					..
				<?php } else { ?>

					<?php echo esc_html( $page->post_title ); ?>

				<?php } ?>
		</option><?php } ?>
	</optgroup>
	<?php
	// v2 lists.
	$base_endpoint_url = trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/list';
	$favorites_url     = $base_endpoint_url . '?post_ids=' . implode( ',', $ids );
	?>
	<optgroup label="Lists">
		<option value="<?php echo esc_attr( $base_endpoint_url ); ?>">Homescreen</option>
		<option value="<?php echo esc_attr( $favorites_url ); ?>">Favorites (using latest posts)</option>
	</optgroup>
	<optgroup label="Custom">
		<option value="-custom-">Custom URL</option>
	</optgroup>
</select>
<a href='#' class='ml_open_preview_btn button-secondary ml-preview-phone-btn'>Preview on phone</a>
<a href='#' class='ml_open_preview_btn button-secondary ml-preview-tablet-btn'>Preview on tablet</a>
<input type="url" id="preview_popup_post_url" style="display: block;width:100%;max-width: 768px;margin-top:20px;" value="<?php echo isset( $base_endpoint_url ) ? esc_attr( $base_endpoint_url ) : ''; ?>">

<!-- hidden preview block  -->
<?php
$device_classes = [];
$top_bar        = '';
$bottom_bar     = '';

function draw_top_bar() {
	$dark_theme = Mobiloud_App_Preview::get_color_brightness( get_option( 'ml_preview_theme_color' ) ) < 190 ? '' : ' dark-theme';

	?>
	<div class="top-bar<?php echo esc_attr( $dark_theme ); ?>" style='background-color: <?php echo esc_attr( get_option( 'ml_preview_theme_color' ) ); ?>;'>
		<div class="ml-preview-top-bar"></div>
		<div class="ml-preview-menu-bar">
			<a href="javascript:void(0);" class="ml-icon ml-icon-menu ml-icon-white"></a>
			<a href="javascript:void(0);" class="ml-preview-logo-holder">
				<?php
				if ( strlen( trim( get_option( 'ml_preview_upload_image' ) ) ) > 0 ) {
					$logo_path = get_option( 'ml_preview_upload_image' );
				} else {
					$logo_path = MOBILOUD_PLUGIN_URL . '/assets/img/ml_preview_nologo.png';
				}
				?>
				<img class="ml-preview-logo" src="<?php echo esc_url( $logo_path ); ?>">
			</a>
			<a href="javascript:void(0);" class="ml-icon ml-icon-search ml-icon-white"></a>
		</div>
	</div>
	<?php
}

function draw_bottom_bar() {
	if ( Mobiloud::get_option( 'ml_tabbed_navigation_enabled' ) ) {
		$data                = Mobiloud::get_option( 'ml_tabbed_navigation' );
		$background_color    = ! empty( $data['background_color'] ) ? $data['background_color'] : '#fff';
		$active_icon_color   = ! empty( $data['active_icon_color'] ) ? $data['active_icon_color'] : '#222';
		$inactive_icon_color = ! empty( $data['inactive_icon_color'] ) ? $data['inactive_icon_color'] : '#222';
		$color               = $active_icon_color;
		?>
		<div class="bottom-bar" style="background-color: <?php echo esc_attr( $background_color ); ?>;">
			<?php
			if ( is_array( $data ) && is_array( $data['tabs'] ) ) {
				foreach ( $data['tabs'] as $tab ) {
					if ( $tab['enabled'] ) {
						?>
						<a href="javascript:void(0);" class="ml-tab-nav-item" style="color: <?php echo esc_attr( $color ); ?>;">
						<img class="tab-icon" src="<?php echo esc_url( $tab['icon_url'] ); ?>" />
						<span><?php echo esc_html( $tab['label'] ); ?></span>
						</a>
						<?php
						$color = $inactive_icon_color;
					}
				}
			}
			?>
		</div>
		<?php
	}
}

$device_classes[] = 'v2-list';
if ( Mobiloud::get_option( 'ml_tabbed_navigation_enabled' ) ) {
	$device_classes[] = 'with-tabbed-nav';
}
if ( get_option( 'ml_rtl_text_enable' ) ) {
	$device_classes[] = 'device-rtl';
}
?>
<div id="preview_popup_content">
	<div class="iphone5s_device <?php echo esc_attr( implode( ' ', $device_classes ) ); ?>">
		<?php draw_top_bar(); ?>
		<iframe id="preview_popup_iframe">
		</iframe>
		<?php draw_bottom_bar(); ?>
	</div>
	<div class="ipadmini_device">
		<iframe id="preview_popup_iframe">
		</iframe>
	</div>
	<div class="ipad2_device device_scale_50 <?php echo esc_attr( implode( ' ', $device_classes ) ); ?>">
		<?php draw_top_bar(); ?>
		<iframe id="preview_popup_iframe">
		</iframe>
		<?php draw_bottom_bar(); ?>
	</div>
</div>
<!-- /hidden preview block  -->
