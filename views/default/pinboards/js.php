elgg.provide('elgg.pinboards');

elgg.pinboards.init = function() {

	// Initialize global variable for ajax requests
	elgg.pinboards.ajax_request = false;

    // Initialize pin click
    elgg.pinboards.pinclick();
		
	// Initialize pin modal close
	elgg.pinboards.closeModal();
	
	// Initialize pinboard text search
	elgg.pinboards.search();
	
	// Initialize the pin action
	elgg.pinboards.pin();
	
	// Initialize unpin click
	elgg.pinboards.unpin();
	
	// Initialize location input
	elgg.pinboards.input();
	
	// Initialize pinboard_item widget search
	elgg.pinboards.pinboard_item();

	// Set the item as the widget input
	elgg.pinboards.pinboard_item_select();
	
	// layout preview
	elgg.pinboards.preview();
	
	// update widget class in real time
	elgg.pinboards.widget_save();
};


/**
 *	Initializes click of the pin button
 * opens dialog listing pinboards that are writeable by the user
 *
 */
elgg.pinboards.pinclick = function() {

  $('.pinboards-pin').live('click', function(e) {
	e.preventDefault();
	
	// remove any existing modals first - we only want one active
	$('.pinboards-selector').remove();
	
	var parent = $(this).parent();
	var span = $(this).children('span').eq(0);
	var guid = span.attr('data-guid');
	var div_id = 'pinboards-selector-'+guid;
	var offset = $(this).offset();
	
	$('body').prepend('<div class="pinboards-selector pinboards-throbber hidden" id="'+div_id+'"></div>');
	var modal = $('#'+div_id);
	var left = Math.round(offset.left - 230);
	var top = Math.round(offset.top + 20);
	
	// position it relative to the pin link
	modal.css('marginTop', top + 'px');
	modal.css('marginLeft', left + 'px');
	modal.hide().fadeIn(1000);
	
	// get the list of writeable pinboards
	elgg.get('ajax/view/pinboards/search', {
      timeout: 120000, //2 min
      data: {
        guid: guid,
		pageowner: elgg.get_page_owner_guid()
      },
      success: function(result, success, xhr){
		modal.removeClass('pinboards-throbber');
        modal.html(result);
      },
      error: function(result, response, xhr) {
		modal.removeClass('pinboards-throbber');
        if (response == 'timeout') {
          modal.html(elgg.echo('pinboards:error:timeout'));
        }
		else {
		  modal.html(elgg.echo('pinboards:error:generic'));
		}
      }
    });
  });
};


/**
 *	closes the pinboards modal
 *
 */
elgg.pinboards.closeModal = function() {
  $('.pinboards-selector-close').live('click', function(e) {
	e.preventDefault();
	
	$('.pinboards-selector').remove();
  });
}


/**
 *	handles the search interface
 *
 */
elgg.pinboards.search = function() {
  $('.pinboards-query').live('keyup', function(e) {
	var query = $(this).val();
	var guid = $(this).attr('data-guid');
	var mine = $('.pinboards-query-mine').is(':checked');
	
	
	// no sense searching for tiny strings
	if (query.length < 3) {
	  return;
	}
	
	// cancel any existing ajax requests
	// there's a good chance one was initiated
	// for fast typers
	if (elgg.pinboards.ajax_request) {
	  elgg.pinboards.ajax_request.abort();
	}
	
	
	// now we can search
	// first clear the existing results and add a throbber
	var results = $('#pinboards-selector-results-'+guid);
	
	results.addClass('pinboards-throbber');
	results.html('');
	
	// get the results
	elgg.pinboards.ajax_request = elgg.get('ajax/view/pinboards/search_results', {
      timeout: 120000, //2 min
      data: {
        guid: guid,
		query: query,
		filter_mine: mine
      },
      success: function(result, success, xhr){
		results.removeClass('pinboards-throbber');
        results.html(result);
      },
      error: function(result, response, xhr) {
		results.removeClass('pinboards-throbber');
        if (response == 'timeout') {
          results.html(elgg.echo('pinboards:error:timeout'));
        }
		else {
		  results.html(elgg.echo('pinboards:error:generic'));
		}
      }
    });
	alert(pinboard_request.toSource());
  });
}

/**
 *	handles the pin action
 *
 */
elgg.pinboards.pin = function() {
  $('.pinboard-result').live('click', function(e) {
	e.preventDefault();
	
	// only attempt pinning if it's not already pinned
	if ($(this).hasClass('pinboard-result-pinned')) {
	  elgg.system_message(elgg.echo('pinboards:error:existing:pin'));
	  return;
	}
	
	var pinboard_guid = $(this).attr('data-set');
	var entity_guid = $(this).attr('data-entity');
	
	// let the user know we're doing something
	// save the html in a var until we're done in case we need to revert
	// then we'll empty it and put in a throbber
	var entity = $(this);
	var html = entity.html();
	entity.addClass('pinboards-throbber');
	entity.html('');
	
	//something went wrong, lets put the html back
	elgg.action('pinboards/pin', {
      timeout: 120000, //2 min
      data: {
        pinboard_guid: pinboard_guid,
		entity_guid: entity_guid
      },
      success: function(result, success, xhr){
		if (result.status == 0) {
		  entity.removeClass('pinboards-throbber');
		  entity.addClass('pinboard-result-pinned');
		  entity.html(html);
		}
		else {
		  entity.removeClass('pinboards-throbber');
		  entity.html(html);
		}
      },
      error: function(result, response, xhr) {
		entity.removeClass('pinboards-throbber');
        if (response == 'timeout') {
          elgg.register_error(elgg.echo('pinboards:error:timeout'));
		  entity.html(html);
        }
		else {
		  elgg.register_error(elgg.echo('pinboards:error:generic'));
		  entity.html(html);
		}
      }
	});
	
  });
}


