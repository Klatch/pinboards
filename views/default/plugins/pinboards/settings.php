<?php

echo elgg_view('input/dropdown', array(
	'name' => 'params[pin_icon]',
	'value' => $vars['entity']->pin_icon ? $vars['entity']->pin_icon : 'yes',
	'options_values' => array(
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no')
	)
));
echo '&nbsp;';
echo elgg_echo('pinboards:use:pin:icon');

echo '<br><br>';

echo elgg_view('input/dropdown', array(
	'name' => 'params[change_bookmark_icon]',
	'value' => $vars['entity']->change_bookmark_icon ? $vars['entity']->change_bookmark_icon : 'yes',
	'options_values' => array(
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no')
	)
));
echo '&nbsp;';
echo elgg_echo('pinboards:change:bookmark:icon');

echo '<br><br>';