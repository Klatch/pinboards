<?php

if (!elgg_in_context('pinboards')) {
  return true;
}

echo '<br>';

echo elgg_echo('pinboards:widget:visibility') . '&nbsp;';
echo elgg_view('input/dropdown', array(
	'name' => 'params[pinboards_hide_style]',
	'value' => $vars['entity']->pinboards_hide_style ? $vars['entity']->pinboards_hide_style : 'no',
	'options_values' => array(
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no')
	),
	'class' => 'pinboards-widget-visibility-select'
));

echo '<br><br>';