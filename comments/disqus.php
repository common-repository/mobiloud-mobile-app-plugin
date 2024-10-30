<?php
if ( ! defined( 'MOBILOUD_API_REQUEST' ) ) {
	require_once dirname( dirname( __FILE__ ) ) . '/api/compability.php';
	ml_compability_api_result( 'disqus', true );
}

$postID                   = sanitize_text_field( $_GET['post_id'] );
$site_url                 = network_site_url( '/' );
$disqus_identifier_string = "$postID $site_url?p=$postID";
$post_permalink           = get_permalink( $postID );
$post_title               = get_the_title( $postID );

$http_prefix = 'http';
if ( ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ) || $_SERVER['SERVER_PORT'] == 443 ) {
	$http_prefix = 'https';
}

?>

<html>
<body>
<head>
	<title><?php echo esc_html( $post_title ); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<div id="disqus_thread"></div>

<script type="text/javascript">

	var disqus_shortname = "<?php echo esc_js( sanitize_text_field( $_GET['shortname'] ) ); ?>";
	var disqus_url = '<?php echo esc_js( $post_permalink ); ?>';
	var disqus_identifier = '<?php echo esc_js( $disqus_identifier_string ); ?>';
	var disqus_title = '<?php echo esc_js( $post_title ); ?>';
	var prefix = '<?php echo esc_js( $http_prefix ); ?>';

	var disqus_container_id = 'disqus_thread';
	var disqus_domain = 'disqus.com';

	(function () {
		var dsq = document.createElement('script');
		dsq.type = 'text/javascript';
		dsq.async = true;
		dsq.src = prefix + '://' + disqus_shortname + '.disqus.com/embed.js';
		(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
	})();

</script>

</body>
</html>
