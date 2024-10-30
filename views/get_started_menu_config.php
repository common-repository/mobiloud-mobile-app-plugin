<?php ini_set( 'display_errors', E_ALL ); ?>
<?php wp_nonce_field( 'tab_menu_config', 'ml_nonce' ); ?>
<?php wp_nonce_field( 'load_ajax', 'ml_nonce_load_ajax' ); ?>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Hamburger menu', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<p>
			<?php
				$menu_slug = Mobiloud::get_option( 'ml_hamburger_nav' );
				$menu      = get_term_by( 'slug', $menu_slug, 'nav_menu' );
				printf(
					__( 'The hamburger menu is displayed on the top left corner of the app, when clicked it displays a side menu. <a href="%s">Click here</a> to edit the links that are displayed in your selected hamburger menu.', 'mobiloud' ),
					Mobiloud_Admin::get_menu_edit_url_by_id( $menu->term_id )
				);
			?>
		</p>
		<p>
			<?php printf( __( 'For more information about the hamburger menu <a href="%s">click here</a>.', 'mobiloud' ), 'https://www.mobiloud.com/help/knowledge-base/how-to-configure-the-hamburger-menu' ); ?>
		</p>
		<p>
			<select name="ml-hamburger-nav" class="ml-select">
				<option value="">Select Menu</option>
				<?php
				$menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
				foreach ( $menus as $menu ) {
					$selected = '';
					if ( Mobiloud::get_option( 'ml_hamburger_nav' ) == $menu->slug ) {
						$selected = 'selected="selected"';
					}
					echo "<option value='" . esc_attr( $menu->slug ) . "' " . esc_attr( $selected ) . '>' . esc_html( $menu->name ) . '</option>';
				}
				?>
			</select>
		</p>
	</div>
</div>

<!-- Sections menu -->
<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Sections menu', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<p>
			<?php
				$menu_slug = Mobiloud::get_option( 'ml_sections_menu' );
				$menu      = get_term_by( 'slug', $menu_slug, 'nav_menu' );
				printf(
					__( 'The sections menu can be used in the app to provide your users with a complete hierarchical view of your app pages and categories. <a href="%s">Click here</a> to edit the links that are displayed in your selected sections menu.', 'mobiloud' ),
					Mobiloud_Admin::get_menu_edit_url_by_id( $menu->term_id )
				);
			?>
		</p>
		<p>
			<?php printf( __( 'For more information about the sections menu <a href="%s">click here</a>.', 'mobiloud' ), 'https://www.mobiloud.com/help/knowledge-base/how-to-configure-the-sections-menu' ); ?>
		</p>
		<p>
			<select name="ml-sections-menu" class="ml-select">
				<option value="">Select Menu</option>
				<?php
				$menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
				foreach ( $menus as $menu ) {
					$selected = '';
					if ( Mobiloud::get_option( 'ml_sections_menu' ) == $menu->slug ) {
						$selected = 'selected="selected"';
					}
					echo "<option value='" . esc_attr( $menu->slug ) . "' " . esc_attr( $selected ) . '>' . esc_html( $menu->name ) . '</option>';
				}
				?>
			</select>
		</p>
	</div>
</div>
<!-- // Sections menu -->

