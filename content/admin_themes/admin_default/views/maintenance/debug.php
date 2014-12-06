<h2><?php //echo $h->lang("admin_theme_maintenance_debug"); ?></h2>
<a class="btn btn-danger" href="<?php echo SITEURL; ?>admin_index.php?page=maintenance&amp;action=delete_debugs#tab_debug">
		<?php echo $h->lang("admin_theme_maintenance_debug_delete"); ?></a>

	<a class="pull-right btn btn-primary" href="<?php echo SITEURL; ?>admin_index.php?page=maintenance&amp;action=system_report#tab_debug">
		<?php echo $h->lang("admin_theme_maintenance_system_report"); ?></a>
<br/>
<br/>
<?php if ($h->vars['debug_files']) {
			echo $h->lang("admin_theme_maintenance_debug_view") . "<br />";
			foreach ($h->vars['debug_files'] as $file) {
				echo "<a href='" . SITEURL . "admin_index.php?page=maintenance&amp;debug=" . $file . "#tab_debug'>" . $file . "</a>  ";
                                echo "<span class='label label-primary'>" . display_filesize(filesize(CACHE . 'debug_logs/' . $file)) . '</span>'; 
                                echo "<br />";
			}
		} else {
			echo $h->lang("admin_theme_maintenance_debug_no_files");
		}
?>