<ul class="">
	<?php if (SITE_OPEN == "true") { ?>
	<li><a class="btn btn-warning" href="<?php echo SITEURL; ?>admin_index.php?page=maintenance&amp;action=close">
		<?php echo $h->lang("admin_theme_maintenance_close_site"); ?></a>&nbsp;&nbsp;- <?php echo $h->lang("admin_theme_maintenance_close_site_desc"); ?></li>
	<?php } else { ?>
	<li><a class="btn btn-primary" href="<?php echo SITEURL; ?>admin_index.php?page=maintenance&amp;action=open">
		<?php echo $h->lang("admin_theme_maintenance_open_site"); ?></a>&nbsp;&nbsp;- <?php echo $h->lang("admin_theme_maintenance_open_site_desc"); ?></li>
	<?php } ?>
	
	<br />
	<?php echo $h->lang("admin_theme_maintenance_announcement"); ?>
	
	<form name='maintenance_announcement' action='<?php echo SITEURL; ?>admin_index.php#tab_home' method='get'>    
	<div>
            <div style='width:80%;'>
		<textarea style='width:100%;' name='announcement_text' rows=3><?php echo $h->vars['admin_announcement']; ?></textarea>
                
                    <input type='checkbox' name='announcement_enabled' value='announcement_enabled' <?php echo $h->vars['admin_announcement_enabled']; ?>>
			<?php echo $h->lang("admin_theme_maintenance_announcement_enable"); ?>
                    <div class="pull-right">
                    <?php echo $h->lang("admin_theme_maintenance_announcement_tags"); ?>
                    </div>
            </div>
            <input class='btn' type='submit' value='<?php echo $h->lang('main_form_submit'); ?>' />

	</div>
	<input type='hidden' name='action' value='announcement'>
	<input type='hidden' name='page' value='maintenance'>
	<input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
	</form>
</ul>