import React, { useRef, useState, useEffect } from 'react';
import { PostList } from '../../components/post-list';
import { PostListExpanded } from '../../components/post-list-expanded';
import { PostGrid } from '../../components/post-grid';
import Select from 'react-select';
import { PostsSkeleton } from './posts-skeleton';
import { useDocGlobals, useBlockHeadingStyles } from '../../hooks/use-doc-globals';
import { Button } from "@wordpress/components";

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import {
	TextControl,
	ToggleControl,
	RangeControl,
	SelectControl,
	RadioControl,
	ColorPalette,
	ColorIndicator,
	CardBody,
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

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
			aspectRatio,
			blockHeadingText,
			colsMobile,
			colsTablet,
			currentPostType,
			dateFormat,
			dateFormatCustom,
			displayAs,
			dividerColor,
			dividerStyle,
			excerptLength,
			highlightFirstPost,
			infiniteScroll,
			loadMoreButton,
			orderby,
			order,
			postCount,
			postItemSpacing,
			selectedTerms,
			showTaxonomies,
			showFeaturedImage,
			showAuthor,
			showDate,
			showExcerpt,
			showDivider,
			showPrice,
			showTitle,
			taxonomyAliases,
		},
		setAttributes,
		clientId,
	} = props;

	const docGlobals = useDocGlobals();

	/**
	 * We don't want to query these post types,
	 * se we exclude them.
	 */
	const EXCLUDED_POST_TYPES = [
		'page',
		'attachment',
		'wp_block',
		'list-builder',
	];

	/**
	 * Returns the current deviceType.
	 */
	const { deviceType } = useSelect( select => {
		const { __experimentalGetPreviewDeviceType } = select( 'core/edit-post' );

		return {
			deviceType: __experimentalGetPreviewDeviceType(),
		}
	}, [] );

	/**
	 * Holds the previously set Post type.
	 * Used in comparison logic to set state if the post type
	 * has changed.
	 */
	const previousPostTypeRef = useRef( currentPostType );

	/**
	 * Sets the flag when a wrong date format is set.
	 * Use to show/hide instructions.
	 */
	const [ dateFormatError, setDateFormatError ] = useState( false );

	/**
	 * Object dictionary which holds alias of a taxonomy.
	 * Basically, the requirement was to display a different "name"
	 * for a taxonnomy. For example, show "Sections" instead of "Category".
	 */
	const [ taxonomyAlias, setTaxonomyAlias ] = useState( false );

	
	const [ postTypesState, setPostTypesState ] = useState( {} );
	const [ postsState, setPostsState ] = useState( [] );
	const [ taxonomiesState, setTaxonomiesState ] = useState( {} );

	let IS_PRODUCT = useRef( false );

	const displayAsOptions = [
		{ label: __( 'List' ), value: "list" },
		{ label: __( 'List (expanded)' ), value: "list-expanded" },
		{ label: __( 'Grid' ), value: "grid" },
	];

	const taxonomies = Object.keys( taxonomiesState ).map( taxonomy => ( {
		label: taxonomiesState[ taxonomy ].label,
		value: taxonomiesState[ taxonomy ].name,
		terms: taxonomiesState[ taxonomy ].terms,
	} ) );

	const postTypes = Object.keys( postTypesState ).map( postType => ( {
		label: postTypesState[ postType ].label,
		value: postTypesState[ postType ].name,
	} ) ).filter( postType => ! EXCLUDED_POST_TYPES.includes( postType.value ) );

	/**
	 * Returns the ID of the last block in the Gutenberg editor.
	 * Used to check whether if the last block is the mobiloud/posts
	 * block.
	 */
	const { lastBlockId } = useSelect( select => {
		const { getBlocks } = select( 'core/block-editor' );

		/**
		 * Return array of all the blocks in the Gutenberg editor screen.
		 */
		const blocks = getBlocks();

		/**
		 * Get the last block from the array.
		 */
		const lastBlock = Array.isArray( blocks ) && blocks.length > 0 ? blocks[ blocks.length - 1 ] : {};

		if ( ! lastBlock ) {
			return;
		}

		return {
			lastBlockId: lastBlock.clientId,
		}
	} );

	useEffect( () => {
		IS_PRODUCT.current = 'product' === currentPostType;
	}, [ currentPostType ] );

	const isStillMounted = useRef();
	useEffect( () => {
		isStillMounted.current = true;

		/**
		 * Reset the `selectedTerms` and the `showTaxonomies` objects
		 * if the post type is changed.
		 */
		 if ( previousPostTypeRef.current !== currentPostType ) {
			setAttributes( { showTaxonomies: {} } );
			setAttributes( { selectedTerms: {} } );

			if ( ! IS_PRODUCT.current ) {
				setAttributes( { showTitle: true } );
				setAttributes( { displayAs: 'list' } )
			}

			setTaxonomiesState( {} );
			previousPostTypeRef.current = currentPostType;
		}

		apiFetch( {
			path: addQueryArgs( `/ml-blocks/v1/posts`, {
				order,
				orderby,
				currentPostType,
				postCount,
				showAuthor,
				highlightFirstPost,
				showDate,
				showFeaturedImage,
				showTaxonomies,
				selectedTerms,
				displayAs,
			} ),
		} )
		.then( ( data ) => {
			if ( isStillMounted.current ) {
				setPostsState( data.posts );
				setPostTypesState( data.postTypes );
				setTaxonomiesState( data.taxonomies );
			}
		} )

		return () => {
			isStillMounted.current = false;
		};
	}, [
		currentPostType,
		highlightFirstPost,
		orderby,
		order,
		postCount,
		selectedTerms,
	] );

	if ( clientId === lastBlockId ) {
		setAttributes( { loadMoreButton: false } );
	} else {
		setAttributes( { infiniteScroll: false } );
	}

	const defaultColors = [
		{ name: __( 'Black' ), color: '#000' },
		{ name: __( 'Dark Gray' ), color: '#333' },
		{ name: __( 'Medium Gray' ), color: '#666' },
		{ name: __( 'Light Gray' ), color: '#999' },
	];

	/**
	 * This is the settings field which appears on the right
	 * of a block when it is selected.
	 */
	const inspectorControlsWrapper = (
		<InspectorControls>
			<CardBody title={ __( 'Posts settings' ) } size="small">
				{/* Renders a range control to set the number of posts to retrieve */}
				<RangeControl
					label={ __( 'Number of posts' ) }
					value={ postCount }
					onChange={ ( postCount ) => setAttributes( { postCount } ) }
					min={ 1 }
					max={ 50 }
				/>

				{
					( 'list' === displayAs || 'list-expanded' === displayAs || 'grid' === displayAs )
					&& ( <ToggleControl
						label={ __( 'Show excerpt' ) }
						checked={ showExcerpt }
						onChange={ showExcerpt => setAttributes( { showExcerpt } ) }
					/> )
				}

				{/* Range field to set the length of the excerpt. */}
				{ showExcerpt && ( 'list' === displayAs || 'list-expanded' === displayAs || 'grid' === displayAs )
					&& (<RangeControl
					label={ __( 'Excerpt length (in words)' ) }
					value={ excerptLength }
					onChange={ ( excerptLength ) => setAttributes( { excerptLength } ) }
					min={ 5 }
					max={ 50 }
				/> ) }
				<p style={ { color: '#007cba' } }>(If the <strong>highlight first post item</strong> option is set, then excerpt length adds 10 more words to the highlighted post item.)</p>

				{/* Select drop down to set a post type. */}
				{ postTypes && <SelectControl
					label={ __( 'Post types' ) }
					options={ postTypes }
					value={ currentPostType }
					onChange={ currentPostType => setAttributes( { currentPostType } ) }
				/> }

				{/* Renders 1 or more toggle controls for each taxonomy
					that is attached to the selected post type. */}
				{
					taxonomies.map( taxonomy => (
						taxonomy.terms.length ? ( <ToggleControl
							label={ `Show ${ taxonomy.label }` }
							checked={ showTaxonomies[ taxonomy.value ] || false }
							onChange={ val => setAttributes( {
								showTaxonomies: {
									...showTaxonomies,
									[ taxonomy.value ]: val
								}
							} ) }
						/> ) : null
					) )
				}

				{/* A multi-select dropdown to set taxonomy terms attached to a post type. */}
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

				{ <p>
					{/* Button to show/hide the text fields to alias
						taxonomy aliases. This is added to declutter the UI. */}
					<p>
						<Button
							isDestructive
							isSmall
							onClick={ () => setTaxonomyAlias( ! taxonomyAlias ) }
						>
							{ __( 'Change taxonomy labels' ) }
						</Button>
					</p>

					{/* Renders 1 or more text fields for each taxonomy
					that is attached to the selected post type. */}
					{
						taxonomyAlias && taxonomies.map( taxonomy => {
							return taxonomy.terms.length ? (
								<TextControl
									label={ `Label for '${ taxonomy.label }' (default: ${ taxonomy.label })` }
									onChange={ ( aliasText ) => {
										setAttributes( {
											taxonomyAliases: {
												...taxonomyAliases,
												[ taxonomy.label ]: aliasText,
											}
										} );
									} }
									value={ taxonomyAliases[ taxonomy.label ] }
								/>
							) : null;
						} )
					}
				</p> }

				{/* Renders radio fields to set the layout. */}
				<RadioControl
					label={ __( 'Display as' ) }
					selected={ displayAs }
					options={ displayAsOptions }
					onChange={ displayAs => setAttributes( { displayAs } ) }
				/>

				{ 'grid' === displayAs && <RadioControl
					label={ __( 'Aspect ratio' ) }
					selected={ aspectRatio }
					options={ [
						{
							label: '1:1',
							value: '1-1',
						},
						{
							label: '16:9',
							value: '16-9',
						},
					] }
					onChange={ aspectRatio => setAttributes( { aspectRatio } ) }
				/> }

				{/* Renders range control to set number of columns on responsive devices.
					Only works if `Display as` is set to `grid` */}
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

				{
					<RangeControl
						label={ __( 'Post item vertical spacing (rem):' ) }
						value={ postItemSpacing }
						min={ 0 }
						max={ 10 }
						step={ 0.01 }
						onChange={ postItemSpacing => setAttributes( { postItemSpacing } ) }
					/>
				}

				{/* Renders a toggle control to show/hide dividers between list items.
					Only works if `display as` is set to `list` or `list-expanded` */}
				{
					( 'list' === displayAs || 'list-expanded' === displayAs ) && (
					<ToggleControl
						label={ __( 'Show divider between list items' ) }
						checked={ showDivider }
						onChange={ showDivider => setAttributes( { showDivider } ) }
					/> )
				}

				{
					( 'list' === displayAs || 'list-expanded' === displayAs ) && showDivider && (
						<>
							<span style={ { fontFamily: 'monospace' } }>{ dividerColor }</span>
							<ColorIndicator colorValue={ dividerColor } />
							<ColorPalette
								colors={ defaultColors }
								value={ dividerColor }
								onChange={ dividerColor => setAttributes( { dividerColor } ) }
								clearable={ false }
							/>

							<RadioControl
								label={ __( 'Post item divider style' ) }
								options={ [
									{ label: __( 'Solid' ), value: 'solid' },
									{ label: __( 'Dotted' ), value: 'dotted' },
									{ label: __( 'Dashed' ), value: 'dashed' },
								] }
								selected={ dividerStyle }
								onChange={ dividerStyle => setAttributes( { dividerStyle } ) }
							/>
						</>
					)
				}

				{
					( 'list' === displayAs || 'list-expanded' === displayAs || 'grid' === displayAs )
					&& ( <ToggleControl
						label={ __( 'Highlight first post item' ) }
						checked={ highlightFirstPost }
						onChange={ highlightFirstPost => setAttributes( { highlightFirstPost } ) }
					/> )
				}

				{ IS_PRODUCT.current && ( 'list' === displayAs || 'list-expanded' === displayAs || 'grid' === displayAs ) && <ToggleControl
					label={ __( 'Show title' ) }
					checked={ showTitle }
					onChange={ showTitle => setAttributes( { showTitle } ) }
				/> }

				<ToggleControl
					label={ __( 'Show featured image' ) }
					checked={ showFeaturedImage }
					onChange={ showFeaturedImage => setAttributes( { showFeaturedImage } ) }
				/>

				<ToggleControl
					label={ __( 'Show author' ) }
					checked={ showAuthor }
					onChange={ showAuthor => setAttributes( { showAuthor } ) }
				/>

				<ToggleControl
					label={ __( 'Show price' ) }
					checked={ showPrice }
					onChange={ showPrice => setAttributes( { showPrice } ) }
				/>

				<ToggleControl
					label={ __( 'Show date' ) }
					checked={ showDate }
					onChange={ showDate => setAttributes( { showDate } ) }
				/>

				{
					showDate && (
						<RadioControl
							label={ __( 'Date format' ) }
							selected={ dateFormat }
							options={ [
								{ label: __( 'do MMMM, yyyy' ), value: "do MMMM, yyyy" },
								{ label: __( 'dd/MM/yyyy' ), value: "dd/MM/yyyy" },
								{ label: __( '`n` days ago' ), value: "format-distance" },
								{ label: __( 'Custom format' ), value: "custom" },
							] }
							onChange={ dateFormat => setAttributes( { dateFormat } ) }
						/>
					)
				}

				{ ( clientId === lastBlockId ) ? ( <ToggleControl
					label={ __( 'Infinite scroll' ) }
					checked={ infiniteScroll }
					onChange={ infiniteScroll => setAttributes( { infiniteScroll } ) }
				/> ) : <ToggleControl
					label={ __( 'Show load more button' ) }
					checked={ loadMoreButton }
					onChange={ loadMoreButton => setAttributes( { loadMoreButton } ) }
				/> }

				{
					'custom' === dateFormat && (
						<>
							<TextControl
								label={ __( 'Custom format' ) }
								onChange={ ( dateFormatCustom ) => {
									setAttributes( { dateFormatCustom } );
								} }
								value={ dateFormatCustom }
							/>
							{ dateFormatError && <p style={ { color: '#DC3232' } }>The date format is invalid. Please visit this <a target="_blank" href="https://date-fns.org/v2.19.0/docs/format">link</a> to find out more formatting options.</p> }
						</>
					)
				}

				{/* Select field to set the orderby query - title | date */}
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

				{/* Select field to set the order - asc | desc */}
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
			</CardBody>
		</InspectorControls>
	);

	const blockHeadingStyles = useBlockHeadingStyles( props.attributes );

	return (
		<div { ...useBlockProps() }>
			{ inspectorControlsWrapper }
			<div className="block-heading" style={ blockHeadingStyles }>{ blockHeadingText }</div>
			{
				postsState && postsState.length ? ( <>
					{ 'list' === displayAs && <PostList
						dateFormat={ dateFormat }
						dateFormatCustom={ dateFormatError }
						deviceType={ deviceType }
						dividerColor={ dividerColor }
						dividerStyle={ dividerStyle }
						excerptLength={ excerptLength }
						highlightFirstPost={ highlightFirstPost }
						posts={ postsState }
						postItemSpacing={ postItemSpacing }
						showAuthor={ showAuthor }
						showDate={ showDate }
						showDivider={ showDivider }
						showExcerpt={ showExcerpt }
						showFeaturedImage={ showFeaturedImage }
						showPrice={ showPrice }
						showTaxonomies={ showTaxonomies }
						taxonomyAliases={ taxonomyAliases }
						docGlobals={ docGlobals }
					/> }
					{ 'list-expanded' === displayAs && <PostListExpanded
						dateFormat={ dateFormat }
						dateFormatCustom={ dateFormatError }
						deviceType={ deviceType }
						dividerColor={ dividerColor }
						dividerStyle={ dividerStyle }
						excerptLength={ excerptLength }
						highlightFirstPost={ highlightFirstPost }
						posts={ postsState }
						postItemSpacing={ postItemSpacing }
						showAuthor={ showAuthor }
						showDate={ showDate }
						showDivider={ showDivider }
						showExcerpt={ showExcerpt }
						showFeaturedImage={ showFeaturedImage }
						showPrice={ showPrice }
						showTaxonomies={ showTaxonomies }
						taxonomyAliases={ taxonomyAliases }
						docGlobals={ docGlobals }
					/> }
					{ 'grid' === displayAs && <PostGrid
						aspectRatio={ aspectRatio }
						colsMobile={ colsMobile }
						colsTablet={ colsTablet }
						dateFormat={ dateFormat }
						dateFormatCustom={ dateFormatError }
						deviceType={ deviceType }
						excerptLength={ excerptLength }
						highlightFirstPost={ highlightFirstPost }
						posts={ postsState }
						postItemSpacing={ postItemSpacing }
						showAuthor={ showAuthor }
						showDate={ showDate }
						showDivider={ showDivider }
						showExcerpt={ showExcerpt }
						showFeaturedImage={ showFeaturedImage }
						showPrice={ showPrice }
						showTaxonomies={ showTaxonomies }
						taxonomyAliases={ taxonomyAliases }
						docGlobals={ docGlobals }
					/> }
					{ loadMoreButton && clientId !== lastBlockId && (
						<div className="load-more-button-wrapper">
							<Button modifier="cta">{ __( 'Load more' ) }</Button>
						</div>
					) }
				</> ) : <PostsSkeleton />
			}
		</div>
	);
}
