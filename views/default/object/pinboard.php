<?php

$full = elgg_extract('full_view', $vars, FALSE);
$pinboard = elgg_extract('entity', $vars, FALSE);

if (!$pinboard) {
	return TRUE;
}

// see if we need to show just a mimimal view for ajax results
// pinboard in the view pinboards/search_results
if ($vars['view_context'] == 'ajax_results') {
  echo elgg_view('object/pinboard/ajax_result', $vars);
  return;
}

$owner = $pinboard->getOwnerEntity();
$categories = elgg_view('output/categories', $vars);
$excerpt = $pinboard->excerpt;
if (!$excerpt) {
	$excerpt = elgg_get_excerpt($pinboard->description);
}

$icon = elgg_view_entity_icon($pinboard, 'tiny');
$link = elgg_view('output/url', array(
	'href' => "pinboards/owner/$owner->username",
	'text' => $owner->name,
	'is_trusted' => true,
));

$authored_by = elgg_echo('pinboards:authored_by', array($link));

$date = elgg_view_friendly_time($pinboard->time_created);

// The "on" status changes for comments, so best to check for !Off
if ($pinboard->comments_on != 'Off') {
	$comments_count = $pinboard->countComments();
	//only display if there are commments
	if ($comments_count != 0) {
		$text = elgg_echo("comments") . " ($comments_count)";
		$comments_link = elgg_view('output/url', array(
			'href' => $pinboard->getURL() . '#pinboard-comments',
			'text' => $text,
			'is_trusted' => true,
		));
	} else {
		$comments_link = '';
	}
} else {
	$comments_link = '';
}

$metadata = elgg_view_menu('entity', array(
	'entity' => $vars['entity'],
	'handler' => 'pinboard',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

$subtitle = "$authored_by $date $comments_link $categories";

// do not show the metadata and controls in widget view
if (elgg_in_context('widgets')) {
	$metadata = '';
}

if ($full) {
  
  $icon = elgg_view_entity_icon($pinboard, 'large');

  $content = elgg_view('output/longtext', array(
	'value' => $pinboard->description,
	'class' => 'pinboard-description',
  ));

  $params = array(
	'entity' => $pinboard,
	'title' => false,
	'metadata' => $metadata,
	'subtitle' => $subtitle,
	'content' => $content
  );
  
  $params = $params + $vars;
  
  $summary = elgg_view('object/elements/summary', $params);
  
  $context = elgg_get_context();
  elgg_set_context('pinboards_profile');
  
  // add invisible markup to contain pinboard guid for js
  $body = '<div class="pinboards-guid-markup" data-set="' . $pinboard->guid . '"></div>';
  
  elgg_set_context($context);

  echo elgg_view('object/elements/full', array(
	'summary' => $summary,
	'icon' => $icon,
	'body' => $body,
	  'class' => 'profile elgg-col-2of3'
  ));
 

} else {
	// brief view

	$params = array(
		'entity' => $pinboard,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'content' => $excerpt,
	);
	$params = $params + $vars;
	$list_body = elgg_view('object/elements/summary', $params);

	echo elgg_view_image_block($icon, $list_body);
}
