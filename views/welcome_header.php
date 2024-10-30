<?php
$step_1_class = 'disabled';
$step_2_class = 'disabled';
$step_3_class = 'disabled';
$step_4_class = 'disabled';
$step_5_class = 'disabled';
$page = isset( $_GET['page_number'] ) ? (int)$_GET['page_number'] : 0;

/**
 * @var string Current step, defined at MobiloudAdmin.
 */
switch ( $page ) {
	case 1:
		$step_1_class = 'active';
		break;
	case 2:
		$step_1_class = 'complete';
		$step_2_class = 'active';
		break;
	case 3:
		$step_1_class = 'complete';
		$step_2_class = 'complete';
		$step_3_class = 'active';
		break;
	default:
		break;
}
?>
<style type="text/css">
.about-wrap .notice, .about-wrap div.error, .about-wrap div.updated, .notice.ml-schedule-demo-block0, .notice.canvas-schedule-demo-block0 {
	display: none!important;
}
</style>
<div class="wrap about-wrap">
	<div class="mlconf__admin-main-title">Welcome to MobiLoud!</div>
	<div class="mlconf__admin-main-subtitle">Let's get you set up so you can start configuring your mobile app</div>