<!-- Tabbed menu -->
<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Tabbed menu', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<p>
			<?php printf( __( 'The tabbed menu is displayed at the bottom of your app, it provides users with quick access to the most important areas.', 'mobiloud' ), '#' ); ?>
		</p>
		
		<p>
			<?php printf( __( ' For more information about the tabbed menu <a href="%s">click here</a>.', 'mobiloud' ), 'https://www.mobiloud.com/help/knowledge-base/how-to-configure-the-tabbed-menu' ); ?>
		</p>

		<p>
			<label for="ml_tabbed_navigation_enabled">
				<input type="checkbox" id="ml_tabbed_navigation_enabled" name="ml_tabbed_navigation_enabled" value="true" <?php echo Mobiloud::get_option( 'ml_tabbed_navigation_enabled' ) ? 'checked' : ''; ?> />
				<?php esc_html_e( 'Enable the tabbed menu', 'mobiloud' ); ?>
			</label>
		</p>

		<?php
			$ml_tabbed_nav = Mobiloud::get_option( 'ml_tabbed_navigation_enabled' );
			$tn_data = Mobiloud::get_option( 'ml_tabbed_navigation', [] );
		?>

		<div class="mlconf__panel-content-row mlconf__row-toggle--inverse mlconf__row-<?php echo empty( $ml_tabbed_nav ) ? 'show' : 'hide'; ?>">
			<h4 class="item-horizontal-navigation">Horizontal Navigation</h4>
			<div class="ml-form-row item-horizontal-navigation">
				<p>Select a WordPress menu to use for the App's horizontal navigation</p>
				<select name="ml-horizontal-nav" class="ml-select">
					<option value="">Select Menu</option>
					<?php $menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) ); ?>
					<?php
					foreach ( $menus as $menu ) {
						$selected = '';
						if ( Mobiloud::get_option( 'ml_horizontal_nav' ) == $menu->slug ) {
							$selected = 'selected="selected"';
						}
						echo "<option value='" . esc_attr( $menu->slug ) . "' " . esc_attr( $selected ) . '>' . esc_html( $menu->name ) . '</option>';
					}
					?>
				</select>
			</div>
		</div>

		<div class="mlconf__panel-content-row mlconf__row-toggle mlconf__row-<?php echo ! empty( $ml_tabbed_nav ) ? 'show' : 'hide'; ?>">
			<div class="mlconf__tabbed-nav-colors">
				<div class="mlconf__tabbed-nav-color mlconf__tabbed-nav-color--active-icon tab-color">
					<label><?php esc_html_e( 'Active icon color', 'mobiloud' ); ?></label>
					<input class="color-picker" value="<?php echo esc_attr( $tn_data['active_icon_color'] ); ?>" name="ml_tabbed_navigation[active_icon_color]" type="text" />
				</div>
				<div class="mlconf__tabbed-nav-color mlconf__tabbed-nav-color--inactive-icon tab-color">
					<label><?php esc_html_e( 'Inactive icon color', 'mobiloud' ); ?></label>
					<input class="color-picker" value="<?php echo esc_attr( $tn_data['inactive_icon_color'] ); ?>" name="ml_tabbed_navigation[inactive_icon_color]" type="text" />
				</div>
				<div class="mlconf__tabbed-nav-color mlconf__tabbed-nav-color--tab-bg-icon tab-color">
					<label><?php esc_html_e( 'Tab background color', 'mobiloud' ); ?></label>
					<input class="color-picker" value="<?php echo esc_attr( $tn_data['background_color'] ); ?>" name="ml_tabbed_navigation[background_color]" type="text" />
				</div>
			</div>
		</div>

		<div class="mlconf__panel-content-row mlconf__row-toggle mlconf__row-<?php echo ! empty( $ml_tabbed_nav ) ? 'show' : 'hide'; ?>">
			<small>Drag items to reorder</small>
			<div class="nav-tab-wrapper" id="ml-tabnav-tabs">

				<?php
				$ml_tabnav_tabs = $tn_data['tabs'];
				$count          = 0;

				function get_icon_uri_for_tab( $tab ) {
					if ( isset( $tab['icon_url'] ) ) {
						return $tab['icon_url'];
					}

					switch ( $tab['label'] ) {
						case 'Home':
							return MOBILOUD_PLUGIN_URL . 'assets/icons/home.png';
						case 'Sections':
							return MOBILOUD_PLUGIN_URL . 'assets/icons/tab-sections.png';
						case 'Favorites':
							return MOBILOUD_PLUGIN_URL . 'assets/icons/tab-star.png';
						case 'Settings':
							return MOBILOUD_PLUGIN_URL . 'assets/icons/tab-settings.png';
						case 'Disabled':
							return MOBILOUD_PLUGIN_URL . 'assets/icons/tab-visibility-off.png';
						default:
							break;
					}
				}

				foreach ( $ml_tabnav_tabs as $tab ) {
					$icon_uri = get_icon_uri_for_tab( $tab );
					?>
					<a id="<?php echo esc_attr( "tabnav-tab-$count" ); ?>" href="<?php echo esc_attr( "#tabnav-$count" ); ?>" class="nav-tab <?php echo ( $count == 0 ) ? 'nav-tab-active' : ''; ?>">
						<div class="mlconf__tab-menu-icon-wrapper">
							<img class="mlconf__tab-menu-icon" src="<?php echo esc_url( $icon_uri ); ?>" />
						</div>
						<div class="mlconf__tab-menu-label"><?php echo esc_html( $tab['label'] ); ?></div>
					</a>
					<?php
					$count++;
				}
				?>
			</div>
			<input type="hidden" name="ml_tabbed_navigation[taborder]" id="ml-tabnav-order" value="0,1,2,3,4" />

			<div id="ml-navtab-contents">
				<?php
				$tab_types = array(
					'homescreen',
					'list',
					'link',
					'favorites',
					'settings',
					'sections',
				);
				$count     = 0;
				foreach ( $ml_tabnav_tabs as $tab ) {
					?>

					<div class="tabnav-content <?php echo ( $count == 0 ) ? 'active' : ''; ?>" id="tabnav-<?php echo intval( $count ); ?>" style="background-color: <?php echo esc_attr( $tab['webview_background_color'] ); ?>">
						<div class="ml-form-row ml-checkbox-wrap">
							<label>
								<input type="checkbox" name="ml_tabbed_navigation[tabs][<?php echo intval( $count ); ?>][enabled]"
									value="1" <?php echo ( $tab['enabled'] == '1' ) ? 'checked' : ''; ?> />
								Enabled
							</label>
						</div>
						<div class="ml-form-row">
							<label>Label</label>
							<input type="text" class="ml-tab-label" name="ml_tabbed_navigation[tabs][<?php echo intval( $count ); ?>][label]" value="<?php echo esc_attr( $tab['label'] ); ?>" />
						</div>

						<div class="ml-form-row ml-iconbox">
							<label>Tab Icon</label>
							<input class="ml-tab-icon-url icon-input" value="<?php echo esc_attr( $tab['icon_url'] ); ?>" name="ml_tabbed_navigation[tabs][<?php echo intval( $count ); ?>][icon_url]" type="text" />
							<button type="button" class="browser button icon-default">Pick from Library</button>
							<button type="button" class="browser button icon-load">Upload icon</button>

							<div class="icon-line">
								<div class="icon-wrap"><img src="" class="icon-view" /></div>
								<button type="button" class="browser button icon-clean">Remove icon</button>
							</div>
						</div>

						<div class="ml-form-row">
							<label>Tab type</label>
							<select class="ml-tab-type" name="ml_tabbed_navigation[tabs][<?php echo intval( $count ); ?>][type]">
								<?php
								foreach ( $tab_types as $type ) {
									echo '<option value="' . esc_attr( $type ) . '" ' . selected( $tab['type'], $type, false ) . '>' . esc_html( ucfirst( $type ) ) . '</option>';
								}
								?>
							</select>
						</div>

						<div class="ml-form-row ml-tabnav-conditional show-link">
							<label>URL</label>
							<input value="<?php echo esc_attr( $tab['url'] ); ?>" name="ml_tabbed_navigation[tabs][<?php echo intval( $count ); ?>][url]" type="text" />
						</div>

						<div class="ml-form-row ml-tabnav-conditional show-list">
							<?php
								$posts = get_posts( array(
									'post_type'      => 'list-builder',
									'posts_per_page' => -1,
								) );
							?>
							<label>List</label>
							<select name="ml_tabbed_navigation[tabs][<?php echo intval( $count ); ?>][list]">
								<?php foreach ( $posts as $post ) : ?>
									<option <?php selected( $tab['list'], $post->ID, true ); ?> value="<?php echo esc_attr( $post->ID ); ?>"><?php echo esc_html( $post->post_title ); ?></option>
								<?php endforeach; ?>
							</select>
							<p><?php esc_html_e( 'Click here to make changes to your home screen list.' ); ?></p>
						</div>

						<div class="ml-form-row ml-tabnav-conditional show-list show-link show-sections show-homescreen">
							<label>Horizontal Navigation</label>

							<select name="ml_tabbed_navigation[tabs][<?php echo intval( $count ); ?>][horizontal_navigation]" class="ml-select">
								<option value="">Select Menu</option>
								<?php $menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) ); ?>
								<?php
								foreach ( $menus as $menu ) {
									echo "<option value='" . esc_attr( $menu->slug ) . "' " . selected( $tab['horizontal_navigation'], $menu->slug, false ) . '>' . esc_html( $menu->name ) . '</option>';
								}
								?>
							</select>
						</div>

						<div class="ml-form-row ml-tabnav-conditional show-list show-link show-sections show-homescreen">
							<label>First Item Label</label>
							<input value="<?php echo esc_attr( $tab['first_item_label'] ); ?>" name="ml_tabbed_navigation[tabs][<?php echo intval( $count ); ?>][first_item_label]" type="text" />
						</div>

						<div class="ml-form-row">
							<label>Webview Background Color</label>
							<input class="color-picker" value="<?php echo esc_attr( $tab['webview_background_color'] ); ?>" name="ml_tabbed_navigation[tabs][<?php echo intval( $count ); ?>][webview_background_color]" type="text" />
						</div>

					</div>

					<?php
					$count++;
				}
				?>
			</div>
		</div>
	</div>
