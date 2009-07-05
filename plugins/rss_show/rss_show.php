<?php
 
/* ********** PLUGIN *********************************************************************************
 * name: RSS Show
 * description: Adds links in the sidebar to the latest posts from a specified RSS feed.
 * version: 0.1
 * folder: rss_show
 * prefix: rs
 * hooks: rss_show, admin_header_include, header_include, admin_sidebar_plugin_settings, admin_plugin_settings, install_plugin_starter_settings
 *
 * Usage: Add <?php $plugin->check_actions('rss_show'); ?> to your theme, wherever you want to show the links.
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
 
return false; die(); // We don't want to just drop into the file.

/* ******************************************************************** 
 *  Function: rs_rss_show
 *  Parameters: None
 *  Purpose: Displays the RSS feed.
 *  Notes: Uses Hotaru's built-in SimplePie library, but extra customization 
 *         to the feed is possible by inserting SimplePie calls before $feed->init();
 ********************************************************************** */

function rs_rss_show($ids) {
    global $hotaru, $plugin;
    
    	// if no feed id is specified in the plugin hook, we default to 1.
    	if(empty($ids)) { $ids[0] = 1; }
    	   	
	foreach($ids as $id) { 
		
		// Get settings from the database:
		$settings = unserialize($plugin->plugin_settings('rss_show', 'rss_show_' . $id . '_settings')); 
	
		// Feed settings:
		$feedurl = $settings['rss_show_feed'];
		if($settings['rss_show_cache']) { $cache = true; } else { $cache = false; }
		$cache_duration = $settings['rss_show_cache_duration'];
		
		// Get the feed...
		$feed = $hotaru->new_simplepie($feedurl, $cache, $cache_duration);
		
		if($feed) {
		
			// Limit the number of items:
			$max_items = $settings['rss_show_max_items'];
			
			// Feed is ready.
			$feed->init();
			
			$output = "";
			$item_count = 0;
			
			// SITE TITLE
			if($settings['rss_show_title']) { 
				$output .= "<li class='rss_show_feed_title'>";
				$output .= "<a href='" . $feed->subscribe_url() . "' title='RSS Feed'><img src='" . baseurl . "images/rss_16.gif'></a>&nbsp;"; // RSS icon
				$output .= "<a href='" . $feed->get_link(). "' title='Visit the site'>" . $settings['rss_show_title'] . "</a></li>"; 
			}
			    
			if ($feed->data) { 
				foreach ($feed->get_items() as $item) {
				        $output .= "";
				        
				        // POST TITLE
				        $output .= "<li class='rss_show_feed_item'>";
				        $output .= "<span class='rss_show_title'>";
				        $output .= "<a href='" . $item->get_permalink() . "'>" . $item->get_title() . "</a></span>";
				        
				        // AUTHOR / DATE
					if(($settings['rss_show_author'] == 'yesauthor') || ($settings['rss_show_date'] == 'yesdate')) {
					        $output .= "<br /><span class='rss_show_author_date'><small>Posted";
					        if($settings['rss_show_author'] == 'yesauthor') {
					        	$output .= " by ";
					                foreach ($item->get_authors() as $author)  {
								$output .= $author->get_name(); 
							}
						}
						if($settings['rss_show_date'] == 'yesdate') {
							$output .= " on " . $item->get_date('j F Y');
						}
						$output .= "</small></span><br />";
					}
					
					// SUMMARY
					if($settings['rss_show_content'] == 'summaries') {
						$output .= "<p class='rss_show_content'>" . substr(strip_tags($item->get_content()), 0, 300);
						$output .= "... ";
						$output .= "<small><a href='" . $item->get_permalink() . "' title='" . $item->get_title() . "'>[Read More]</a>";
						$output .= "</small></p>";
					}
					
					// FULL POST
					if($settings['rss_show_content'] == 'full') {
						$output .= "<p class='rss_show_content'>" . $item->get_content() . "</p>";
					}
					$output .= '</li>';
					
				    $item_count++;
				    if($item_count >= $max_items) { break;}
				}
			}
		}
		
		// Display the whole thing:
		if(isset($output)) { echo $output; }
	}
}


/* ******************************************************************** 
 *  Function: rs_header_include()
 *  Parameters: None
 *  Purpose: Includes the RSS Show css file
 *  Notes: ---
 ********************************************************************** */
 
function rs_header_include() {
	echo "<link rel='stylesheet' href='" . baseurl . "plugins/rss_show/rss_show.css' type='text/css'>\n";
}



/* *************************************
 * ********** ADMIN FUNCTIONS **********
 * ************************************* */


/* ******************************************************************** 
 *  Function: rs_admin_sidebar_plugin_settings
 *  Parameters: None
 *  Purpose: Includes jQuery for hiding and showing "cache duration" in plugin settings
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
	echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>'rss_show'), 'admin') . "'>RSS Show</a></li>";
}


/* ******************************************************************** 
 *  Function: rs_install_plugin_starter_settings
 *  Parameters: None
 *  Purpose: When the plugin is installed, this function inserts some prelimnary 
 *           settings into the pluginsettings table.
 *  Notes: All database queries should use the prepare function.
 ********************************************************************** */
 
