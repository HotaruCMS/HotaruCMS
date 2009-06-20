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
?>

<div id="main">
	<h2>Hotaru Admin Control Panel &raquo; Plugin Management</h2>
		
	<div id="plugin_list">
	
	<div id="ajax-loader"></div>
	
	<table><tr>
	
	<td class="widget-column">
	<p class="plugins_column_header">Inactive plugins</p>
		<?php foreach($plugin_widgets as $plugin_widget) { ?>
			<?php if($plugin_widget['status'] == "inactive") { ?>
				<div class="widget-place" id="inactive">
					<div id="<?php echo $plugin_widget['folder']; ?>"  class="widget movable">
						<div class="widget-header">
							<a href="#" class="widget-expand" style="float: right;">+</a>
							<?php echo $plugin_widget['name']; ?> 
						</div>
						<div class="widget-content" style="display:none">
							<?php echo $plugin_widget['description']; ?> <br />
							<?php echo "Version: " . $plugin_widget['version']; ?> <br />
							<div class="widget_uninstall"><a href="javascript://" onclick="hide_show_replace('<?php echo baseurl ?>', 'changetext', 'widget_uninstall_result-<?php echo $plugin_widget['folder'] ?>', '<?php echo baseurl ?>admin/admin_plugins.php', 'plugin_folder=<?php echo $plugin_widget['folder'] ?>');">Uninstall</a></div>
							<div id="widget_uninstall_result-<?php echo $plugin_widget['folder'] ?>"></div>
						</div>
					</div>
				</div>
			<?php } ?> 
		<?php } ?>
		<!-- EMPTY PLACEHOLDER FOR WIDGET TO BE MOVED INTO IF NO OTHERS EXIST -->		
		<div class="widget-place" id="inactive" style="min-height: 5.0em; height: 5.0em;">
				<div id="<?php echo $plugin_widget['folder']; ?>" >
					<div class="widget-header" style="display:none;"></div>
					<div class="widget-content" style="display:none"></div>
				</div>
		</div> 
	</td>
	
	<td class="widget-column">
	<p class="plugins_column_header">Active plugins</p>
		<?php foreach($plugin_widgets as $plugin_widget) { ?>
			<?php if($plugin_widget['status'] == "active") { ?>
				<div class="widget-place" id="active">
					<div id="<?php echo $plugin_widget['folder']; ?>"  class="widget movable">
						<div class="widget-header">
							<a href="#" class="widget-expand" style="float: right;">+</a>
							<?php echo $plugin_widget['name']; ?> 
						</div>
						<div class="widget-content" style="display:none">
							<?php echo $plugin_widget['description']; ?> <br />
							<?php echo "Version: " . $plugin_widget['version']; ?>
							<div class="widget_uninstall"><a href="javascript://" onclick="hide_show_replace('<?php echo baseurl ?>', 'changetext', 'widget_uninstall_result-<?php echo $plugin_widget['folder'] ?>', '<?php echo baseurl ?>admin/admin_plugins.php', 'plugin_folder=<?php echo $plugin_widget['folder'] ?>');">Uninstall</a></div>
							<div id="widget_uninstall_result-<?php echo $plugin_widget['folder'] ?>"></div>
						</div>
					</div>
				</div>
			<?php } ?> 
		<?php } ?>
		<!-- EMPTY PLACEHOLDER FOR WIDGET TO BE MOVED INTO IF NO OTHERS EXIST -->
		<div class="widget-place" id="active" style="min-height: 5.0em; height: 5.0em;">
				<div id="<?php echo $plugin_widget['folder']; ?>" >
					<div class="widget-header" style="display:none;"></div>
					<div class="widget-content" style="display:none"></div>
				</div>
		</div> 
	</td>
	</tr></table>
	</div>
	<div class="clear"></div>
	<div id="plugin_management_notice" class="info_box">
		<p class="info_header">Plugin Management Guide</p>
		&raquo; To enable or disable plugins, drag them below "Inactive plugins" or "Active plugins".<br />
		&raquo; Click the " + " icon to expand a plugin and view its details.<br />
		&raquo; Click "Uninstall" (when expanded) to delete a plugin from the database.<br />
		&raquo; A red asterisk shows by a plugin's title if a newer version is available in the plugins folder.<br />
		&raquo; Deactivate and uninstall a plugin before activating a newer version.<br />
		&raquo; After activating a plugin, refresh the page for its settings link to appear under "Plugin Settings".<br />
	</div>
</div>