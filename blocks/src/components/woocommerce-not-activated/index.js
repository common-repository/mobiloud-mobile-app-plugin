import React from 'react';
import { __ } from '@wordpress/i18n';

export function WooCommerceNotActivated( props ) {
	return (
		<h3>
			<code>{ __( 'Activate WooCommerce to use this block.' ) }</code>
		</h3>
	);
}
