<?php
/* ********** PLUGIN *********************************************************************************
 * name: Sidebar
 * description: Manages the contents of the sidebar
 * version: 0.1
 * folder: sidebar
 * prefix: sidebar
 * hooks: install_plugin, hotaru_header, header_include, admin_plugin_settings, admin_sidebar_plugin_settings, sidebar
 *
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


 /* ******************************************************************** 
 * ********************************************************************* 
 * ********************* FUNCTIONS FOR POST CLASS ********************** 
 * *********************************************************************
 * ****************************************************************** */
 
 
/* ******************************************************************** 
 *  Function: sidebar_install_plugin
 *  Parameters: None
 *  Purpose: Set default settings for the sidebar
 *  Notes: Happens when the plugin is installed. The field is never deleted.
 ********************************************************************** */
 
function sidebar_install_plugin() {
	global $db, $plugin, $post;
	
	// A plugin hook so other plugin developers can add defaultsettings
	$plugin->check_actions('sidebar_install_plugin');
	
	$plugin->include_language_file('sidebar');
}


/* ******************************************************************** 
 *  Function: sidebar_hotaru_header
 *  Parameters: None
 *  Purpose: Set things up when the page is first loaded
 *  Notes: ---
 ********************************************************************** */
 
function sidebar_hotaru_header() {
	global $hotaru, $plugin, $sidebar;
	
	$plugin->include_language_file('sidebar');
	
	if($hotaru->sidebar) {
		require_once(plugins . 'sidebar/class.sidebar.php');
		// Create a new global object called "sidebar".
		$sidebar = new Sidebar();
		
		$sidebar->initialize_sidebar_widgets();
		
		$vars['sidebar'] = $sidebar; 
		return $vars; 
	}
}


/* ******************************************************************** 
 *  Function: sidebar_header_include
 *  Parameters: None
 *  Purpose: Includes css and language files.
 *  Notes: ---
 ********************************************************************** */
 
function sidebar_header_include() {
	global $plugin;
	$plugin->include_css_file('sidebar');
	
	// A plugin hook so other plugin developers can include files here
	$plugin->check_actions('sidebar_admin_header_include');
}



 /* ******************************************************************** 
 * ********************************************************************* 
 * ************ FUNCTIONS FOR SHOWING THE SIDEBAR CONTENT ************** 
 * *********************************************************************
 * ****************************************************************** */

/* ******************************************************************** 
 *  Function: sidebar_sidebar
 *  Parameters: None
 *  Purpose: This is the hook in the sidebar template. 
 *  Notes: It builds a new function name from the widget name and calls it.
 ********************************************************************** */
 
function sidebar_sidebar($sidebar_id = array(1)) {
	global $plugin, $sidebar;

	$sidebar_id = $sidebar_id[0];
		
	$widgets = $sidebar->get_sidebar_widgets();

	foreach($widgets as $widget => $details) {
		$function_name = "sidebar_widget_" . $widget;
		
		// Only show widgets intended for this sidebar
		if($details['sidebar'] == $sidebar_id) {
		
			// Call this widget's function
			if(function_exists($function_name)) {
				$function_name($details['args']);	// pass an argument, e.g. a feed ID for the RSS Show plugin
			} else {
				/* For multiple instances of widgets, we need to strip the id off the end and use the argument as the identifier.
				   E.g. CHANGE sidebar_widget_rss_show_1(1); 
				        TO     sidebar_widget_rss_show(1); */
				
				$function_name_array = explode('_', $function_name);
				array_pop($function_name_array); 
				$function_name = implode('_', $function_name_array);
				$function_name($details['args']);	// pass an argument, e.g. a feed ID for the RSS Show plugin
			}
		}
	}
}



 /* ******************************************************************** 
 * ********************************************************************* 
 * ******************* FUNCTIONS FOR ADMIN SETTINGS ******************** 
 * *********************************************************************
 * ****************************************************************** */

/* ******************************************************************** 
 *  Function: sidebar_admin_sidebar_plugin_settings
 *  Parameters: None
 *  Purpose: Link to settings page in the Admin sidebar
 *  Notes: ---
 ********************************************************************** */
 
function sidebar_admin_sidebar_plugin_settings() {
	global $lang;
	
	echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>'sidebar'), 'admin') . "'>" . $lang["sidebar_admin_sidebar"] . "</a></li>";
}


/* ******************************************************************** 
 *  Function: sidebar_admin_plugin_settings
 *  Parameters: None
 *  Purpose: Sidebar Settings Page
 *  Notes: ---
 ********************************************************************** */
 
