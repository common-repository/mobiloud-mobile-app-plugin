import { blocksWithSharedAttributes } from './blocks-list';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor'
import { CardBody, TextControl, RangeControl, SelectControl } from '@wordpress/components';
import { useFonts, useFontWeights } from '../../hooks/use-fonts';
import { ColorPicker } from '../../components/color-picker';

const addSharedAttributes = ( settings, name ) => {

	if ( ! blocksWithSharedAttributes.includes( name ) ) {
		return settings;
	}
	
	settings.attributes = Object.assign( settings.attributes, {
		blockHeadingText: {
			type: 'string',
			default: '',
		},
		blockHeadingFontFamily: {
			type: 'string',
			default: 'Roboto',
		},
		blockHeadingFontSize: {
			type: 'number',
			default: 1.5,
		},
		blockHeadingFontWeight: {
			type: 'string',
			default: '100',
		},
		blockHeadingLineHeight: {
			type: 'number',
			default: 1.8,
		},
		blockHeadingColor: {
			type: 'string',
			default: '#000',
		},
		userSelectedBlockHeadingColors: {
			type: 'array',
			default: [],
		},
	} );

	return settings;
};

addFilter( 'blocks.registerBlockType', 'mobiloud/attribute/sharedAttributes', addSharedAttributes );

const withSharedAttributesControl = createHigherOrderComponent( ( BlockWithASharedAttribute ) => {
	return ( props ) => {

		if ( ! blocksWithSharedAttributes.includes( props.name ) ) {
			return <BlockWithASharedAttribute { ...props } />;
		}

		const {
			attributes: {
				blockHeadingText,
				blockHeadingFontFamily,
				blockHeadingFontSize,
				blockHeadingFontWeight,
				blockHeadingLineHeight,
				blockHeadingColor,
				userSelectedBlockHeadingColors,
			},
			setAttributes,
		} = props;

		const fontArray = useFonts();
		const headingFontWeightArray = useFontWeights( blockHeadingFontFamily );

		function setBlockHeadingColor( color ) {
			setAttributes( { blockHeadingColor: color.hex } )
		}

		function addColorToPalette( color ) {
			if ( userSelectedBlockHeadingColors.includes( color ) ) {
				return;
			}

			if ( userSelectedBlockHeadingColors.length < 8 ) {
				setAttributes( { userSelectedBlockHeadingColors: [ ...userSelectedBlockHeadingColors, color ] } );
			} else {
				setAttributes( { userSelectedBlockHeadingColors: [ ...userSelectedBlockHeadingColors.slice( 1 ), color ] } );
			}
		}

		return (
			<>
				<InspectorControls>
					<CardBody size="small">
						<TextControl
							label={ __( 'Block heading text' ) }
							value={ blockHeadingText }
							onChange={ ( blockHeadingText ) => setAttributes( { blockHeadingText } ) }
						/>

						<SelectControl
							label={ __( 'Block Heading font family:' ) }
							options={ fontArray }
							value={ blockHeadingFontFamily }
							onChange={ ( blockHeadingFontFamily ) => setAttributes( { blockHeadingFontFamily } ) }
						/>

						<RangeControl
							label={ __( 'Block Heading font size (rem):' ) }
							value={ blockHeadingFontSize }
							onChange={ ( blockHeadingFontSize ) => setAttributes( { blockHeadingFontSize } ) }
							min={ 0 }
							max={ 10 }
							step={ 0.01 }
						/>

						<SelectControl
							label={ __( 'Block Heading font weight:' ) }
							options={ headingFontWeightArray }
							value={ blockHeadingFontWeight }
							onChange={ ( blockHeadingFontWeight ) => setAttributes( { blockHeadingFontWeight } ) }
						/>

						<RangeControl
							label={ __( 'Block heading line height (rem):' ) }
							value={ blockHeadingLineHeight }
							onChange={ blockHeadingLineHeight => setAttributes( { blockHeadingLineHeight } ) }
							min={ 0 }
							max={ 10 }
							step={ 0.01 }
						/>

						<p>{ __( 'Color:' ) }</p>
						<ColorPicker
							color={ blockHeadingColor }
							extraColors={ userSelectedBlockHeadingColors }
							onChange={ setBlockHeadingColor }
							onFocusOutside={ () => addColorToPalette( blockHeadingColor ) }
						/>
					</CardBody>
				</InspectorControls>
				<BlockWithASharedAttribute { ...props } />
			</>
		);
	}
}, 'withSharedAttributesControl' );
addFilter( 'editor.BlockEdit', 'mobiloud/sharedControl', withSharedAttributesControl, 1 );
