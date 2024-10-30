<?php $plugin_list = Mobiloud_Admin::get_required_plugins_details(); ?>

<div class="ml2-block ml2-welcome-block welcome-step-2 canvas-setup">
	<div class="ml2-body text-left">
		<h3 class="title_big"><?php esc_html_e( 'The ecommerce setup requires the following plugins to be installed.' ); ?></h3>
		<div class="ml-setup__commerce-wrapper">
			<?php foreach ( $plugin_list as $slug => $plugin_item ) : ?>
				<div class="ml-setup__plugin-item-wrapper">
					<label class="ml-setup__plugin-item-label" for="<?php echo esc_attr( $slug ); ?>">
						<input
							type="checkbox"
							checked
							data-entry-file="<?php echo esc_attr( $plugin_item['entry'] ); ?>"
							data-plugin-exists="<?php echo esc_attr( $plugin_item['exists'] ); ?>"
							data-plugin-active="<?php echo esc_attr( $plugin_item['active'] ); ?>"
							class="ml-commerce-required-plugins-cb"
							id="<?php echo esc_attr( $slug ); ?>" name="ml-commerce-required-plugins[<?php echo esc_attr( $slug ); ?>]"
						>
						<div class="ml-setup__plugin-item-logo">
							<img src="<?php echo esc_url( $plugin_item['logo-url'] ); ?>" alt="<?php echo esc_attr( $plugin_item['name'] ); ?>">
						</div>
						<div class="ml-setup__plugin-item-details">
							<div class="ml-setup__plugin-item-name"><?php echo esc_html( $plugin_item['name'] ) ?></div>
							<a class="ml-setup__plugin-repo-link" href="<?php echo esc_url( $plugin_item['repo-url'] ); ?>"><?php esc_html_e( 'Repo link' ) ?></a>
						</div>
					</label>
				</div>
			<?php endforeach; ?>
		</div>
		<hr class="ml-required-plugins-separator">
		<div class="ml-col-row ml-init-button">
			<button type="button" name="submit" id="submit" class="button button-hero button-primary ladda-button ml-install-plugins-button" data-style="zoom-out">
				<span class="ladda-label"><?php esc_html_e( 'Install plugins' ); ?></span>
				<span class="ladda-spinner"></span>
			</button>
			<div class="ml-plugin-installation-status"></div>
		</div>
	</div>
</div>
