import React, { useState, createContext, useContext, useRef, useEffect } from 'react';
import { PostList } from '../src/components/post-list';
import { ProductCarousel } from '../src/components/post-carousel';
import { ProductSearchBox } from '../src/blocks/product-search/product-search-box';
import { PostGrid } from '../src/components/post-grid';
import { PostListExpanded } from '../src/components/post-list-expanded';
import PullToRefresh from 'react-simple-pull-to-refresh';
import { useBlockHeadingStyles } from '../src/hooks/use-doc-globals';
import 'onsenui/js/onsenui.min.js';

const ModuleContext = createContext( {} );

const Posts = () => {
	const moduleContext = useContext( ModuleContext );
	const attrs = JSON.parse( moduleContext.attrs );

	const {
		aspectRatio,
		blockHeadingText,
		colsMobile,
		colsTablet,
		dateFormat,
		displayAs,
		dividerColor,
		dividerStyle,
		excerptLength,
		highlightFirstPost,
		infiniteScroll,
		loadMoreButton,
		postItemSpacing,
		showAuthor,
		showDate,
		showDivider,
		showExcerpt,
		showFeaturedImage,
		showPrice,
		showTaxonomies,
		taxonomyAliases,
	} = attrs;

	const [ posts, setPosts ] = useState( JSON.parse( moduleContext.posts ).posts );

	const { ajaxUrl, siteUrl } = ml_list_builder_assets;

	let pageNumber = useRef( 1 );
	let isAjaxInProgress = useRef( false );
	let [ noMoreAjax, setNoMoreAjax ] = useState( false );
	
	function onScrollLoadMore() {
		if ( isAjaxInProgress.current ) {
			return;
		}

		if ( window.innerHeight + window.scrollY > document.getElementById( 'list-builder-root' ).offsetHeight - 10 ) {
			loadMorePosts();
		}
	}

	useEffect( () => {
		if ( ! infiniteScroll ) {
			return;
		}

		document.addEventListener( 'scroll', onScrollLoadMore );

		return () => document.removeEventListener( 'scroll', onScrollLoadMore )
	}, [ noMoreAjax ] );

	function loadMorePosts() {
		if ( noMoreAjax ) {
			return;
		}

		var formData = new FormData();
		formData.append( 'attrs', moduleContext.attrs );
		formData.append( 'action', 'mobiloud_block_posts_load_more' );
		formData.append( 'page', ++pageNumber.current );

		isAjaxInProgress.current = true;

		fetch( ajaxUrl, {
			method: 'POST',
			body: formData,
		} ).then( response => {
			if ( response.status !== 200 ) {
				isAjaxInProgress.current = false;
				return false;
			}

			return response.json();
		} ).then( data => {
			if ( ! data.success ) {
				isAjaxInProgress.current = false;
				return;
			}

			const newPosts = data.data.posts;

			if ( ! newPosts.length ) {
				isAjaxInProgress.current = false;
				setNoMoreAjax( true );
				return;
			}

			setPosts( ( prev ) => [ ...prev, ...newPosts ] )

			isAjaxInProgress.current = false;
		} )
	}

	const blockHeadingStyles = useBlockHeadingStyles( attrs );

	return (
		<div className="wp-block-mobiloud-posts ml-block">
			<div className="block-heading" style={ blockHeadingStyles }>{ blockHeadingText }</div>
			{
				posts.length ? ( <>
					{ 'list' === displayAs && <PostList
						dateFormat={ dateFormat }
						dividerColor={ dividerColor }
						dividerStyle={ dividerStyle }
						excerptLength={ excerptLength }
						highlightFirstPost={ highlightFirstPost }
						posts={ posts }
						postItemSpacing={ postItemSpacing }
						showAuthor={ showAuthor }
						showDate={ showDate }
						showDivider={ showDivider }
						showExcerpt={ showExcerpt }
						showFeaturedImage={ showFeaturedImage }
						showPrice={ showPrice }
						showTaxonomies={ showTaxonomies }
						taxonomyAliases={ taxonomyAliases }
						isLazy={ true }
						docGlobals={ docGlobals }
					/> }
					{ 'grid' === displayAs && <PostGrid
						aspectRatio={ aspectRatio }
						dateFormat={ dateFormat }
						excerptLength={ excerptLength }
						highlightFirstPost={ highlightFirstPost }
						posts={ posts }
						postItemSpacing={ postItemSpacing }
						showAuthor={ showAuthor }
						showDate={ showDate }
						showDivider={ showDivider }
						showExcerpt={ showExcerpt }
						showFeaturedImage={ showFeaturedImage }
						showPrice={ showPrice }
						showTaxonomies={ showTaxonomies }
						taxonomyAliases={ taxonomyAliases }
						colsMobile={ colsMobile }
						colsTablet={ colsTablet }
						isLazy={ true }
						docGlobals={ docGlobals }
					/> }
					{ 'list-expanded' === displayAs && <PostListExpanded
						dateFormat={ dateFormat }
						dividerColor={ dividerColor }
						dividerStyle={ dividerStyle }
						excerptLength={ excerptLength }
						highlightFirstPost={ highlightFirstPost }
						posts={ posts }
						postItemSpacing={ postItemSpacing }
						showAuthor={ showAuthor }
						showDate={ showDate }
						showDivider={ showDivider }
						showExcerpt={ showExcerpt }
						showFeaturedImage={ showFeaturedImage }
						showPrice={ showPrice }
						showTaxonomies={ showTaxonomies }
						taxonomyAliases={ taxonomyAliases }
						colsMobile={ colsMobile }
						colsTablet={ colsTablet }
						isLazy={ true }
						docGlobals={ docGlobals }
					/> }
					{ loadMoreButton && (
						<div className="load-more-button-wrapper">
							<ons-button modifier="cta" onClick={ loadMorePosts }>Load more</ons-button>
						</div>
					) }
				</> ) : <PostsSkeleton />
			}
			{ infiniteScroll && ! noMoreAjax && (
				<div className="spinner">
					<ons-progress-circular indeterminate></ons-progress-circular>
				</div>
			) }
		</div>
	)
};

