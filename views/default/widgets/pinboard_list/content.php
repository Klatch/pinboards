<?php

$pinboard = $vars['entity']->getContainerEntity();
$limit = $vars['entity']->num_results ? $vars['entity']->num_results : 10;

echo elgg_list_entities_from_relationship(array(
	  'relationship_guid' => $pinboard->guid,
	  'relationship' => PINBOARDS_PINNED_RELATIONSHIP,
	  'inverse_relationship' => true,
	  'full_view' => false,
	  'limit' => $limit,
	  'order_by' => 'r.time_created DESC',
	  'pagination' => false
  ));