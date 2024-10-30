import { CirclePicker, SketchPicker } from 'react-color';
import { useState, useEffect } from '@wordpress/element';
import { useDefaultFontColorsHex } from '../../hooks/use-fonts';
import { Button, ColorIndicator, PanelRow, Popover } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export function ColorPicker( props ) {
	const {
		color,
		onChange,
		extraColors,
		onChangeComplete,
		onFocusOutside,
	} = props;

	const [ isPickerOpen, setIsPickerOpen ] = useState( false );
	const defaultColors = useDefaultFontColorsHex();
	const mergedColors = [ ...defaultColors, ...extraColors ];

	function toggleSketchPicker() {
		setIsPickerOpen( ! isPickerOpen );
	}

	return (
		<>
			<PanelRow>
				<span>{ __( 'Selected color:' ) } { color }</span>
				<ColorIndicator colorValue={ color } />
			</PanelRow>
			<CirclePicker
				color={ color }
				colors={ mergedColors }
				onChange={ onChange }
			/>
			<Button isLink onClick={ toggleSketchPicker }>{ __( 'Custom colours' ) }</Button>
			{ isPickerOpen &&
				<div>
					<Popover
						className="popover-color-picker"
						animate={ true }
						onFocusOutside={ () => {
							toggleSketchPicker();
							onFocusOutside();
						} }
					>
						<SketchPicker
							color={ color }
							presetColors={ [] }
							onChange={ onChange }
							onChangeComplete={ onChangeComplete }
							disableAlpha
						/>
					</Popover>
				</div>
			}
		</>
	);
}

ColorPicker.defaultProps = {
	extraColors: [],
	onFocusOutside: () => {},
};

