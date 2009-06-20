<?php 

/* ******* ADMIN TEMPLATE *********
Theme name: admin_default
Template name: sidebar.php
Template author: Nick Ramsay
Version: 0.1
***************************** */

/* ******* USAGE ************

***************************** */

global $hotaru, $plugin; // don't remove
?>
<ul id="sidebar">
	<li><a href="<?php echo baseurl; ?>admin/admin_index.php">Admin Home</a></li>
	<li><a href="<?php echo baseurl; ?>admin/admin_index.php?page=plugins">Plugin Management</a></li>
	<li><a class="dropdown" href="#">Plugin Settings</a></li>
	<ul id="plugin_settings_list" style="display: none;">
		<?php $plugin->check_actions('admin_sidebar_plugin_settings'); ?>
	</ul>
</ul>