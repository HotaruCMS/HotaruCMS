<?php
/**
 * name: RSS Sidebar
 * description: Adds links in the sidebar to the latest posts from a specified RSS feed.
 * version: 0.1
 * folder: rss_sidebar
 * hooks: rss_sidebar, admin_sidebar_plugin_settings, admin_plugin_settings
 *
 * Usage: Add <?php $plugin->check_actions('rss_sidebar'); ?> to your theme, wherever you want to show the links.
 */
	
/* ***** START ********************************************* 
 * If we fall into this file directly, we don't want to continue...
 * ************************************************ ******************* */

return false;
die();


/* ******************************************************************** 
 *  Function: rss_sidebar
 *  Parameters: None
 *  Purpose: Retrieves parameters passed by URL, e.g. a saved feed url, and calls the appropriate functions
 *  Notes: ---
 ********************************************************************** */
	 
function rss_sidebar(&$parameters = array()) {
	if($parameters) {
		save_settings($parameters);
	} else {
		show_feed();
	}  
}


/* ******************************************************************** 
 *  Function: show_feed
 *  Parameters: None
 *  Purpose: Displays the RSS feed.
 *  Notes: Uses Hotaru's built-in SimplePie library, but extra customization 
 *         to the feed is possible by inserting SimplePie calls before $feed->init();
 ********************************************************************** */

function show_feed() {
    global $hotaru, $plugin;
	    
    /* *********** EDIT THESE SETTINGS ****************************** */
    $cache = true;
    $cache_duration = 10; // minutes
    $max_items = 10;
    /* ************************************************************** */
    
    $feedurl = $plugin->plugin_settings('rss_sidebar', 'rss_sidebar_feed');
    $feed = $hotaru->new_simplepie($feedurl, $cache, $cache_duration);
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

/* ********** ADMIN FUNCTIONS ********** */

/* ******************************************************************** 
 *  Function: admin_sidebar_plugin_settings
 *  Parameters: None
 *  Purpose: Puts a link to the settings page in the Admin sidebar under Plugin Settings
 *  Notes: ---
 ********************************************************************** */
 
function admin_sidebar_plugin_settings() {
	echo "<li><a href='admin_index.php?page=plugin_settings&plugin=rss_sidebar'>RSS Sidebar</a></li>";
}


/* ******************************************************************** 
 *  Function: admin_sidebar_settings
 *  Parameters: None
 *  Purpose: Displays the contents of the plugin settings page.
 *  Notes: Forms must be opened using Hotaru's plugin_form_open function for santization
 ********************************************************************** */
 
function admin_plugin_settings() {
	global $plugin;
	echo "<h1>RSS Sidebar Configuration</h1>\n";
	echo $plugin->plugin_form_open('rss_sidebar', 'get');
	echo "Feed URL: <input type='text' size=60 name='rss_sidebar_feed' value='" . $plugin->plugin_settings('rss_sidebar', 'rss_sidebar_feed') . "' />\n";
	echo "<input type='submit' value='Save' />\n";
	echo $plugin->plugin_form_close();
}


/* ******************************************************************** 
 *  Function: save_settings
 *  Parameters: The parameters from the form as an array of key-value pairs
 *  Purpose: Saves new or modified settings for this plugin
 *  Notes: Returns to the plugin_settings page via a redirect - not ideal. 
 *         Hoping to find a better way in order to pass success or failure messages.
 ********************************************************************** */
 
function save_settings(&$parameters) {
	global $plugin;
	if($parameters) {
		foreach($parameters as $key => $value) {
			if($value && ($key != "plugin")) {
				$plugin->plugin_settings_update('rss_sidebar', $key, $value);
			}
		}
	}
	
	$plugin->message = "Saved successfully"; // doesn't work
	header("Location: " . baseurl . "admin/admin_index.php?page=plugin_settings&plugin=rss_sidebar");
	die();
}
 	
?>