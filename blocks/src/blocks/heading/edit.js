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
import { RichText } from "@wordpress/block-editor";
import { useDocGlobals } from '../../hooks/use-doc-globals';

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
		titleBottomMargin,
		titleText,
		titleTopMargin,
	} = attributes;

	const {
		headingColor,
		headingFont,
		headingFontSize,
		headingFontWeight,
		headingLineHeight,
	} = useDocGlobals();

	const textStyles = {
		color: headingColor,
		font: `${ headingFontWeight } ${ headingFontSize }rem/${ headingLineHeight }rem ${ headingFont }`,
	}

	const wrapperStyles = {
		marginTop: `${ titleTopMargin }px`,
		marginBottom: `${ titleBottomMargin }px`,
	};

	return (
		<div { ...useBlockProps() }>
			<div className="heading--wrapper" style={ wrapperStyles }>
				<RichText
					tagName="div"
					multiline={ false }
					withoutInteractiveFormatting={ false }
					allowedFormats={ [] }
					value={ titleText }
					style={ textStyles }
					placeholder={ __( 'Add a title...' ) }
					onChange={ ( titleText ) => setAttributes( { titleText } ) }
				/>
			</div>
		</div>
	);
}