const Divider = () => {
	const moduleContext = useContext( ModuleContext );

	const {
		borderColor,
		borderStyle,
		dividerBottomMargin,
		dividerHorizontalAlignment,
		dividerTopMargin,
		dividerWidth,
	} = moduleContext.attrs;

	const dividerWrapperStyles = {
		display: 'flex',
		justifyContent: dividerHorizontalAlignment,
		overflow: 'hidden',
		marginTop: `${ dividerTopMargin }px`,
		marginBottom: `${ dividerBottomMargin }px`,
	};
	
	const dividerStyles = {
		width: `${ dividerWidth }%`,
		height: `1px`,
		borderBottom: `1px ${ borderStyle } ${ borderColor }`,
	};

	return (
		<div className="wp-block-mobiloud-divider ml-block">
			<div style={ dividerWrapperStyles }>
				<div style={ dividerStyles }></div>
			</div>
		</div>
	)
}

const ProductSearchContainer = () => {
	const moduleContext = useContext( ModuleContext );
	const attrs = JSON.parse( moduleContext.attrs );

	const {
		searchPlaceholder,
	} = attrs;

	return (
		<div className="wp-block-mobiloud-product-search ml-block">
			<ProductSearchBox disabled={ false } placeholder={ searchPlaceholder } />
		</div>
	)
};

