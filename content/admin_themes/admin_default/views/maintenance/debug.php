<h2><?php //echo $h->lang("admin_theme_maintenance_debug"); ?></h2>
<ul>
	<li><a href="<?php echo SITEURL; ?>admin_index.php?page=maintenance&amp;action=delete_debugs#tab_debug">
		<?php echo $h->lang("admin_theme_maintenance_debug_delete"); ?></a></li>
	<li style="margin-bottom: 1em;"><a href="<?php echo SITEURL; ?>admin_index.php?page=maintenance&amp;action=system_report#tab_debug">
		<?php echo $h->lang("admin_theme_maintenance_system_report"); ?></a></li>	
</ul>

<?php if ($h->vars['debug_files']) {
			echo $h->lang("admin_theme_maintenance_debug_view") . "<br />";
			foreach ($h->vars['debug_files'] as $file) {
				echo "<a href='" . SITEURL . "admin_index.php?page=maintenance&amp;debug=" . $file . "#tab_debug'>" . $file . "</a><br />";
			}
		} else {
			echo $h->lang("admin_theme_maintenance_debug_no_files");
		}
?>