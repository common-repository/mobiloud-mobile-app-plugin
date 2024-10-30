<?php

/**
 * Removes force inline <script> injection by Gravity forms in post content
 * which breaks the JSON Schema.
 *
 * @param bool $force_output Whether to force the script output.
 *
 * @return bool
 */
function mobiloud_disable_force_injection_by_gravity_forms( $force_output ) {
	global $wp;

	if ( isset( $wp->query_vars['__ml-api'] ) && 'posts' === $wp->query_vars['__ml-api'] ) {
		return false;
	}

	return true;
}
add_filter( 'gform_force_hooks_js_output', 'mobiloud_disable_force_injection_by_gravity_forms' );