const ProductCarouselContainer = () => {
	const moduleContext = useContext( ModuleContext );
	const attrs = JSON.parse( moduleContext.attrs );
	const {
		showFeaturedImage,
		showPrice,
		blockHeadingText,
	} = attrs;

	const { posts } = JSON.parse( moduleContext.posts );

	const blockHeadingStyles = useBlockHeadingStyles( attrs );

	return (
		<div className="wp-block-mobiloud-product-carousel ml-block">
			{ blockHeadingText && <div className="block-heading" style={ blockHeadingStyles }>{ blockHeadingText }</div> }
			<ProductCarousel posts={ posts } showFeaturedImage={ showFeaturedImage } showPrice={ showPrice } isLazy={ true } docGlobals={ docGlobals } />
		</div>
	);
}

const ProductsFromMenuContainer = () => {
	const moduleContext = useContext( ModuleContext );
	const {
		blockHeadingText,
		colsMobile,
		colsTablet,
		displayAs,
		showAuthor,
		showDate,
		showFeaturedImage,
		showPrice,
	} = moduleContext.attrs;

	const { posts } = moduleContext.posts;

	const blockHeadingStyles = useBlockHeadingStyles( moduleContext.attrs );

	return (
		<div className="wp-block-mobiloud-products-from-menu ml-block">
			{ blockHeadingText && <div className="block-heading" style={ blockHeadingStyles }>{ blockHeadingText }</div> }
			{ 'list' === displayAs && posts.length && <PostList posts={ posts } showFeaturedImage={ showFeaturedImage } showDate={ showDate } showAuthor={ showAuthor } showPrice={ showPrice } isLazy={ true } docGlobals={ docGlobals } /> }
			{ 'grid' === displayAs && posts.length && <PostGrid posts={ posts } colsMobile={ colsMobile } colsTablet={ colsTablet } highlightFirstPost={ false } showFeaturedImage={ showFeaturedImage } showDate={ showDate } showAuthor={ showAuthor } showPrice={ showPrice } isLazy={ true } docGlobals={ docGlobals } /> }
			{ 'carousel' === displayAs && posts.length && <ProductCarousel posts={ posts } showFeaturedImage={ showFeaturedImage } showDate={ showDate } showAuthor={ showAuthor } showPrice={ showPrice } isLazy={ true } docGlobals={ docGlobals } /> }
		</div>
	)
}

const RecentlyPurchasedProductsContainer = () => {
	const moduleContext = useContext( ModuleContext );
	const {
		blockHeadingText,
		colsMobile,
		colsTablet,
		displayAs,
		showAuthor,
		showDate,
		showFeaturedImage,
		showPrice,
	} = moduleContext.attrs;

	const { posts } = moduleContext.posts;

	const blockHeadingStyles = useBlockHeadingStyles( moduleContext.attrs );

	return (
		<div className="wp-block-mobiloud-recently-purchased-products ml-block">
			{ blockHeadingText && <div className="block-heading" style={ blockHeadingStyles }>{ blockHeadingText }</div> }
			{ 'list' === displayAs && posts.length && <PostList posts={ posts } showFeaturedImage={ showFeaturedImage } showDate={ showDate } showAuthor={ showAuthor } showPrice={ showPrice } isLazy={ true } docGlobals={ docGlobals } /> }
			{ 'grid' === displayAs && posts.length && <PostGrid posts={ posts } colsMobile={ colsMobile } colsTablet={ colsTablet } highlightFirstPost={ false } showFeaturedImage={ showFeaturedImage } showDate={ showDate } showAuthor={ showAuthor } showPrice={ showPrice } isLazy={ true } docGlobals={ docGlobals } /> }
			{ 'carousel' === displayAs && posts.length && <ProductCarousel posts={ posts } showFeaturedImage={ showFeaturedImage } showDate={ showDate } showAuthor={ showAuthor } showPrice={ showPrice } isLazy={ true } docGlobals={ docGlobals } /> }
		</div>
	)
}