function rs_install_plugin_starter_settings($id) {
	global $db, $plugin;
	
	if(!$id || !is_int($id)) { $id = 1; }
	
	$settings['rss_show_feed'] = 'http://feeds2.feedburner.com/hotarucms';
	$settings['rss_show_title'] = 'Hotaru CMS Forums';
	$settings['rss_show_cache'] = 'on';
	$settings['rss_show_cache_duration'] = 10;
	$settings['rss_show_max_items'] = 10;
	$settings['rss_show_author'] = "noauthor";
	$settings['rss_show_date'] = "nodate";
	$settings['rss_show_content'] = "none";
	
	// parameters: plugin folder name, setting name, setting value
	$plugin->plugin_settings_update('rss_show', 'rss_show_' . $id . '_settings', serialize($settings));
}


/* ******************************************************************** 
 *  Function: rs_admin_sidebar_settings
 *  Parameters: None
 *  Purpose: Displays the contents of the plugin settings page.
 *  Notes: ---
 ********************************************************************** */
 
function rs_admin_plugin_settings() {
	global $hotaru, $plugin;
	
	rs_get_params();	// get any arguments passed from the form
	
	$hotaru->show_message();	// display any success or failure messages
	
	// Cycle through the RSS feeds, displaying their settings...
	$id = 1;
	while($settings = unserialize($plugin->plugin_settings('rss_show', 'rss_show_' . $id . '_settings'))) {
		echo "<h1>RSS Show Configuration [ id: " . $id . " ]</h1>\n";
		echo "<form name='rss_show_form' action='" . baseurl . "admin/admin_index.php' method='get'>\n";
		
		echo "Feed URL: <input type='text' size=60 name='rss_show_feed' value='" . $settings['rss_show_feed'] . "' /><br /><br />\n";
		
		echo "Feed title: <input type='text' size=30 name='rss_show_title' value='" . $settings['rss_show_title'] . "' /><br /><br />\n";
		if($settings['rss_show_cache']) { $checked = "checked"; } else { $checked = ""; }
		echo "Cache: <input type='checkbox' id='rs_cache' name='rss_show_cache' " . $checked . " /><br /><br />\n";
		
		if(!$checked) { $display = "style='display:none;'"; } else { $display = ""; }
		echo "<div id='rs_cache_duration' " . $display . ">";
		echo "Cache duration: \n";
			echo "<select name='rss_show_cache_duration'>\n";
				$cache_duration = $settings['rss_show_cache_duration'];
				if($cache_duration) { echo "<option value='" . $cache_duration . "'>" . $cache_duration . " mins</option>\n"; }
				echo "<option value='10'>10 mins</option>\n";
				echo "<option value='30'>30 mins</option>\n";
				echo "<option value='60'>60 mins</option>\n";
			echo "</select><br /><br />\n";
		echo "</div>";
		
		echo "Max. items: \n"; 
			echo "<select name='rss_show_max_items'>\n";
				$max_items = $settings['rss_show_max_items'];
				if($max_items) { echo "<option value='" . $max_items . "'>" . $max_items . "</option>\n"; }
				echo "<option value='5'>5</option>\n";
				echo "<option value='10'>10</option>\n";
				echo "<option value='20'>20</option>\n";
			echo "</select><br /><br />\n";
		
		if($settings['rss_show_author'] == 'yesauthor') { $yeschecked = "checked"; $nochecked = ""; } else { $yeschecked = ""; $nochecked = "checked";}
		echo "Show author: &nbsp;&nbsp;<input type='radio' name='rss_show_author' value='yesauthor' " . $yeschecked . " /> Yes &nbsp;&nbsp;\n";
		echo "<input type='radio' name='rss_show_author' value='noauthor' " . $nochecked . " /> No<br /><br />\n";	
		
		if($settings['rss_show_date'] == 'yesdate') { $yeschecked = "checked"; $nochecked = ""; } else { $yeschecked = ""; $nochecked = "checked";}
		echo "Show date: &nbsp;&nbsp;<input type='radio' name='rss_show_date' value='yesdate' " . $yeschecked . " /> Yes &nbsp;&nbsp;\n";
		echo "<input type='radio' name='rss_show_date' value='nodate' " . $nochecked . " /> No<br /><br />\n";	
		
		if($settings['rss_show_content'] == 'none') { 
			$contentnone = "checked"; $contentsummaries = ""; $contentfull = "";
		} elseif($settings['rss_show_content'] == 'summaries') { 
			$contentnone = ""; $contentsummaries = "checked"; $contentfull = "";
		} else {
			$contentnone = ""; $contentsummaries = ""; $contentfull = "checked";
		}
		echo "Show content: &nbsp;&nbsp;<input type='radio' name='rss_show_content' value='none' " . $contentnone . " /> Titles only &nbsp;&nbsp;\n";
		echo "<input type='radio' name='rss_show_content' value='summaries' " . $contentsummaries . " /> Summaries &nbsp;&nbsp;\n";
		echo "<input type='radio' name='rss_show_content' value='full' " . $contentfull . " /> Full<br /><br />\n";	
		
		echo "<input type='hidden' name='page' value='plugin_settings' />\n";
		echo "<input type='hidden' name='plugin' value='rss_show' />\n";
		echo "<input type='hidden' name='rss_show_id' value='" . $id . "' />\n";
		echo "<input type='submit' value='Save' />\n";
		echo "</form>\n";
		$id++;
	}
	
	echo "<br /><a href='" . baseurl . "admin/admin_index.php?page=plugin_settings&amp;plugin=rss_show&amp;action=delete_feed&amp;id=" . ($id-1) . "' style='color: red;'>Delete the last feed</a> | <a href='" . baseurl . "admin/admin_index.php?page=plugin_settings&amp;plugin=rss_show&amp;action=new_feed&amp;id=" . $id . "'>Add another RSS feed</a><br /><br />";
	echo "<div style='padding: 0.8em; line-height: 2.0em; background-color: #f0f0f0; -moz-border-radius: 0.5em;- webkit-border-radius: 0.5em;'>\n";
		echo "<b>Usage:</b><br />\n";
		echo "To show the first feed, paste this into your template:<br />\n";
		echo "<pre>&lt;?php &#36;plugin-&gt;check_actions('rss_show'); ?&gt;</pre><br />\n";
		echo "To show another feed, use this with the feed id in the array:<br />\n";
		echo "<pre>&lt;?php &#36;plugin-&gt;check_actions('rss_show', true, '', array(2)); ?&gt;</pre><br />\n";
		echo "To show two feeds back to back, paste this into your template with the ids in the array:<br />\n";
		echo "<pre>&lt;?php &#36;plugin-&gt;check_actions('rss_show', true, '', array(1, 2)); ?&gt;</pre><br />\n";
	echo "</div>\n";
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
	global $cage, $hotaru, $plugin;
	if($action = $cage->get->testRegex('action', '/^([a-z0-9_-])+$/i')) {
		if($action == 'new_feed') {
			$id = $cage->get->getInt('id');
			rs_install_plugin_starter_settings($id);
			$hotaru->message = "New default feed added.";
			$hotaru->message_type = "green";
			//header("Location: " . baseurl . "admin/admin_index.php?page=plugin_settings&plugin=rss_show&message=" . $message . "&message_type=" . $message_type);
			//die();
		} elseif($action == 'delete_feed') {
			$id = $cage->get->getInt('id');
			$plugin->plugin_settings_remove_setting('rss_show_' . $id . '_settings');
			$hotaru->message = "Feed removed.";
			$hotaru->message_type = "green";
			//header("Location: " . baseurl . "admin/admin_index.php?page=plugin_settings&plugin=rss_show&message=" . $message . "&message_type=" . $message_type);
			//die();		
		}
	} elseif($id = $cage->get->getInt('rss_show_id')) {
		$parameters = array();
		$parameters['rss_show_feed'] = $cage->get->noTags('rss_show_feed');
		$parameters['rss_show_title'] = $cage->get->noTags('rss_show_title');
		$parameters['rss_show_cache'] = $cage->get->getAlpha('rss_show_cache');
		$parameters['rss_show_cache_duration'] = $cage->get->getInt('rss_show_cache_duration');
		$parameters['rss_show_max_items'] = $cage->get->getInt('rss_show_max_items');
		$parameters['rss_show_author'] = $cage->get->getAlpha('rss_show_author');
		$parameters['rss_show_date'] = $cage->get->getAlpha('rss_show_date');
		$parameters['rss_show_content'] = $cage->get->getAlpha('rss_show_content');
		rs_save_settings($id, $parameters);
	}
}


/* ******************************************************************** 
 *  Function: rs_save_settings
 *  Parameters: The parameters from the form as an array of key-value pairs
 *  Purpose: Saves new or modified settings for this plugin
 *  Notes: Returns to the plugin_settings page via a redirect. 
 ********************************************************************** */
 
function rs_save_settings($id, &$parameters) {
	global $plugin, $hotaru;
	$hotaru->message = "";
	if($parameters) {
		if($parameters['rss_show_feed'] == "") {
				$hotaru->message = "No feed provided, so no changes were made.";
				$hotaru->message_type = "red";
		}
			
		if($hotaru->message == "") {
			$values = serialize($parameters);
			$hotaru->message = "RSS Show settings updated successfully.";
			$hotaru->message_type = "green";
			$plugin->plugin_settings_update('rss_show', 'rss_show_' . $id . '_settings', $values);	
		}
	}
	
	//header("Location: " . baseurl . "admin/admin_index.php?page=plugin_settings&plugin=rss_show&message=" . $message . "&message_type=" . $message_type);
	//die();
}
 	
?>