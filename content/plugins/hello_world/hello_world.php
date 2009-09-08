<?php
/**
 * name: Hello World
 * description: Displays "Hello World!"
 * version: 0.1
 * folder: hello_world
 * prefix: hw
 * hooks: hello_world
 *
 * Usage: Add <?php $plugin->check_actions('hello_world'); ?> to your theme, wherever you want to show "Hello World".
*
 * PHP version 5
 *
 * LICENSE: Hotaru CMS is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version. 
 *
 * Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE. 
 *
 * You should have received a copy of the GNU General Public License along 
 * with Hotaru CMS. If not, see http://www.gnu.org/licenses/.
 * 
 * @category  Content Management System
 * @package   HotaruCMS
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */


/**
 * Displays "Hello World!" wherever the plugin hook is.
 */
function hw_hello_world()
{
    echo "Hello World!";
}

/* 

Four MUST-DO's when making a Hotaru plugin (if you want it to work!):

1. You must have a folder, e.g. "hello_world"
2. You must have a .php file with exactly the same name, e.g. "hello_world"
3. You must include comments at the top of the file, listing name, description, version, folder, prefix and hooks (comma separated). Use the same formatting as shown above, i.e. colon separators.
4. You must have a function with the same name as each hook with the prefix attached, e.g. function hw_hello_world();
 
Make your own hooks like this: 
In a template, add <?php $hotaru->check_actions('HOOK_NAME'); ?> to call your plugin.

*/
?>