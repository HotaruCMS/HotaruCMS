<?php
/* ********** PLUGIN *********************************************************************************
 * name: Hello Universe
 * description: Demonstrates how to make plugins
 * version: 0.1
 * folder: hello_universe
 * prefix: hu
 * hooks: theme_index_main, theme_index_sidebar, hotaru_header
 *
 * You can type notes here, such as the license below:
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
 *  FUNCTION #1
 *  -----------
 *  Function: hw_theme_index_main
 *  Parameters: None
 *  Purpose: Plugin welcome page with various options.
 *  Notes: Uses theme_index_main in the hooks list at the top of this file.
 ********************************************************************** */
 
function hu_theme_index_main() {
	global $hotaru;
	
	// These lines get the current page and display any matches...
	$page = $hotaru->get_page_name();
	switch($page) {
		case 'plugin_template':
			$hotaru->display_template('plugin_template', 'hello_universe'); // Displays the page from this plugin folder
			return true;
			break;
		case 'form_example':
			$hotaru->display_template('form_example', 'hello_universe'); // Displays the page from this plugin folder
			return true;
			break;
		case 'main':
			hu_main_page();
			return true;
			break;
		default:
			break;
	}
}


/* ******************************************************************** 
 *  FUNCTION #2
 *  -----------
 *  Function: hu_main_page
 *  Parameters: None
 *  Purpose: Output text for the main page.
 *  Notes: No hooks. Called by Function #1
 *  IMPORTANT: 	Since we're echo'ing text to the main page, there's a very good chance it will appear 
 *		above or below content from *another* plugin using the same page. The way around that, 
 * 		which we're not doing here, is to create another template along with plugin_template.php 
 *		and form_example.php and echo the text from there. Any other pages we make could use the 
 *		same template instead of making new ones for every page.
 ********************************************************************** */
 
function hu_main_page() {
	// If the current page is "main" (which it is by default)...
	echo "<div class='main_inner' style='margin: 1.0em; background-color: #eee;'>";
	echo "<h2>Hello Universe!</h2>";
	echo "<p>This text is shown by including the <i>theme_index_main</i> hook. See Functions #1 and #2 in hello_universe.php</p>";  
	echo "<ul>";
	// Note these links allow for either friendly or unfriendly urls...
	echo "<li><a href='" . url(array('page'=>'plugin_template')) . "'>See a theme page made by this plugin</a></li>";
	echo "<li><a href='" . url(array('page'=>'form_example')) . "'>See an example form made with this plugin</a></li>";
	echo "</ul></div>";
}

/* ******************************************************************** 
 *  FUNCTION #3
 *  -----------
 *  Function: hu_theme_index_sidebar
 *  Parameters: None
 *  Purpose: A sidebar that overrides the real sidebar!
 *  Notes: Uses theme_index_sidebar in the hooks list at the top of this file.
 ********************************************************************** */
 
function hu_theme_index_sidebar() {
	global $hotaru;

	$hotaru->display_template('sidebar', 'hello_universe'); // Overrides the current sidebar with a new one.
	return true;
}


/* ******************************************************************** 
 *  FUNCTION #4
 *  -----------
 *  Function: hu_hotaru_header
 *  Parameters: None
 *  Purpose: Includes the Hello Universe language file
 *  Notes: This is used in the example form.
 ********************************************************************** */
 
function hu_hotaru_header() {
	global $lang, $plugin;
	
	// include hello_universe language file
	$plugin->include_language_file('hello_universe');
}

?>