const Heading = () => {
	const moduleContext = useContext( ModuleContext );
	const {
		titleBottomMargin,
		titleText,
		titleTopMargin,
	} = moduleContext.attrs;

	const {
		headingColor,
		headingFont,
		headingFontSize,
		headingFontWeight,
		headingLineHeight,
	} = docGlobals;

	const textStyles = {
		color: headingColor,
		font: `${ headingFontWeight } ${ headingFontSize }rem/${ headingLineHeight }rem ${ headingFont }`,
	}

	const wrapperStyles = {
		marginTop: `${ titleTopMargin }px`,
		marginBottom: `${ titleBottomMargin }px`,
	}

	return (
		<div className="wp-block-mobiloud-heading ml-block">
			<div className="heading--wrapper" style={ wrapperStyles }>
				<div style={ textStyles }>{ titleText  }</div>
			</div>
		</div>
	)
}

const ClassContainer = ( { blockName, id, children } ) => {
	const filteredBlockName = blockName.replace( '/', '-' );

	return (
		<div className={ `ml-module ml-module--${ filteredBlockName } ml-module--${ filteredBlockName }--${ id }` }>
			{ children }
		</div>
	);
}

export const ListBuilderApp = () => {
	function handlePullDownRefresh() {
		return new Promise( ( resolve, reject ) => {
			if ( 'undefined' === typeof nativeFunctions ) {
				reject();
			}

			nativeFunctions.reloadWebview();
			setTimeout( () => {
				resolve();
			}, 30000 );
		} )
	}

	return (
		<PullToRefresh
			className="pull-to-refresh-loader"
			onRefresh={ handlePullDownRefresh }
			pullingContent=''
			refreshingContent={ <ons-progress-circular indeterminate></ons-progress-circular> }
		>
		{
			ml_block_data.map( ( block, index ) => {
				const filteredBlockName = block.blockName.replace( '/', '-' );
		
				switch ( block.blockName ) {
					case 'mobiloud/posts':
						return (
							<ModuleContext.Provider key={ index } value={ block.blockData }>
								<ClassContainer blockName={ block.blockName } id={ index }>
									<Posts />
								</ClassContainer>
							</ModuleContext.Provider>
						);
		
					case 'mobiloud/divider':
						return (
							<ModuleContext.Provider key={ index } value={ block.blockData }>
								<ClassContainer blockName={ block.blockName } id={ index }>
									<Divider />
								</ClassContainer>
							</ModuleContext.Provider>
						);
		
					case 'mobiloud/product-carousel':
						return (
							<ModuleContext.Provider key={ index } value={ block.blockData }>
								<ClassContainer blockName={ block.blockName } id={ index }>
									<ProductCarouselContainer />
								</ClassContainer>
							</ModuleContext.Provider>
						);
		
					case 'mobiloud/product-search':
						return (
							<ModuleContext.Provider key={ index } value={ block.blockData }>
								<ClassContainer blockName={ block.blockName } id={ index }>
									<ProductSearchContainer />
								</ClassContainer>
							</ModuleContext.Provider>
						);
		
					case 'mobiloud/products-from-menu':
						return (
							<ModuleContext.Provider key={ index } value={ block.blockData }>
								<ClassContainer blockName={ block.blockName } id={ index }>
									<ProductsFromMenuContainer />
								</ClassContainer>
							</ModuleContext.Provider>
						);
		
					case 'mobiloud/recently-purchased-products':
						return (
							<ModuleContext.Provider key={ index } value={ block.blockData }>
								<ClassContainer blockName={ block.blockName } id={ index }>
									<RecentlyPurchasedProductsContainer />
								</ClassContainer>
							</ModuleContext.Provider>
						);
		
					case 'mobiloud/heading':
						return (
							<ModuleContext.Provider key={ index } value={ block.blockData }>
								<ClassContainer blockName={ block.blockName } id={ index }>
									<Heading />
								</ClassContainer>
							</ModuleContext.Provider>
						);
		
					default:
						return null;
				}
			} )
		}		
		</PullToRefresh>
	)
};