</div>
<!-- Tabbed menu // -->

<!-- Notifications menu -->
<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Notifications menu', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<p>
			<?php
				$menu_slug = Mobiloud::get_option( 'ml_push_notification_menu' );
				$menu      = get_term_by( 'slug', $menu_slug, 'nav_menu' );
				printf( __( 'The notifications menu is used to determine from which categories your users will receive notifications for. You can edit the notifications menu <a href="%s">clicking here</a>.', 'mobiloud' ), Mobiloud_Admin::get_menu_edit_url_by_id( $menu->term_id ) );
			?>
		</p>

		<p>
			<?php printf( __( 'For more information about the notifications menu <a href="%s">click here</a>.', 'mobiloud' ), 'https://www.mobiloud.com/help/knowledge-base/how-to-configure-the-notifications-menu' ); ?>
		</p>

		<p>
			<div class="mlconf__checkbox-control-wrapper">
				<input type="checkbox" id="ml_push_notification_settings_enabled" name="ml_push_notification_settings_enabled" value="true" <?php echo ( Mobiloud::get_option( 'ml_push_notification_settings_enabled', '0' ) === '1' ) ? 'checked' : ''; ?>/>
				<div class="mlconf__panel-checkbox-label-desc">
					<label for="ml_push_notification_settings_enabled"><?php esc_html_e( 'Display notifications menu', 'mobiloud' ); ?></label>
					<div class="mlconf__panel-checkbox-desc"><?php esc_html_e( 'Enable this setting to display the tab in your tabbed menu', 'mobiloud' ); ?></div>
				</div>
			</div>
		</p>

		<div class="mlconf__panel-content-row">
			<select name="ml_push_notification_menu" class="ml-select">
				<option value="">Select Menu</option>
				<?php
				$menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
				foreach ( $menus as $menu ) {
					$selected = '';
					if ( Mobiloud::get_option( 'ml_push_notification_menu' ) == $menu->slug ) {
						$selected = 'selected="selected"';
					}
					echo "<option value='" . esc_attr( $menu->slug ) . "' " . esc_attr( $selected ) . '>' . esc_html( $menu->name ) . '</option>';
				}
				?>
			</select>
		</div>
	</div>
