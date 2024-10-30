import React, { useState, useRef, useEffect } from 'react';
import Select from 'react-select';
import { ProductCarousel } from '../../components/post-carousel';
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

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';

import {
	PanelBody,
	RangeControl,
	SelectControl,
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
			orderby,
			order,
			postCount,
			selectedTerms,
			showFeaturedImage,
			showPrice,
			blockHeadingText,
		},
		setAttributes
	} = props;

	const [ postsState, setPostsState ] = useState( [] );
	const [ taxonomiesState, setTaxonomiesState ] = useState( {} );
	const [ plugins, setPlugins ] = useState( {} );
	const docGlobals = useDocGlobals();

	const taxonomies = Object.keys( taxonomiesState ).map( taxonomy => ( {
		label: taxonomiesState[ taxonomy ].label,
		value: taxonomiesState[ taxonomy ].name,
		terms: taxonomiesState[ taxonomy ].terms,
	} ) );

	const isStillMounted = useRef();

	useEffect( () => {
		isStillMounted.current = true;

		apiFetch( {
			path: addQueryArgs( `/ml-blocks/v1/posts`, {
				currentPostType: 'product',
				order,
				orderby,
				postCount,
				selectedTerms,
				taxonomies,
			} ),
		} )
		.then( ( data ) => {
			if ( isStillMounted.current ) {
				setPostsState( data.posts );
				setTaxonomiesState( data.taxonomies );
				setPlugins( data.plugins );
			}
		} )

		return () => {
			isStillMounted.current = false;
		};
	}, [
		orderby,
		order,
		postCount,
		selectedTerms,
	] );

	const inspectorControlsWrapper = (
		<InspectorControls>
			<PanelBody title={ __( 'Product Carousel settings' ) }>
				{/* Renders a range control to set the number of posts to retrieve */}
				<RangeControl
					label={ __( 'Number of posts' ) }
					value={ postCount }
					onChange={ ( postCount ) => setAttributes( { postCount } ) }
					min={ 1 }
					max={ 50 }
				/>

				{
					taxonomies.map( taxonomy => {
						return taxonomy.terms.length ? (
							<p>
								<label>{ taxonomy.label }</label>
								<Select
									value={ selectedTerms[ taxonomy.value ] }
									options={ taxonomy.terms }
									isMulti={ true }
									onChange={ ( newTerms ) => {
										setAttributes( {
											selectedTerms: {
												...selectedTerms,
												[ taxonomy.value ]: newTerms,
											}
										} )
									} }
								/>
							</p>
						) : null
					} )
				}

				<SelectControl
					label={ __( 'Order By' ) }
					options={ [
						{
							label: __( 'Date' ),
							value: 'date'
						},
						{
							label: __( 'Title' ),
							value: 'title'
						}
					] }
					value={ orderby }
					onChange={ ( orderby ) => setAttributes( { orderby } ) }
				/>

				<SelectControl
					label={ __( 'Order' ) }
					options={ [
						{
							label: __( 'ASC' ),
							value: 'asc'
						},
						{
							label: __( 'DESC' ),
							value: 'desc'
						}
					] }
					value={ order }
					onChange={ ( order ) => setAttributes( { order } ) }
				/>

				<ToggleControl
					label={ __( 'Show featured image' ) }
					checked={ showFeaturedImage }
					onChange={ showFeaturedImage => setAttributes( { showFeaturedImage } ) }
				/>

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
			{ blockHeadingText && <div className="block-heading" style={ blockHeadingStyles }>{ blockHeadingText }</div> }
			{
				plugins.woocommerce
				? <ProductCarousel posts={ postsState } showFeaturedImage={ showFeaturedImage } showPrice={ showPrice } docGlobals={ docGlobals } />
				: <WooCommerceNotActivated />
			}
		</div>
	);
}
