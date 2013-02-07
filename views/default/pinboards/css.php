
form#pinboard-post-edit #description_parent #description_ifr {
	height:400px !important;
}

.pinboards-bookmark-icon {
  background-image: url(<?php echo elgg_get_site_url(); ?>mod/pinboards/graphics/bookmark.png);
  background-position: 0 0;
}

.pinboards-selector {
  position: absolute;
  width: 250px;
  min-height: 50px;
  background-color: white;
  border: 1px solid black;
  color: black;
  border-radius: 5px;
  padding: 4px;
  box-shadow: 5px 5px 2px #888;
  z-index: 9999;
}

.pinboard-result:hover {
  cursor: pointer;
  background-color: #FAFFA8;
}

.pinboard-result.pinboard-result-pinned {
  cursor: not-allowed;
  background-color: #cccccc;
}

.pinboard-result.pinboard-result-pinned a,
.pinboard-result.pinboard-result-pinned a:hover,
.pinboard-result.pinboard-result-pinned h4 {
  cursor: not-allowed;
  color: #454545;
  text-decoration: none;
}


.elgg-button.pinboards-selector-close {
  display: inline-block;
  margin-top: 4px;
  float: right;
}

.pinboards-selector-close-top {
  display: inline-block;
  float: right;
}

.pinboards-throbber {
  background-image: url('<?php echo elgg_get_site_url(); ?>/_graphics/ajax_loader_bw.gif');
  background-position: center center;
  background-repeat: no-repeat;
  min-height: 35px;
}

.pinboard {
  background-color: white;
}

.pinboard .pinboard-title-menu {
  margin-top: -35px;
  margin-bottom: 5px;
}

.pinboard-widgets-wrapper {
  clear: both;
}

.pinboards-item-search-results {
  margin-top: 5px;
  max-height: 150px;
  overflow-y: auto;
}

.pinboard-item-preview {
  padding: 4px;
  border-bottom: 1px dashed #cccccc;
  cursor: pointer;
}

.pinboard-item-preview:hover {
  background-color: #FAFFA8
}

/* Widget moving mods */
.pinboards-display-placeholder {
  height: 100px;
  border: 1px dashed #cccccc;
}

/* hide display on select widgets */
.pinboards-widgets .elgg-module-widget.pinboards-hide-style .elgg-head {
  display: none;
}

.pinboards-widgets .elgg-module-widget.pinboards-hide-style .elgg-body {
  border: 0;
}

.pinboard-widgets .elgg-module-widget.pinboards-hide-style {
  background-color: transparent;
}

/*  Layout Preview  */
#pinboard-layout-preview {
  width: 410px;
  float: right;
}

.pinboards-preview-wrapper {
  float: left;
  width: 150px;
  min-height: 50px;
  border: 1px solid black;
  background-color: #cccccc;
  padding: 5px;
  text-align: center;
  margin: 4px;
  cursor: pointer;
}

.pinboards-preview-wrapper.selected {
  border: 2px solid red;
  background-color: white;
  margin: 3px;
}

.pinboards-preview {
  float: right;
  background-color: #333333;
  border: 1px solid white;
  min-height: 50px;
  text-align: center;
  color: white;
}

/* make the preview lighter if selected */
.pinboards-preview-wrapper.selected .pinboards-preview {
  background-color: #676767;
}

<?php
// generate a css class for each width 1-100%

for ($i=1; $i<101; $i++) {
?>
.pinboards-widget-width-<?php echo $i; ?> {
  width: <?php echo $i; ?>%;
}

<?php
}