function sidebar_admin_plugin_settings() {
	global $hotaru, $plugin, $cage, $lang, $sidebar;
	
	echo "<h1>" . $lang["sidebar_settings_header"] . "</h1>\n";
	
	if($cage->get->testAlpha('action')) {
	
		// Get sidebar settings from the database...
		$sidebar_settings = unserialize($plugin->plugin_settings('sidebar', 'sidebar_settings')); 
		
		// Get the list of sidebar widgets...
		$widgets = $sidebar->get_sidebar_widgets();
		$last = count($widgets);
			
		$this_widget_name = $cage->get->testAlnumLines('widget');
		$this_widget_order = $cage->get->testInt('order');
		$this_widget_sidebar = $cage->get->testInt('sidebar');
		
		if($cage->get->testAlpha('action') == 'orderup') {
			if($this_widget_order > 1) {
				// find widget in the target spot...
				foreach($widgets as $widget => $details) {
					if($details['order'] == ($this_widget_order - 1)) {
					
						//Check if this widget and the target are in the same sidebar
						if($sidebar_settings['sidebar_settings_block_order'][$widget]['sidebar'] == $sidebar_settings['sidebar_settings_block_order'][$this_widget_name]['sidebar']) {
						
							$sidebar_settings['sidebar_settings_block_order'][$widget]['order'] = $details['order'] + 1;
							$sidebar_settings['sidebar_settings_block_order'][$this_widget_name]['order'] = $this_widget_order - 1;
							$hotaru->messages[$lang['sidebar_order_updated']] = 'green';
							break;
						} else {
							// In different sidebars so don't change the order, just the sidebar value
							$sidebar_settings['sidebar_settings_block_order'][$this_widget_name]['sidebar']--;
						}
					}
				}
						
			} else {
				// prevent moving into sidebar 0:
				if(($sidebar->get_last_sidebar($widgets) > 1) && ($sidebar_settings['sidebar_settings_block_order'][$this_widget_name]['sidebar'] > 1)) {
					$sidebar_settings['sidebar_settings_block_order'][$this_widget_name]['sidebar']--;
				} else {
					$hotaru->messages[$lang['sidebar_order_already_first']] = 'red';
				}
			}
			
		} elseif($cage->get->testAlpha('action') == 'orderdown') {
			if($this_widget_order < $last) {
				// find widget in the target spot...
				foreach($widgets as $widget => $details) {
					if($details['order'] == ($this_widget_order + 1)) {
						$sidebar_settings['sidebar_settings_block_order'][$widget]['order'] = $details['order'] - 1;
						$sidebar_settings['sidebar_settings_block_order'][$this_widget_name]['order'] = $this_widget_order + 1;
						$hotaru->messages[$lang['sidebar_order_updated']] = 'green';
						break;
					}
				}
			} else {
				$sidebar_settings['sidebar_settings_block_order'][$this_widget_name]['sidebar']++;
				//$hotaru->messages[$lang['sidebar_order_already_last']] = 'red';
			}		
		}
		
		// Save updated sidebar settings
		$plugin->plugin_settings_update('sidebar', 'sidebar_settings', serialize($sidebar_settings));
		
	}
	
	$hotaru->show_messages();
	$hotaru->display_template('sidebar_ordering', 'sidebar');

	
	/* FORM STUFF
	// If the form has been submitted, go and save the data...
	if($cage->post->getAlpha('submitted') == 'true') { 
		sidebar_save_settings(); 
	}    

	// Get settings from the database if they exist...
	$sidebar_settings = unserialize($plugin->plugin_settings('sidebar', 'sidebar_settings')); 
		
	// A plugin hook so other plugin developers can add settings
	$plugin->check_actions('sidebar_settings_get_values');
	
	// The form should be submitted to the admin_index.php page:
	echo "<form name='sidebar_settings_form' action='" . baseurl . "admin/admin_index.php?page=plugin_settings&amp;plugin=sidebar' method='post'>\n";
	
	// A plugin hook so other plugin developers can show settings
	$plugin->check_actions('sidebar_settings_form');
	
	echo "<br />\n";    
	echo "<input type='hidden' name='submitted' value='true' />\n";
	echo "<input type='submit' value='" . $lang["sidebar_settings_save"] . "' />\n";
	echo "</form>\n";
	*/
}


/* ******************************************************************** 
 *  Function: sidebar_save_settings
 *  Parameters: None
 *  Purpose: Save Sidebar Settings
 *  Notes: ---
 ********************************************************************** */

function sidebar_save_settings() {
	global $cage, $hotaru, $plugin, $lang;
	
	$error = 0;
	
	// Get settings from the database if they exist...
	$sidebar_settings = unserialize($plugin->plugin_settings('sidebar', 'sidebar_settings')); 
		
	// A plugin hook so other plugin developers can save settings   
	$plugin->check_actions('sidebar_save_settings');
	
	// Save new settings...	

	
	// parameters: plugin folder name, setting name, setting value
	$plugin->plugin_settings_update('sidebar', 'sidebar_settings', serialize($sidebar_settings));
	
	if($error == 0) {
		$hotaru->messages[$lang["sidebar_settings_saved"]] = "green";
	}
	
	$hotaru->show_messages();
	
	return true;    
} 

?>