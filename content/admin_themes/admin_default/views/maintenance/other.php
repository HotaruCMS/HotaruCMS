<?php $plugin_settings = $h->vars['admin_plugin_settings']; ?>

<?php $h->pluginHook('admin_maintenance_middle'); ?>

<h2><?php echo $h->lang("admin_theme_maintenance_plugin_settings"); ?></h2>
<?php echo $h->lang("admin_theme_maintenance_plugin_settings_explanation"); ?><br /><br />
<ul>
<?php if ($plugin_settings) { ?>
	<?php foreach ($plugin_settings as $settings) { ?>
	<li><a href="<?php echo SITEURL; ?>admin_index.php?page=maintenance&amp;action=remove_settings&amp;settings=<?php echo $settings; ?>#tab_other">
		<?php echo $h->lang("admin_theme_maintenance_remove") . " " . make_name($settings) . " " . $h->lang("admin_theme_maintenance_settings"); ?> </a></li>
	<?php } ?>
<?php } else { ?>
	<i><?php echo $h->lang("admin_theme_maintenance_no_plugin_settings_to_delete"); ?></i>
<?php } ?>
</ul>