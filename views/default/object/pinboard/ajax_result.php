<?php

elgg_load_library('pinboards');

$pinboard = elgg_extract('entity', $vars, FALSE);
$entity_guid = elgg_extract('target_entity_guid', $vars, false);
$entity = get_entity($entity_guid);

if (!$pinboard) {
	return TRUE;
}

$owner = $pinboard->getOwnerEntity();
$icon = elgg_view_entity_icon($pinboard, 'tiny');
$body = "<h4>" . strip_tags($pinboard->title) . "</h4>";

$owner_link = '';
if ($owner) {
	$owner_link = elgg_view('output/url', array(
		'href' => $owner->getURL(),
		'text' => $owner->name,
		'is_trusted' => true,
	));
}

$ingroup = '';
$container = $pinboard->getContainerEntity();
if (elgg_instanceof($container, 'group')) {
  $ingroup = elgg_echo('pinboards:ingroup', array(
	  elgg_view('output/url', array('text' => $container->name, 'href' => $container->getURL()))
  ));
}

$date = elgg_view_friendly_time($pinboard->time_created);

$body .= elgg_view('output/longtext', array(
	'value' => elgg_echo('pinboards:authored_by', array($owner_link)) . '&nbsp;' . $ingroup . '&nbsp;' . $date, 'class' => 'elgg-subtext'));


$pin_link = elgg_view('output/url', array(
	'text' => elgg_echo('pinboards:pin:to:this'),
	'href' => '#',
	'class' => 'pinboards-pin-action',
	'rel' => $pinboard->getGUID()
));


$class = 'pinboard-result';
$title = '';
if (pinboards_is_pinned($entity, $pinboard)) {
  $class .= ' pinboard-result-pinned';
  $title .= ' title="' . elgg_echo('pinboards:error:existing:pin') . '"';
}

echo '<div class="' . $class . '" data-set="' . $pinboard->getGUID() . '" data-entity="' . $entity_guid . '"' . $title . '>';
echo elgg_view_image_block($icon, $body);
echo '</div>';