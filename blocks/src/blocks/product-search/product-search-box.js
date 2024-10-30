import React, { useState, useRef, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import { DebounceInput } from 'react-debounce-input';
import { handleNavigation } from '../../components/utilities';
import { Cross } from '../../assets/cross';

export function ProductSearchBox( props ) {
	const [ searchString, setSearchString ] = useState( '' );
	const [ postsState, setPostsState ] = useState( [] );
	const isStillMounted = useRef();
	const pageLoaded = useRef( true );
	const isAjaxInProgress = useRef( false );

	function clearSearch() {
		setSearchString( '' );
	}

	useEffect( () => {
		isStillMounted.current = true;

		if ( pageLoaded.current ) {
			pageLoaded.current = false;
			return;
		}

		if ( ! searchString.length ) {
			setPostsState( [] );
			return;
		}

		isAjaxInProgress.current = true;

		apiFetch( {
			path: addQueryArgs( `/wp-json/ml-blocks/v1/posts`, {
				currentPostType: 'product',
				searchString,
			} ),
		} )
		.then( ( data ) => {
			if ( isStillMounted.current ) {
				setPostsState( data.posts );
			}

			isAjaxInProgress.current = false;
		} )

		return () => {
			isStillMounted.current = false;
		};
	}, [ searchString ] );

	let visibilityClass = 'hidden';

	if ( searchString.length > 0 && postsState.length > 0 ) {
		visibilityClass = 'has-results';
	} else if ( searchString.length > 0 && postsState.length === 0 ) {
		visibilityClass = 'no-results';
	}

	return (
		<div className="search-wrapper">
			<DebounceInput
				className={ `search-input search-input--${ visibilityClass }` }
				minLength={ 3 }
				debounceTimeout={ 300 }
				value={ searchString }
				onChange={ ( e ) => setSearchString( e.target.value ) }
				{ ...props }
			/>
			<div className={ `search-results-overlay search-results-overlay--${ visibilityClass }` }></div>
			<div className={ `search-results-wrapper search-results-wrapper--${ visibilityClass }` }>
				<div className="search-results-wrapper__close-button" onClick={ clearSearch }>
					<Cross />
				</div>
				<div className="search-results-wrapper--inner">
					{ visibilityClass === 'no-results' && <div className="product-not-found-message">{ searchString } not found.</div> }
					{ postsState.map( ( post, index ) => {
						return (
							<div className="search-result__product-item" key={ index } onClick={ ( e ) => handleNavigation( e, post.id, 'product', { title: post.title, url: post.url } ) }>
								<ons-ripple color='rgba(0, 0, 0, 0.05)'></ons-ripple>
								<div className="search-result__thumbnail-wrapper">
									{ post.imageInfo && <img className="thumbnail" src={ post.imageInfo.url } /> }
								</div>
								<div className="title">{ post.title }</div>
								{ post.productInfo && <div className="price" dangerouslySetInnerHTML={ { __html: post.productInfo.priceHtml } } /> }
							</div>
						);
					} ) }
				</div>
			</div>
		</div>
	)
}

