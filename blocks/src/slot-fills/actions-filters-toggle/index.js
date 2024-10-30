import { registerPlugin } from '@wordpress/plugins';
import { PluginPostStatusInfo } from '@wordpress/edit-post';
import { ToggleControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

/**
 * Adds a text field in the Status Info section
 * to set the page title.
 */
const PluginPostStatusInfoTest = () => {
	/**
	 * Get the edited title.
	 */
	let { postType, actionFilterStatus } = useSelect( select => {
		const { getCurrentPostType, getEditedPostAttribute } = select( 'core/editor' );
		const meta = getEditedPostAttribute( 'meta' );

		return {
			postType: getCurrentPostType(),
			actionFilterStatus: meta['_mobiloud_action_filters_status'],
		}
	}, [] );

	if ( 'app-pages' !== postType ) {
		return null;
	}

	const { editPost } = useDispatch( 'core/editor' );

	function setActionFilterStatus( status ) {
		editPost( { meta: { '_mobiloud_action_filters_status': status } } );
	}

	return (
		<PluginPostStatusInfo>
			<ToggleControl
				checked={ actionFilterStatus }
				onChange={ setActionFilterStatus }
				label={ __( 'Enable actions and filters' ) }
				help={ __( `Enable header and footer hooks for this page.` ) }
			/>
		</PluginPostStatusInfo>
	)
}
 
registerPlugin(
	'post-status-actions-filters-toggle',
	{
		render: PluginPostStatusInfoTest
	}
);
