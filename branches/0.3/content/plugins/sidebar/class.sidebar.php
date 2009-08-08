<?php

/* ********** PLUGIN CLASSES**************************************************************************
 * name: Sidebar
 * description: Class to manage the sidebar
 * file: /plugins/sidebar/class.sidebar.php
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
	
class Sidebar {	


	var $sidebar_vars = array();


	/* ******************************************************************** 
	 *  Functions: PHP __set Magic Method
	 *  Parameters: The name of the member variable and the value to set it to.
	 *  Purpose: Plugins use this to set additonal member variables
	 *  Notes: ---
	 ********************************************************************** */
	 			
	function __set($name, $value) {
        	$this->sidebar_vars[$name] = $value;
    	}
    	
    	
	/* ******************************************************************** 
	 *  Functions: PHP __get Magic Method
	 *  Parameters: The name of the member variable to retrieve.
	 *  Purpose: Plugins use this to read values of additonal member variables
	 *  Notes: ---
	 ********************************************************************** */
    	
	function __get($name) {
		if (array_key_exists($name, $this->sidebar_vars)) {
			return $this->sidebar_vars[$name];
		}
    	}


	/* ******************************************************************** 
	 *  Functions: initialize_sidebar_widgets
	 *  Parameters: None
	 *  Purpose: Find which plugins have "plugin_settings" for "sidebar_widgets", give them an initial order and serialize them in "sidebar_settings"
	 *  Notes: ---
	 ********************************************************************** */
	 
	function initialize_sidebar_widgets() {
		global $plugin;
		
		// Get settings from the database if they exist...
		$sidebar_settings = $this->get_sidebar_settings();
			
		$sidebar_widgets = $plugin->plugin_settings_array('sidebar_widgets');
		
		if($sidebar_widgets) {
			$count = 1;
			foreach($sidebar_widgets as $widget) {
				// Only reset order if it doesn't already exist.
				if(!isset($sidebar_settings['sidebar_settings_block_order'][$widget->plugin_setting]['order'])) {
					$sidebar_settings['sidebar_settings_block_order'][$widget->plugin_setting]['order'] = $count;
				}
				// Only reset sidebar_id if it doesn't already exist.
				if(!isset($sidebar_settings['sidebar_settings_block_order'][$widget->plugin_setting]['sidebar'])) {
					$sidebar_settings['sidebar_settings_block_order'][$widget->plugin_setting]['sidebar'] = 1;
				}
				$sidebar_settings['sidebar_settings_block_order'][$widget->plugin_setting]['args'] = $widget->plugin_value;
				$count++;
			}
			$plugin->plugin_settings_update('sidebar', 'sidebar_settings', serialize($sidebar_settings));
		}
	}


	/* ******************************************************************** 
	 *  Functions: get_sidebar_settings
	 *  Parameters: None
	 *  Purpose: Returns an array of sidebar settings
	 *  Notes:
	 ********************************************************************** */
	 
	function get_sidebar_settings() {
		global $plugin;
		
		// Get settings from the database if they exist...
		$sidebar_settings = unserialize($plugin->plugin_settings('sidebar', 'sidebar_settings')); 		
		return $sidebar_settings;
	}
	
	
	/* ******************************************************************** 
	 *  Functions: get_sidebar_widgets
	 *  Parameters: None
	 *  Purpose: Returns an array of sidebar widgets
	 *  USAGE: foreach($widgets as $widget=>$details) { echo "Name: " . $widget; echo $details['order']; echo $details['args']; } 
	 ********************************************************************** */
	 
	function get_sidebar_widgets() {
		global $plugin;
		
		// Get settings from the database if they exist...
		$sidebar_settings = $this->get_sidebar_settings();
		
		if($sidebar_settings['sidebar_settings_block_order']) {
			$widgets = $sidebar_settings['sidebar_settings_block_order'];	// associative array
					
			$widgets = $this->order_sidebar_widgets($widgets);	// sorts plugins by "order"
	
			return $widgets;
		}
	}

	/* ******************************************************************** 
	 *  Functions: order_sidebar_widgets
	 *  Parameters: array of widgets
	 *  Purpose: Sort the widgets by order number
	 *  Notes: ---
	 ********************************************************************** */
	 	
	function order_sidebar_widgets($widgets) {
		return sksort($widgets, "order", "int", true);
	}
	
	/* ******************************************************************** 
	 *  Functions: get_last_sidebar
	 *  Parameters: array of widgets
	 *  Purpose: Returns the highest sidebar value of all the widgets. In other words, the number of sidebars. 
	 *  Notes: ---
	 ********************************************************************** */
	 	
	function get_last_sidebar($widgets) {
		global $plugin;
			
		$highest = 1;
		foreach($widgets as $widget => $details) {
			if(isset($details['sidebar']) && ($details['sidebar'] > $highest)) { $highest = $details['sidebar']; }
		}
		return $highest;
	}
}

?>