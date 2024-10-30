import { registerPlugin } from '@wordpress/plugins';
import { PluginPostStatusInfo } from '@wordpress/edit-post';
import { TextControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { useEffect } from 'react';

/**
 * Adds a text field in the Status Info section
 * to set the page title.
 */
const PluginPostStatusInfoTest = () => {
	/**
	 * Get the edited title.
	 */
	let { title, postType, welcomeGuide } = useSelect( select => {
		const { getEditedPostAttribute, getCurrentPostType } = select( 'core/editor' );
		const { isFeatureActive } = select( 'core/edit-post' );

		return {
			title: getEditedPostAttribute( 'title' ),
			postType: getCurrentPostType(),
			welcomeGuide: isFeatureActive( 'welcomeGuide' ),
		}
	}, [] );

	if ( 'list-builder' !== postType ) {
		return null;
	}

	const { editPost } = useDispatch( 'core/editor' );
	const { setIsInserterOpened, toggleFeature } = useDispatch( 'core/edit-post' );

	/**
	 * Sets the title through the text field.
	 */
	function setTitle( title ) {
		editPost( { title } );
	}

	/**
	 * Get the function which sets the title.
	 */
	const { __experimentalSetPreviewDeviceType } = useDispatch( 'core/edit-post' );

	/**
	 * Set the default view to mobile.
	 */
	useEffect( () => {
		__experimentalSetPreviewDeviceType( 'Mobile' );
		setIsInserterOpened( true );

		if ( welcomeGuide ) {
			toggleFeature( 'welcomeGuide' );
		}
	}, [] );

	return (
		<PluginPostStatusInfo>
			<TextControl
				label={ __( 'Page title (List name):' ) }
				value={ title }
				onChange={ setTitle }
				help={ __( `This is for internal use, it's not visible in your app.` ) }
			/>
		</PluginPostStatusInfo>
	)
}
 
registerPlugin(
	'post-status-page-title',
	{
		render: PluginPostStatusInfoTest
	}
);
