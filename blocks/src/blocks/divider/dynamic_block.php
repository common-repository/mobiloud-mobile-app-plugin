<?php
$block_data = \Blocks\get_values_from_json_attr_keys( MOBILOUD_PLUGIN_DIR . 'blocks/src/blocks/divider/attributes.json', $block_attributes );

$divider_wrapper_styles = array (
	'display'         => 'flex',
	'justify-content' => $block_data['dividerHorizontalAlignment'],
	'overflow'        => 'hidden',
	'margin-top'      => $block_data['dividerTopMargin'] . 'px',
	'margin-bottom'   => $block_data['dividerBottomMargin'] . 'px',
);

$divider_styles = array(
	'width'         => $block_data['dividerWidth'] . '%',
	'height'        => '1px',
	'border-bottom' => '1px ' . $block_data['borderStyle'] . ' ' . $block_data['borderColor'],
);

$divider_wrapper_styles = \Blocks\mobiloud_assoc_array_to_css( $divider_wrapper_styles );
$divider_styles = \Blocks\mobiloud_assoc_array_to_css( $divider_styles );

?>
<div class="wp-block-mobiloud-divider ml-block">
	<div style="<?php echo $divider_wrapper_styles; ?>">
		<div style="<?php echo $divider_styles; ?>"></div>
	</div>
</div>
