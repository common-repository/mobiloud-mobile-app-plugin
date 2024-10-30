<!DOCTYPE html>
<html lang="en">
	<head>
		<?php do_action( 'mobiloud_block_header' ); ?>
		<link rel="stylesheet" href="https://unpkg.com/onsenui/css/onsenui.css">
		<link rel="stylesheet" href="https://unpkg.com/onsenui/css/onsen-css-components.min.css">
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="<?php echo MOBILOUD_PLUGIN_URL . 'blocks/build/style-index.css'; ?>" rel="stylesheet" />
		<meta name="robots" content="max-image-preview:large">
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
		<title>Home page</title>
		<?php
			$custom_css = stripslashes( get_option( 'ml_post_custom_css' ) );
			echo $custom_css ? '<style type="text/css" media="screen">' . $custom_css . '</style>' : ''; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
			$lb_id    = get_the_ID(); // List builder ID.
			$doc_meta = mobiloud_get_global_doc_meta( $lb_id );
		?>

		<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Condensed:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Roboto+Slab:wght@100;200;300;400;500;600;700;800;900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

		<style>
			body {
				overflow: scroll;
			}

			#list-builder-root {
				padding-bottom: 100px;
			}
		</style>
		<script>
			var ml_list_builder_assets = window.ml_list_builder_assets || [];
			ml_list_builder_assets = JSON.parse( JSON.stringify( <?php echo wp_json_encode(
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'siteUrl' => get_site_url(),
				) ); ?> )
			);
		</script>
		<script>
			var docGlobals = JSON.parse( JSON.stringify( <?php echo wp_json_encode( $doc_meta ); ?> ) );
		</script>
	</head>
	<body>
		<div id="list-builder-root" class="list-builder-root list-builder-root--<?php echo esc_attr( $lb_id ); ?>"></div>
		<script src="<?php echo MOBILOUD_PLUGIN_URL . 'blocks/build-front/mobiloud-front.js' ?>"></script>
	</body>
</html>
