<?php
/**
 * This view is called via ajax
 * $vars:
 * 'entity' => the entity being pinned
 * 'pageowner' => the guid of the pageowner
 */

if (!elgg_is_logged_in() || !$vars['entity']) {
  return;
}


echo elgg_view('output/url', array(
	'text' => '<span class="elgg-icon elgg-icon-delete-alt"></span>',
	'href' => '#',
	'class' => 'pinboards-selector-close pinboards-selector-close-top'
));

echo "<h3>" . elgg_echo('pinboards:pin:to') . "</h3>";

echo elgg_view('output/url', array(
	'text' => elgg_echo('pinboards:create:new:pinboard:with:pin'),
	'href' => elgg_get_site_url() . 'pinboards/add/' . elgg_get_logged_in_user_guid() . '?pin=' . $vars['entity']->guid,
));

echo '<br>';

echo '<div id="pinboards-selector-results-' . $vars['entity']->guid . '">';

echo elgg_view('pinboards/search_results', $vars);

echo '</div>';

echo elgg_echo('pinboards:search');
echo elgg_view('input/text', array(
	'name' => 'query',
	'class' => 'pinboards-query',
	'data-guid' => $vars['entity']->guid
));
echo elgg_view('output/longtext', array(
	'value' => elgg_echo('pinboards:search:help'),
	'class' => 'elgg-subtext'
));

echo '<br>';

echo '<input class="pinboards-query-mine" type="checkbox" name="mine" value="1" checked="checked"> ' . elgg_echo('pinboards:search:mine');

echo elgg_view('output/url', array(
	'text' => elgg_echo('close'),
	'href' => '#',
	'class' => 'pinboards-selector-close elgg-button elgg-button-delete'
));