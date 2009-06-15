<?php 

/* ******* ADMIN TEMPLATE *********
Theme name: default
Template name: plugins.php
Template author: Nick Ramsay
Version: 0.1
Last updated: June 15th 2009
***************************** */

/* ******* USAGE ************
<?php echo $hotaru->display_admin_template('TEMPLATE_NAME.php'); ?>		// e.g. header.php
<?php echo $hotaru->show_plugin_list(); ?>					// List of available plugins
***************************** */

global $hotaru; 	// don't remove
global $plugins;	// don't remove
?>

<?php 
	echo $plugins->show_plugins();
	
?>	