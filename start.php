<?php

require_once 'lib/hooks.php';

define(PINBOARDS_PINNED_RELATIONSHIP, 'pinboards_pinned_to');

/**
 * our init process
 */
function pinboards_init() {
  elgg_extend_view('css/elgg', 'pinboards/css');
  elgg_extend_view('js/elgg', 'pinboards/js');
  elgg_extend_view('page/layouts/one_column', 'pinboards/navigation/title_menu', 0);
  elgg_register_library('pinboards', elgg_get_plugins_path() . 'pinboards/lib/pinboards.php');

  //register our actions
  elgg_register_action("pinboards/save", dirname(__FILE__) . "/actions/save.php");
  elgg_register_action("pinboard/delete", dirname(__FILE__) . "/actions/delete.php");
  elgg_register_action("pinboards/pin", dirname(__FILE__) . "/actions/pin.php");
  elgg_register_action("pinboards/unpin", dirname(__FILE__) . "/actions/unpin.php");
  elgg_register_event_handler('pagesetup', 'system', 'pinboards_pagesetup');

  // register page handler
  elgg_register_page_handler('pinboards','pinboards_page_handler');

  // make it show up in search
  elgg_register_entity_type('object', 'pinboard');
  elgg_register_plugin_hook_handler('permissions_check', 'object', 'pinboards_permissions_check');
  elgg_register_plugin_hook_handler('permissions_check', 'widget_layout', 'pinboards_widget_layout_perms');
  elgg_register_plugin_hook_handler('register', 'menu:entity', 'pinboards_entity_menu');
  elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'pinboards_owner_block_menu');
  $replace_bookmarks_icon = elgg_get_plugin_setting('change_bookmark_icon', 'pinboards');
  if ($replace_bookmarks_icon != 'no') {
	elgg_register_plugin_hook_handler('register', 'menu:extras', 'pinboards_extras_menu', 1000);
  }

  // notifications
  register_notification_object('object', 'pinboard', elgg_echo('pinboards:newpinboard'));
  elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'pinboards_notify_message');

  // determine urls
  elgg_register_entity_url_handler('object', 'pinboard', 'pinboards_url_handler');
  elgg_register_plugin_hook_handler('entity:icon:url', 'object', 'pinboards_icon_url_override');

  // add a site navigation item
  $item = new ElggMenuItem('pinboards', elgg_echo('pinboards:pinboards'), 'pinboards/all');
  elgg_register_menu_item('site', $item);

  // Add group option
  add_group_tool_option('pinboards', elgg_echo('pinboards:enablepinboards'), true);
  elgg_extend_view('groups/tool_latest', 'pinboards/group_module');
  elgg_register_ajax_view('pinboards/search');
  elgg_register_ajax_view('pinboards/search_results');
  elgg_register_ajax_view('pinboards/item_search');
  elgg_register_widget_type('pinboard_avatar', elgg_echo("pinboards:widget:pinboard_avatar:title"), elgg_echo("pinboards:widget:pinboard_avatar:description"), 'pinboards', TRUE);
  elgg_register_widget_type('pinboard_description', elgg_echo("pinboards:widget:pinboard_description:title"), elgg_echo("pinboards:widget:pinboard_description:description"), 'pinboards', TRUE);
  elgg_register_widget_type('pinboard_list', elgg_echo("pinboards:widget:pinboard_list:title"), elgg_echo("pinboards:widget:pinboard_list:description"), 'pinboards', TRUE);
  elgg_register_widget_type('pinboard_item', elgg_echo("pinboards:widget:pinboard_item:title"), elgg_echo("pinboards:widget:pinboard_item:description"), 'pinboards', TRUE);
  elgg_register_widget_type('pinboard_comments', elgg_echo("pinboards:widget:pinboard_comments:title"), elgg_echo("pinboards:widget:pinboard_comments:description"), 'pinboards', TRUE);
  pinboards_add_widget_context('free_html', 'pinboards');
  pinboards_add_widget_context('tabtext', 'pinboards');
  pinboards_add_widget_context('rss', 'pinboards');
  pinboards_add_widget_context('xgadget', 'pinboards');

  // get all widget handlers and extend the edit form
  $types = elgg_get_widget_types('pinboards', true);
  if (is_array($types)) {
	foreach ($types as $handle => $info) {
	  elgg_extend_view("widgets/{$handle}/edit", 'pinboards/widgets/style_visibility');
	}
  }
}


