<?php wp_nonce_field( 'tab_design', 'ml_nonce' ); ?>

<!-- Panel: Design -->
<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Design', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<div class="mlconf__panel-content-row">
			<div class="mlconf__panel-content-row-title">
				<?php esc_html_e( 'Upload your logo', 'mobiloud' ); ?>
			</div>
			<div class="mlconf__panel-content-row-desc">
				<?php esc_html_e( 'Your logo will be displayed at the top of the app, make sure to upload a high quality version of your logo with a transparent background', 'mobiloud' ); ?>
			</div>
			<?php $logoPath = Mobiloud::get_option( 'ml_preview_upload_image' ); ?>
			<input id="ml_preview_upload_image" type="text" size="36" name="ml_preview_upload_image" value="<?php echo esc_url( get_option( 'ml_preview_upload_image' ) ); ?>"/>

			<div class="mlconf__logo-buttons-wrapper">
				<input id="ml_preview_upload_image_button" type="button" value="<?php esc_attr_e( 'Upload Image', 'mobiloud' ); ?>" class="browser button"/>
				<button id="mlconf__remove-logo" class="button"><?php esc_html_e( 'Remove image', 'mobiloud' ); ?></button>
			</div>

			<div style="display: <?php echo empty( $logoPath ) ? 'none' : ''; ?>" class="mlconf__logo-image-wrapper">
				<img class="mlconf__logo-image" src='<?php echo esc_url( $logoPath ); ?>'/>
			</div>
		</div>

		<div class="mlconf__panel-content-row">
			<div class="mlconf__panel-content-row-title">
				<?php esc_html_e( 'Main color', 'mobiloud' ); ?>
			</div>
			<div class="mlconf__panel-content-row-desc">
				<?php esc_html_e( 'The selected main color will be used in various places of your app, pick a color that works well with your logo', 'mobiloud' ); ?>
			</div>
			<div class="mlconfig__main-color">
				<input name="ml_preview_theme_color" id="ml_preview_theme_color" type="text" value="<?php echo esc_attr( get_option( 'ml_preview_theme_color' ) ); ?>"/>
			</div>
		</div>

		<div class="mlconf__panel-content-row">
			<div class="mlconf__panel-content-row-title">
				<?php esc_html_e( 'Article list style', 'mobiloud' ); ?>
			</div>
			<div class="mlconf__panel-content-row-desc">
				<?php esc_html_e( 'Determine how your articles will be presented in the lists', 'mobiloud' ); ?>
			</div>
			<div class="mlconf__article-list-control-wrapper">
				<input class="mlconf__article-list-control-radio" type="radio" id="ml_article_list_view_type_extended" name="ml_article_list_view_type" value="extended" <?php echo get_option( 'ml_article_list_view_type' ) == 'extended' ? 'checked' : ''; ?>>
				<label class="mlconf__article-list-control" for="ml_article_list_view_type_extended">
					<img class="mlconf__article-list-control-icon" src="<?php echo esc_url( MOBILOUD_PLUGIN_URL . 'assets/icons/extended.svg' ); ?>" alt="<?php esc_attr_e( 'Extended', 'mobiloud' ); ?>">
					<div class="mlconf__article-list-control-title">
						<?php esc_html_e( 'Extended', 'mobiloud' ); ?>
					</div>
				</label>

				<input class="mlconf__article-list-control-radio" type="radio" id="ml_article_list_view_type_compact" name="ml_article_list_view_type" value="compact" <?php echo get_option( 'ml_article_list_view_type' ) == 'compact' ? 'checked' : ''; ?>>
				<label class="mlconf__article-list-control" for="ml_article_list_view_type_compact">
					<img class="mlconf__article-list-control-icon" src="<?php echo esc_url( MOBILOUD_PLUGIN_URL . 'assets/icons/compact.svg' ); ?>" alt="<?php esc_attr_e( 'Compact', 'mobiloud' ); ?>">
					<div class="mlconf__article-list-control-title">
						<?php esc_html_e( 'Compact', 'mobiloud' ); ?>
					</div>
				</label>
			</div>
		</div>

		<div class="mlconf__panel-content-row">
			<div class="mlconf__panel-content-row-title">
				<?php esc_html_e( 'Device Rotation', 'mobiloud' ); ?>
			</div>
			<div class="mlconf__panel-content-row-desc">
				<div class="mlconf__left-to-right-control">
					<label for="ml_rtl_text_enable">
					<input type="checkbox" id="ml_allow_landscape" name="ml_allow_landscape" value="true" <?php echo Mobiloud::get_option( 'ml_allow_landscape', true ) ? 'checked' : ''; ?>/>
						<?php esc_html_e( 'Allow device rotation to landscape mode', 'mobiloud' ); ?>
					</label>
				</div>
			</div>
		</div>

		<div class="mlconf__panel-content-row">
			<div class="mlconf__panel-content-row-title">
				<?php esc_html_e( 'Right to left support', 'mobiloud' ); ?>
			</div>
			<div class="mlconf__panel-content-row-desc">
				<?php esc_html_e( 'If your content is in Arabic or Hebrew, enable support for RTL', 'mobiloud' ); ?>
				<div class="mlconf__left-to-right-control">
					<label for="ml_rtl_text_enable">
						<input type="checkbox" id="ml_rtl_text_enable" name="ml_rtl_text_enable" value="true" <?php echo Mobiloud::get_option( 'ml_rtl_text_enable' ) ? 'checked' : ''; ?>/>
						<?php esc_html_e( 'Enable right to left support', 'mobiloud' ); ?>
					</label>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Panel: Design // -->

