<?php
function mobiloud_get_push_service() {
	$service = Mobiloud::get_option( 'ml_push_service', '1' );

	if ( '1' === $service ) {
		return $service;
	}

	return false;
}
