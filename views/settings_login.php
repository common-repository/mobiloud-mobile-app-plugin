<?php ini_set( 'display_errors', 0 ); ?>

<div class="ml2-block mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Login Settings', 'mobiloud' ); ?>
	</div>
	<div class="ml2-body mlconf__panel-content-wrapper" id="ml-login-settings">

		<div class='ml-col-row'>

			<?php
			$ml_login_settings = array(
				// iOS defaults.
				'ios_login_type'                           => 'disabled',
				'ios_background_color'                     => '#FFFFFF',
				'ios_login_input_border'                   => '1',
				'ios_login_input_border_color'             => '#CCCCCC',
				'ios_login_submit_button_background_color' => '#6fab18',
				'ios_login_submit_button_text_color'       => '#FFFFFF',
				'ios_login_links_color'                    => '#666666',
				'ios_login_logo_image'                     => get_option( 'ml_preview_upload_image' ),
				'ios_login_forgotten_password_text'        => 'Forgot your password?',
				'ios_login_forgotten_password_url'         => wp_lostpassword_url(),
				'ios_login_registration_text'              => 'Sign Up',
				'ios_login_registration_url'               => wp_registration_url(),
				'ios_login_terms_text'                     => 'Terms of Service',
				'ios_login_terms_url'                      => '',
				'ios_login_button_text'                    => 'Login',
				'ios_login_intro'                          => '',
				'ios_login_registration_intro'             => '',
				'ios_login_spinner_color'                  => '#6fab18',

				// Android defaults.
				'android_login_type'                       => 'disabled',
				'android_background_color'                 => '#FFFFFF',
				'android_login_input_border'               => '1',
				'android_login_input_border_color'         => '#CCCCCC',
				'android_login_submit_button_background_color' => '#6fab18',
				'android_login_submit_button_text_color'   => '#FFFFFF',
				'android_login_links_color'                => '#666666',
				'android_login_logo_image'                 => get_option( 'ml_preview_upload_image' ),
				'android_login_forgotten_password_text'    => 'Forgot your password?',
				'android_login_forgotten_password_url'     => wp_lostpassword_url(),
				'android_login_registration_text'          => 'Sign Up',
				'android_login_registration_url'           => wp_registration_url(),
				'android_login_terms_text'                 => 'Terms of Service',
				'android_login_terms_url'                  => '',
				'android_login_button_text'                => 'Login',
				'android_login_intro'                      => '',
				'android_login_registration_intro'         => '',
				'android_login_spinner_color'              => '#6fab18',
			);

			$ml_login_settings_option = Mobiloud::get_option( 'ml_login_settings', array() );
			if ( count( $ml_login_settings_option ) > 0 ) {
				$ml_login_settings = $ml_login_settings_option;
			}

			?>

			<div class="ml-col-half">

				<h3>iOS Login Settings</h3>
				<div class="ml-col-row">
					<label>
						<span>Login Type: </span>
						<select name="ml_login_settings[ios_login_type]">
							<option <?php selected( $ml_login_settings['ios_login_type'], 'disabled', true ); ?> value="disabled">Disabled</option>
							<option <?php selected( $ml_login_settings['ios_login_type'], 'login', true ); ?> value="login">Login</option>
							<option <?php selected( $ml_login_settings['ios_login_type'], 'subscription', true ); ?> value="subscription">Subscription</option>
							<option <?php selected( $ml_login_settings['ios_login_type'], 'login_subscription', true ); ?> value="login_subscription">Login and Subscription</option>
						</select>
					</label>
				</div>

				<h3>iOS Login Design Settings</h3>
				<div class="ml-col-row">
					<label>Background Color: </label>
					<input class="color-picker" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['ios_background_color'] ); ?>" name="ml_login_settings[ios_background_color]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Input Border: </label>
					<input type="checkbox" value="1" <?php Mobiloud_Admin::echo_if_set( $ml_login_settings['ios_login_input_border'], '1', 'checked' ); ?> name="ml_login_settings[ios_login_input_border]" />
					Enabled
				</div>

				<div class="ml-col-row">
					<label>Input Border Color: </label>
					<input class="color-picker" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['ios_login_input_border_color'] ); ?>" name="ml_login_settings[ios_login_input_border_color]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Button Background Color: </label>
					<input class="color-picker" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['ios_login_submit_button_background_color'] ); ?>" name="ml_login_settings[ios_login_submit_button_background_color]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Button Text Color: </label>
					<input class="color-picker" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['ios_login_submit_button_text_color'] ); ?>" name="ml_login_settings[ios_login_submit_button_text_color]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Links Color: </label>
					<input class="color-picker" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['ios_login_links_color'] ); ?>" name="ml_login_settings[ios_login_links_color]" type="text" />
				</div>

				<div class="ml-form-row">
					<label>Login Logo</label>
					<input class="image-selector" id="ml_login_logo_upload_image_ios" type="text" size="36" name="ml_login_settings[ios_login_logo_image]"
						value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['ios_login_logo_image'] ); ?>"/>
					<input id="ml_login_logo_upload_image_ios_button" type="button" value="Upload Image" class="browser button"/>
				</div>
				<?php $logoPath = Mobiloud::get_option( 'ml_preview_upload_image' ); ?>
				<div class="ml-form-row ml-preview-upload-image-row" <?php echo ( strlen( $logoPath ) === 0 ) ? 'style="display:none;"' : ''; ?>>
					<div class='ml-preview-image-holder'>
						<img src='<?php echo esc_url( $logoPath ); ?>'/>
					</div>
					<a href='#' class='ml-preview-image-remove-btn'>Remove logo</a>
				</div>

				<h3>iOS Login Content Settings</h3>

				<div class="ml-col-row">
					<label>"Forgot Password" Text:</label>
					<input size="80" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['ios_login_forgotten_password_text'] ); ?>" name="ml_login_settings[ios_login_forgotten_password_text]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>"Forgot Password" URL:</label>
					<input size="80" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['ios_login_forgotten_password_url'] ); ?>" name="ml_login_settings[ios_login_forgotten_password_url]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Registration Text</label>
					<input size="80" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['ios_login_registration_text'] ); ?>" name="ml_login_settings[ios_login_registration_text]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Registration URL</label>
					<input size="80" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['ios_login_registration_url'] ); ?>" name="ml_login_settings[ios_login_registration_url]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Terms Text</label>
					<input size="80" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['ios_login_terms_text'] ); ?>" name="ml_login_settings[ios_login_terms_text]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Terms URL</label>
					<input size="80" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['ios_login_terms_url'] ); ?>" name="ml_login_settings[ios_login_terms_url]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Button Text</label>
					<input size="30" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['ios_login_button_text'] ); ?>" name="ml_login_settings[ios_login_button_text]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Login Intro</label>
					<input size="80" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['ios_login_intro'] ); ?>" name="ml_login_settings[ios_login_intro]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Registration Intro</label>
					<input size="80" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['ios_login_registration_intro'] ); ?>" name="ml_login_settings[ios_login_registration_intro]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Spinner Color: </label>
					<input class="color-picker" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['ios_login_spinner_color'] ); ?>" name="ml_login_settings[ios_login_spinner_color]" type="text" />
				</div>


			</div>

			<div class="ml-col-half">

				<h3>Android Login Settings</h3>
				<div class="ml-col-row">
					<label>
						<span>Login Type: </span>
						<select name="ml_login_settings[android_login_type]">
							<option <?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_type'], 'disabled', 'selected' ); ?> value="disabled">Disabled</option>
							<option <?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_type'], 'login', 'selected' ); ?> value="login">Login</option>
							<option <?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_type'], 'subscription', 'selected' ); ?> value="subscription">Subscription</option>
							<option <?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_type'], 'login_subscription', 'selected' ); ?> value="login_subscription">Login and Subscription</option>
						</select>
					</label>
				</div>

				<h3>Android Login Design Settings</h3>
				<div class="ml-col-row">
					<label>Background Color: </label>
					<input class="color-picker" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_background_color'] ); ?>" name="ml_login_settings[android_background_color]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Input Border: </label>
					<input type="checkbox" value="1" <?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_input_border'], '1', 'checked' ); ?> name="ml_login_settings[android_login_input_border]" />
					Enabled
				</div>

				<div class="ml-col-row">
					<label>Input Border Color: </label>
					<input class="color-picker" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_input_border_color'] ); ?>" name="ml_login_settings[android_login_input_border_color]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Button Background Color: </label>
					<input class="color-picker" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_submit_button_background_color'] ); ?>" name="ml_login_settings[android_login_submit_button_background_color]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Button Text Color: </label>
					<input class="color-picker" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_submit_button_text_color'] ); ?>" name="ml_login_settings[android_login_submit_button_text_color]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Links Color: </label>
					<input class="color-picker" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_links_color'] ); ?>" name="ml_login_settings[android_login_links_color]" type="text" />
				</div>

				<div class="ml-form-row">
					<label>Login Logo</label>
					<input class="image-selector" id="ml_login_logo_upload_image_android" type="text" size="36" name="ml_login_settings[android_login_logo_image]"
						value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_logo_image'] ); ?>"/>
					<input id="ml_login_logo_upload_image_android_button" type="button" value="Upload Image" class="browser button"/>
				</div>
				<?php $logoPath = Mobiloud::get_option( 'ml_preview_upload_image' ); ?>
				<div class="ml-form-row ml-preview-upload-image-row" <?php echo ( strlen( $logoPath ) === 0 ) ? 'style="display:none;"' : ''; ?>>
					<div class='ml-preview-image-holder'>
						<img src='<?php echo esc_url( $logoPath ); ?>'/>
					</div>
					<a href='#' class='ml-preview-image-remove-btn'>Remove logo</a>
				</div>

				<h3>Android Login Content Settings</h3>

				<div class="ml-col-row">
					<label>"Forgot Password" Text:</label>
					<input size="80" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_forgotten_password_text'] ); ?>" name="ml_login_settings[android_login_forgotten_password_text]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>"Forgot Password" URL:</label>
					<input size="80" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_forgotten_password_url'] ); ?>" name="ml_login_settings[android_login_forgotten_password_url]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Registration Text</label>
					<input size="80" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_registration_text'] ); ?>" name="ml_login_settings[android_login_registration_text]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Registration URL</label>
					<input size="80" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_registration_url'] ); ?>" name="ml_login_settings[android_login_registration_url]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Terms Text</label>
					<input size="80" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_terms_text'] ); ?>" name="ml_login_settings[android_login_terms_text]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Terms URL</label>
					<input size="80" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_terms_url'] ); ?>" name="ml_login_settings[android_login_terms_url]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Button Text</label>
					<input size="30" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_button_text'] ); ?>" name="ml_login_settings[android_login_button_text]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Login Intro</label>
					<input size="80" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_intro'] ); ?>" name="ml_login_settings[android_login_intro]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Registration Intro</label>
					<input size="80" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_registration_intro'] ); ?>" name="ml_login_settings[android_login_registration_intro]" type="text" />
				</div>

				<div class="ml-col-row">
					<label>Spinner Color: </label>
					<input class="color-picker" value="<?php Mobiloud_Admin::echo_if_set( $ml_login_settings['android_login_spinner_color'] ); ?>" name="ml_login_settings[android_login_spinner_color]" type="text" />
				</div>

			</div>

		</div>

	</div>
