<?php


function pinboards_get_icon($pinboard, $size = "medium") {

  if (!elgg_instanceof($pinboard, 'object', 'pinboard')) {
	header("HTTP/1.1 404 Not Found");
	exit;
  }

  // If is the same ETag, content didn't changed.
  $etag = $pinboard->icontime . $pinboard->getGUID();
  if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == "\"$etag\"") {
	header("HTTP/1.1 304 Not Modified");
	exit;
  }

  if (!in_array($size, array('large', 'medium', 'small', 'tiny', 'master', 'topbar'))) {
	$size = "medium";
  }

  $filehandler = new ElggFile();
  $filehandler->owner_guid = $pinboard->owner_guid;
  $filehandler->setFilename("pinboards/" . $pinboard->guid . $size . ".jpg");

  $success = false;
  if ($filehandler->open("read")) {
	if ($contents = $filehandler->read($filehandler->size())) {
		$success = true;
	}
  }

  if (!$success) {
	$location = elgg_get_plugins_path() . "pinboards/graphics/default{$size}.jpg";
	$contents = @file_get_contents($location);
  }

  header("Content-type: image/jpeg");
  header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', strtotime("+10 days")), true);
  header("Pragma: public");
  header("Cache-Control: public");
  header("Content-Length: " . strlen($contents));
  header("ETag: \"$etag\"");
  echo $contents;
}

/**
 * Get page components to view a pinboard.
 *
 * @param int $guid GUID of a pinboard entity.
 * @return array
 */
function pinboards_get_page_content_read($guid = NULL) {

	$params = array();

	$pinboard = get_entity($guid);

	// no header or tabs for viewing an individual blog
	$params['filter'] = '';

	if (!elgg_instanceof($pinboard, 'object', 'pinboard')) {
		register_error(elgg_echo('noaccess'));
		$_SESSION['last_forward_from'] = current_page_url();
		forward('');
	}

	elgg_set_page_owner_guid($pinboard->guid);
	$params['title'] = $pinboard->title;

	$container = $pinboard->getContainerEntity();
	$crumbs_title = $container->name;
	if (elgg_instanceof($container, 'group')) {
		elgg_push_breadcrumb($crumbs_title, "pinboards/group/$container->guid/all");
	} else {
		elgg_push_breadcrumb($crumbs_title, "pinboards/owner/$container->username");
	}

	elgg_push_breadcrumb($pinboard->title);

	$content = elgg_view_layout('configurable_widgets', array(
		'widget_layout' => json_decode($pinboard->layout),
		'exact_match' => true
	));

	$menu = elgg_view_menu('entity', array(
	  'entity' => $pinboard,
	  'handler' => 'pinboard',
	  'sort_by' => 'priority',
	  'class' => 'elgg-menu-hz pinboard-title-menu',
	));
	$params['content'] = $menu . '<div class="pinboard-widgets-wrapper">' . $content . '</div>';
	$params['class'] = 'pinboard';

	elgg_set_context('pinboards');
	$body = elgg_view_layout('one_column', $params);

	echo elgg_view_page($params['title'], $body);
	return true;
}

/**
 * Get page components to list a user's or all pinboards.
 *
 * @param int $container_guid The GUID of the page owner or NULL for all pinboards
 * @return array
 */
function pinboards_get_page_content_list($container_guid = NULL) {

	$return = array();

	$return['filter_context'] = $container_guid ? 'mine' : 'all';

	$options = array(
		'type' => 'object',
		'subtype' => 'pinboard',
		'full_view' => false,
	);

	$current_user = elgg_get_logged_in_user_entity();

	if ($container_guid) {
		// access check for closed groups
		group_gatekeeper();

		$options['container_guid'] = $container_guid;
		$container = get_entity($container_guid);
		if (!$container) {
		  // @TODO
		  // what do we do if we don't have a container?
		}
		$return['title'] = elgg_echo('pinboards:title:user_pinboards', array($container->name));

		$crumbs_title = $container->name;
		elgg_push_breadcrumb($crumbs_title);

		if ($current_user && ($container_guid == $current_user->guid)) {
			$return['filter_context'] = 'mine';
		} else if (elgg_instanceof($container, 'group')) {
			$return['filter'] = false;
		} else {
			// do not show button or select a tab when viewing someone else's posts
			$return['filter_context'] = 'none';
		}
	} else {
		$return['filter_context'] = 'all';
		$return['title'] = elgg_echo('pinboards:title:all_pinboards');
		elgg_pop_breadcrumb();
		elgg_push_breadcrumb(elgg_echo('pinboards:pinboards'));
	}

	elgg_register_title_button();

	$list = elgg_list_entities_from_metadata($options);
	if (!$list) {
		$return['content'] = elgg_echo('pinboards:none');
	} else {
		$return['content'] = $list;
	}

	return $return;
}

