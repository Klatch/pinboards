<?php

function pinboards_entity_menu($hook, $type, $return, $params) {
  if (is_array($return) && elgg_instanceof($params['entity'], 'object', 'pinboard')) {
	foreach ($return as $key => $item) {
	  if ($item->getName() == 'edit') {
		$return[$key]->setHref('pinboards/edit/' . $params['entity']->getGUID());
	  }
	}
  }

  if (elgg_is_logged_in() && !elgg_in_context('widgets')) {
	$use_icon = elgg_get_plugin_setting('pin_icon', 'pinboards');

	if ($use_icon != 'no') {
	  $text = '<span class="elgg-icon elgg-icon-push-pin-alt" data-guid="' . $params['entity']->getGUID() . '">';
	  $text .= '</span>';
	}
	else {
	  $text = '<span data-guid="' . $params['entity']->getGUID() . '">';
	  $text .= elgg_echo('pinboards:pin');
	  $text .= '</span>';
	}

	$pin = new ElggMenuItem('pinboards_pin', $text, '#');
	$pin->setLinkClass('pinboards-pin');

	$return[] = $pin;
  }

  // add unpin link if we're displaying the entity on a pinboard profile
  if (elgg_get_context() == 'pinboards_list') {

	$text = '<span data-guid="' . $params['entity']->getGUID() . '">';
	$text .= elgg_echo('pinboards:unpin');
	$text .= '</span>';
	$unpin = new ElggMenuItem('pinboards:unpin', $text, '#');
	$unpin->setLinkClass('pinboards-unpin');

	$return[] = $unpin;
  }

  // add link for viewing the layout if we're in read mode and can edit the pinboard
  if (stristr($params['class'], 'pinboard-title-menu') && $params['entity']->canEdit()) {
	$layout_active = get_input('view_layout', false);

	if (!$layout_active) {
	  $url = elgg_http_add_url_query_elements(current_page_url(), array('view_layout' => 1));
	  $view_layout = new ElggMenuItem('pinboards:view_layout', elgg_echo('pinboards:view:layout'), $url);
	}
	else {
	  $url = elgg_http_remove_url_query_element(current_page_url(), 'view_layout');
	  $view_layout = new ElggMenuItem('pinboards:view_layout', elgg_echo('pinboards:hide:layout'), $url);
	}

	$return[] = $view_layout;
  }

  return $return;
}


/*
 * replaces the bookmarks icon
 */
function pinboards_extras_menu($hook, $type, $return, $params) {
  foreach ($return as $key => $item) {
	if ($item->getName() == 'bookmark') {
	  $return[$key]->setText('<span class="elgg-icon pinboards-bookmark-icon"></span>');
	}
  }

  return $return;
}


function pinboards_icon_url_override($hook, $type, $return, $params) {
  if (!elgg_instanceof($params['entity'], 'object', 'pinboard')) {
	return $return;
  }

  // get our icon url
  $icontime = $params['entity']->icontime;
  if (!$icontime) {
	$icontime = 'default';
  }
  return elgg_get_site_url() . 'pinboards/icon/' . $params['entity']->getGUID() . '/' . $params['size'] . '/' . $icontime . '.jpg';
}



/**
 * Set the notification message body
 *
 * @param string $hook    Hook name
 * @param string $type    Hook type
 * @param string $message The current message body
 * @param array  $params  Parameters about the blog posted
 * @return string
 */
function pinboards_notify_message($hook, $type, $message, $params) {
	$entity = $params['entity'];
	$to_entity = $params['to_entity'];
	$method = $params['method'];
	if (elgg_instanceof($entity, 'object', 'pinboard')) {
		$descr = $entity->excerpt;
		$title = $entity->title;
		$owner = $entity->getOwnerEntity();
		return elgg_echo('pinboards:notification', array(
			$owner->name,
			$title,
			$descr,
			$entity->getURL()
		));
	}
	return null;
}


/**
 * Add a menu item to an ownerblock
 */
function pinboards_owner_block_menu($hook, $type, $return, $params) {
  if (elgg_instanceof($params['entity'], 'user')) {
	$url = "pinboards/owner/{$params['entity']->username}";
	$item = new ElggMenuItem('pinboard', elgg_echo('pinboards:pinboards'), $url);
	$return[] = $item;
  } else {
	if ($params['entity']->pinboards_enable != "no") {
	  $url = "pinboards/group/{$params['entity']->guid}/all";
	  $item = new ElggMenuItem('pinboard', elgg_echo('pinboards:group'), $url);
	  $return[] = $item;
	}
  }

  return $return;
}

/**
 * Determines if the user canEdit() an object
 * @param type $hook
 * @param type $type
 * @param type $return
 * @param type $params
 */
function pinboards_permissions_check($hook, $type, $return, $params) {
  if (!elgg_instanceof($params['entity'], 'object', 'pinboard')) {
	return $return;
  }

  if (!elgg_is_logged_in()) {
	return $return;
  }

  // this is our object, lets determine if we can edit it
  $pinboard = $params['entity'];
  $user = $params['user'];
  $owner = $params['entity']->getOwnerEntity();

  // owners and admins can always edit
  if ($user->getGUID() == $owner->getGUID() || $user->isAdmin()) {
	return true;
  }


  // check for friends special case
  if ($pinboard->write_access_id == ACCESS_FRIENDS) {
	return $owner->isFriendsWith($user->getGUID());
  }

  // write access is set using acl nomenclature
  $access = pinboards_get_write_accesses($user);

  // now we just look at remaining acls
  if (in_array($pinboard->write_access_id, $access)) {
	return true;
  }

  return $return;
}


function pinboards_widget_layout_perms($hook, $type, $return, $params) {
  if (elgg_instanceof($params['page_owner'], 'object', 'pinboard')) {
	return $params['page_owner']->canEdit($params['user']->guid);
  }

  return $return;
}
