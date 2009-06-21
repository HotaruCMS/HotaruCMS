<?php
/**
 * name: Hello World
 * description: Displays "Hello World!"
 * version: 0.1
 * folder: hello_world
 * prefix: hw
 * hooks: hello_world, admin_sidebar_plugin_settings
 *
 * You can type notes here, e.g:
 * Usage: Add <?php $plugin->check_actions('hello_world'); ?> to your theme, wherever you want to show "Hello World".
 */

function hw_hello_world() {
	echo "Hello World!";
}

function hw_admin_sidebar_plugin_settings() {
	echo "<li><a href='admin_index.php?page=plugin_settings&plugin=hello_world'>Hello World</a></li>";
}

/* 

Four MUST-DO's when making a Hotaru plugin (if you want it to work!):

1. You must have a folder, e.g. "hello_world"
2. You must have a .php file with exactly the same name, e.g. "hello_world"
3. You must include comments at the top of the file, listing name, description, version, folder and hooks (comma separated). Use the same formatting as shown above, i.e. colon separators.
4. You must have a function with the same name as each hook, e.g. function hello_world();
 
In a template, add <?php $hotaru->check_actions('HOOK_NAME'); ?> to call your plugin.

You can add as many files and folders to your plugin as you like, just as long as it all starts with the above steps.

*/
?>