<?php $db_tables = $h->vars['admin_plugin_tables']; ?>

<h2><?php echo $h->lang("admin_theme_maintenance_optimize"); ?></h2>
<ul>
	<li><a href="<?php echo SITEURL; ?>admin_index.php?page=maintenance&amp;action=optimize#tab_database">
		<?php echo $h->lang("admin_theme_maintenance_optimize_database"); ?></a> - <?php echo $h->lang("admin_theme_maintenance_optimize_desc"); ?></li>
        <li><a href="<?php echo SITEURL; ?>admin_index.php?page=maintenance&amp;action=export#tab_database">
		<?php echo $h->lang("admin_theme_maintenance_export_database"); ?></a> - <?php echo $h->lang("admin_theme_maintenance_export_desc"); ?></li>
        <?php $h->pluginHook('admin_maintenance_database'); ?>
</ul>

<br />

<h2><?php echo $h->lang("admin_theme_maintenance_db_tables"); ?></h2>
<span style='color: red;'><?php echo $h->lang("admin_theme_maintenance_db_table_warning"); ?></span><br /><br />
<?php echo $h->lang("admin_theme_maintenance_empty_explanation"); ?><br /><br />
<ul>
<?php if($db_tables) { ?>
	<?php foreach ($db_tables as $table) { ?>
	<li><a href="<?php echo SITEURL; ?>admin_index.php?page=maintenance&amp;action=empty&amp;table=<?php echo $table; ?>#tab_database">
		<?php echo $h->lang("admin_theme_maintenance_empty") . " " . $table; ?> </a></li>
	<?php } ?>
<?php } else { ?>
	<i><?php echo $h->lang("admin_theme_maintenance_no_db_tables_to_empty"); ?></i>
<?php } ?>
</ul>