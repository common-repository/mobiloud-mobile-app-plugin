import { registerPlugin } from '@wordpress/plugins';
import { useSelect, useDispatch, subscribe } from "@wordpress/data";
import { store as editorStore } from '@wordpress/editor';
import { useRef } from "@wordpress/element";
import { __ } from "@wordpress/i18n";

function RateLimitChecker() {
    const publishClicked = useRef( false );

    const { post } = useSelect( select => {
        const { getCurrentPost } = select( editorStore );

        return {
            post: getCurrentPost()
        }
    } );

    const { createNotice } = useDispatch( 'core/notices' );

    if ( ! ( 'auto-draft' === post.status || 'draft' === post.status ) ) {
        return null;
    }

    subscribe( () => {
        const publishBtnEl = document.querySelector( '.editor-post-publish-button__button' );
        const cancelBtnEl = document.querySelector( '.editor-post-publish-panel__header-cancel-button' );

    
        if ( publishBtnEl && cancelBtnEl ) {
            jQuery( document ).on( 'click', '.editor-post-publish-button', function() {
                if ( publishClicked.current ) {
                    return;
                }

                fetch( `${ ajaxurl }?action=ml_check_rate_limit` ).then( ( response ) => {
                    return response.json();
                } ).then( response => {
                    if ( ! response.success ) {   
                        createNotice( 'warning', __( 'You have reached your limit of notifications in a 15 minutes interval, please wait a few minutes and try again.', 'mobiloud' ) );
                    }
                } );

                publishClicked.current = true;
            } );
        }
    } );



    return null;
}

registerPlugin(
    'ml-push-rate-limit-checker',
    {
        render: RateLimitChecker
    }
);