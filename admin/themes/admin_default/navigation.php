<?php 

/* ******* DEFAULT TEMPLATE *********
Theme name: admin_default
Template name: navigation.php
Template author: Nick Ramsay
Version: 0.1
***************************** */

/* ******* USAGE ************

***************************** */

global $hotaru, $plugin; // don't remove
?>

<ul id="navigation">
	<?php if(!$plugin->plugin_active('users')) { ?><li><a href="<?php echo baseurl; ?>admin/admin_index.php">Admin</a></li><?php } ?>
	<li><a href="<?php echo baseurl; ?>index.php">Home</a></li>
	<?php $plugin->check_actions('navigation'); ?>
</ul>