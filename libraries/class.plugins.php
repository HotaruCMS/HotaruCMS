<?php

/* The Plugin class is used for plugins. It contains common methods for plugins. */

// includes
if(file_exists('hotaru_header.php')) {
	require_once('hotaru_header.php');	// assumes we are in the root directory
} else {
	require_once('../hotaru_header.php');	// assumes we are one level deep, e.g. in the admin directory
}
require_once(libraries . 'class.metadata.php');	// This is the generic_pmd class that reads post metadata from the top of a plugin file.

class Plugin extends generic_pmd {
	
	var $id = '';
	var $enabled = 0;
	var $name = '';
	var $desc = '';
	var $folder = '';
	var $version = 0;
/*
	function __construct($enabled = 0, $name = "", $desc = "", $folder = "", $version = 0) {
		$this->enabled = $enabled;
		$this->name = $name;
		$this->desc = $desc;
		$this->folder = $folder;
		$this->version = $version;
	}
	
	function register_plugin() {
		global $db;
		
		$sql = "INSERT INTO " . table_plugins . " (plugin_enabled, plugin_name, plugin_desc, plugin_folder, plugin_version) VALUES (" . $this->enabled . ", " . $this->name . ", " . $this->desc . ", " . $this->folder . ", " . $this->version . ")";
		
		if($db->query($sql)) {
			return true;
		} else {
			return false;
		}
	}
	
	function enable_plugin($enabled = false) {
		
	}
*/
	
	function get_plugins_array() {
		$plugin_list = getFilenames(plugins, "short");
		$plugins_array = array();
		foreach($plugin_list as $plugin_folder_name) {
			$plugin_metadata = $this->read(plugins . $plugin_folder_name . "/" . $plugin_folder_name . ".php");
			if($plugin_metadata) {
				array_push($plugins_array, $plugin_metadata);
			}
		}	
		return $plugins_array;
	}
	
	function show_plugins() {
		$plugins_array = $this->get_plugins_array();
		
		$full_plugins_list = '';
		if($plugins_array) {
			foreach($plugins_array as $plugin_details) {
				$full_plugins_list .= "<p class='admin_plugins_titles'>" . $plugin_details['name'] . " <span>Version: " . $plugin_details['version'] . "</span></p>\n";
				$full_plugins_list .= "<p class='admin_plugins_descriptions'>" . $plugin_details['description']. "</p>\n";
				$full_plugins_list .= "<hr>\n";
			}	
		}
		return $full_plugins_list;
	}
}

