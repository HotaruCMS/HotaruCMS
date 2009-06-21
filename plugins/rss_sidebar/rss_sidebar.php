<?php
/**
 * name: RSS Sidebar
 * description: Adds links in the sidebar to the latest posts from a specified RSS feed.
 * version: 0.1
 * folder: rss_sidebar
 * prefix: rs
 * hooks: rss_sidebar, admin_sidebar_plugin_settings, admin_plugin_settings, install_plugin_starter_settings
 *
 * Usage: Add <?php $plugin->check_actions('rss_sidebar'); ?> to your theme, wherever you want to show the links.
 */
	
/* ***** ACCESS ********************************************************* 
 * This plugin is accessed in two ways:
 * 1. Directly opened via http. This happens if a file links to it <a href=""> or 
 *    sends data from a form, in which case we want to include the Hotaru environment
 *    (hotaru_header.php) and then the get_params() function to process the data;  
 * 2. Included via check_actions() in class.plugins.php. This is done to give Hotaru 
 *    access to the functions, but we don't want to actually run the script from the 
 *    top so we return false for now.
 * ******************************************************************** */

if(!is_object($plugin)) { 
	// Accessed via 1 above;
	require_once('../../hotaru_header.php');
	rs_get_params(); 
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
	       
    $feedurl = $plugin->plugin_settings('rss_sidebar', 'rss_sidebar_feed');
    
    if($plugin->plugin_settings('rss_sidebar', 'rss_sidebar_cache')) { $cache = true; } else { $cache = false; }
    
    $cache_duration = $plugin->plugin_settings('rss_sidebar', 'rss_sidebar_cache_duration');
    
    $feed = $hotaru->new_simplepie($feedurl, $cache, $cache_duration);
    
    $max_items = $plugin->plugin_settings('rss_sidebar', 'rss_sidebar_max_items');
    
    $feed->init();
        
    $output = "";
    $item_count = 0;
	    
    if ($feed->data) { 
        foreach ($feed->get_items() as $item) {
                $output .= '';
                $output .= '<li><a href="' . $item->get_permalink() . '">' . $item->get_title() . '</a></li>';
            $item_count++;
            if($item_count >= $max_items) { break;}
        }
    }
	
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
	// parameters: plugin folder name, setting name, setting value
	$plugin->plugin_settings_update('rss_sidebar', 'rss_sidebar_feed', 'http://feeds2.feedburner.com/hotarucms');
	$plugin->plugin_settings_update('rss_sidebar', 'rss_sidebar_cache', 'on');
	$plugin->plugin_settings_update('rss_sidebar', 'rss_sidebar_cache_duration', 10);
	$plugin->plugin_settings_update('rss_sidebar', 'rss_sidebar_max_items', 10);	
}


/* ******************************************************************** 
 *  Function: rs_admin_sidebar_settings
 *  Parameters: None
 *  Purpose: Displays the contents of the plugin settings page.
 *  Notes: ---
 ********************************************************************** */
 
function rs_admin_plugin_settings() {
	global $plugin;
	echo "<h1>RSS Sidebar Configuration</h1>\n";
	echo "<form name='rss_sidebar_form' action='" . baseurl . "plugins/rss_sidebar/rss_sidebar.php' method='get'>\n";
	echo "Feed URL: <input type='text' size=60 name='rss_sidebar_feed' value='" . $plugin->plugin_settings('rss_sidebar', 'rss_sidebar_feed') . "' /><br /><br />\n";
	if($plugin->plugin_settings('rss_sidebar', 'rss_sidebar_cache')) { $checked = "checked"; } else { $checked = ""; }
	echo "Cache: <input type='checkbox' name='rss_sidebar_cache' " . $checked . " /><br /><br />\n";
	echo "Cache duration: \n";
		echo "<select name='rss_sidebar_cache_duration'>\n";
			$cache_duration = $plugin->plugin_settings('rss_sidebar', 'rss_sidebar_cache_duration');
			if($cache_duration) { echo "<option value='" . $cache_duration . "'>" . $cache_duration . " mins</option>\n"; }
			echo "<option value='10'>10 mins</option>\n";
			echo "<option value='30'>30 mins</option>\n";
			echo "<option value='60'>60 mins</option>\n";
		echo "</select><br /><br />\n";
	echo "Max. Items: \n"; 
		echo "<select name='rss_sidebar_max_items'>\n";
			$max_items = $plugin->plugin_settings('rss_sidebar', 'rss_sidebar_max_items');
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
	$parameters['rss_sidebar_cache'] = $cage->get->noTags('rss_sidebar_cache');
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
			foreach($parameters as $key => $value) {
				$plugin->plugin_settings_update('rss_sidebar', $key, $value);
				$message = "RSS Sidebar settings updated successfully.";
				$message_type = "green";
			}		
		}
	}
	
	header("Location: " . baseurl . "admin/admin_index.php?page=plugin_settings&plugin=rss_sidebar&message=" . $message . "&message_type=" . $message_type);
	die();
}
 	
?>