import React from 'react';
import { decodeHTML, handleNavigation } from '../utilities';
import Lazyload from 'react-lazyload';
import { usePostItemStyles } from '../../hooks/use-doc-globals';

export function ProductCarousel( props ) {
	const {
		isLazy,
		posts,
		showFeaturedImage,
		showPrice,
		docGlobals,
	} = props;

	const {
		titleStyles,
		metaStyles,
		bodyStyles,
		wooPriceStyles,
	} = usePostItemStyles( docGlobals );

	return (
		<div className="product-carousel">
			<div className="product-carousel--container">
				{
					posts.map( ( post, index ) => {

						const imageElements = (
							showFeaturedImage && ( <div className="product-carousel--item-image-wrapper">
								{ post.imageInfo && <img src={ post.imageInfo.url } /> }
							</div> )
						);

						return (
							<div key={ index } className="product-carousel--item" onClick={ ( e ) => handleNavigation( e, post.id, 'product', { title: post.title, url: post.url } ) }>
								<ons-ripple color='rgba(0, 0, 0, 0.05)'></ons-ripple>

								{ isLazy ? ( <Lazyload height={ 300 } offset={ 100 }>
									{ imageElements }
								</Lazyload> ) : imageElements }

								<div className="post-carousel__item-title" style={ titleStyles }>{ decodeHTML( post.title ) }</div>

								{ post.productInfo && showPrice && <div className="post-item__product-price product-price" style={ wooPriceStyles } dangerouslySetInnerHTML={ { __html: post.productInfo.priceHtml } } /> }
							</div>
						);
					} )
				}
			</div>
		</div>
	);
}

ProductCarousel.defaultProps = {
	bodyColor: '#333',
	bodyFont: '',
	posts: [],
	showAuthor: true,
	showDate: true,
	showFeaturedImage: true,
	isLazy: false,
	titleColor: '#333',
	titleFont: '',
	titleFontSize: 1,
};