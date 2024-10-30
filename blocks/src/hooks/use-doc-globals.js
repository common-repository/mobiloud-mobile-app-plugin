import { useSelect } from '@wordpress/data';

export function useDocGlobals() {
	return useSelect( select => {
		const { getEditedPostAttribute } = select( 'core/editor' );

		let metaObj = getEditedPostAttribute( 'meta' );

		return Object.keys( metaObj ).reduce( ( a, c ) => {
			if ( c.startsWith( '_ml_' ) ) {
				return {
					...a,
					[ c.substr( 4 ) ]: metaObj[ c ]
				}
			} else {
				return a;
			}
		}, {} );
	} );
}

export function useUserDefinedGlobalFontColors() {
	return useSelect( select => {
		const { getEditedPostAttribute } = select( 'core/editor' );

		let metaObj = getEditedPostAttribute( 'meta' );
		let useUserDefinedGlobalFontColorsMeta = Object.keys( metaObj ).filter( key => '_mlglobal_userfontcolors' === key );

		return useUserDefinedGlobalFontColorsMeta.length > 0 ? metaObj['_mlglobal_userfontcolors'] : {};
	} );
} 

export function useFontStyles( fontWeight = 100, fontSize = 1, lineHeight = 1.2, fontFamily = 'Roboto' ) {
	return `${ fontWeight } ${ fontSize }rem/${ lineHeight }rem ${ fontFamily }`;
}

export function useBlockHeadingStyles( attributes ) {
	const {
		blockHeadingFontFamily,
		blockHeadingFontSize,
		blockHeadingFontWeight,
		blockHeadingLineHeight,
		blockHeadingColor,
	} = attributes;

	return {
		color: blockHeadingColor,
		font: useFontStyles( blockHeadingFontWeight, blockHeadingFontSize, blockHeadingLineHeight, blockHeadingFontFamily )
	};
}

export function usePostItemStyles( docGlobals ) {
	const {
		titleColor,
		titleFont,
		titleFontSize,
		titleFontWeight,
		titleLineHeight,
		metaColor,
		metaFont,
		metaFontSize,
		metaFontWeight,
		metaLineHeight,
		bodyColor,
		bodyFont,
		bodyFontSize,
		bodyFontWeight,
		bodyLineHeight,
		wooPriceColor,
		wooPriceFont,
		wooPriceFontSize,
		wooPriceFontWeight,
		wooPriceLineHeight,
		headingColor,
		headingFont,
		headingFontSize,
		headingFontWeight,
		headingLineHeight,
	} = docGlobals;

	return {
		titleStyles: {
			color: titleColor,
			font: useFontStyles( titleFontWeight, titleFontSize, titleLineHeight, titleFont ),
		},
		metaStyles: {
			color: metaColor,
			font: useFontStyles( metaFontWeight, metaFontSize, metaLineHeight, metaFont ),
		},
		bodyStyles: {
			color: bodyColor,
			font: useFontStyles( bodyFontWeight, bodyFontSize, bodyLineHeight, bodyFont ),
		},
		wooPriceStyles: {
			color: wooPriceColor,
			font: useFontStyles( wooPriceFontWeight, wooPriceFontSize, wooPriceLineHeight, wooPriceFont ),
		},
		headingStyles: {
			color: headingColor,
			font: useFontStyles( headingFontWeight, headingFontSize, headingLineHeight, headingFont ),
		},
	}
}