elgg.pinboards.unpin = function() {
  $('.pinboards-unpin').live('click', function(e) {
	e.preventDefault();
	
	if (!confirm(elgg.echo('pinboards:unpin:confirm'))) {
	  return;
	}
	
	var span = $(this).children('span').eq(0);
	var entity_guid = span.attr('data-guid');
	var pinboard_guid = $('.pinboards-guid-markup').attr('data-set');
	var entity = $(this).parents('.elgg-item').eq(0);
	
	// store html in case of failure
	var html = entity.html();
	
	// make it a throbber for feedback
	entity.html('');
	entity.addClass('pinboards-throbber');
	
	//something went wrong, lets put the html back
	elgg.action('pinboards/unpin', {
      timeout: 120000, //2 min
      data: {
        pinboard_guid: pinboard_guid,
		entity_guid: entity_guid
      },
      success: function(result, success, xhr){
		if (result.status == 0) {
		  entity.fadeOut(1500, function() { entity.remove(); });
		}
		else {
		  entity.removeClass('pinboards-throbber');
		  entity.html(html);
		}
      },
      error: function(result, response, xhr) {
		entity.removeClass('pinboards-throbber');
        if (response == 'timeout') {
          elgg.register_error(elgg.echo('pinboards:error:timeout'));
		  entity.html(html);
        }
		else {
		  elgg.register_error(elgg.echo('pinboards:error:generic'));
		  entity.html(html);
		}
      }
	});
	
  });
}


elgg.pinboards.input = function() {
  $('.pinboard-add-new-row').live('click', function(e) {
	e.preventDefault();
	var numcols = $('.pinboard-num-columns').val();
	
	if (numcols == 0) {
	  return;
	}
	
	
	
  });
}


elgg.pinboards.pinboard_item = function() {
  $('.pinboard-item-add').live('click', function(e) {
	e.preventDefault();
	
	// remove any existing modals first - we only want one active
	$('.pinboards-selector').remove();
	
	var widget_guid = $(this).attr('data-widget');
	var pinboard_guid = $(this).attr('data-set');
	var offset = $(this).offset();
	var div_id = 'pinboard-item-select-'+widget_guid;
	
	$('body').prepend('<div class="pinboards-selector pinboards-throbber hidden" id="'+div_id+'"></div>');
	var modal = $('#'+div_id);
	var left = Math.round(offset.left - 230);
	var top = Math.round(offset.top + 20);

	// position it relative to the pin link
	modal.css('marginTop', top + 'px');
	modal.css('marginLeft', left + 'px');
	modal.hide().fadeIn(1000);
	
	// get the list of writeable pinboards
	elgg.get('ajax/view/pinboards/item_search', {
      timeout: 120000, //2 min
      data: {
        guid: pinboard_guid,
		widget_guid: widget_guid
      },
      success: function(result, success, xhr){
		modal.removeClass('pinboards-throbber');
        modal.html(result);
      },
      error: function(result, response, xhr) {
		modal.removeClass('pinboards-throbber');
        if (response == 'timeout') {
          modal.html(elgg.echo('pinboards:error:timeout'));
        }
		else {
		  modal.html(elgg.echo('pinboards:error:generic'));
		}
      }
    });
  });
}

/**
*
*	Selects the individual pin to use on the single pin widget
*
*/
elgg.pinboards.pinboard_item_select = function() {
  $('.pinboards-item-search-results .pinboard-item-preview').live('click', function(e) {
	e.preventDefault();
	
	var widget_guid = $(this).attr('data-widget');
	var item_guid = $(this).attr('data-item');
	var html = $(this).html();
	
	// insert the html into the widget
	$('#pinboard-item-selected-'+widget_guid).html(html);
	
	// set this hidden input value
	$('#pinboard-item-input-'+widget_guid).val(item_guid);
	
	// remove the modal
	$('.pinboards-selector').remove();
  });
}


elgg.pinboards.preview = function() {
  $('.pinboards-preview-wrapper').live('click', function(e) {
	e.preventDefault();
	
	$('.pinboards-preview-wrapper').removeClass('selected');
	$(this).addClass('selected');
	
	// make our input use this layout
	var layout = $(this).attr('data-layout');
	
	$('#pinboards-layout-input').val(layout);
  });
}

// reassign the widgets move function to our variable
// then reassign the widgets move variable
elgg.pinboards.widgets_move = elgg.ui.widgets.move;

elgg.ui.widgets.move = function(event, ui) {
  elgg.pinboards.widgets_move(event, ui);
  
  // reset row heights
  // only if our normalization is defined
  if (typeof pinboards_normalize_widget_height == 'function') {
	pinboards_normalize_widget_height();
  }
}


elgg.pinboards.widget_save = function() {
  $('.elgg-form').live('submit', function() {
	var parent = $(this).parent();
	var guid = parent.attr('id').substr(12);
	var hide = true;
	
	var split = location.search.replace('?', '').split('&').map(function(val){
	  return val.split('=');
	});
	
	for (var i=0; i < split.length; i++) {
	  if (split[i][0] == 'view_layout' && split[i][1] == 1) {
		hide = false;
	  }
	}
	
	if (hide) {
	  if ($('#elgg-widget-'+guid+' .pinboards-widget-visibility-select').val() == 'yes') {
		$('#elgg-widget-'+guid).addClass('pinboards-hide-style');
	  }
	  else {
		$('#elgg-widget-'+guid).removeClass('pinboards-hide-style');
	  }
	}
	
  });
}

elgg.register_hook_handler('init', 'system', elgg.pinboards.init);
