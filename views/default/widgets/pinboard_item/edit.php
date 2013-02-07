<?php

$pinboard = $vars['entity']->getContainerEntity();

echo elgg_echo('pinboards:pinboard_list:full_view');

echo elgg_view('input/dropdown', array(
	'name' => 'params[full_view]',
	'value' => $vars['entity']->full_view ? 1 : 0,
	'options_values' => array(
		0 => elgg_echo('pinboards:full_view:false'),
		1 => elgg_echo('pinboards:full_view:true')
	)
));

echo elgg_view('output/longtext', array(
	'value' => elgg_echo('pinboards:pinboard_list:full_view:help'),
	'class' => 'elgg-subtext'
));

echo '<div class="pinboard-item-preview pinboard-item-add" id="pinboard-item-selected-' . $vars['entity']->guid . '" data-set="' . $pinboard->guid . '" data-widget="' . $vars['entity']->guid . '">';

if ($vars['entity']->subject_guid) {
  $subject = get_entity($vars['entity']->subject_guid);
  
  if (elgg_instanceof($subject)) {
	$icon_subject = $subject;
	$default_icon = elgg_get_site_url() . '_graphics/icons/default/tiny.png';
	if ($subject->getIconURL('tiny') == $default_icon) {
	  $icon_subject = $subject->getOwnerEntity();
	}
	
	echo elgg_view_image_block(
			elgg_view_entity_icon($icon_subject, 'tiny', array('use_hover' => false, 'href' => false, 'use_link' => false)),
			$subject->title ? $subject->title : $subject->name
	);
  }
}
else {
  
  echo elgg_view('output/url', array(
	  'text' => elgg_echo('pinboards:item:add'),
	  'href' => '#',
  ));
}

echo '</div>';

echo elgg_view('input/hidden', array(
	'name' => 'params[subject_guid]',
	'value' => $vars['entity']->subject_guid ? $vars['entity']->subject_guid : 0,
	'id' => 'pinboard-item-input-' . $vars['entity']->guid
));

echo elgg_view('output/longtext', array(
	'value' => elgg_echo('pinboards:item:add:help'),
	'class' => 'elgg-subtext'
));