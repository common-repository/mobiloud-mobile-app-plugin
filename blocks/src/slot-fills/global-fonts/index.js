import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import {
	SelectControl,
	ColorPalette,
	RangeControl,
	ColorIndicator,
	PanelRow,
	Card,
	CardHeader,
	CardDivider,
	CardBody,
} from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from 'react';
import { useDocGlobals } from '../../hooks/use-doc-globals';
import { useFonts, useFontWeights, useDefaultFontColors } from '../../hooks/use-fonts';
import { useUserDefinedGlobalFontColors } from '../../hooks/use-doc-globals';
import { ColorPicker } from '../../components/color-picker';

/**
 * Adds a text field in the Status Info section
 * to set the page title.
 */
const PluginPostStatusInfoTest = () => {
	const fontArray = useFonts();

	/**
	 * Get the edited title.
	 */
	 let {
		postType,
	} = useSelect( select => {
		const { getCurrentPostType } = select( 'core/editor' );

		return {
			postType: getCurrentPostType(),
		}
	}, [] );

	if ( 'list-builder' !== postType ) {
		return null;
	}

	const {
		bodyColor,
		bodyFont,
		bodyFontSize,
		bodyFontWeight,
		bodyLineHeight,
		metaColor,
		metaFont,
		metaFontSize,
		metaFontWeight,
		metaLineHeight,
		titleFont,
		titleFontSize,
		titleColor,
		titleFontWeight,
		titleLineHeight,
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
	} = useDocGlobals();

	const titleFontWeightArray = useFontWeights( titleFont );
	const metaFontWeightArray = useFontWeights( metaFont );
	const bodyFontWeightArray = useFontWeights( bodyFont );
	const wooPriceFontWeightArray = useFontWeights( wooPriceFont );
	const headingFontWeightArray = useFontWeights( headingFont );

	const userDefinedGlobalFontColors = useUserDefinedGlobalFontColors();

	const { editPost } = useDispatch( 'core/editor' );

	const titleFontRef = useRef( titleFont );
	const metaFontRef = useRef( metaFont );
	const bodyFontRef = useRef( bodyFont );
	const wooPriceFontRef = useRef( wooPriceFont );
	const headingFontRef = useRef( headingFont );

	/**
	 * Reset font weight if font family is changed.
	 */
	useEffect( () => {
		if ( titleFontRef.current !== titleFont ) {
			editPost( { meta: { '_ml_titleFontWeight': '' } } )
			titleFontRef.current = titleFont;
		}

		if ( metaFontRef.current !== metaFont ) {
			editPost( { meta: { '_ml_metaFontWeight': '' } } )
			metaFontRef.current = metaFont;
		}

		if ( bodyFontRef.current !== bodyFont ) {
			editPost( { meta: { '_ml_bodyFontWeight': '' } } )
			bodyFontRef.current = bodyFont;
		}

		if ( wooPriceFontRef.current !== wooPriceFont ) {
			editPost( { meta: { '_ml_wooPriceFontWeight': '' } } )
			wooPriceFontRef.current = wooPriceFont;
		}

		if ( headingFontRef.current !== headingFont ) {
			editPost( { meta: { '_ml_headingFontWeight': '' } } )
			headingFontRef.current = headingFont;
		}
	}, [ titleFont, metaFont, bodyFont, wooPriceFont, headingFont ] );

	function addColorToPalette( color, field ) {
		const key = `${ field }User`;

		if ( userDefinedGlobalFontColors[ key ].includes( color ) ) {
			return;
		}

		if ( userDefinedGlobalFontColors[ key ].length < 8 ) {
			editPost( { meta: {
				'_mlglobal_userfontcolors': {
					...userDefinedGlobalFontColors,
					[ key ]: [ ...userDefinedGlobalFontColors[ key ], color ]
				}
			} } );
		} else {
			editPost( { meta: {
				'_mlglobal_userfontcolors': {
					...userDefinedGlobalFontColors,
					[ key ]: [ ...userDefinedGlobalFontColors[ key ].slice( 1 ), color ]
				}
			} } );
		}
	}

	return (
		<PluginDocumentSettingPanel
			title={ __( 'Global list styles settings' ) }
		>
			<Card isBorderless>
				<CardHeader isShady size="extraSmall">{ __( 'Headings' ) }</CardHeader>
				<CardBody size="extraSmall">
					<p>
						<SelectControl
							label={ __( 'Font family:' ) }
							options={ fontArray }
							value={ headingFont }
							onChange={ headingFont => editPost( { meta: { '_ml_headingFont': headingFont } } ) }
						/>
					</p>
					<p>
						<RangeControl
							label={ __( 'Font size (rem):' ) }
							value={ headingFontSize }
							onChange={ headingFontSize => editPost( { meta: { '_ml_headingFontSize': headingFontSize } } ) }
							min={ 0 }
							max={ 10 }
							step={ 0.01 }
						/>
					</p>
					<p>
						<SelectControl
							label={ __( 'Font weight:' ) }
							options={ headingFontWeightArray }
							value={ headingFontWeight }
							onChange={ headingFontWeight => editPost( { meta: { '_ml_headingFontWeight': headingFontWeight } } ) }
						/>
					</p>
					<p>
						<RangeControl
							label={ __( 'Line height (rem):' ) }
							value={ headingLineHeight }
							onChange={ headingLineHeight => editPost( { meta: { '_ml_headingLineHeight': headingLineHeight } } ) }
							min={ 0 }
							max={ 10 }
							step={ 0.01 }
						/>
					</p>
					<p>{ __( 'Color:' ) }</p>
					<ColorPicker
						color={ headingColor }
						extraColors={ userDefinedGlobalFontColors.headingColorUser || [] }
						onChange={ headingColor => editPost( { meta: { '_ml_headingColor': headingColor.hex } } ) }
						onFocusOutside={ () => addColorToPalette( headingColor, 'headingColor' ) }
					/>
				</CardBody>
			</Card>
			<CardDivider />

			{/* Title. */}
			<Card isBorderless>
				<CardHeader isShady size="extraSmall">{ __( 'Titles' ) }</CardHeader>
				<CardBody size="extraSmall">
					<p>
						<SelectControl
							label={ __( 'Font family:' ) }
							options={ fontArray }
							value={ titleFont }
							onChange={ titleFont => editPost( { meta: { '_ml_titleFont': titleFont } } ) }
						/>
					</p>
					<p>
						<RangeControl
							label={ __( 'Font size (rem):' ) }
							value={ titleFontSize }
							onChange={ titleFontSize => editPost( { meta: { '_ml_titleFontSize': titleFontSize } } ) }
							min={ 0 }
							max={ 10 }
							step={ 0.01 }
						/>
					</p>
					<p>
						<SelectControl
							label={ __( 'Font weight:' ) }
							options={ titleFontWeightArray }
							value={ titleFontWeight }
							onChange={ titleFontWeight => editPost( { meta: { '_ml_titleFontWeight': titleFontWeight } } ) }
						/>
					</p>
					<p>
						<RangeControl
							label={ __( 'Line height (rem):' ) }
							value={ titleLineHeight }
							onChange={ titleLineHeight => editPost( { meta: { '_ml_titleLineHeight': titleLineHeight } } ) }
							min={ 0 }
							max={ 10 }
							step={ 0.01 }
						/>
					</p>
					<p>{ __( 'Color:' ) }</p>
					<ColorPicker
						color={ titleColor }
						extraColors={ userDefinedGlobalFontColors.titleColorUser || [] }
						onChange={ titleColor => editPost( { meta: { '_ml_titleColor': titleColor.hex } } ) }
						onFocusOutside={ () => addColorToPalette( titleColor, 'titleColor' ) }
					/>
				</CardBody>
			</Card>
			<CardDivider />
			<Card isBorderless>
				<CardHeader isShady size="extraSmall">{ __( 'Meta information' ) }</CardHeader>
				<CardBody size="extraSmall">
					<p>
						<SelectControl
							label={ __( 'Font family:' ) }
							options={ fontArray }
							value={ metaFont }
							onChange={ metaFont => editPost( { meta: { '_ml_metaFont': metaFont } } ) }
						/>
					</p>
					<p>
						<RangeControl
							label={ __( 'Font size (rem):' ) }
							value={ metaFontSize }
							onChange={ metaFontSize => editPost( { meta: { '_ml_metaFontSize': metaFontSize } } ) }
							min={ 0 }
							max={ 10 }
							step={ 0.01 }
						/>
					</p>
					<p>
						<SelectControl
							label={ __( 'Font weight:' ) }
							options={ metaFontWeightArray }
							value={ metaFontWeight }
							onChange={ metaFontWeight => editPost( { meta: { '_ml_metaFontWeight': metaFontWeight } } ) }
						/>
					</p>
					<p>
						<RangeControl
							label={ __( 'Line height (rem):' ) }
							value={ metaLineHeight }
							onChange={ metaLineHeight => editPost( { meta: { '_ml_metaLineHeight': metaLineHeight } } ) }
							min={ 0 }
							max={ 10 }
							step={ 0.01 }
						/>
					</p>
					<p>{ __( 'Color:' ) }</p>
					<ColorPicker
						color={ metaColor }
						extraColors={ userDefinedGlobalFontColors.metaColorUser || [] }
						onChange={ metaColor => editPost( { meta: { '_ml_metaColor': metaColor.hex } } ) }
						onFocusOutside={ () => addColorToPalette( metaColor, 'metaColor' ) }
					/>
				</CardBody>
			</Card>
			<CardDivider />
			<Card isBorderless>
				<CardHeader isShady size="extraSmall">{ __( 'Excerpts' ) }</CardHeader>
				<CardBody size="extraSmall">
					<p>
						<SelectControl
							label={ __( 'Body font:' ) }
							options={ fontArray }
							value={ bodyFont }
							onChange={ bodyFont => editPost( { meta: { '_ml_bodyFont': bodyFont } } ) }
						/>
					</p>
					<p>
						<RangeControl
							label={ __( 'Font size (rem):' ) }
							value={ bodyFontSize }
							onChange={ bodyFontSize => editPost( { meta: { '_ml_bodyFontSize': bodyFontSize } } ) }
							min={ 0 }
							max={ 10 }
							step={ 0.01 }
						/>
					</p>
					<p>
						<SelectControl
							label={ __( 'Font weight:' ) }
							options={ bodyFontWeightArray }
							value={ bodyFontWeight }
							onChange={ bodyFontWeight => editPost( { meta: { '_ml_bodyFontWeight': bodyFontWeight } } ) }
						/>
					</p>
					<p>
						<RangeControl
							label={ __( 'Line height (rem):' ) }
							value={ bodyLineHeight }
							onChange={ bodyLineHeight => editPost( { meta: { '_ml_bodyLineHeight': bodyLineHeight } } ) }
							min={ 0 }
							max={ 10 }
							step={ 0.01 }
						/>
					</p>
					<p>{ __( 'Color:' ) }</p>
					<ColorPicker
						color={ bodyColor }
						extraColors={ userDefinedGlobalFontColors.bodyColorUser || [] }
						onChange={ bodyColor => editPost( { meta: { '_ml_bodyColor': bodyColor.hex } } ) }
						onFocusOutside={ () => addColorToPalette( bodyColor, 'bodyColor' ) }
					/>
				</CardBody>
			</Card>
			{
				window?.mobiloudBlockGlobals?.plugins?.woocommerce && ( <>
					<CardDivider />
					<Card isBorderless>
						<CardHeader isShady size="extraSmall">{ __( 'Price' ) }</CardHeader>
						<CardBody size="extraSmall">
							<p>
								<SelectControl
									label={ __( 'Font:' ) }
									options={ fontArray }
									value={ wooPriceFont }
									onChange={ wooPriceFont => editPost( { meta: { '_ml_wooPriceFont': wooPriceFont } } ) }
								/>
							</p>
							<p>
								<RangeControl
									label={ __( 'Font size (rem):' ) }
									value={ wooPriceFontSize }
									onChange={ wooPriceFontSize => editPost( { meta: { '_ml_wooPriceFontSize': wooPriceFontSize } } ) }
									min={ 0 }
									max={ 10 }
									step={ 0.01 }
								/>
							</p>
							<p>
								<SelectControl
									label={ __( 'Font weight:' ) }
									options={ wooPriceFontWeightArray }
									value={ wooPriceFontWeight }
									onChange={ wooPriceFontWeight => editPost( { meta: { '_ml_wooPriceFontWeight': wooPriceFontWeight } } ) }
								/>
							</p>
							<p>
								<RangeControl
									label={ __( 'Line height (rem):' ) }
									value={ wooPriceLineHeight }
									onChange={ wooPriceLineHeight => editPost( { meta: { '_ml_wooPriceLineHeight': wooPriceLineHeight } } ) }
									min={ 0 }
									max={ 10 }
									step={ 0.01 }
								/>
							</p>
							<p>{ __( 'Color:' ) }</p>
							<ColorPicker
								color={ wooPriceColor }
								extraColors={ userDefinedGlobalFontColors.wooPriceColorUser || [] }
								onChange={ wooPriceColor => editPost( { meta: { '_ml_wooPriceColor': wooPriceColor.hex } } ) }
								onFocusOutside={ () => addColorToPalette( wooPriceColor, 'wooPriceColor' ) }
							/>
						</CardBody>
					</Card>
				</> )
			}
		</PluginDocumentSettingPanel>
	);
}
 
registerPlugin(
	'post-status-global-fonts',
	{
		render: PluginPostStatusInfoTest
	}
);