</div>
<!-- Notifications menu // -->

<!-- Generally settings menu -->
<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'General settings menu', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<p>
			<?php
				$menu_slug = Mobiloud::get_option( 'ml_general_settings_menu' );
				$menu      = get_term_by( 'slug', $menu_slug, 'nav_menu' );
				printf( __( 'The settings menu is displayed under the “Settings” area of your app, we recommend using it to display links to your terms, policies and contact pages. You can edit the settings menu <a href="%s">clicking here</a>.', 'mobiloud' ), Mobiloud_Admin::get_menu_edit_url_by_id( $menu->term_id ) );
			?>
			</p>

		<p>
			<?php printf( __( 'For more information about the settings menu <a href="%s">click here</a>.', 'mobiloud' ), 'https://www.mobiloud.com/help/knowledge-base/how-to-configure-the-settings-menu' ); ?>
			</p>

		<p>
			<div class="mlconf__checkbox-control-wrapper">
				<input type="checkbox" id="ml_general_settings_enabled" name="ml_general_settings_enabled" value="true" <?php echo ( Mobiloud::get_option( 'ml_general_settings_enabled', '0' ) === '1' ) ? 'checked' : ''; ?>/>
				<div class="mlconf__panel-checkbox-label-desc">
					<label for="ml_general_settings_enabled"><?php esc_html_e( 'Display settings menu', 'mobiloud' ); ?></label>
					<div class="mlconf__panel-checkbox-desc"><?php esc_html_e( 'Enable this setting to display the tab in your tabbed menu', 'mobiloud' ); ?></div>
				</div>
			</div>
		</p>

		<div class="mlconf__panel-content-row">
			<select name="ml_general_settings_menu" class="ml-select">
				<option value="">Select Menu</option>
				<?php
				$selected_option = Mobiloud::get_option( 'ml_general_settings_menu', '' );
				foreach ( $menus as $ml_menu ) {
					$selected = '';
					if ( $selected_option === $ml_menu->slug ) {
						$selected = 'selected="selected"';
					}
					echo "<option value='" . esc_attr( $ml_menu->slug ) . "' " . esc_attr( $selected ) . '>' . esc_html( $ml_menu->name ) . '</option>';
				}
				?>
			</select>
		</div>
	</div>
