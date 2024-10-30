import { __ } from '@wordpress/i18n';

export function useFonts() {
	return [
		{
			label: 'Roboto',
			value: 'Roboto',
			weights: [
				{ value: '100', label: '100' },
				{ value: 'italic 100', label: '100 italic' },
				{ value: '300', label: '300' },
				{ value: 'italic 300', label: '300 italic' },
				{ value: '500', label: '500' },
				{ value: 'italic 500', label: '500 italic' },
				{ value: '700', label: '700' },
				{ value: 'italic 700', label: '700 italic' },
				{ value: '900', label: '900' },
				{ value: 'italic 900', label: '900 italic' },
			],
		},
		{
			label: 'Roboto Condensed',
			value: 'Roboto Condensed',
			weights: [
				{ label: '300', value: '300' },
				{ label: 'italic 300', value: '300 italic' },
				{ label: '400', value: '400' },
				{ label: 'italic 400', value: '400 italic' },
				{ label: '700', value: '700' },
				{ label: 'italic 700', value: '700 italic' },
			],
		},
		{
			label: 'Open Sans',
			value: 'Open Sans',
			weights: [
				{ label: '100', value: '100' },
				{ label: 'italic 100', value: '100 italic' },
				{ label: '400', value: '400' },
				{ label: 'italic 400', value: '400 italic' },
				{ label: '600', value: '600' },
				{ label: 'italic 600', value: '600 italic' },
				{ label: '700', value: '700' },
				{ label: 'italic 700', value: '700 italic' },
				{ label: '800', value: '800' },
				{ label: 'italic 800', value: '800 italic' },
			],
		},
		{
			label: 'Montserrat',
			value: 'Montserrat',
			weights: [
				{ label: '100', value: '100' },
				{ label: 'italic 100', value: '100 italic' },
				{ label: '200', value: '200' },
				{ label: 'italic 200', value: '200 italic' },
				{ label: '300', value: '300' },
				{ label: 'italic 300', value: '300 italic' },
				{ label: '400', value: '400' },
				{ label: 'italic 400', value: '400 italic' },
				{ label: '500', value: '500' },
				{ label: 'italic 500', value: '500 italic' },
				{ label: '600', value: '600' },
				{ label: 'italic 600', value: '600 italic' },
				{ label: '700', value: '700' },
				{ label: 'italic 700', value: '700 italic' },
				{ label: '800', value: '800' },
				{ label: 'italic 800', value: '800 italic' },
				{ label: '900', value: '900' },
				{ label: 'italic 900', value: '900 italic' },
			],
		},
		{
			label: 'Merriweather',
			value: 'Merriweather',
			weights: [
				{ label: '300', value: '300' },
				{ label: 'italic 300', value: '300 italic' },
				{ label: '400', value: '400' },
				{ label: 'italic 400', value: '400 italic' },
				{ label: '700', value: '700' },
				{ label: 'italic 700', value: '700 italic' },
				{ label: '900', value: '900' },
				{ label: 'italic 900', value: '900 italic' },
			],
		},
		{
			label: 'Roboto Slab',
			value: 'Roboto Slab',
			weights: [
				{ label: '100', value: '100' },
				{ label: '200', value: '200' },
				{ label: '300', value: '300' },
				{ label: '400', value: '400' },
				{ label: '500', value: '500' },
				{ label: '600', value: '600' },
				{ label: '700', value: '700' },
				{ label: '800', value: '800' },
				{ label: '900', value: '900' },
			],
		},
		{
			label: 'Playfair Display',
			value: 'Playfair Display',
			weights: [
				{ label: '400', value: '400' },
				{ label: 'italic 400', value: '400 italic' },
				{ label: '500', value: '500' },
				{ label: 'italic 500', value: '500 italic' },
				{ label: '600', value: '600' },
				{ label: 'italic 600', value: '600 italic' },
				{ label: '700', value: '700' },
				{ label: 'italic 700', value: '700 italic' },
				{ label: '800', value: '800' },
				{ label: 'italic 800', value: '800 italic' },
				{ label: '900', value: '900' },
				{ label: 'italic 900', value: '900 italic' },
			],
		},
		{
			label: 'Libre Baskerville',
			value: 'Libre Baskerville',
			weights: [
				{ label: '400', value: '400' },
				{ label: 'italic 400', value: '400 italic' },
				{ label: '700', value: '700' },
			],
		},
	];
}

export function useFontWeights( selectedFont ) {
	const fontArray = useFonts();

	if ( ! selectedFont ) {
		return fontArray[0].weights;
	}

	return fontArray
		.filter( fontItem => selectedFont === fontItem.value )
		.map( fontItem => fontItem.weights )[0];
}

export function useDefaultFontColors() {
	return [
		{ name: __( 'Black' ), color: '#000' },
		{ name: __( 'Darkslategray' ), color: '#2F4F4F' },
		{ name: __( 'Slategray' ), color: '#708090' },
		{ name: __( 'Light slate gray' ), color: '#778899' },
		{ name: __( 'Dim Gray' ), color: '#696969' },
		{ name: __( 'Gray' ), color: '#808080' },
		{ name: __( 'Dark Gray' ), color: '#A9A9A9' },
		{ name: __( 'Silver' ), color: '#C0C0C0' },
		{ name: __( 'Light Gray' ), color: '#D3D3D3' },
		{ name: __( 'Gainsboro' ), color: '#DCDCDC' },
	];
}

export function useDefaultFontColorsHex() {
	return useDefaultFontColors().map( i => i.color );
}