<?php
$template = get_option( 'ml-templates', 'legacy' );
if ( 'default' === $template ) :
?>
<div class="ml2-block">
	<div class="ml2-header"><h2><?php esc_html_e( 'Customization' ); ?></h2></div>
	<div class="dt-admin__wrapper">
		<h2 class="dt-admin__section-title"><?php esc_html_e( 'Lists' ); ?></h2>
		<?php
			$prefix = 'dt-list-';
			$dt_fonts = require MOBILOUD_PLUGIN_DIR . 'fonts.php';
			$xxx = Mobiloud::get_option( $prefix . 'title-toggle', 'true' );
		?>

		<!-- Post title -->
		<div class="dt-admin__item-content">
			<div class="dt-admin__item">
				<h4><?php esc_html_e( 'Post title' ); ?></h4>
				<div class="ml-dt-row ml-checkbox-wrap">
					<?php $is_checked = 'true' === Mobiloud::get_option( $prefix . 'title-toggle', 'true' ); ?>
					<input type="hidden" name="<?php echo esc_attr( $prefix . 'title-toggle' ); ?>" value="false">
					<input class="dt-list-cb-toggle" type="checkbox" id="<?php echo esc_attr( $prefix . 'title-toggle' ); ?>" name="<?php echo esc_attr( $prefix . 'title-toggle' ); ?>"
						value="true" <?php echo 'true' === Mobiloud::get_option( $prefix . 'title-toggle', 'true' ) ? 'checked' : ''; ?>/>
					<label for="<?php echo esc_attr( $prefix . 'title-toggle' ); ?>"><?php esc_html_e( 'Display post title' ); ?></label>
				</div>

				<div class="<?php echo esc_attr( $prefix . 'title-toggle' ); ?> <?php echo $is_checked ? esc_attr( $prefix . 'title-toggle--show' ) : '' ?>">
					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font family:' ); ?></label>
						<select name="<?php echo esc_attr( $prefix . 'title-font' ); ?>" id="">
						<?php foreach ( $dt_fonts as $font ) : ?>
							<option <?php selected( $font['value'], Mobiloud::get_option( $prefix . 'title-font', 'Roboto' ) ); ?> value="<?php echo esc_attr( $font['value'] ); ?>"><?php echo esc_html( $font['label'] ); ?></option>
						<?php endforeach ?>
						</select>
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font size:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'title-font-size' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'title-font-size', 1.8 ); ?>" />
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Line Height:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'title-line-height' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'title-line-height', 1.3 ); ?>" />
					</div>

					<div class='ml-form-row ml-dt-row ml-color'>
						<label><?php esc_html_e( 'Color', 'mobiloud' ); ?></label>
						<input class="color-picker"
							name="<?php echo esc_attr( $prefix . 'title-color' ); ?>" type="text"
							value="<?php echo esc_attr( Mobiloud::get_option( $prefix . 'title-color', '#000' ) ); ?>"
						/>
					</div>
				</div>
			</div>

			<!-- Post author -->
			<div class="dt-admin__item">
				<h4><?php esc_html_e( 'Post meta' ); ?></h4>
				<div class="ml-dt-row ml-checkbox-wrap">
					<?php $is_checked = 'true' === Mobiloud::get_option( $prefix . 'author-toggle', 'true' ); ?>
					<input type="hidden" name="<?php echo esc_attr( $prefix . 'author-toggle' ); ?>">
					<input class="dt-list-cb-toggle" type="checkbox" id="<?php echo esc_attr( $prefix . 'author-toggle' ); ?>" name="<?php echo esc_attr( $prefix . 'author-toggle' ); ?>"
						value="true" <?php echo 'true' === Mobiloud::get_option( $prefix . 'author-toggle', 'true' ) ? 'checked' : ''; ?>/>
					<label for="<?php echo esc_attr( $prefix . 'author-toggle' ); ?>"><?php esc_html_e( 'Display post author' ); ?></label>
				</div>

				<div class="<?php echo esc_attr( $prefix . 'author-toggle' ); ?> <?php echo $is_checked ? esc_attr( $prefix . 'author-toggle--show' ) : '' ?>">
					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font family:' ); ?></label>
						<select name="<?php echo esc_attr( $prefix . 'author-font' ); ?>" id="">
						<?php foreach ( $dt_fonts as $font ) : ?>
							<option <?php selected( $font['value'], Mobiloud::get_option( $prefix . 'author-font', 'Roboto' ) ); ?> value="<?php echo esc_attr( $font['value'] ); ?>"><?php echo esc_html( $font['label'] ); ?></option>
						<?php endforeach ?>
						</select>
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font size:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'author-font-size' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'author-font-size', 0.6785 ); ?>" />
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Line Height:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'author-line-height' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'author-line-height', 0.92 ); ?>" />
					</div>

					<div class='ml-form-row ml-dt-row ml-color'>
						<label><?php esc_html_e( 'Color', 'mobiloud' ); ?></label>
						<input class="color-picker"
							name="<?php echo esc_attr( $prefix . 'author-color' ); ?>" type="text"
							value="<?php echo esc_attr( Mobiloud::get_option( $prefix . 'author-color', '#000' ) ); ?>"
						/>
					</div>
				</div>

				<!-- Post category -->
				<div class="ml-dt-row ml-checkbox-wrap">
					<?php $is_checked = 'true' === Mobiloud::get_option( $prefix . 'category-toggle', 'true' ); ?>
					<input type="hidden" name="<?php echo esc_attr( $prefix . 'category-toggle' ); ?>">
					<input class="dt-list-cb-toggle" type="checkbox" id="<?php echo esc_attr( $prefix . 'category-toggle' ); ?>" name="<?php echo esc_attr( $prefix . 'category-toggle' ); ?>"
						value="true" <?php echo 'true' === Mobiloud::get_option( $prefix . 'category-toggle', 'true' ) ? 'checked' : ''; ?>/>
					<label for="<?php echo esc_attr( $prefix . 'category-toggle' ); ?>"><?php esc_html_e( 'Display post category' ); ?></label>
				</div>

				<div class="<?php echo esc_attr( $prefix . 'category-toggle' ); ?> <?php echo $is_checked ? esc_attr( $prefix . 'category-toggle--show' ) : '' ?>">
					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font family:' ); ?></label>
						<select name="<?php echo esc_attr( $prefix . 'category-font' ); ?>" id="">
						<?php foreach ( $dt_fonts as $font ) : ?>
							<option <?php selected( $font['value'], Mobiloud::get_option( $prefix . 'category-font', 'Roboto' ) ); ?> value="<?php echo esc_attr( $font['value'] ); ?>"><?php echo esc_html( $font['label'] ); ?></option>
						<?php endforeach ?>
						</select>
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font size:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'category-font-size' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'category-font-size', 0.6785 ); ?>" />
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Line Height:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'category-line-height' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'category-line-height', 0.92 ); ?>" />
					</div>

					<div class='ml-form-row ml-dt-row ml-color'>
						<label><?php esc_html_e( 'Color', 'mobiloud' ); ?></label>
						<input class="color-picker"
							name="<?php echo esc_attr( $prefix . 'category-color' ); ?>" type="text"
							value="<?php echo esc_attr( Mobiloud::get_option( $prefix . 'category-color', '#000' ) ); ?>"
						/>
					</div>
				</div>

				<!-- Post date -->
				<div class="ml-dt-row ml-checkbox-wrap">
					<?php $is_checked = 'true' === Mobiloud::get_option( $prefix . 'date-toggle', 'true' ); ?>
					<input type="hidden" name="<?php echo esc_attr( $prefix . 'date-toggle' ); ?>">
					<input class="dt-list-cb-toggle" type="checkbox" id="<?php echo esc_attr( $prefix . 'date-toggle' ); ?>" name="<?php echo esc_attr( $prefix . 'date-toggle' ); ?>"
						value="true" <?php echo 'true' === Mobiloud::get_option( $prefix . 'date-toggle', 'true' ) ? 'checked' : ''; ?>/>
					<label for="<?php echo esc_attr( $prefix . 'date-toggle' ); ?>"><?php esc_html_e( 'Display post date' ); ?></label>
				</div>

				<div class="<?php echo esc_attr( $prefix . 'date-toggle' ); ?> <?php echo $is_checked ? esc_attr( $prefix . 'date-toggle--show' ) : '' ?>">
					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font family:' ); ?></label>
						<select name="<?php echo esc_attr( $prefix . 'date-font' ); ?>" id="">
						<?php foreach ( $dt_fonts as $font ) : ?>
							<option <?php selected( $font['value'], Mobiloud::get_option( $prefix . 'date-font', 'Roboto' ) ); ?> value="<?php echo esc_attr( $font['value'] ); ?>"><?php echo esc_html( $font['label'] ); ?></option>
						<?php endforeach ?>
						</select>
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font size:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'date-font-size' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'date-font-size', 0.85 ); ?>" />
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Line Height:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'date-line-height' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'date-line-height', 0.92 ); ?>" />
					</div>

					<div class='ml-form-row ml-dt-row ml-color'>
						<label><?php esc_html_e( 'Color', 'mobiloud' ); ?></label>
						<input class="color-picker"
							name="<?php echo esc_attr( $prefix . 'date-color' ); ?>" type="text"
							value="<?php echo esc_attr( Mobiloud::get_option( $prefix . 'date-color', '#000' ) ); ?>"
						/>
					</div>
				</div>
			</div>

			<!-- Post content -->
			<div class="dt-admin__item">
				<h4><?php esc_html_e( 'Post content' ); ?></h4>
				<div class="ml-dt-row ml-checkbox-wrap">
					<?php $is_checked = 'true' === Mobiloud::get_option( $prefix . 'content-toggle', 'true' ); ?>
					<input type="hidden" name="<?php echo esc_attr( $prefix . 'content-toggle' ); ?>">
					<input class="dt-list-cb-toggle" type="checkbox" id="<?php echo esc_attr( $prefix . 'content-toggle' ); ?>" name="<?php echo esc_attr( $prefix . 'content-toggle' ); ?>"
						value="true" <?php echo 'true' === Mobiloud::get_option( $prefix . 'content-toggle', 'true' ) ? 'checked' : ''; ?>/>
					<label for="<?php echo esc_attr( $prefix . 'content-toggle' ); ?>"><?php esc_html_e( 'Display content' ); ?></label>
				</div>

				<div class="<?php echo esc_attr( $prefix . 'content-toggle' ); ?> <?php echo $is_checked ? esc_attr( $prefix . 'content-toggle--show' ) : '' ?>">
					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font family:' ); ?></label>
						<select name="<?php echo esc_attr( $prefix . 'content-font' ); ?>" id="">
						<?php foreach ( $dt_fonts as $font ) : ?>
							<option <?php selected( $font['value'], Mobiloud::get_option( $prefix . 'content-font', 'Roboto' ) ); ?> value="<?php echo esc_attr( $font['value'] ); ?>"><?php echo esc_html( $font['label'] ); ?></option>
						<?php endforeach ?>
						</select>
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font size:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'content-font-size' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'content-font-size', 0.85 ); ?>" />
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Line Height:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'content-line-height' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'content-line-height', 1.2 ); ?>" />
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Excerpt length:' ); ?></label>
						<input type="number" min="0" max="100" step="1" name="<?php echo esc_attr( $prefix . 'content-excerpt-length' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'content-excerpt-length', 30 ); ?>" />
					</div>

					<div class='ml-form-row ml-dt-row ml-color'>
						<label><?php esc_html_e( 'Color', 'mobiloud' ); ?></label>
						<input class="color-picker"
							name="<?php echo esc_attr( $prefix . 'content-color' ); ?>" type="text"
							value="<?php echo esc_attr( Mobiloud::get_option( $prefix . 'content-color', '#000' ) ); ?>"
						/>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="dt-admin__wrapper">
		<h2 class="dt-admin__section-title"><?php esc_html_e( 'Post and Pages' ); ?></h2>
		<?php $prefix = 'dt-post-page-'; ?>

		<div class="dt-admin__item-content">
			<!-- Post title -->
			<div class="dt-admin__item">
				<h4><?php esc_html_e( 'Post title' ); ?></h4>
				<div class="ml-dt-row ml-checkbox-wrap">
					<?php $is_checked = 'true' === Mobiloud::get_option( $prefix . 'title-toggle', 'true' ); ?>
					<input type="hidden" name="<?php echo esc_attr( $prefix . 'title-toggle' ); ?>">
					<input class="dt-list-cb-toggle" type="checkbox" id="<?php echo esc_attr( $prefix . 'title-toggle' ); ?>" name="<?php echo esc_attr( $prefix . 'title-toggle' ); ?>"
						value="true" <?php echo 'true' === Mobiloud::get_option( $prefix . 'title-toggle', 'true' ) ? 'checked' : ''; ?>/>
					<label for="<?php echo esc_attr( $prefix . 'title-toggle' ); ?>"><?php esc_html_e( 'Display post title' ); ?></label>
				</div>

				<div class="<?php echo esc_attr( $prefix . 'title-toggle' ); ?> <?php echo $is_checked ? esc_attr( $prefix . 'title-toggle--show' ) : '' ?>">
					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font family:' ); ?></label>
						<select name="<?php echo esc_attr( $prefix . 'title-font' ); ?>" id="">
						<?php foreach ( $dt_fonts as $font ) : ?>
							<option <?php selected( $font['value'], Mobiloud::get_option( $prefix . 'title-font', 'Roboto' ) ); ?> value="<?php echo esc_attr( $font['value'] ); ?>"><?php echo esc_html( $font['label'] ); ?></option>
						<?php endforeach ?>
						</select>
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font size:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'title-font-size' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'title-font-size', 1.8 ); ?>" />
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Line Height:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'title-line-height' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'title-line-height', 1.3 ); ?>" />
					</div>

					<div class='ml-form-row ml-dt-row ml-color'>
						<label><?php esc_html_e( 'Color', 'mobiloud' ); ?></label>
						<input class="color-picker"
							name="<?php echo esc_attr( $prefix . 'title-color' ); ?>" type="text"
							value="<?php echo esc_attr( Mobiloud::get_option( $prefix . 'title-color', '#000' ) ); ?>"
						/>
					</div>
				</div>
			</div>

			<!-- Post author -->
			<div class="dt-admin__item">
				<h4><?php esc_html_e( 'Post meta' ); ?></h4>
				<div class="ml-dt-row ml-checkbox-wrap">
					<?php $is_checked = 'true' === Mobiloud::get_option( $prefix . 'author-toggle', 'true' ); ?>
					<input type="hidden" name="<?php echo esc_attr( $prefix . 'author-toggle' ); ?>">
					<input class="dt-list-cb-toggle" type="checkbox" id="<?php echo esc_attr( $prefix . 'author-toggle' ); ?>" name="<?php echo esc_attr( $prefix . 'author-toggle' ); ?>"
						value="true" <?php echo 'true' === Mobiloud::get_option( $prefix . 'author-toggle', 'true' ) ? 'checked' : ''; ?>/>
					<label for="<?php echo esc_attr( $prefix . 'author-toggle' ); ?>"><?php esc_html_e( 'Display post author' ); ?></label>
				</div>

				<div class="<?php echo esc_attr( $prefix . 'author-toggle' ); ?> <?php echo $is_checked ? esc_attr( $prefix . 'author-toggle--show' ) : '' ?>">
					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font family:' ); ?></label>
						<select name="<?php echo esc_attr( $prefix . 'author-font' ); ?>" id="">
						<?php foreach ( $dt_fonts as $font ) : ?>
							<option <?php selected( $font['value'], Mobiloud::get_option( $prefix . 'author-font', 'Roboto' ) ); ?> value="<?php echo esc_attr( $font['value'] ); ?>"><?php echo esc_html( $font['label'] ); ?></option>
						<?php endforeach ?>
						</select>
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font size:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'author-font-size' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'author-font-size', 0.6785 ); ?>" />
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Line Height:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'author-line-height' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'author-line-height', 0.92 ); ?>" />
					</div>

					<div class='ml-form-row ml-dt-row ml-color'>
						<label><?php esc_html_e( 'Color', 'mobiloud' ); ?></label>
						<input class="color-picker"
							name="<?php echo esc_attr( $prefix . 'author-color' ); ?>" type="text"
							value="<?php echo esc_attr( Mobiloud::get_option( $prefix . 'author-color', '#000' ) ); ?>"
						/>
					</div>
				</div>

				<!-- Post category -->
				<div class="ml-dt-row ml-checkbox-wrap">
					<?php $is_checked = 'true' === Mobiloud::get_option( $prefix . 'category-toggle', 'true' ); ?>
					<input type="hidden" name="<?php echo esc_attr( $prefix . 'category-toggle' ); ?>">
					<input class="dt-list-cb-toggle" type="checkbox" id="<?php echo esc_attr( $prefix . 'category-toggle' ); ?>" name="<?php echo esc_attr( $prefix . 'category-toggle' ); ?>"
						value="true" <?php echo 'true' === Mobiloud::get_option( $prefix . 'category-toggle', 'true' ) ? 'checked' : ''; ?>/>
					<label for="<?php echo esc_attr( $prefix . 'category-toggle' ); ?>"><?php esc_html_e( 'Display post category' ); ?></label>
				</div>

				<div class="<?php echo esc_attr( $prefix . 'category-toggle' ); ?> <?php echo $is_checked ? esc_attr( $prefix . 'category-toggle--show' ) : '' ?>">
					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font family:' ); ?></label>
						<select name="<?php echo esc_attr( $prefix . 'category-font' ); ?>" id="">
						<?php foreach ( $dt_fonts as $font ) : ?>
							<option <?php selected( $font['value'], Mobiloud::get_option( $prefix . 'category-font', 'Roboto' ) ); ?> value="<?php echo esc_attr( $font['value'] ); ?>"><?php echo esc_html( $font['label'] ); ?></option>
						<?php endforeach ?>
						</select>
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font size:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'category-font-size' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'category-font-size', 0.6785 ); ?>" />
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Line Height:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'category-line-height' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'category-line-height', 0.92 ); ?>" />
					</div>

					<div class='ml-form-row ml-dt-row ml-color'>
						<label><?php esc_html_e( 'Color', 'mobiloud' ); ?></label>
						<input class="color-picker"
							name="<?php echo esc_attr( $prefix . 'category-color' ); ?>" type="text"
							value="<?php echo esc_attr( Mobiloud::get_option( $prefix . 'category-color', '#000' ) ); ?>"
						/>
					</div>
				</div>

				<!-- Post date -->
				<div class="ml-dt-row ml-checkbox-wrap">
					<?php $is_checked = 'true' === Mobiloud::get_option( $prefix . 'date-toggle', 'true' ); ?>
					<input type="hidden" name="<?php echo esc_attr( $prefix . 'date-toggle' ); ?>">
					<input class="dt-list-cb-toggle" type="checkbox" id="<?php echo esc_attr( $prefix . 'date-toggle' ); ?>" name="<?php echo esc_attr( $prefix . 'date-toggle' ); ?>"
						value="true" <?php echo 'true' === Mobiloud::get_option( $prefix . 'date-toggle', 'true' ) ? 'checked' : ''; ?>/>
					<label for="<?php echo esc_attr( $prefix . 'date-toggle' ); ?>"><?php esc_html_e( 'Display post date' ); ?></label>
				</div>

				<div class="<?php echo esc_attr( $prefix . 'date-toggle' ); ?> <?php echo $is_checked ? esc_attr( $prefix . 'date-toggle--show' ) : '' ?>">
					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font family:' ); ?></label>
						<select name="<?php echo esc_attr( $prefix . 'date-font' ); ?>" id="">
						<?php foreach ( $dt_fonts as $font ) : ?>
							<option <?php selected( $font['value'], Mobiloud::get_option( $prefix . 'date-font', 'Roboto' ) ); ?> value="<?php echo esc_attr( $font['value'] ); ?>"><?php echo esc_html( $font['label'] ); ?></option>
						<?php endforeach ?>
						</select>
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font size:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'date-font-size' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'date-font-size', 0.85 ); ?>" />
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Line Height:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'date-line-height' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'date-line-height', 0.92 ); ?>" />
					</div>

					<div class='ml-form-row ml-dt-row ml-color'>
						<label><?php esc_html_e( 'Color', 'mobiloud' ); ?></label>
						<input class="color-picker"
							name="<?php echo esc_attr( $prefix . 'date-color' ); ?>" type="text"
							value="<?php echo esc_attr( Mobiloud::get_option( $prefix . 'date-color', '#000' ) ); ?>"
						/>
					</div>
				</div>
			</div>

			<!-- Post content -->
			<div class="dt-admin__item">
				<h4><?php esc_html_e( 'Post content' ); ?></h4>
				<div class="ml-dt-row ml-checkbox-wrap">
					<?php $is_checked = 'true' === Mobiloud::get_option( $prefix . 'content-toggle', 'true' ); ?>
					<input type="hidden" name="<?php echo esc_attr( $prefix . 'content-toggle' ); ?>">
					<input class="dt-list-cb-toggle" type="checkbox" id="<?php echo esc_attr( $prefix . 'content-toggle' ); ?>" name="<?php echo esc_attr( $prefix . 'content-toggle' ); ?>"
						value="true" <?php echo 'true' === Mobiloud::get_option( $prefix . 'content-toggle', 'true' ) ? 'checked' : ''; ?>/>
					<label for="<?php echo esc_attr( $prefix . 'content-toggle' ); ?>"><?php esc_html_e( 'Display content' ); ?></label>
				</div>

				<div class="<?php echo esc_attr( $prefix . 'content-toggle' ); ?> <?php echo $is_checked ? esc_attr( $prefix . 'content-toggle--show' ) : '' ?>">
					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font family:' ); ?></label>
						<select name="<?php echo esc_attr( $prefix . 'content-font' ); ?>" id="">
						<?php foreach ( $dt_fonts as $font ) : ?>
							<option <?php selected( $font['value'], Mobiloud::get_option( $prefix . 'content-font', 'Roboto' ) ); ?> value="<?php echo esc_attr( $font['value'] ); ?>"><?php echo esc_html( $font['label'] ); ?></option>
						<?php endforeach ?>
						</select>
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Font size:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'content-font-size' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'content-font-size', 0.85 ); ?>" />
					</div>

					<div class='ml-dt-row'>
						<label><?php esc_html_e( 'Line Height:' ); ?></label>
						<input type="number" min="0" max="50" step="0.0001" name="<?php echo esc_attr( $prefix . 'content-line-height' ); ?>" value="<?php echo Mobiloud::get_option( $prefix . 'content-line-height', 1.2 ); ?>" />
					</div>

					<div class='ml-form-row ml-dt-row ml-color'>
						<label><?php esc_html_e( 'Color', 'mobiloud' ); ?></label>
						<input class="color-picker"
							name="<?php echo esc_attr( $prefix . 'content-color' ); ?>" type="text"
							value="<?php echo esc_attr( Mobiloud::get_option( $prefix . 'content-color', '#000' ) ); ?>"
						/>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>