</div>


<div class="ml2-block mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Registration screen settings', 'mobiloud' ); ?>
	</div>
	<div class="ml2-body mlconf__panel-content-wrapper">

		<h4>HTML Content</h4>
		<div class="ml-form-row">
			<textarea class="ml-editor-area ml-editor-area-html ml-show" name="ml_app_registration_block_content"><?php echo esc_html( Mobiloud::get_option( 'ml_app_registration_block_content', '' ) ); ?></textarea>
		</div>

		<h4>CSS rules</h4>
		<div class="ml-form-row">
			<textarea class="ml-editor-area ml-editor-area-css ml-show" name="ml_app_registration_block_css"><?php echo esc_html( Mobiloud::get_option( 'ml_app_registration_block_css', '' ) ); ?></textarea>
		</div>

		<div class="ml-form-row">
			<p>
				<em>
					HTML Content must include at least <code>block for error messages</code> and <code>form</code> with <code>username</code>, <code>password</code>, <code>terms</code> fields.<br>
					Please note, you must use predefined id values for items:<br>
					Block with errors: id="reg_errors"<br>
					Form: id="reg_form"<br>
					Username field: id="reg_user"<br>
					Password field: id="reg_pass"<br>
					Terms field: id="reg_terms"<br>
					Body tag will have class <code>is-ios</code> for iOS devices and <code>is-android</code> for other devices.<br>
					<code>%LOGOURL%</code> string replaced with logo image url, ex:<code>&lt;img src="%LOGOURL%" width="250" height="auto" /&gt;</code>.
				</em>
			</p>
		</div>
	</div>
</div>
