<?php
/**
 * Delete pinboard entity
 *
 */
$guid = get_input('guid');
$pinboard = get_entity($guid);
if (elgg_instanceof($pinboard, 'object', 'pinboard') && $pinboard->canEdit()) {
	$container = $pinboard->getContainerEntity();
	if ($pinboard->delete()) {
		system_message(elgg_echo('pinboards:message:deleted'));
		if (elgg_instanceof($container, 'group')) {
			forward("pinboards/group/$container->guid/all");
		} else {
			forward("pinboards/owner/$container->username");
		}
	} else {
		register_error(elgg_echo('pinboards:error:cannot_delete'));
	}
} else {
	register_error(elgg_echo('pinboards:error:not_found'));
}
forward(REFERER);