</div>
<!-- Generally settings menu // -->

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Settings screen colors', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<div class="ml-form-row ml-color">
		<label>Title Color</label>
			<?php $def_color = Mobiloud::get_option( 'ml_settings_title_color', '#444444' ); ?>
			<input class="color-picker" value="<?php echo esc_attr( $def_color ); ?>" name="ml_settings_title_color" type="text" />
		</div>

		<div class="ml-form-row ml-color">
			<label>Active Switch Color</label>
			<?php
			$def_color = Mobiloud::get_option( 'ml_settings_active_switch_color', '#4CD964' );
			?>
			<input class="color-picker" value="<?php echo esc_attr( $def_color ); ?>" name="ml_settings_active_switch_color" type="text" />
		</div>

		<div class="ml-form-row ml-color">
			<label>Active Switch Background Color</label>
			<?php
			$def_color = Mobiloud::get_option( 'ml_settings_active_switch_background_color', '#b4ffc1' );
			?>
			<input class="color-picker" value="<?php echo esc_attr( $def_color ); ?>" name="ml_settings_active_switch_background_color" type="text" />
		</div>

		<div class="ml-form-row ml-color">
			<label>Inactive Switch Color</label>
			<?php
			$def_color = Mobiloud::get_option( 'ml_settings_inactive_switch_color', '#A3A3A3' );
			?>
			<input class="color-picker" value="<?php echo esc_attr( $def_color ); ?>" name="ml_settings_inactive_switch_color" type="text" />
		</div>

		<div class="ml-form-row ml-color">
			<label>Inactive Switch Background Color</label>
			<?php
			$def_color = Mobiloud::get_option( 'ml_settings_inactive_switch_background_color', '#d4d4d4' );
			?>
			<input class="color-picker" value="<?php echo esc_attr( $def_color ); ?>" name="ml_settings_inactive_switch_background_color" type="text" />
		</div>


	</div>
</div>

