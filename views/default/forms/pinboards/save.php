<?php

$pinboard = get_entity($vars['guid']);
$vars['entity'] = $pinboard;
$pin = get_input('pin');

$action_buttons = '';
$delete_link = '';

if ($vars['guid']) {
	// add a delete button if editing
	$delete_url = "action/pinboard/delete?guid={$vars['guid']}";
	$delete_link = elgg_view('output/confirmlink', array(
		'href' => $delete_url,
		'text' => elgg_echo('delete'),
		'class' => 'elgg-button elgg-button-delete float-alt'
	));
}

$save_button = elgg_view('input/submit', array(
	'value' => elgg_echo('save'),
	'name' => 'save',
));
$action_buttons = $save_button . $delete_link;

$icon_label = elgg_echo('pinboards:label:icon');
$icon_input = elgg_view("input/file", array('name' => 'icon'));

$title_label = elgg_echo('title');
$title_input = elgg_view('input/text', array(
	'name' => 'title',
	'id' => 'pinboard_title',
	'value' => $vars['title']
));


$body_label = elgg_echo('pinboards:body');
$body_input = elgg_view('input/longtext', array(
	'name' => 'description',
	'id' => 'pinboard_description',
	'value' => $vars['description']
));


$comments_label = elgg_echo('comments');
$comments_input = elgg_view('input/dropdown', array(
	'name' => 'comments_on',
	'id' => 'pinboard_comments_on',
	'value' => $vars['comments_on'],
	'options_values' => array('On' => elgg_echo('on'), 'Off' => elgg_echo('off'))
));

$tags_label = elgg_echo('tags');
$tags_input = elgg_view('input/tags', array(
	'name' => 'tags',
	'id' => 'pinboard_tags',
	'value' => $vars['tags']
));

$access_label = elgg_echo('access');
$access_input = elgg_view('input/access', array(
	'name' => 'access_id',
	'id' => 'pinboard_access_id',
	'value' => $vars['access_id']
));

$write_access_label = elgg_echo('pinboards:label:write_access');
$write_access_input = elgg_view('input/access', array(
	'name' => 'write_access_id',
	'id' => 'pinboard_write_access_id',
	'value' => $vars['write_access_id']
));

$categories_input = elgg_view('input/categories', $vars);

// hidden inputs
$container_guid_input = elgg_view('input/hidden', array('name' => 'container_guid', 'value' => elgg_get_page_owner_guid()));
$guid_input = elgg_view('input/hidden', array('name' => 'guid', 'value' => $vars['guid']));


// Layout
$layout_label = elgg_echo('pinboards:label:layout');
$layout_input = elgg_view('pinboards/input/layout', $vars);

// autopin
$pin_input = '';
if ($pin) {
  $pin_input = elgg_view('input/hidden', array('name' => 'pin', 'value' => $pin));
}

echo <<<___HTML

 <div>
  <label for="pinboard_icon">$icon_label</label>
	$icon_input
</div>

<div>
	<label for="pinboard_title">$title_label</label>
	$title_input
</div>

<div>
	<label for="pinboard_description">$body_label</label>
	$body_input
</div>

<div>
	<label for="pinboard_tags">$tags_label</label>
	$tags_input
</div>

$categories_input

<div>
	<label for="pinboard_comments_on">$comments_label</label>
	$comments_input
</div>

<div>
	<label for="pinboard_access_id">$access_label</label>
	$access_input
</div>

<div>
  <label for="pinboard_write_access_id">$write_access_label</label>
	$write_access_input
</div>

<div>
  <label for="pinboard_layout">$layout_label</label>
	$layout_input
</div>

<div class="elgg-foot">

	$guid_input
	$container_guid_input

	$pin_input
	$action_buttons
</div>

___HTML;
