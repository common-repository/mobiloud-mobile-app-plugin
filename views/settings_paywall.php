<?php ini_set( 'display_errors', 0 ); ?>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Paywall Settings', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper ml2-body" id="ml-login-settings">
	<h2>Memberships</h2>
		<div class="ml-col-row">
			<label>Membership integration: </label>
			<select name="ml_membership_class" class="ml-select">
				<option value="">Disabled</option>
				<?php
				$items           = ml_get_memberships_list();
				$selected_option = Mobiloud::get_option( 'ml_membership_class', '' );
				foreach ( $items as $key => $title ) {
					?>
				<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $selected_option, $key ); ?>><?php echo esc_html( $title ); ?></option>
					<?php
				}
				?>
			</select>
			<?php
			$error = ml_get_paywall( true )->activate_error_message();
			if ( ! is_null( $error ) ) {
				?>
			<div class="error ml-<?php get_class( ml_get_paywall( true ) ); ?>"><?php echo esc_html( $error ); ?></div>
				<?php
			}
			?>

		</div>
		<br/>

		<h2>Paywall Block Settings</h2>

		<div class="ml-col-row">
			<label>HTML Content: </label>
			<textarea class="ml-editor-area ml-editor-area-html ml-show" name="ml_paywall_pblock_content"><?php echo esc_html( Mobiloud::get_option( 'ml_paywall_pblock_content', '' ) ); ?></textarea>
		</div>
		<br/>

		<div class="ml-col-row">
			<label>CSS rules: </label>
			<textarea class="ml-editor-area ml-editor-area-css ml-show" name="ml_paywall_pblock_css"><?php echo esc_html( Mobiloud::get_option( 'ml_paywall_pblock_css', '' ) ); ?></textarea>
		</div>
		<br/>
	</div>
</div>
