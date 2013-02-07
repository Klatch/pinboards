<?php

$pinboard_guid = get_input('pinboard_guid');
$entity_guid = get_input('entity_guid');

$pinboard = get_entity($pinboard_guid);
$entity = get_entity($entity_guid);

// make sure we load our functions
elgg_load_library('pinboards');

$error = pinboards_pin_sanity_check($entity, $pinboard);

if ($error) {
  register_error($error);
}
elseif (pinboards_is_pinned($entity, $pinboard)) {
  register_error(elgg_echo('pinboards:error:existing:pin'));
}
elseif (pinboards_pin_entity($entity, $pinboard)) {
  system_message(elgg_echo('pinboards:success:pinned'));
}
else {
  register_error(elgg_echo('pinboards:error:generic'));
}

forward(REFERER);