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
<?php $lists = $hotaru->show_plugin_list(); ?>					// gets 3 lists of plugins
<?php echo $lists[0]; ?>							// 0 = Active plugins, 1 = Inactive, 2 = Not installed 
***************************** */

global $hotaru, $plugins; // don't remove
$plugin_widgets = $plugins->get_plugins(); // don't remove
$active_exists = false; $inactive_exists = false;
?>

<h2>Hotaru Admin Control Panel &raquo; Plugin Management</h2>

<div id="plugin_list">

<div id="ajax-loader"></div>

<table><tr>

<td class="widget-column">
<p class="plugins_column_header">Inactive plugins</p>
	<?php foreach($plugin_widgets as $plugin_widget) { ?>
		<?php if($plugin_widget['status'] == "inactive") { ?>
			<?php $inactive_exists = true; ?>
			<div class="widget-place" id="inactive">
				<div id="<?php echo $plugin_widget['folder']; ?>"  class="widget movable">
					<div class="widget-header">
						<?php echo $plugin_widget['name']; ?> 
					</div>
					<div class="widget-content">
						<?php echo $plugin_widget['description']; ?> <br />
						<?php echo "Version: " . $plugin_widget['version']; ?>
					</div>
				</div>
			</div>
		<?php } ?> 
	<?php } ?>
	
	<?php if($inactive_exists == false) { ?><div class="widget-place" id="inactive"></div> <?php } ?>
</td>

<td class="widget-column">
<p class="plugins_column_header">Active plugins</p>
	<?php foreach($plugin_widgets as $plugin_widget) { ?>
		<?php if($plugin_widget['status'] == "active") { ?>
			<?php $active_exists = true; ?>
			<div class="widget-place" id="active">
				<div id="<?php echo $plugin_widget['folder']; ?>"  class="widget movable">
					<div class="widget-header">
						<?php echo $plugin_widget['name']; ?> 
					</div>
					<div class="widget-content">
						<?php echo $plugin_widget['description']; ?> <br />
						<?php echo "Version: " . $plugin_widget['version']; ?>
					</div>
				</div>
			</div>
		<?php } ?> 
	<?php } ?>
	<?php if($active_exists == false) { ?><div class="widget-place" id="active"></div> <?php } ?>
</td>
</tr></table>
</div>