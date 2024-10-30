import React from 'react';
import { format, formatDistance } from 'date-fns';

/**
 * A utility to decode HTML special encoded entities like &#8217;
 * There was a case found on a site where the title contained
 * &#8217; instead of the normal apostrophe which didn't decode.
 *
 * @param {String} html The HTML string
 * @returns string
 */
export function decodeHTML( html ) {
	var txt = document.createElement( 'textarea' );
	txt.innerHTML = html;
	return txt.value;
};

export function PostDate( { date, dateFormat, dateFormatCustom } ) {
	let postDate = '';

	if ( 'format-distance' !== dateFormat ) {
		try {
			postDate = format( new Date( date ), 'custom' === dateFormat ? dateFormatCustom : dateFormat );
		} catch( e ) {
		}
	} else {
		postDate = formatDistance( new Date( date ), new Date(), { addSuffix: true } );
	}

	return postDate ? (
		<div className="post-list__item-date">{ postDate }</div>
	) : false
};

export function TaxonomyBar( { taxonomy, terms, taxonomyAliases } ) {
	if ( ! terms.length ) {
		return false;
	}

	return (
		<div className="post-list__item-taxonomy-row">
			<span className="post-list__item-taxonomy-type">{ taxonomyAliases[ taxonomy ] || taxonomy }: </span>
			{
				terms.map( ( term, index ) => [
					index > 0 && ', ',
					<span key={ index } className="post-list__item-taxonomy-term">{ term.name }</span>
				] )
			}
		</div>
	);
};

export function Author( { authorName } ) {
	return (
		<div className="post-list__item-author">by - { authorName }</div>
	);
}

export function handleNavigation( e, postId = 0, postType = 'post', handleLinkObj = {} ) {
	if ( 'undefined' === typeof nativeFunctions ) {
		console.log( 'nativeFunctions is not defined.' );
		return;
	}

	if ( 'product' === postType ) {
		const { url, title } = handleLinkObj;
		nativeFunctions.handleLink( url, title, 'native' );
	} else {
		nativeFunctions.handlePost( postId );
	}
};
