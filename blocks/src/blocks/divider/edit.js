import React from 'react';

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

import {
	PanelBody,
	RangeControl,
	ColorPicker,
	ColorPalette,
	RadioControl,
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
export default function Edit( { attributes, setAttributes } ) {
	const {
		borderColor,
		borderStyle,
		dividerBottomMargin,
		dividerWidth,
		dividerHorizontalAlignment,
		dividerTopMargin,
	} = attributes;

	const inspectorControlsWrapper = (
		<InspectorControls>
			<PanelBody title={ __( 'Divider settings' ) }>
				<RangeControl
					label={ __( 'Width (%)' ) }
					value={ dividerWidth }
					onChange={ ( dividerWidth ) => setAttributes( { dividerWidth } ) }
					min={ 0 }
					max={ 100 }
				/>

				{ dividerWidth < 100 && <RadioControl
					label={ __( 'Horizontal alignment' ) }
					selected={ dividerHorizontalAlignment }
					options={ [
						{ label: __( 'Left' ), value: 'flex-start' },
						{ label: __( 'Center' ), value: 'center' },
						{ label: __( 'Right' ), value: 'flex-end' },
					] }
					onChange={ dividerHorizontalAlignment => setAttributes( { dividerHorizontalAlignment } ) }
				/> }

				<p>{ __( 'Border color' ) }</p>
				<ColorPalette
					colors={ [
						{ name: 'Dark Gray', color: '#333' },
						{ name: 'Medium Gray', color: '#999' },
						{ name: 'Light Gray', color: '#e3e3e3' }
					] }
					value={ borderColor }
					onChange={ ( borderColor ) => setAttributes(  { borderColor } ) }
					clearable={ false }
				/>

				<RadioControl
					label={ __( 'Border style' ) }
					selected={ borderStyle }
					options={ [
						{ label: __( 'Solid' ), value: 'solid' },
						{ label: __( 'Dashed' ), value: 'dashed' },
						{ label: __( 'Dotted' ), value: 'dotted' },
					] }
					onChange={ borderStyle => setAttributes( { borderStyle } ) }
				/>

				<RangeControl
					label={ __( 'Top margin (px)' ) }
					value={ dividerTopMargin }
					onChange={ ( dividerTopMargin ) => setAttributes( { dividerTopMargin } ) }
					min={ 0 }
					max={ 100 }
				/>

				<RangeControl
					label={ __( 'Bottom margin (px)' ) }
					value={ dividerBottomMargin }
					onChange={ ( dividerBottomMargin ) => setAttributes( { dividerBottomMargin } ) }
					min={ 0 }
					max={ 100 }
				/>
			</PanelBody>
		</InspectorControls>
	);

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
		<div { ...useBlockProps() }>
			{ inspectorControlsWrapper }
			<div style={ dividerWrapperStyles }>
				<div style={ dividerStyles }></div>
			</div>
		</div>
	);
}
