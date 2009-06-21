<?php 

/* ******* ADMIN TEMPLATE *********
Theme name: default
Template name: plugin_settings.php
Template author: Nick Ramsay
Version: 0.1
***************************** */

/* ******* USAGE ************

***************************** */

global $hotaru, $plugin; // don't remove
?>

<div id="main">
	<h2>Hotaru Admin Control Panel &raquo; Plugin Settings &raquo; <?php echo $plugin->name; ?></h2>
	
	<?php if(!empty($plugin->message)) { echo "<div class='message " . $plugin->message_type . "'>" . $plugin->message . "</div>"; } ?>
	
	<div id="plugin_settings">
		<?php $plugin->check_actions('admin_plugin_settings', $plugin->folder); ?>
	</div>
</div>
