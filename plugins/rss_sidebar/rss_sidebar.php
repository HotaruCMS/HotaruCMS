<?php
 
/* ********** PLUGIN *********************************************************************************
 * name: RSS Sidebar
 * description: Adds links in the sidebar to the latest posts from a specified RSS feed.
 * version: 0.1
 * folder: rss_sidebar
 * prefix: rs
 * hooks: rss_sidebar, admin_header_include, admin_sidebar_plugin_settings, admin_plugin_settings, install_plugin_starter_settings
 *
 * Usage: Add <?php $plugin->check_actions('rss_sidebar'); ?> to your theme, wherever you want to show the links.
 *
 *  License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */
 
	
/* ***** ACCESS ********************************************************* 
 * This plugin is accessed in two ways:
 * 1. Directly opened via http. This happens if a file links to it <a href=""> or 
 *    sends data from a form, in which case we want to include the Hotaru environment
 *    (hotaru_header.php) and then the get_params() function to process the data;  
 * 2. Included via check_actions() in class.plugins.php. This is done to give Hotaru 
 *    access to the functions, but we don't want to actually run the script from the 
 *    top so we return false for now.
 * ******************************************************************** */

if(isset($user)) {
	if(!is_object($plugin)) { 
		// Accessed via 1 above;
		require_once('../../hotaru_header.php');
		rs_get_params(); 
	} else {
		// Not the object we were expecting...
		return false; die(); 
	}
} else { 
	// Accessed via 2 above;
	return false; die(); 
}


/* ******************************************************************** 
 *  Function: rs_rss_sidebar
 *  Parameters: None
 *  Purpose: Displays the RSS feed.
 *  Notes: Uses Hotaru's built-in SimplePie library, but extra customization 
 *         to the feed is possible by inserting SimplePie calls before $feed->init();
 ********************************************************************** */

function rs_rss_sidebar() {
    global $hotaru, $plugin;
	      
	// Get settings from the database:
	$settings = unserialize($plugin->plugin_settings('rss_sidebar', 'rss_sidebar_settings')); 

	// Feed settings:
	$feedurl = $settings['rss_sidebar_feed'];
	if($settings['rss_sidebar_cache']) { $cache = true; } else { $cache = false; }
	$cache_duration = $settings['rss_sidebar_cache_duration'];
	
	// Get the feed...
	$feed = $hotaru->new_simplepie($feedurl, $cache, $cache_duration);
	
	// Limit the number of items:
	$max_items = $settings['rss_sidebar_max_items'];
	
	// Feed is ready.
	$feed->init();
	
	$output = "";
	$item_count = 0;
	
	if($settings['rss_sidebar_title']) { 
		$output.= "<li class='sidebar_list_title'><a href='" . $settings['rss_sidebar_feed']. "' title='RSS Feed'>" . $settings['rss_sidebar_title'] . "</a></li>"; 
	}
	    
	if ($feed->data) { 
		foreach ($feed->get_items() as $item) {
		        $output .= '';
		        $output .= '<li><a href="' . $item->get_permalink() . '">' . $item->get_title() . '</a></li>';
		    $item_count++;
		    if($item_count >= $max_items) { break;}
		}
	}
	
	// Display the whole thing:
	echo $output;
}

/* *************************************
 * ********** ADMIN FUNCTIONS **********
 * ************************************* */


/* ******************************************************************** 
 *  Function: rs_admin_sidebar_plugin_settings
 *  Parameters: None
 *  Purpose: Puts a link to the settings page in the Admin sidebar under Plugin Settings
 *  Notes: ---
 ********************************************************************** */
 
function rs_admin_header_include() {
	echo "<script type='text/javascript'>\n";
	echo "$(document).ready(function(){\n";
		echo "$('#rs_cache').click(function () {\n";
		echo "$('#rs_cache_duration').slideToggle();\n";
		echo "});\n";
	echo "});\n";
	echo "</script>\n";
}

/* ******************************************************************** 
 *  Function: rs_admin_sidebar_plugin_settings
 *  Parameters: None
 *  Purpose: Puts a link to the settings page in the Admin sidebar under Plugin Settings
 *  Notes: ---
 ********************************************************************** */
 
function rs_admin_sidebar_plugin_settings() {
	echo "<li><a href='admin_index.php?page=plugin_settings&plugin=rss_sidebar'>RSS Sidebar</a></li>";
}


/* ******************************************************************** 
 *  Function: rs_install_plugin_starter_settings
 *  Parameters: None
 *  Purpose: When the plugin is installed, this function inserts some prelimnary 
 *           settings into the pluginsettings table.
 *  Notes: All database queries should use the prepare function.
 ********************************************************************** */
 