/**
 * Get page components to list of the user's friends' pinboards.
 *
 * @param int $user_guid
 * @return array
 */
function pinboards_get_page_content_friends($user_guid) {

	$user = get_user($user_guid);
	if (!$user) {
		forward('pinboards/all');
	}

	$return = array();

	$return['filter_context'] = 'friends';
	$return['title'] = elgg_echo('pinboards:title:friends');

	$crumbs_title = $user->name;
	elgg_push_breadcrumb($crumbs_title, "pinboards/owner/{$user->username}");
	elgg_push_breadcrumb(elgg_echo('friends'));

	elgg_register_title_button();

	if (!$friends = get_user_friends($user_guid, ELGG_ENTITIES_ANY_VALUE, 0)) {
		$return['content'] .= elgg_echo('friends:none:you');
		return $return;
	} else {
		$options = array(
			'type' => 'object',
			'subtype' => 'pinboard',
			'full_view' => FALSE,
		);

		foreach ($friends as $friend) {
			$options['container_guids'][] = $friend->getGUID();
		}

		$list = elgg_list_entities($options);
		if (!$list) {
			$return['content'] = elgg_echo('pinboards:none');
		} else {
			$return['content'] = $list;
		}
	}

	return $return;
}


/**
 * Get page components to edit/create a pinboard.
 *
 * @param string  $page     'edit' or 'new'
 * @param int     $guid     GUID of pinboard or container
 * @param int     $revision Annotation id for revision to edit (optional)
 * @return array
 */
function pinboards_get_page_content_edit($page, $guid = 0) {

	$return = array(
		'filter' => '',
	);

	$vars = array();
	$vars['id'] = 'pinboard-post-edit';
	$vars['class'] = 'elgg-form-alt';

	$sidebar = '';
	if ($page == 'edit') {
		$pinboard = get_entity((int)$guid);

		$title = elgg_echo('pinboards:edit');

		if (elgg_instanceof($pinboard, 'object', 'pinboard') && $pinboard->canEdit()) {
			$return['filter'] = elgg_view('pinboards/navigation/edit', array('entity' => $pinboard));
			$vars['entity'] = $pinboard;

			$title .= ": \"$pinboard->title\"";

			$body_vars = pinboards_prepare_form_vars($pinboard);
			$form_vars = array('enctype' => 'multipart/form-data');

			elgg_push_breadcrumb($pinboard->title, $pinboard->getURL());
			elgg_push_breadcrumb(elgg_echo('edit'));

			$content = elgg_view_form('pinboards/save', $form_vars, $body_vars);
		} else {
			$content = elgg_echo('pinboard:error:cannot_edit');
		}
	} else {
		elgg_push_breadcrumb(elgg_echo('pinboards:add'));
		$body_vars = pinboards_prepare_form_vars(null);
		$form_vars = array('enctype' => 'multipart/form-data');

		$title = elgg_echo('pinboards:add');
		$content = elgg_view_form('pinboards/save', $form_vars, $body_vars);
	}

	$return['title'] = $title;
	$return['content'] = $content;
	return $return;
}


