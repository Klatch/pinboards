<?php

$pinboard_guid = get_input('pinboard_guid');
$entity_guid = get_input('entity_guid');

$pinboard = get_entity($pinboard_guid);
$entity = get_entity($entity_guid);

// make sure we load our functions
elgg_load_library('pinboards');

if (!pinboards_is_pinned($entity, $pinboard)) {
  register_error(elgg_echo('pinboards:error:unpinned'));
  forward(REFERER);
}

if (pinboards_unpin_entity($entity, $pinboard)) {
  system_message(elgg_echo('pinboards:success:unpinned'));
}

forward(REFERER);