function rs_install_plugin_starter_settings() {
	global $db, $plugin;
	
	$settings['rss_sidebar_feed'] = 'http://feeds2.feedburner.com/hotarucms';
	$settings['rss_sidebar_title'] = 'Hotaru CMS Forums';
	$settings['rss_sidebar_cache'] = 'on';
	$settings['rss_sidebar_cache_duration'] = 10;
	$settings['rss_sidebar_max_items'] = 10;
	
	// parameters: plugin folder name, setting name, setting value
	$plugin->plugin_settings_update('rss_sidebar', 'rss_sidebar_settings', serialize($settings));
}


/* ******************************************************************** 
 *  Function: rs_admin_sidebar_settings
 *  Parameters: None
 *  Purpose: Displays the contents of the plugin settings page.
 *  Notes: ---
 ********************************************************************** */
 
function rs_admin_plugin_settings() {
	global $plugin;
	$settings = unserialize($plugin->plugin_settings('rss_sidebar', 'rss_sidebar_settings'));
	echo "<h1>RSS Sidebar Configuration</h1>\n";
	echo "<form name='rss_sidebar_form' action='" . baseurl . "plugins/rss_sidebar/rss_sidebar.php' method='get'>\n";
	echo "Feed URL: <input type='text' size=60 name='rss_sidebar_feed' value='" . $settings['rss_sidebar_feed'] . "' /><br /><br />\n";
	echo "Feed Title: <input type='text' size=30 name='rss_sidebar_title' value='" . $settings['rss_sidebar_title'] . "' /><br /><br />\n";
	if($settings['rss_sidebar_cache']) { $checked = "checked"; } else { $checked = ""; }
	echo "Cache: <input type='checkbox' id='rs_cache' name='rss_sidebar_cache' " . $checked . " /><br /><br />\n";
	if(!$checked) { $display = "style='display:none;'"; } else { $display = ""; }
	echo "<div id='rs_cache_duration' " . $display . ">";
	echo "Cache duration: \n";
		echo "<select name='rss_sidebar_cache_duration'>\n";
			$cache_duration = $settings['rss_sidebar_cache_duration'];
			if($cache_duration) { echo "<option value='" . $cache_duration . "'>" . $cache_duration . " mins</option>\n"; }
			echo "<option value='10'>10 mins</option>\n";
			echo "<option value='30'>30 mins</option>\n";
			echo "<option value='60'>60 mins</option>\n";
		echo "</select><br /><br />\n";
	echo "</div>";
	echo "Max. Items: \n"; 
		echo "<select name='rss_sidebar_max_items'>\n";
			$max_items = $settings['rss_sidebar_max_items'];
			if($max_items) { echo "<option value='" . $max_items . "'>" . $max_items . " mins</option>\n"; }
			echo "<option value='5'>5</option>\n";
			echo "<option value='10'>10</option>\n";
			echo "<option value='20'>20</option>\n";
		echo "</select><br /><br />\n";
	echo "<input type='submit' value='Save' />\n";
	echo "</form>\n";
}


/* ******************************************************************** 
 *  Function: rs_get_params
 *  Parameters: None
 *  Purpose: Retrieves parameters passed by URL, e.g. a saved feed url, and calls the appropriate functions
 *  Notes: Access to $_GET and $_POST is disabled for security reasons. Please use Inspekt to 
 *         access those parameters. See http://funkatron.com/inspekt/user_docs/
 *         Hotaru uses $cage, an instance of Inspekt's SuperCage object.
 ********************************************************************** */
 
function rs_get_params() {
	global $cage;
	$parameters = array();
	$parameters['rss_sidebar_feed'] = $cage->get->noTags('rss_sidebar_feed');
	$parameters['rss_sidebar_title'] = $cage->get->noTags('rss_sidebar_title');
	$parameters['rss_sidebar_cache'] = $cage->get->getAlpha('rss_sidebar_cache');
	$parameters['rss_sidebar_cache_duration'] = $cage->get->getInt('rss_sidebar_cache_duration');
	$parameters['rss_sidebar_max_items'] = $cage->get->getInt('rss_sidebar_max_items');
	rs_save_settings($parameters);
}


/* ******************************************************************** 
 *  Function: rs_save_settings
 *  Parameters: The parameters from the form as an array of key-value pairs
 *  Purpose: Saves new or modified settings for this plugin
 *  Notes: Returns to the plugin_settings page via a redirect. 
 ********************************************************************** */
 
function rs_save_settings(&$parameters) {
	global $plugin;
	$message = "";
	if($parameters) {
		if($parameters['rss_sidebar_feed'] == "") {
				$message = "No feed provided, so no changes were made.";
				$message_type = "red";
		}
			
		if($message == "") {
			$values = serialize($parameters);
			$message = "RSS Sidebar settings updated successfully.";
			$message_type = "green";
			$plugin->plugin_settings_update('rss_sidebar', 'rss_sidebar_settings', $values);	
		}
	}
	
	header("Location: " . baseurl . "admin/admin_index.php?page=plugin_settings&plugin=rss_sidebar&message=" . $message . "&message_type=" . $message_type);
	die();
}
 	
?>