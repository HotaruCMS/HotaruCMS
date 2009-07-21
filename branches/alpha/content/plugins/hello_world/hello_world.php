<?php
/* ********** PLUGIN *********************************************************************************
 * name: Hello World
 * description: Displays "Hello World!"
 * version: 0.1
 * folder: hello_world
 * prefix: hw
 * hooks: hello_world
 *
 * You can type notes here, e.g:
 * Usage: Add <?php $plugin->check_actions('hello_world'); ?> to your theme, wherever you want to show "Hello World".
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
 *  Function: hw_hello_world
 *  Parameters: None
 *  Purpose: Displays "Hello World!" wherever the plugin hook is.
 *  Notes: ---
 ********************************************************************** */
 
function hw_hello_world() {
	echo "Hello World!";
}

/* 

Four MUST-DO's when making a Hotaru plugin (if you want it to work!):

1. You must have a folder, e.g. "hello_world"
2. You must have a .php file with exactly the same name, e.g. "hello_world"
3. You must include comments at the top of the file, listing name, description, version, folder, prefix and hooks (comma separated). Use the same formatting as shown above, i.e. colon separators.
4. You must have a function with the same name as each hook with the prefix attached, e.g. function hw_hello_world();
 
In a template, add <?php $hotaru->check_actions('HOOK_NAME'); ?> to call your plugin.

*/
?>