/**
 * Dispatches pinboards pages.
 * URLs take the form of
 *  All pinboards:        pinboards/all
 *  User's pinboards:     pinboards/owner/<username>
 *  Friends' pinboards:   pinboards/friends/<username>
 *  Set:             pinboards/view/<guid>/<title>
 *  New pinboard:         pinboards/add/<guid>
 *  Edit pinboard:        pinboards/edit/<guid>/
 *  Group pinboard:       pinboards/group/<guid>/all
 *
 * Title is ignored
 *
 * @param array $page
 * @return bool
 */
function pinboards_page_handler($page) {
	elgg_load_library('pinboards');
	elgg_set_context('pinboards');

	// push all pinboards breadcrumb
	elgg_push_breadcrumb(elgg_echo('pinboards:pinboards'), "pinboards/all");
	if (!isset($page[0])) {
		$page[0] = 'all';
	}
	switch ($page[0]) {
		case 'owner':
			$user = get_user_by_username($page[1]);
			$params = pinboards_get_page_content_list($user->guid);
			break;
		case 'friends':
			$user = get_user_by_username($page[1]);
			$params = pinboards_get_page_content_friends($user->guid);
			break;
		case 'view':
			return pinboards_get_page_content_read($page[1]);
			break;
		case 'add':
			gatekeeper();
			$params = pinboards_get_page_content_edit($page[0], $page[1]);
			break;
		case 'edit':
			gatekeeper();
			$params = pinboards_get_page_content_edit($page[0], $page[1]);
			break;
		case 'group':
			$params = pinboards_get_page_content_list($page[1]);
			break;
		case 'all':
			$params = pinboards_get_page_content_list();
			break;
		case 'icon':
			$pinboard = get_entity($page[1]);
			pinboards_get_icon($pinboard, $page[2]);
			return true;
		case 'list':
			$params = pinboards_get_pinboard_list($page[1]);
			break;
		default:
			return false;
	}

	if (isset($params['sidebar'])) {
		$params['sidebar'] .= elgg_view('pinboards/sidebar', array('page' => $page[0]));
	} else {
		$params['sidebar'] = elgg_view('pinboards/sidebar', array('page' => $page[0]));
	}

	$body = elgg_view_layout('content', $params);
	echo elgg_view_page($params['title'], $body);
	return true;
}

/**
 * Set a url for a specific pinboard
 * @param type $entity
 * @return boolean
 */
function pinboards_url_handler($entity) {
  if (!$entity->getOwnerEntity()) {
	// default to a standard view if no owner.
	return FALSE;
  }

  $friendly_title = elgg_get_friendly_title($entity->title);
  return "pinboards/view/{$entity->guid}/$friendly_title";
}


/**
 *  returns an array of accesses the user can write to pinboards
 *	this is in start because we use it for a lot of hooks
 *
 * @param type $user
 * @return type
 */
function pinboards_get_write_accesses($user) {
  if (!elgg_instanceof($user, 'user')) {
	return array(ACCESS_PUBLIC);
  }

  // write access is set using acl nomenclature
  $access = get_access_array($user->getGUID());

  // remove private and friends ids
  foreach (array(ACCESS_PRIVATE, ACCESS_FRIENDS) as $id) {
	if (($key = array_search($id, $access)) !== false) {
	  unset($access[$key]);
	}
  }
  return $access;
}

function pinboards_pagesetup() {
  if (elgg_get_context() == 'pinboards') {
	$pinboard = elgg_get_page_owner_entity();
	if (elgg_instanceof($pinboard, 'object', 'pinboard') && $pinboard->canEdit()) {
	  elgg_register_title_button('pinboards', 'edit');
	}
  }
}

function pinboards_add_widget_context($handle, $context) {
  if (!elgg_is_widget_type($handle)) {
	return false;
  }
  $widgets = elgg_get_config('widgets');
  if (!in_array($context, $widgets->handlers[$handle]->context)) {
	array_push($widgets->handlers[$handle]->context, $context);
  }
  elgg_set_config('widgets', $widgets);
  return true;
}

elgg_register_event_handler('init', 'system', 'pinboards_init');