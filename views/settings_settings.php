<?php
wp_nonce_field( 'tab_settings', 'ml_nonce' );
wp_nonce_field( 'load_ajax', 'ml_nonce_load_ajax' );

function ml_cache_block( string $title, string $option_number, string $option_is_private, string $endpoint, bool $disable_private = false ) {
	$default_number          = MLAPI::cache_default_age( $endpoint );
	$default_type_is_private = MLAPI::cache_default_is_private( $endpoint );
	$is_private              = (bool) Mobiloud::get_option( $option_is_private, $default_type_is_private );
	?>
	<div class="ml-col-row cache-control-block app-v2-only-feature">
		<div class="ml-col-half-small">
			<p><?php echo esc_html( $title ); ?></p>
		</div>
		<div class="ml-col-half-wide">
			<label class="cache-option-age-label">max-age: <input type="number" class="cache-type-number" min="0" step="1" name="<?php echo esc_attr( $option_number ); ?>" value=<?php echo esc_attr( Mobiloud::get_option( $option_number, $default_number ) ); ?>> min</label>
			<label class="cache-option-type-label"><input type="radio" class="cache-type-checkbox" name="<?php echo esc_attr( $option_is_private ); ?>" value="0" <?php checked( ! $is_private ); ?>>Public</label>
			<label class="cache-option-type-label"><input type="radio" class="cache-type-checkbox" name="<?php echo esc_attr( $option_is_private ); ?>" value="1" <?php checked( $is_private ); ?> <?php disabled( $disable_private ); ?>>Private</label>
		</div>
	</div>
	<?php
}
?>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php echo esc_html( Mobiloud_Admin::$settings_tabs[ $active_tab ]['title'] ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<p>The options on this page let you define exactly what content is presented in the app's home screen, article lists and single article screens.</p>
		<p>Any questions or need some help? Contact us at <a class="contact" href="mailto:support@mobiloud.com">support@mobiloud.com</a></p>
	</div>
</div>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Application details', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<h4>Email Contact</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Setup email contact details.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_show_email_contact_link" name="ml_show_email_contact_link"
						value="true" <?php echo Mobiloud::get_option( 'ml_show_email_contact_link' ) ? 'checked' : ''; ?>/>
					<label for="ml_show_email_contact_link">Show email contact link?</label>
				</div>
				<div class="ml-email-contact-row ml-form-row"
					<?php
					echo ! Mobiloud::get_option( 'ml_show_email_contact_link' ) ? 'style="display:none;"' : '';
					?>
					>
					<label for="ml_contact_link_email">Enter public email address</label>
					<input id="ml_contact_link_email" type="text" size="36" name="ml_contact_link_email"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_contact_link_email', '' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Copyright Notice</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Enter the copyright notice which will be displayed in your app's settings screen.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<textarea id="ml_copyright_string" name="ml_copyright_string" rows="4"
					style="width:100%"><?php echo esc_attr( Mobiloud::get_option( 'ml_copyright_string', '' ) ); ?></textarea>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Home Screen Settings', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<div class="ml-col-row">
			<h4>Choose what to show on your app's home screen.</h4>
			<div class="ml-radio-wrap">
				<input type="radio" id="ml_home_article_list_enabled" name="homepagetype"
					value="ml_home_article_list_enabled" <?php echo get_option( 'ml_home_article_list_enabled', true ) ? 'checked' : ''; ?>/>
				<label for="ml_home_article_list_enabled">Article List (Recommended)</label>
			</div>
			<div class="ml-radio-wrap">
				<input type="radio" id="ml_home_page_enabled" name="homepagetype"
					value="ml_home_page_enabled" <?php echo get_option( 'ml_home_page_enabled' ) ? 'checked' : ''; ?>/>
				<label for="ml_home_page_enabled">Page contents</label>
				<select name="ml_home_page_id" style="max-width: 460px;">
					<option value="">Select a page</option>
					<?php $pages = get_pages(); ?>
					<?php
					foreach ( $pages as $p ) {
						$selected = '';
						if ( Mobiloud::get_option( 'ml_home_page_id' ) == $p->ID ) {
							$selected = 'selected="selected"';
						}
						?>
						<option value="<?php echo esc_attr( $p->ID ); ?>" <?php echo esc_attr( $selected ); ?>>
							<?php echo esc_html( $p->post_title ); ?>
						</option>
						<?php
					}
					?>
				</select>
			</div>
			<div class="ml-radio-wrap">
				<input type="radio" id="ml_home_url_enabled" name="homepagetype"
					value="ml_home_url_enabled" <?php echo get_option( 'ml_home_url_enabled' ) ? 'checked' : ''; ?>/>
				<label for="ml_home_url_enabled">URL (e.g. homepage)</label>
				<input id="ml_home_url" placeholder="http://" name="ml_home_url" type="url"
					value="<?php echo get_option( 'ml_home_url_enabled' ) ? esc_url( get_option( 'ml_home_url' ) ) : ''; ?>">
			</div>
		</div>

		<div class="ml-form-row ml-home-screen-label ml-list-disabled">
			<label>Articles Menu Item</label>
			<p>Enter the label you'd like to use for the 'Articles' menu item, letting users list your articles.</p>
			<div class="ml-form-row ml-checkbox-wrap">
				<input type="checkbox" id="ml_show_article_list_menu_item" name="ml_show_article_list_menu_item"
					value="true" <?php echo Mobiloud::get_option( 'ml_show_article_list_menu_item' ) ? 'checked' : ''; ?>/>
				<label for="ml_show_article_list_menu_item">Show 'Article' list menu item</label>
			</div>
			<input type='text' id='ml_article_list_menu_item_title' name='ml_article_list_menu_item_title'
				value='<?php echo esc_attr( Mobiloud::get_option( 'ml_article_list_menu_item_title', 'Articles' ) ); ?>'/>
		</div>


		<h4 class="ml-list-enabled">Custom Post Types</h4>
		<div id="mlconf__settings__search-checkbox-list-post-types" class='ml-col-row ml-list-enabled'>
		</div>

		<h4 class="ml-list-enabled">Categories</h4>
		<div id="mlconf__settings__search-checkbox-list-categories" class='ml-col-row ml-list-enabled'>
		</div>

		<h4 class="ml-list-enabled">Custom Taxonomies</h4>
		<div class='ml-col-row ml-list-enabled'>
			<div class='ml-col-half'>
				<p>Select which taxonomies should be included in the article list.</p>
				<?php Mobiloud_Admin::load_ajax_insert( 'settings_tax' ); ?>
			</div>
		</div>

		<h4 class="ml-list-enabled">Restrict search results</h4>
		<div class='ml-col-row ml-list-enabled'>
			<div class='ml-col-half'>
				<p>Prevent results from unchecked categories from being displayed in search results.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_restrict_search_results" name="ml_restrict_search_results"
						value="true" <?php echo Mobiloud::get_option( 'ml_restrict_search_results' ) ? 'checked' : ''; ?>/>
					<label for="ml_restrict_search_results">If checked, search results should only display post types,
						categories and taxonomies that were selected in the settings.</label>
				</div>

			</div>
		</div>

		<h4>Sticky categories</h4>
		<div class='ml-col-row ml-list-enabled'>
			<div class='ml-col-half'>
				<p>The first posts from each sticky category are displayed before all others in the app's article
					list.</p>
			</div>
			<div class='ml-col-half'>
				<div class='ml-form-row ml-left-align clearfix'>
					<label class='ml-width-120'>First category</label>
					<?php Mobiloud_Admin::load_ajax_insert( 'settings_sticky_cat_1' ); ?>
					<label>No. of Posts</label>
					<input type='text' size='2' id='ml_sticky_category_1_posts' name='ml_sticky_category_1_posts'
						value='<?php echo esc_attr( Mobiloud::get_option( 'ml_sticky_category_1_posts', 3 ) ); ?>'/>
				</div>
				<div class='ml-form-row ml-left-align clearfix'>
					<label class='ml-width-120'>Second category</label>
					<?php Mobiloud_Admin::load_ajax_insert( 'settings_sticky_cat_2' ); ?>
					<label>No. of Posts</label>
					<input type='text' size='2' id='ml_sticky_category_2_posts' name='ml_sticky_category_2_posts'
						value='<?php echo esc_attr( Mobiloud::get_option( 'ml_sticky_category_2_posts', 3 ) ); ?>'/>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Article List settings', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<?php
			$template = get_option( 'ml-templates', 'legacy' );
			$template_types = array(
				'default' => 'Default',
				'legacy'  => 'Legacy',
			);
		?>

		<?php if ( 'legacy' === $template ) : ?>
			<div class="ml_legacy_settings">
				<h4>Date display options</h4>
				<div class="ml-col-row">
					<div class="ml-radio-wrap">
						<input type="radio" id="ml_date_type_pretty" name="ml_datetype"
							value="prettydate" <?php echo get_option( 'ml_datetype', 'prettydate' ) === 'prettydate' ? 'checked' : ''; ?>/>
						<label for="ml_date_type_pretty">Show pretty dates (e.g. "2 hours ago")</label>
					</div>
					<div class="ml-radio-wrap">
						<input type="radio" id="ml_date_type_date" name="ml_datetype"
							value="datetime" <?php echo get_option( 'ml_datetype', 'prettydate' ) === 'datetime' ? 'checked' : ''; ?>/>
						<label for="ml_date_type_date">Show full dates</label>
						<input name="ml_dateformat" id="ml_dateformat" type="text"
							value="<?php echo esc_attr( get_option( 'ml_dateformat', 'F j, Y' ) ); ?>"/>
					</div>
				</div>

				<h4>List preferences</h4>
				<div class='ml-col-row'>
					<div class='ml-col-half'>
						<p>Adjust how your content will show in article lists, affecting your app's main list as well as
							category lists.</p>
					</div>
					<div class='ml-col-half'>
						<div class="ml-form-row ml-checkbox-wrap">
							<input type="checkbox" id="ml_article_list_enable_dates" name="ml_article_list_enable_dates"
								value="true" <?php echo Mobiloud::get_option( 'ml_article_list_enable_dates' ) ? 'checked' : ''; ?>/>
							<label for="ml_article_list_enable_dates">Show post dates in the list</label>
						</div>
						<div class="ml-form-row ml-checkbox-wrap no-margin">
							<input type="checkbox" id="ml_article_list_show_excerpt" name="ml_article_list_show_excerpt"
								value="true" <?php echo Mobiloud::get_option( 'ml_article_list_show_excerpt' ) ? 'checked' : ''; ?>/>
							<label for="ml_article_list_show_excerpt">Show excerpts in article list</label>
						</div>
						<div class="ml-form-row ml-checkbox-wrap no-margin">
							<input type="checkbox" id="ml_article_list_show_comment_count"
								name="ml_article_list_show_comment_count"
								value="true" <?php echo Mobiloud::get_option( 'ml_article_list_show_comment_count' ) ? 'checked' : ''; ?>/>
							<label for="ml_article_list_show_comment_count">Show comments count in article list</label>
						</div>
						<div class="ml-form-row ml-checkbox-wrap no-margin ml-list-type-web-only">
							<input type="checkbox" id="ml_article_list_show_category"
								name="ml_article_list_show_category"
								value="true" <?php echo Mobiloud::get_option( 'ml_article_list_show_category' ) ? 'checked' : ''; ?>/>
							<label for="ml_article_list_show_category">Show category in article list</label>
						</div>
						<div class="ml-form-row ml-checkbox-wrap no-margin ml-list-type-web-only">
							<input type="checkbox" id="ml_article_list_show_author"
								name="ml_article_list_show_author"
								value="true" <?php echo Mobiloud::get_option( 'ml_article_list_show_author' ) ? 'checked' : ''; ?>/>
							<label for="ml_article_list_show_author">Show author in article list</label>
						</div>
						<div class="ml-form-row ml-checkbox-wrap no-margin">
							<input type="checkbox" id="ml_original_size_image_list" name="ml_original_size_image_list"
								value="true" <?php echo Mobiloud::get_option( 'ml_original_size_image_list', true ) ? 'checked' : ''; ?>/>
							<label for="ml_original_size_image_list">Resize article cards in the list to follow the original
								image proportions</label>
						</div>

					</div>
				</div>
				<?php
				$is_excerpt_style = ( Mobiloud::get_option( 'ml_article_list_view_type', 'extended' ) === 'extended' )
				&& ( Mobiloud::get_option( 'ml_article_list_show_excerpt' ) ) ? '' : ' style="display:none;"';
				?>
				<h4 class="show_excerpt_1" <?php echo esc_attr( $is_excerpt_style ); ?>>Excerpts length</h4>
				<div class='ml-col-row show_excerpt_1' <?php echo esc_attr( $is_excerpt_style ); ?>>
					<div class='ml-col-half'>
						<p>You can define a maximum length for excerpts in number of words.</p>
					</div>
					<div class='ml-col-half'>
						<div class="ml-form-row">
							<input type="number" id="ml_excerpt_length" name="ml_excerpt_length" min="1" max="10000"
								value="<?php echo esc_attr( Mobiloud::get_option( 'ml_excerpt_length', 100 ) ); ?>"/>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<h4>Number of articles</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Number of articles returned in each request.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input type="number" id="ml_articles_per_request" name="ml_articles_per_request" min="1" max="100"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_articles_per_request', 15 ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Custom field in article list</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Your app's article list can show data from a Custom Field (e.g. author, price, source) defined in
					your posts.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_custom_field_enable" name="ml_custom_field_enable"
						value="true" <?php echo Mobiloud::get_option( 'ml_custom_field_enable' ) ? 'checked' : ''; ?>/>
					<label for="ml_custom_field_enable">Show custom field in article list</label>
				</div>
				<div class="ml-form-row ml-left-align clearfix">
					<label class='ml-width-120' for="ml_custom_field_name">Field Name</label>
					<input type="text" placeholder="Custom Field Name" id="ml_custom_field_name"
						name="ml_custom_field_name"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_custom_field_name' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Default Article Image</h4>
		<div class='ml-col-row' id="ml_default_article_image">
			<div class='ml-col-half'>
				<p>Image to display when an article doesn't have a featured image set.</p>
			</div>
			<div class='ml-col-half'>
				<label>Default Image</label><br/>
				<input class="image-selector" id="ml_default_featured_image" type="text" size="36" name="ml_default_featured_image"
					value="<?php echo esc_attr( Mobiloud::get_option( 'ml_default_featured_image' ) ); ?>"/>
				<input id="ml_default_featured_image_button" type="button" value="Upload Image" class="browser button"/>

				<?php $imagePath = Mobiloud::get_option( 'ml_default_featured_image' ); ?>
				<div class='ml-preview-image-holder'>
					<img src='<?php echo esc_url( $imagePath ); ?>'/>
				</div>
				<a href='#' class='ml-preview-image-remove-btn'>Remove image</a>
			</div>
		</div>
	</div>
</div>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Post and Page screen settings', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
	<h4>Featured image in the article screen</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>You can switch display or hide the featured image in the article screen. You can also add featured
					images manually using the Editor functionality, <a target="_blank"
						href="https://www.mobiloud.com/help/knowledge-base/featured-images/?utm_source=wp-plugin-admin&utm_medium=web&utm_campaign=content_page">read
						our guide</a>.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_show_article_featuredimage" name="ml_show_article_featuredimage"
						value="true" <?php echo Mobiloud::get_option( 'ml_show_article_featuredimage' ) ? 'checked' : ''; ?>/>
					<label for="ml_show_article_featuredimage">Show featured image</label>
				</div>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_original_size_featured_image" name="ml_original_size_featured_image"
						value="true" <?php echo Mobiloud::get_option( 'ml_original_size_featured_image' ) ? 'checked' : ''; ?>/>
					<label for="ml_original_size_featured_image">Show featured images respecting the original image
						proportions</label>
				</div>
			</div>
		</div>

		<h4>Image galleries</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Your app will ignore links attached to images to ensure that these open in the built-in image
					gallery. If instead you'd prefer image links to work inside the app, you can change this setting
					accordingly.</p>
				<p>As an exception, say to allow an image banner within the content to load an external link while
					ensuring other images are always opened in the gallery, you can assign the class
					<i>ml_followlinks</i> to the image banner.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_followimagelinks" name="ml_followimagelinks"
						value="1" <?php echo Mobiloud::get_option( 'ml_followimagelinks' ) ? 'checked' : ''; ?>/>
					<label for="ml_followimagelinks">Load links instead of image gallery for images with links</label>
				</div>
			</div>
		</div>

		<h4>Post and page meta information</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Change which meta elements of your posts and pages should be displayed in the post and page screens.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_post_author_enabled" name="ml_post_author_enabled"
						value="true" <?php echo Mobiloud::get_option( 'ml_post_author_enabled' ) ? 'checked' : ''; ?>/>
					<label for="ml_post_author_enabled">Show author in posts</label>
				</div>
				<div class="ml-form-row ml-checkbox-wrap no-margin">
					<input type="checkbox" id="ml_page_author_enabled" name="ml_page_author_enabled"
						value="true" <?php echo Mobiloud::get_option( 'ml_page_author_enabled' ) ? 'checked' : ''; ?>/>
					<label for="ml_page_author_enabled">Show author in pages</label>
				</div>
				<div class="ml-form-row ml-checkbox-wrap no-margin">
					<input type="checkbox" id="ml_post_date_enabled" name="ml_post_date_enabled"
						value="true" <?php echo Mobiloud::get_option( 'ml_post_date_enabled' ) ? 'checked' : ''; ?>/>
					<label for="ml_post_date_enabled">Show date in posts</label>
				</div>
				<div class="ml-form-row ml-checkbox-wrap no-margin">
					<input type="checkbox" id="ml_page_date_enabled" name="ml_page_date_enabled"
						value="true" <?php echo Mobiloud::get_option( 'ml_page_date_enabled' ) ? 'checked' : ''; ?>/>
					<label for="ml_page_date_enabled">Show date in pages</label>
				</div>
				<div class="ml-form-row ml-checkbox-wrap no-margin">
					<input type="checkbox" id="ml_post_title_enabled" name="ml_post_title_enabled"
						value="true" <?php echo Mobiloud::get_option( 'ml_post_title_enabled' ) ? 'checked' : ''; ?>/>
					<label for="ml_post_title_enabled">Show title in posts</label>
				</div>
				<div class="ml-form-row ml-checkbox-wrap no-margin">
					<input type="checkbox" id="ml_page_title_enabled" name="ml_page_title_enabled"
						value="true" <?php echo Mobiloud::get_option( 'ml_page_title_enabled' ) ? 'checked' : ''; ?>/>
					<label for="ml_page_title_enabled">Show title in pages</label>
				</div>
			</div>
		</div>

		<h4>Internal links</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Your app can open internal links (e.g. to posts, pages or categories) and open them in the native article or category views. You can disable this and links will open in the internal browser normally used for external links.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_internal_links" name="ml_internal_links"
						value="true" <?php echo Mobiloud::get_option( 'ml_internal_links' ) ? 'checked' : ''; ?>/>
					<label for="ml_internal_links">Open internal links in native views</label>
				</div>
			</div>
		</div>

		<h4>Ignore shortcodes for in-app articles</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Add a list of shortcodes, separated by commas. The shortcodes in the list should all be ignored and removed from the content of posts.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<textarea id="ml_ignore_shortcodes" name="ml_ignore_shortcodes" rows="4"
						style="width:100%"><?php echo esc_attr( implode( ', ', Mobiloud::get_option( 'ml_ignore_shortcodes', [] ) ) ); ?></textarea>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Dark mode', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<div class='ml-col-row'>
			<p>This setting determines if the dark mode option should be displayed in the app's settings screen or not. If disabled, dark mode should not be applied to the app at all, even if the user previously enabled it.</p>
			<div class="ml-form-row ml-checkbox-wrap">
				<input type="checkbox" id="ml_dark_mode_enabled" name="ml_dark_mode_enabled"
					value="true" <?php checked( Mobiloud::get_option( 'ml_dark_mode_enabled' ) ); ?>/>
				<label for="ml_dark_mode_enabled">Enable Dark mode</label>
			</div>
		</div>

		<h4>Dark mode logo</h4>
		<div class="ml-col-row">
			<div class="ml-col-half">
				<p>Custom version of the logo used in the app's header when dark mode is enabled on the user device.</p>
			</div>
			<div class="ml-col-half">
				<?php $logo = Mobiloud::get_option( 'ml_dark_mode_logo', '' ); ?>

				<input type="text" size="36" name="ml_dark_mode_logo" class="upload-logo-input"
					value="<?php echo esc_url( $logo ); ?>"/>
				<input type="button" value="Upload Image" class="browser button upload-logo-button"/>
				<div class="clearfix"></div>
				<div class="ml-col-half ml-preview-upload-image-row" <?php echo ( strlen( $logo ) === 0 ) ? 'style="display:none;"' : ''; ?>>
					<div class="ml-preview-image-holder">
						<img src="<?php echo esc_url( $logo ); ?>" class="upload-logo-view"/>
					</div>
					<a href="#" class="ml-preview-image-remove-btn upload-logo-clean">Remove logo</a>
				</div>
			</div>
		</div>

		<h4>Header color</h4>
		<div class="ml-col-row ml-color">
			<div class="ml-col-half">
				<p>Custom color for the header background when dark mode is enabled.</p>
			</div>
			<div class="ml-col-half">
				<input  class="color-picker" name="ml_dark_mode_header_color" type="text"
					value="<?php echo esc_attr( get_option( 'ml_dark_mode_header_color' ) ); ?>"/>
			</div>
		</div>

		<h4>Tabbed navigation color</h4>
		<div class="ml-col-row ml-color">
			<div class="ml-col-half">
				<p>Custom color for the tabbed navigation background when dark mode is enabled.</p>
			</div>
			<div class="ml-col-half">
				<input  class="color-picker" name="ml_dark_mode_tabbed_navigation_color" type="text"
					value="<?php echo esc_attr( get_option( 'ml_dark_mode_tabbed_navigation_color' ) ); ?>"/>
			</div>
		</div>

		<h4>Tabbed navigation icons color</h4>
		<div class="ml-col-row ml-color">
			<div class="ml-col-half">
				<p>Custom color for the icons displayed in the tabbed navigation when dark mode is enabled</p>
			</div>
			<div class="ml-col-half">
				<input  class="color-picker" name="ml_dark_mode_tabbed_navigation_icons_color" type="text"
					value="<?php echo esc_attr( get_option( 'ml_dark_mode_tabbed_navigation_icons_color' ) ); ?>"/>
			</div>
		</div>

		<h4>Tabbed navigation active icon color</h4>
		<div class="ml-col-row ml-color">
			<div class="ml-col-half">
				<p>Custom color for the tabbed navigation active icon color when dark mode is enabled</p>
			</div>
			<div class="ml-col-half">
				<input  class="color-picker" name="ml_dark_mode_tabbed_navigation_active_icon_color" type="text"
					value="<?php echo esc_attr( get_option( 'ml_dark_mode_tabbed_navigation_active_icon_color' ) ); ?>"/>
			</div>
		</div>

		<h4>Notification switch main color</h4>
		<div class="ml-col-row ml-color">
			<div class="ml-col-half">
				<p>Custom color for the notification switch main color when dark mode is enabled</p>
			</div>
			<div class="ml-col-half">
				<input  class="color-picker" name="ml_dark_mode_notification_switch_main_color" type="text"
					value="<?php echo esc_attr( get_option( 'ml_dark_mode_notification_switch_main_color' ) ); ?>"/>
			</div>
		</div>

		<h4>Notification switch background color</h4>
		<div class="ml-col-row ml-color">
			<div class="ml-col-half">
				<p>Custom color for the notification switch background color when dark mode is enabled</p>
			</div>
			<div class="ml-col-half">
				<input  class="color-picker" name="ml_dark_mode_notification_switch_background_color" type="text"
					value="<?php echo esc_attr( get_option( 'ml_dark_mode_notification_switch_background_color' ) ); ?>"/>
			</div>
		</div>

		<h4>Hamburger menu background color</h4>
		<div class="ml-col-row ml-color">
			<div class="ml-col-half">
				<p>Custom color for the hamburger menu background color when dark mode is enabled</p>
			</div>
			<div class="ml-col-half">
				<input  class="color-picker" name="ml_dark_mode_hamburger_menu_background_color" type="text"
					value="<?php echo esc_attr( get_option( 'ml_dark_mode_hamburger_menu_background_color' ) ); ?>"/>
			</div>
		</div>

		<h4>Hamburger menu text color</h4>
		<div class="ml-col-row ml-color">
			<div class="ml-col-half">
				<p>Custom color for the hamburger menu text color when dark mode is enabled</p>
			</div>
			<div class="ml-col-half">
				<input  class="color-picker" name="ml_dark_mode_hamburger_menu_text_color" type="text"
					value="<?php echo esc_attr( get_option( 'ml_dark_mode_hamburger_menu_text_color' ) ); ?>"/>
			</div>
		</div>

		<h4>Custom CSS</h4>
		<div class="ml-col-row">
			<label>Custom CSS to be injected in the app's content when dark mode is enabled, allowing us to customize the appearance of the content in dark mode specifically.</label>
			<textarea class="ml-editor-area ml-editor-area-css ml-show" name="ml_dark_mode_custom_css"><?php echo esc_html( Mobiloud::get_option( 'ml_dark_mode_custom_css', '' ) ); ?></textarea>
		</div>
	</div>
</div>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Related Posts', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<div class='ml-col-row'>
			<p>In order to use the Related Posts feature you will need to install the Jetpack Plugin and make sure the "Related Posts" feature is enabled.</p>
			<div class="ml-form-row ml-checkbox-wrap">
				<input type="checkbox" id="ml_related_posts" name="ml_related_posts"
					value="true" <?php echo Mobiloud::get_option( 'ml_related_posts' ) ? 'checked' : ''; ?>/>
				<label for="ml_related_posts">Enable Related Posts</label>
			</div>
		</div>

		<h4 class='ml-related-items'>Header</h4>
		<div class='ml-col-row ml-related-items'>
			<div class='ml-col-half'>
				<p>Enter the header you'd like to use.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input id="ml_related_header" type="text" size="36" name="ml_related_header"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_related_header', '' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4 class='ml-related-items'>Items</h4>
		<div class='ml-col-row ml-related-items'>
			<div class='ml-col-half'>
				<p>Check items you'd like to show.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_related_image" name="ml_related_image"
						value="true" <?php echo Mobiloud::get_option( 'ml_related_image' ) ? 'checked' : ''; ?>/>
					<label for="ml_related_image">Show Image</label>
				</div>
				<div class="ml-form-row ml-checkbox-wrap no-margin">
					<input type="checkbox" id="ml_related_excerpt" name="ml_related_excerpt"
						value="true" <?php echo Mobiloud::get_option( 'ml_related_excerpt' ) ? 'checked' : ''; ?>/>
					<label for="ml_related_excerpt">Show Excerpt</label>
				</div>
				<div class="ml-form-row ml-checkbox-wrap no-margin">
					<input type="checkbox" id="ml_related_date" name="ml_related_date"
						value="true" <?php echo Mobiloud::get_option( 'ml_related_date' ) ? 'checked' : ''; ?>/>
					<label for="ml_related_date">Show Date</label>
				</div>

			</div>
		</div>
	</div>
</div>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Commenting settings', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Select the commenting system you'd like to use in your app.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<div class="ml-radio-wrap">
						<input type="radio" id="ml_comments_system_wordpress" name="ml_comments_system"
							value="wordpress" <?php echo get_option( 'ml_comments_system', 'wordpress' ) === 'wordpress' ? 'checked' : ''; // phpcs:ignore WordPress.WP.CapitalPDangit.Misspelled ?>/>
						<label for="ml_comments_system_wordpress">WordPress</label>
					</div>

					<div class='ml-form-row ml-rest-api-row ml-checkbox-wrap' <?php echo Mobiloud::get_option( 'ml_comments_system', 'wordpress' ) === 'wordpress' ? '' : 'style="display: none;"'; // phpcs:ignore WordPress.WP.CapitalPDangit.Misspelled ?>>
						<input type="checkbox" id="ml_comments_rest_api" name="ml_comments_rest_api_enabled" value="true"
							<?php echo get_option( 'ml_comments_rest_api_enabled', 'none' ) === 1 ? 'checked' : ''; ?> />
						<label for="ml_comments_rest_api">
							Allow users to comment without an account from the app and via the WordPress API
						</label>
						<p id="ml_comments_rest_api_enabled_msg" class="error-message hidden">This enables anyone to post comments to the site using the WordPress API</p>
					</div>

					<div class="ml-radio-wrap">
						<input type="radio" id="ml_comments_system_disqus" name="ml_comments_system"
							value="disqus" <?php echo get_option( 'ml_comments_system', 'wordpress' ) === 'disqus' ? 'checked' : ''; // phpcs:ignore WordPress.WP.CapitalPDangit.Misspelled ?>/>
						<label for="ml_comments_system_disqus">Disqus</label>
					</div>
					<div class="ml-radio-wrap">
						<input type="radio" id="ml_comments_system_facebook" name="ml_comments_system"
							value="facebook" <?php echo get_option( 'ml_comments_system', 'wordpress' ) === 'facebook' ? 'checked' : ''; // phpcs:ignore WordPress.WP.CapitalPDangit.Misspelled ?>/>
						<label for="ml_comments_system_facebook">Facebook Comments</label>
					</div>
					<div class="ml-radio-wrap">
						<input type="radio" id="ml_comments_system_disabled" name="ml_comments_system"
							value="disabled" <?php echo get_option( 'ml_comments_system', 'wordpress' ) === 'disabled' ? 'checked' : ''; // phpcs:ignore WordPress.WP.CapitalPDangit.Misspelled ?>/>
						<label for="ml_comments_system_disabled">Comments should be disabled</label>
					</div>
				</div>
				<div
					class="ml-disqus-row ml-form-row" <?php echo Mobiloud::get_option( 'ml_comments_system', 'wordpress' ) === 'disqus' ? '' : 'style="display: none;"'; // phpcs:ignore WordPress.WP.CapitalPDangit.Misspelled ?>>
					<label>Disqus shortname <span class="required">*</span></label>
					<input name="ml_disqus_shortname" id="ml_disqus_shortname" type="text"
						value="<?php echo esc_attr( get_option( 'ml_disqus_shortname', '' ) ); ?>"/>
					<p>A shortname is the unique identifier assigned to a Disqus site. All the comments posted to a site
						are referenced with the shortname.
						See <a href="#">how to find your shortname</a>.</p>
				</div>
			</div>
		</div>

		<p>
			<div class='ml-col-row ml-color'>
				<div class='ml-col-half'>
					<p><?php esc_html_e( 'Setup commenting UI colors.', 'mobiloud' ); ?></p>
				</div>
				<div class="ml-col-half">
					<p>
						<input class="color-picker"
							name="ml_commenting_bg_ui_color" type="text"
							value="<?php echo esc_attr( get_option( 'ml_commenting_bg_ui_color' ) ); ?>"
						/>
						<label><?php esc_html_e( 'Background color', 'mobiloud' ); ?></label>
					</p>

					<p>
						<input class="color-picker"
							name="ml_commenting_fg_ui_color" type="text"
							value="<?php echo esc_attr( get_option( 'ml_commenting_fg_ui_color' ) ); ?>"
						/>
						<label><?php esc_html_e( 'Foreground color', 'mobiloud' ); ?></label>
					</p>
				</div>
			</div>
		</p>

		<p>
			<div class='ml-col-row ml-color'>
				<div class='ml-col-half'>
					<p><?php esc_html_e( 'Toggle comment nonce.', 'mobiloud' ); ?></p>
				</div>
				<?php
					$toggle_nonce = Mobiloud::get_option( 'ml_commenting_toggle_nonce', 'yes' );
				?>
				<div class="ml-col-half">
					<div class="ml-radio-wrap">
						<input
							name="ml_commenting_toggle_nonce" type="radio"
							value="yes"
							<?php checked( $toggle_nonce, 'yes' ); ?>
						/>
						<label><?php esc_html_e( 'Enable', 'mobiloud' ); ?></label>
					</div>
					<div class="ml-radio-wrap">
						<input
							name="ml_commenting_toggle_nonce" type="radio"
							value="no"
							<?php checked( $toggle_nonce, 'no' ); ?>
						/>
						<label><?php esc_html_e( 'Disable', 'mobiloud' ); ?></label>
					</div>
					<div class="ml-radio-wrap">
						<?php esc_html_e( 'Toggle this feature if commenting returns a 403 Error.' ); ?>
					</div>
				</div>
			</div>
		</p>
	</div>
</div>

<div class="ml2-block app-v1-only-feature">
	<div class="ml2-header"><h2>Login settings</h2></div>
	<div class="ml2-body">

		<div class='ml-col-row'>
			<p>MobiLoud can integrate with a number of WordPress membership plugins and require your users to
				authenticate to access the contents of your app.</p>
			<p>Don't see your membership plugin here? <a class="contact" href="mailto:support@mobiloud.com">Contact us</a> for more
				information.</p>
			<div class="ml-form-row ml-checkbox-wrap">
				<input type="checkbox" id="ml_subscriptions_enable" name="ml_subscriptions_enable"
					value="true" <?php echo Mobiloud::get_option( 'ml_subscriptions_enable' ) ? 'checked' : ''; ?>/>
				<label for="ml_subscriptions_enable">Enable <a target="_blank"
					href="https://wordpress.org/plugins/groups/">WP-Groups</a> integration</label>
			</div>
		</div>
	</div>
</div>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Settings Page', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<div class="ml-form-row">
			<label for="ml_share_app_url">App link to the App Store</label>
			<input size="50" name="ml_share_app_url" id="ml_share_app_url" type="text"
				value="<?php echo esc_url( get_option( 'ml_share_app_url', '' ) ); ?>"/>
		</div>
	</div>
</div>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Rating Prompt Settings', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<div class='ml-col-row'>
			<p>If enabled, a prompt will be displayed after the selected interval so the user can rate your app.</p>
			<div class="ml-form-row ml-checkbox-wrap">
				<input type="checkbox" id="ml_show_rating_prompt" name="ml_show_rating_prompt"
					value="true" <?php echo Mobiloud::get_option( 'ml_show_rating_prompt' ) ? 'checked' : ''; ?>/>
				<label for="ml_show_rating_prompt">Enable rating prompt</label>
			</div>
		</div>

		<h4 class='ml-rating-items'>Display after specific number of days</h4>
		<div class='ml-col-row ml-rating-items'>
			<div class='ml-col-half'>
				<p>Select the number of days that must pass after the app installation for the rating prompt to be displayed</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input type="number" id="ml_days_interval_rating_prompt" name="ml_days_interval_rating_prompt" min="1" max="365"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_days_interval_rating_prompt', 1 ) ); ?>"/>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Welcome screen settings', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<h4>Welcome screen URL</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>The welcome screen will be displayed when the user first opens the app, you can <a href="https://www.mobiloud.com/help/knowledge-base/how-to-use-the-welcome-screen-feature">click here</a> for more details on how to create and configure your welcome screen.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input id="ml_welcome_screen_url" placeholder="http://" name="ml_welcome_screen_url" type="url"
						value="<?php echo esc_attr( get_option( 'ml_welcome_screen_url' ) ); ?>">
				</div>
			</div>
		</div>

		<h4>Welcome screen required version</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>In case changes were made to your welcome screen you can adjust its version to make sure it gets displayed to users the next time they open the app.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input type="text" id="ml_welcome_screen_required_version" name="ml_welcome_screen_required_version" required="required" maxlength="20"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_welcome_screen_required_version', '1.0' ) ); ?>"/>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'CDN and Cache', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<h4 class="app-v2-only-feature">Cache control settings</h4>
		<div class="ml-form-row app-v2-only-feature">
			<p>Your server and any caching plugin installed can improve loading times by caching responses from the plugin.
			This setting allows you to define the duration of the cache (in minutes), after which a new version is created.
			This affects the "Cache-Control" header in the MobiLoud content API. No-cache applied if max-age is 0.</p>
		</div>
		<?php
			ml_cache_block( 'Lists', 'ml_cache_list_age', 'ml_cache_list_is_private', 'list' );
			ml_cache_block( 'Posts', 'ml_cache_post_age', 'ml_cache_post_is_private', 'post' );
			ml_cache_block( 'Pages', 'ml_cache_page_age', 'ml_cache_page_is_private', 'page' );
			ml_cache_block( 'Configuration', 'ml_cache_config_age', 'ml_cache_config_is_private', 'config', true );
		?>

		<h4 class="app-v2-only-feature">Cache Busting</h4>
		<div class="ml-col-row app-v2-only-feature">
			<div class="ml-col-half">
			<p>This setting will determine if the cache busting feature should be enabled and put to use in the app.</p>
			</div>
			<div class="ml-col-half ml-checkbox-wrap">
				<input type="checkbox" id="ml_cache_busting_enabled" name="ml_cache_busting_enabled"
					value="true" <?php checked( Mobiloud::get_option( 'ml_cache_busting_enabled' ) ); ?>/>
				<label for="ml_cache_busting_enabled">Enable Cache Busting</label>
			</div>
		</div>

		<div class="ml-col-row app-v2-only-feature">
			<div class="ml-col-half">
			<p><?php esc_html_e( 'If checked, when opening posts the app will always perform a new request to the website in order to pull the latest content', 'mobiloud' ); ?></p>
			</div>
			<div class="ml-col-half ml-checkbox-wrap">
				<input type="checkbox" id="ml_always_pull_post" name="ml_always_pull_post"
					value="true" <?php checked( Mobiloud::get_option( 'ml_always_pull_post' ) ); ?>/>
				<label for="ml_always_pull_post">
					<?php esc_html_e( 'Always pull latest content', 'mobiloud' ); ?>
				</label>
			</div>
		</div>

		<h4 class="cache-busting-on app-v2-only-feature">Cache Busting Interval</h4>
		<div class="ml-col-row cache-busting-on app-v2-only-feature">
			<div class="ml-col-half">
				<p>This setting will determine the frequency in which the requests performed by the app should be changed, to make sure the latest content is pulled from the endpoints.</p>
			</div>
			<div class="ml-col-half">
				<label class="cache-option-interval-label"><input type="number" class="cache-type-number" min="1" step="1" name="ml_cache_busting_interval" value=<?php echo esc_attr( Mobiloud::get_option( 'ml_cache_busting_interval', '15' ) ); ?>></label>
			</div>
		</div>
	</div>
</div>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Advanced settings', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
	<h4 class="app-v1-only-feature">Cache expiration</h4>
		<div class='ml-col-row app-v1-only-feature'>
			<div class='ml-col-half'>
				<p>Your server and any caching plugin installed can improve loading times by caching responses from the plugin.
					This setting allows you to define the duration of the cache (in minutes), after which a new version is created.
					This affects the "Cache-Control" header in the MobiLoud content API.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input type="number" id="ml_cache_expiration" name="ml_cache_expiration" min="1" max="1440"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_cache_expiration', 30 ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4 class="app-v1-only-feature">Children Page Navigation</h4>
		<div class='ml-col-row app-v1-only-feature'>
			<div class='ml-col-half'>
				<p>Did you built a site with a complex page hierarchy and you'd like to have this available in the app?
					The page hierarchy navigation feature allows users to see a list of children pages at the bottom of
					every page within your app.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_hierarchical_pages_enabled" name="ml_hierarchical_pages_enabled"
						value="true" <?php echo Mobiloud::get_option( 'ml_hierarchical_pages_enabled' ) ? 'checked' : ''; ?>/>
					<label for="ml_hierarchical_pages_enabled">Enable page hierarchy navigation</label>
				</div>
			</div>
		</div>

		<!-- <h4>Image preloading</h4>
		<div class='ml-col-row'>
		<div class='ml-col-half'>
		<p>When this option is enabled, the app will preload images for all posts on start.</p>
		</div>
		<div class='ml-col-half'>
		<div class="ml-form-row ml-checkbox-wrap">
		<input type="checkbox" id="ml_image_cache_preload" name="ml_image_cache_preload"
		value="true" <?php echo Mobiloud::get_option( 'ml_image_cache_preload' ) ? 'checked' : ''; ?>/>
		<label for="ml_image_cache_preload">Enable preloading of images</label>
		</div>
		</div>
		</div> -->

		<h4>Remove unused shortcodes</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>To remove any shortcodes that remain visibile in the app, you can enable this feature.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_remove_unused_shortcodes" name="ml_remove_unused_shortcodes"
						value="true" <?php echo Mobiloud::get_option( 'ml_remove_unused_shortcodes', true ) ? 'checked' : ''; ?>/>
					<label for="ml_remove_unused_shortcodes">Remove unused shortcodes</label>
				</div>
			</div>
		</div>

		<h4>Exclude posts from lists</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>When this option is checked, shows a metabox with an option at single post or page edit screen and removes the selected post from all queries performed by the app. Please note, only few items from regular list may be excluded.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_exclude_posts_enabled" name="ml_exclude_posts_enabled"
						value="true" <?php echo Mobiloud::get_option( 'ml_exclude_posts_enabled' ) ? 'checked' : ''; ?>/>
					<label for="ml_exclude_posts_enabled">Exclude posts from lists</label>
				</div>
			</div>
		</div>

		<h4 class="app-v1-only-feature">Really Simple SSL plugin</h4>
		<div class='ml-col-row app-v1-only-feature'>
			<div class='ml-col-half'>
				<p>Please turn on this option if you are using this plugin to avoid it breaking the plugin's content feed.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_fix_rsssl" name="ml_fix_rsssl"
						value="true" <?php echo Mobiloud::get_option( 'ml_fix_rsssl' ) ? 'checked' : ''; ?>/>
					<label for="ml_fix_rsssl">Support Really Simple SSL plugin</label>
				</div>
			</div>
		</div>

		<h4>PHP notices</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Disable notices and warnings in the API response.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_disable_notices" name="ml_disable_notices"
						value="true" <?php echo Mobiloud::get_option( 'ml_disable_notices', true ) ? 'checked' : ''; ?>/>
					<label for="ml_disable_notices">Disable PHP notices</label>
				</div>
			</div>
		</div>

		<h4>Alternative Featured Image</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>You can override the featured image used in article lists and at the top of every article with a
					secondary image you can define for every post.</p>
				<p>Install the <a href="https://wordpress.org/plugins/multiple-post-thumbnails/">Multiple Post
						Thumbnails</a> plugin and enter the ID of the secondary featured image field you've setup,
					normally "secondary-image".</p>
				<p>Alternatively enter the name of a custom field where you'll enter, for each post, the full URL of the
					alternative image.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-left-align clearfix">
					<label class='ml-width-120' for="ml_custom_featured_image">Image ID</label>
					<input type="text" placeholder="Image ID" id="ml_custom_featured_image"
						name="ml_custom_featured_image"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_custom_featured_image' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Override Article/Page URL with a custom field</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>When sharing your content, users will normally share the article's URL. For a curation-based
					app, though, you might want users to share the source for that story.</p>
				<p>Enter a custom field name to the right which you can fill for every post with the URL you want users
					to share.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-left-align clearfix">
					<label class='ml-width-120' for="ml_custom_field_url">URL Field Name</label>
					<input type="text" placeholder="Custom Field Name" id="ml_custom_field_url"
						name="ml_custom_field_url"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_custom_field_url' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Live preview</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p></p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_live_preview_enabled" name="ml_live_preview_enabled"
						value="true" <?php checked( Mobiloud::get_option( 'ml_live_preview_enabled' ) ); ?>/>
					<label for="ml_live_preview_enabled">Enable Live preview button</label>
				</div>
			</div>
		</div>

		<h4>App Type</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p></p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-left-align clearfix">
					<label class='ml-width-120' for="ml_user_sitetype">Site type</label>
					<?php
					$type = Mobiloud::get_option( 'ml_user_sitetype', '' );
					if ( ! in_array( $type, [ 'content', 'learning', 'ecommerce', 'directory', 'other' ], true ) ) {
						$type = 'content';
					}
					?>
					<select id="ml_user_sitetype" name="ml_user_sitetype">
						<option value="content" <?php selected( Mobiloud::get_option( 'ml_user_sitetype', 'content' ), 'content', true ); ?>>Content site, blog, or news site</option>
						<option value="learning" <?php selected( Mobiloud::get_option( 'ml_user_sitetype' ), 'learning', true ); ?>>Learning website</option>
						<option value="ecommerce" <?php selected( Mobiloud::get_option( 'ml_user_sitetype' ), 'ecommerce', true ); ?>>Ecommerce</option>
						<option value="directory" <?php selected( Mobiloud::get_option( 'ml_user_sitetype' ), 'directory', true ); ?>>Directory site</option>
						<option value="other" <?php selected( Mobiloud::get_option( 'ml_user_sitetype' ), 'other', true ); ?>>Something else</option>
					</select>
				</div>
			</div>
		</div>

		<h4>App Version</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p></p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row ml-left-align clearfix">
					<label class='ml-width-120' for="ml_app_version">Configuration options</label>
					<select id="ml_app_version" name="ml_app_version">
						<option value="1" <?php selected( Mobiloud::get_option( 'ml_app_version', 2 ), 1, true ); ?>>Show V1 configuration options</option>
						<option value="2" <?php selected( Mobiloud::get_option( 'ml_app_version', 2 ), 2, true ); ?>>Show V2 configuration options</option>
					</select>
				</div>
			</div>
		</div>

		<h4>Templates</h4>
		<div class='ml-col-row'>
			<div class="ml-col-half">
				<p></p>
			</div>
			<div class="ml-col-half">
				<div class="ml-form-row ml-left-align clearfix">
					<label class='ml-width-120' for="ml_app_version">Template options</label>
					<select name="ml-templates" class="ml-select">
					<?php foreach ( $template_types as $key => $value ) : ?>
						<option <?php selected( $template, $key ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>

		<!--
		<h4>Universal linking</h4>
		<div class='ml-col-row'>
		<div class='ml-col-half'>
		<p>Enable universal linking to allow the app to open links to pages on your domain.
		This is ideal to make sure people have the best experience when clicking links in emails or social media.</p>
		</div>

		<div class='ml-col-half'>
		<div class="ml-form-row ml-checkbox-wrap">
		<input type="checkbox" id="ml_universal_link_enable" name="ml_universal_link_enable"
		value="true" <?php echo Mobiloud::get_option( 'ml_universal_link_enable' ) ? 'checked' : ''; ?>/>
		<label for="ml_universal_link_enable">Enable Universal linking</label>
		</div>
		<div class="ml-universal_link-row ml-form-row ml-left-align clearfix"
		<?php
		if ( ! Mobiloud::get_option( 'ml_universal_link_enable' ) ) {

			?>
			style="display:none;"
			<?php
		}
		?>
		>
		<label class='ml-width-120' for="ml_universal_link_ios">iOS App ID</label>
		<input type="text" id="ml_universal_link_ios"
		name="ml_universal_link_ios"
		value="<?php echo esc_attr( Mobiloud::get_option( 'ml_universal_link_ios' ) ); ?>"/>
		</div>
		</div>
		</div>
		-->
	</div>
</div>
