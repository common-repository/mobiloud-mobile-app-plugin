import React, { useState, useRef, useEffect } from 'react';
import { ProductCarousel } from '../../components/post-carousel';
import { PostList } from '../../components/post-list';
import { PostGrid } from '../../components/post-grid';
import { WooCommerceNotActivated } from '../../components/woocommerce-not-activated';
import { useDocGlobals, useBlockHeadingStyles } from '../../hooks/use-doc-globals';

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import { useSelect } from '@wordpress/data';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';

import {
	PanelBody,
	RadioControl,
	RangeControl,
	ToggleControl,
} from '@wordpress/components';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit( props ) {
	const {
		attributes: {
			blockHeadingText,
			colsMobile,
			colsTablet,
			displayAs,
			showAuthor,
			showDate,
			showFeaturedImage,
			showPrice,
		},
		setAttributes,
	} = props;

	const [ products, setProducts ] = useState( [] );
	const [ plugins, setPlugins ] = useState( {} );
	const isStillMounted = useRef();
	const docGlobals = useDocGlobals();

	const { userId, deviceType } = useSelect( select => {
		const { getCurrentUser } = select( 'core' );
		const { __experimentalGetPreviewDeviceType } = select( 'core/edit-post' );
		const user = getCurrentUser();

		return {
			userId: user.id,
			deviceType: __experimentalGetPreviewDeviceType(),
		}
	} );

	useEffect( function() {
		isStillMounted.current = true;

		if ( userId < 1 ) {
			return;
		}

		apiFetch( {
			path: addQueryArgs( `/ml-blocks/v1/recently_purchased_products`, {
				userId,
			} ),
		} )
		.then( ( data ) => {
			if ( isStillMounted.current ) {
				if ( data.posts && data.posts.length > 0 ) {
					setProducts( data.posts );
					setPlugins( data.plugins );
				} else {
					setProducts( [] );
				}
			}
		} )

		return () => {
			isStillMounted.current = false;
		};
	} , [ userId ] );

	const blockHeadingStyles = useBlockHeadingStyles( props.attributes );

	const inspectorControlsWrapper = (
		<InspectorControls>
			<PanelBody title={ __( 'Product Carousel settings' ) }>
				<RadioControl
					label={ __( 'Display as' ) }
					options={ [
						{
							label: 'List',
							value: 'list',
						},
						{
							label: 'Grid',
							value: 'grid',
						},
						{
							label: 'Carousel',
							value: 'carousel',
						}
					] }
					selected={ displayAs }
					onChange={ displayAs => setAttributes( { displayAs } ) }
				/>
				{
					'grid' === displayAs && ( <>
						<RangeControl
						label={ __( 'Columns (mobile)' ) }
						value={ colsMobile }
						onChange={ ( colsMobile ) => setAttributes( { colsMobile } ) }
						min={ 1 }
						max={ 2 }
						marks
						/>

						<RangeControl
							label={ __( 'Columns (tablet)' ) }
							value={ colsTablet }
							onChange={ ( colsTablet ) => setAttributes( { colsTablet } ) }
							min={ 1 }
							max={ 3 }
							marks
						/>
					</> )
				}
				<ToggleControl
					label={ __( 'Show featured image' ) }
					checked={ showFeaturedImage }
					onChange={ showFeaturedImage => setAttributes( { showFeaturedImage } ) }
				/>

				{ ( 'list' === displayAs || 'grid' === displayAs ) && <ToggleControl
					label={ __( 'Show author' ) }
					checked={ showAuthor }
					onChange={ showAuthor => setAttributes( { showAuthor } ) }
				/> }

				{ ( 'list' === displayAs || 'grid' === displayAs ) && ( <ToggleControl
					label={ __( 'Show date' ) }
					checked={ showDate }
					onChange={ showDate => setAttributes( { showDate } ) }
				/> ) }

				<ToggleControl
					label={ __( 'Show price' ) }
					checked={ showPrice }
					onChange={ showPrice => setAttributes( { showPrice } ) }
				/>
			</PanelBody>
		</InspectorControls>
	);

	return (
		<div { ...useBlockProps() }>
			{ plugins.woocommerce && inspectorControlsWrapper }
			{ blockHeadingText && <div className="block-heading" style={ blockHeadingStyles }>{ blockHeadingText }</div> }
			{
				plugins.woocommerce
				? ( <>
					{ 'list' === displayAs && ( products.length ? <PostList posts={ products } showFeaturedImage={ showFeaturedImage } showDate={ showDate } showAuthor={ showAuthor } showPrice={ showPrice } deviceType={ deviceType } docGlobals={ docGlobals } /> : <p>{ __( 'You order list is empty.' ) }</p> ) }
					{ 'grid' === displayAs && ( products.length ? <PostGrid posts={ products } colsMobile={ colsMobile } colsTablet={ colsTablet } showFeaturedImage={ showFeaturedImage } showDate={ showDate } showAuthor={ showAuthor } highlightFirstPost={ false } showPrice={ showPrice } deviceType={ deviceType } docGlobals={ docGlobals } /> : <p>{ __( 'You order list is empty.' ) }</p> ) }
					{ 'carousel' === displayAs && ( products.length ? <ProductCarousel posts={ products } showFeaturedImage={ showFeaturedImage } showDate={ showDate } showAuthor={ showAuthor } showPrice={ showPrice } docGlobals={ docGlobals } /> : <p>{ __( 'You order list is empty.' ) }</p> ) }
				</> ) : <WooCommerceNotActivated />
			}
		</div>
	);
}