<div class="ml2-block app-v1-only-feature">
	<div class="ml2-header"><h2>Menu Structure</h2></div>
	<div class="ml2-body">

		<p>Drag each item into the order you prefer. Any questions or need some help with the app's menu configuration?
			<a class="contact" href="mailto:support@mobiloud.com">Send us a message</a>.</p>
		<div class='ml-col-row'>
			<div class="ml-col-row">
				<h4>Categories</h4>
				<div class="ml-form-row">
					<?php Mobiloud_Admin::load_ajax_insert( 'menu_cat' ); ?>
					<a href="#" class="button-secondary ml-add-category-btn" style="display: none">Add</a>
				</div>
				<ul class="ml-menu-holder ml-menu-categories-holder">
				</ul>
				<h4>Custom Taxonomies</h4>
				<div class="ml-form-row">
					<select name="ml-tax-group" class="ml-select-add">
						<option value="">Select Taxonomy</option>
						<?php $taxonomies = get_taxonomies( array( '_builtin' => false ), 'objects' ); ?>
						<?php
						foreach ( $taxonomies as $tax ) {
							echo "<option value=' " . esc_attr( $tax->query_var ) . "'>" . esc_html( $tax->label ) . '</option>';
						}
						?>
					</select>
				</div>
				<div class="ml-form-row ml-tax-group-row" style="display:none;">
					<select name="ml-terms" class="ml-select-add">
						<option value="">Select Term</option>
					</select>
					<a href="#" class="button-secondary ml-add-term-btn">Add</a>
				</div>
				<ul class="ml-menu-holder ml-menu-terms-holder">
					<?php
					$menu_terms = Mobiloud::get_option( 'ml_menu_terms', array() );
					foreach ( $menu_terms as $menu_term_data ) {
						$menu_term_data_ex = explode( '=', $menu_term_data );
						$menu_term_object  = get_term_by( 'id', $menu_term_data_ex[1], $menu_term_data_ex[0] );

						?>
						<li rel="<?php echo esc_attr( $menu_term_object->term_id ); ?>">
							<span
								class="dashicons-before dashicons-menu"></span><?php echo( isset( $menu_term_object->name ) ? esc_html( $menu_term_object->name ) : '' ); ?>
							<input type="hidden" name="ml-menu-terms[]" value="<?php echo esc_attr( $menu_term_data ); ?>"/>
							<a href="#" class="dashicons-before dashicons-trash ml-item-remove"></a>
						</li>
						<?php

					}
					?>
				</ul>

				<h4>Tags</h4>
				<div class="ml-form-row">
					<?php Mobiloud_Admin::load_ajax_insert( 'menu_tags' ); ?>
					<a href="#" class="button-secondary ml-add-tag-btn" style="display: none">Add</a>
				</div>
				<ul class="ml-menu-holder ml-menu-tags-holder">
				</ul>

				<h4>Pages</h4>
				<div class="ml-form-row">
					<?php Mobiloud_Admin::load_ajax_insert( 'menu_page' ); ?>
					<a href="#" class="button-secondary ml-add-page-btn" style="display: none">Add</a>
				</div>
				<ul class="ml-menu-holder ml-menu-pages-holder">
				</ul>

				<h4>Links</h4>
				<div class="ml-form-row">
					<input type="text" placeholder="Menu Title" id="ml_menu_url_title" name="ml_menu_url_title"/>
					<input type="text" placeholder="http://www.domain.com/" size="32" id="ml_menu_url" name="ml_menu_url"/>
					<a href="#" class="button-secondary ml-add-link-btn">Add</a>
				</div>
				<ul class="ml-menu-holder ml-menu-links-holder">
					<?php
					$menu_urls = get_option( 'ml_menu_urls', array() );
					foreach ( $menu_urls as $menu_url ) {
						?>
						<li rel="<?php echo esc_attr( $menu_url['url'] ); ?>">
							<span
								class="dashicons-before dashicons-menu"></span><?php echo esc_html( $menu_url['urlTitle'] ); ?>
							- <span
								class="ml-sub-title"><?php echo esc_html( Mobiloud::trim_string( esc_html( $menu_url['url'] ), 50 ) ); ?></span>
							<input type="hidden" name="ml-menu-links[]"
								value="<?php echo esc_attr( $menu_url['urlTitle'] ) . ':=:' . esc_attr( $menu_url['url'] ); ?>"/>
							<a href="#" class="dashicons-before dashicons-trash ml-item-remove"></a>
						</li>
						<?php
					}
					?>
				</ul>
			</div>
		</div>
	</div>
</div>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Menu Settings', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<div class='ml-col-row'>
			<div class="ml-col-half">
				<p>Customise your app menu by adjusting what it should display.</p>
			</div>
			<div class="ml-col-half">
				<div class="ml-form-row ml-checkbox-wrap">
					<input type="checkbox" id="ml_menu_show_favorites" name="ml_menu_show_favorites"
						value="true" <?php echo Mobiloud::get_option( 'ml_menu_show_favorites' ) ? 'checked' : ''; ?>/>
					<label for="ml_menu_show_favorites">Show Favourites in the app menu</label>
				</div>
			</div>
		</div>
	</div>
</div>
