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
	SelectControl,
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
			menuId,
			displayAs,
			showAuthor,
			showDate,
			showFeaturedImage,
			showPrice,
		},
		setAttributes,
	} = props;

	const [ menuArray, setMenuArray ] = useState( [] );
	const [ products, setProducts ] = useState( [] );
	const [ plugins, setPlugins ] = useState( {} );
	const isStillMounted = useRef();
	const docGlobals = useDocGlobals();

	/**
	 * Returns the current deviceType.
	 */
	 const { deviceType } = useSelect( select => {
		const { __experimentalGetPreviewDeviceType } = select( 'core/edit-post' );

		return {
			deviceType: __experimentalGetPreviewDeviceType(),
		}
	}, [] );

	useEffect( function() {
		isStillMounted.current = true;

		apiFetch( {
			path: addQueryArgs( `/ml-blocks/v1/products_from_menu`, {
				menuId,
			} ),
		} )
		.then( ( data ) => {
			if ( isStillMounted.current ) {
				setMenuArray( [ { label: 'None', value: 0 }, ...data.menus ] );
				setPlugins( data.plugins );

				if ( data.productArray && data.productArray.length > 0 ) {
					setProducts( data.productArray );
				} else {
					setProducts( [] );
				}
			}
		} )

		return () => {
			isStillMounted.current = false;
		};
	} , [ menuId ]);

	const inspectorControlsWrapper = (
		<InspectorControls>
			<PanelBody title={ __( 'Product Carousel settings' ) }>
				{ menuArray.length && <SelectControl
					label={ __( 'Menus' ) }
					options={ menuArray }
					value={ menuId }
					onChange={ menuId => setAttributes( { menuId } ) }
				/> }

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

	const blockHeadingStyles = useBlockHeadingStyles( props.attributes );

	return (
		<div { ...useBlockProps() }>
			{ plugins.woocommerce && inspectorControlsWrapper }
			{ ! menuArray.length && <div>Select a menu with products from the sidebar.</div> }
			{ blockHeadingText && <div className="block-heading" style={ blockHeadingStyles }>{ blockHeadingText }</div> }
			{
				plugins.woocommerce
				? ( <>
					{ 'list' === displayAs && ( products.length ? <PostList posts={ products } showFeaturedImage={ showFeaturedImage } showDate={ showDate } showAuthor={ showAuthor } showPrice={ showPrice } deviceType={ deviceType } docGlobals={ docGlobals } /> : <p>{ __( 'No products found.' ) }</p> ) }
					{ 'grid' === displayAs && ( products.length ? <PostGrid posts={ products } colsMobile={ colsMobile } colsTablet={ colsTablet } showFeaturedImage={ showFeaturedImage } showDate={ showDate } showAuthor={ showAuthor } highlightFirstPost={ false } showPrice={ showPrice } deviceType={ deviceType } docGlobals={ docGlobals } /> : <p>{ __( 'No products found.' ) }</p> ) }
					{ 'carousel' === displayAs && ( products.length ? <ProductCarousel posts={ products } showFeaturedImage={ showFeaturedImage } showDate={ showDate } showAuthor={ showAuthor } showPrice={ showPrice } docGlobals={ docGlobals } /> : <p>{ __( 'No products found.' ) }</p> ) }
				 </> )
				: <WooCommerceNotActivated />
			}
		</div>
	);
}