function pinboards_get_pinboard_list($guid) {
  $pinboard = get_entity($guid);
  if (!elgg_instanceof($pinboard, 'object', 'pinboard') || !$pinboard->canEdit()) {
	register_error(elgg_echo('pinboards:error:invalid:pinboard'));
	forward(REFERER);
  }

  elgg_push_breadcrumb($pinboard->title, $pinboard->getURL());
  elgg_push_breadcrumb(elgg_echo('List'));

  $context = elgg_get_context();
  elgg_set_context('pinboards_list');
  $list = elgg_list_entities_from_relationship(array(
	  'relationship_guid' => $pinboard->guid,
	  'relationship' => PINBOARDS_PINNED_RELATIONSHIP,
	  'inverse_relationship' => true,
	  'full_view' => false,
	  'limit' => 10,
	  'order_by' => 'r.time_created DESC'
  ));

  $list .= '<div class="pinboards-guid-markup" data-set="' . $pinboard->guid . '"></div>';
  elgg_set_context($context);

  $params = array(
	'filter' => elgg_view('pinboards/navigation/edit', array('entity' => $pinboard)),
	'content' => $list,
	'title' => $pinboard->title
  );

  return $params;
}

/**
 * Returns bool whether the entity is already pinned
 * assumes $entity and $pinboard are valid objects
 *
 * @param type $entity
 * @param type $pinboard
 */
function pinboards_is_pinned($entity, $pinboard) {
  return check_entity_relationship($entity->getGUID(), PINBOARDS_PINNED_RELATIONSHIP, $pinboard->getGUID());
}


/**
 * Pull together pinboard variables for the save form
 *
 * @param ElggObject       $pinboard
 * @return array
 */
function pinboards_prepare_form_vars($pinboard = NULL) {

	// input names => defaults
	$values = array(
		'title' => NULL,
		'description' => NULL,
		'access_id' => ACCESS_DEFAULT,
		'write_access_id' => ACCESS_PRIVATE,
		'comments_on' => 'On',
		'tags' => NULL,
		'container_guid' => NULL,
		'guid' => NULL,
	);

	if ($pinboard) {
		foreach (array_keys($values) as $field) {
			if (isset($pinboard->$field)) {
				$values[$field] = $pinboard->$field;
			}
		}
	}

	if (elgg_is_sticky_form('pinboard')) {
		$sticky_values = elgg_get_sticky_values('pinboard');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}
	elgg_clear_sticky_form('pinboard');
	return $values;
}


/**
 * Pins an entity to a given pinboard
 *
 * @param type $entity
 * @param type $pinboard
 * @param type $user
 */
function pinboards_pin_entity($entity, $pinboard, $user = NULL) {

  if (!pinboards_pin_sanity_check($entity, $pinboard, $user)) {
	return add_entity_relationship($entity->getGUID(), PINBOARDS_PINNED_RELATIONSHIP, $pinboard->getGUID());
  }

  return false;
}

/**
 * Checks to make sure there are no errors with pinning/unpinning entities
 *
 * @param type $entity
 * @param type $pinboard
 * @param type $user
 * @return boolean
 */
function pinboards_pin_sanity_check($entity, $pinboard, $user = NULL) {
  //make sure we have an entity
  if (!elgg_instanceof($entity)) {
	return elgg_echo('pinboards:error:invalid:entity');
  }

  if (!elgg_instanceof($pinboard, 'object', 'pinboard')) {
	return elgg_echo('pinboards:error:invalid:pinboard');
  }

  if ($pinboard->getGUID() == $entity->getGUID()) {
	return elgg_echo('pinboards:error:recursive:pin');
  }

  if (!elgg_instanceof($user, 'user')) {
	$user = elgg_get_logged_in_user_entity();
  }

  if (!$user) {
	return elgg_echo('pinboards:error:invalid:user');
  }

  // make sure we can edit the pinboard
  if (!$pinboard->canEdit($user->guid)) {
	return elgg_echo('pinboards:error:cannot:edit');
  }

  return false;
}

/**
 * Pins an entity to a given pinboard
 *
 * @param type $entity
 * @param type $pinboard
 * @param type $user
 */
function pinboards_unpin_entity($entity, $pinboard, $user = NULL) {

  if (!pinboards_pin_sanity_check($entity, $pinboard, $user)) {
	return remove_entity_relationship($entity->getGUID(), PINBOARDS_PINNED_RELATIONSHIP, $pinboard->getGUID());
  }
  return false;
}
