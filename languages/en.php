<?php

$english = array(
	'pinboards:pinboard' => 'Pinboard',
	'pinboards:pinboards' => 'Pinboards',
	'item:object:pinboard' => 'Pinboard',
	'pinboards:enablepinboards' => "Enable group Pinboards",
	'pinboards:group' => 'Group Pinboards',
	'pinboards:title:user_pinboards' => '%s\'s Pinboards',
	'pinboards:title:all_pinboards' => "All site Pinboards",
	'pinboards:none' => "No Pinboards",
	'pinboards:title:friends' => 'Friends\' Pinboards',
	'pinboards:message:deleted' => "Pinboard has been deleted",
	'pinboards:error:cannot_delete' => "Could not delete the Pinboard",
	'pinboards:error:not_found' => "Could not find the Pinboard",
	'pinboards:error:invalid:pinboard' => "Invalid Pinboard",
	'pinboards:error:invalid:entity' => "Invalid entity",
	'pinboards:error:cannot:edit' => "You do not have permission to edit this Pinboard",
	'pinboards:error:invalid:user' => "Invalid user",
	'pinboards:error:recursive:pin' => "Cannot pin a board to itself",
	'pinboards:pin' => "Pin",
	'pinboards:pin:to:this' => "Pin it",
	'pinboards:error:timeout' => "A timeout error has occurred...",
	'pinboards:error:generic' => "An error has occurred...",
	'pinboards:pin:to' => "Pin to a board",
	'pinboards:search' => "Filter Pinboards",
	'pinboards:search:help' => "Type a word or words that appears in the title or description of the Pinboard you are looking for",
	'pinboards:success:pinned' => "Content has been pinned",
	'pinboards:success:unpinned' => "Content has been unpinned",
	'pinboards:authored_by' => "By %s",
	'pinboards:unpin' => 'UnPin',
	'pinboards:unpin:confirm' => "Are you sure you want to unpin this item?",
	'pinboards:error:existing:pin' => "This content is already pinned to that board",
	'pinboards:search:mine' => 'Restrict results to Pinboards I created',
	'pinboards:ingroup' => "in the group %s",
	'pinboards:error:unpinned' => "Content is already unpinned - possibly an incorrect id",
	'pinboards:create:new:pinboard:with:pin' => "Create a new Pinboard and pin this",
	
	// Editing
	'pinboards:add' => 'Add Pinboard',
	'pinboards:add' => 'Add Pinboard',
	'pinboards:edit' => 'Edit Pinboard',
	'pinboards:body' => 'Body',
	'pinboards:never' => 'Never',
	'pinboards:label:icon' => "Pinboard Icon (Leave blank to remain unchanged)",
	'pinboards:label:write_access' => "Who can edit this Pinboard?",
	'pinboards:label:layout' => "Choose a layout",
	'pinboards:add:new:row' => "Add new row",
	'pinboards:how:many:columns' => "How many columns?",
	'pinboards:edit' => 'Edit',
	'pinboards:layout:type' => "Layout %s",
	'pinboards:invalid:layout' => "Invalid Layout",
	'pinboards:view:layout' => "Layout Mode",
	'pinboards:hide:layout' => "View Mode",
	'pinboards:mode:layout:help' => "Here you can see all potential places for widgets, as well as edit all widgets.  To view the page as others will see it switch to 'view mode'",
	'pinboards:mode:view:help' => "If you cannot find the space to move a widget, or cannot edit a widget with styles disabled switch to 'layout mode'",
	'pinboards:autopinned' => "The item '%s' has been pinned to this board",
	
	
	// messages
	'pinboards:error:cannot_save' => 'Cannot save Pinboard.',
	'pinboards:error:cannot_edit' => 'This Pinboard may not exist or you may not have permissions to edit it.',
	'pinboards:error:post_not_found' => "This Pinboard cannot be found",
	'pinboards:error:missing:title' => "Title cannot be empty",
	'pinboards:error:cannot_write_to_container' => "Error - cannot write to container",
	'pinboards:error:cannot_save' => "Error - cannot save the value %s",
	'pinboards:message:saved' => "Pinboard has been saved",
	'pinboards:error:cannot_save' => "Cannot save Pinboard",
	
	// river
	'pinboards:river:create' => "%s has created a new Pinboard %s",
	'river:comment:object:pinboard' => '%s commented on the Pinboard %s',
	
	/* notifications */
	'pinboards:newpinboard' => 'A new Pinboard has been created',
	'pinboards:notification' =>
'
%s made a new Pinboard.

%s
%s

View and comment on the new Pinboard:
%s
',
	
	// widget manager
	'widget_manager:widgets:lightbox:title:pinboards_profile' => "Widgets for Pinboards",
	'widget_manager:widgets:lightbox:title:pinboards' => "Widgets for Pinboards",
	
	// widgets
	'pinboards:widget:pinboard_avatar:description' => "Display the avatar of the Pinboard in configurable size",
	'pinboards:widget:pinboard_avatar:title' => "Pinboard Avatar",
	'pinboards:widget:pinboard_description:title' => "Pinboard Description",
	'pinboards:widget:pinboard_description:description' => "Profile information for the Pinboard, description, author, tags, etc.",
	'pinboards:num:results' => "Number of items to display",
	'pinboards:widget:pinboard_list:title' => "Recent Pins",
	'pinboards:widget:pinboard_list:description' => "Display a list of recently pinned content",
	'pinboards:pinboard_list:invalid:entity' => "Widget is not configured or the entity is no longer accessible",
	'pinboards:pinboard_list:full_view:help' => "Note: some content may not change support this option",
	'pinboards:widget:pinboard_item:title' => "Single Pin",
	'pinboards:widget:pinboard_item:description' => "Display a single pinned item in either a full or condensed view",
	'pinboards:pinboard_list:full_view' => "Choose how to display the content",
	'pinboards:full_view:false' => "Condensed View",
	'pinboards:full_view:true' => "Full View",
	'pinboards:item:add' => "Select Pinned Item",
	'pinboards:item:add:help' => "Note that all users may not be able to see this content depending on its access settings which are independent of the access of this display",
	'pinboards:not:pinned' => "Content is no longer pinned",
	'pinboards:comments:off' => "Comments are disabled for this Pinboard",
	'pinboards:widget:pinboard_comments:title' => "Comments",
	'pinboards:widget:pinboard_comments:description' => "Display comments for this Pinboard",
	'pinboards:comments:new_comments' => "Allow the addition of new comments?",
	'pinboards:widget:visibility' => "Hide default widget style?",

	// settings
	'pinboards:use:pin:icon' => "Use the pin icon to launch the pin modal?",
	'pinboards:change:bookmark:icon' => "Replace the default bookmarks icon? (may reduce confusion with pins)",
);
					
add_translation("en",$english);