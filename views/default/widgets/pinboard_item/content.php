<?php

elgg_load_library('pinboards');

$guid = $vars['entity']->subject_guid;
$full = $vars['entity']->full_view ? true : false;
$pinboard = $vars['entity']->getContainerEntity();

$subject = get_entity($guid);
if (elgg_instanceof($subject)) {
  if (pinboards_is_pinned($subject, $pinboard)) {
	
	if ($full) {
	  $title = $subject->title ? $subject->title : $subject->name;
	  echo "<h3>" . elgg_view('output/url', array('text' => $title, 'href' => $subject->getURL())) . "</h3>";
	}
	
	echo elgg_view_entity_list(array($subject), array('full_view' => $full));
  }
  else {
	echo elgg_echo('pinboards:not:pinned');
  }
}
else {
  echo elgg_echo('pinboards:pinboard_list:invalid:entity');
}