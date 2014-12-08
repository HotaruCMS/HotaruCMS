<br/>
<div id="admin_theme_maintenance">
    <div>
	<?php if (SITE_OPEN == "true") { ?>
	<span>
            <button class="btn btn-warning" id="admin_theme_maintenance_openclose_site" name='close'>
		<?php echo $h->lang("admin_theme_maintenance_close_site"); ?>
            </button>&nbsp;&nbsp;<?php echo $h->lang("admin_theme_maintenance_close_site_desc"); ?>
        </span>
	<?php } else { ?>
	<span>
            <button class="btn btn-primary" id="admin_theme_maintenance_openclose_site" name='open'>
		<?php echo $h->lang("admin_theme_maintenance_open_site"); ?>
            </button>&nbsp;&nbsp;<?php //echo $h->lang("admin_theme_maintenance_open_site_desc"); ?>
        </span>
	<?php } ?>
</div>
	<br />
	<?php echo $h->lang("admin_theme_maintenance_announcement"); ?>
	
	<form role='form' name='maintenance_announcement' action='<?php echo SITEURL; ?>admin_index.php?action=announcement#tab_home' method='post'>    
	<div>
            <div class="form-group" style='width:80%;'>
		<textarea style='width:100%;' name='announcement_text' rows=3><?php echo $h->vars['admin_announcement']; ?></textarea>
                
                    <input type='checkbox' name='announcement_enabled' value='announcement_enabled' <?php echo $h->vars['admin_announcement_enabled']; ?>>
			<?php echo $h->lang("admin_theme_maintenance_announcement_enable"); ?>
                    <div class="pull-right">
                    <?php echo $h->lang("admin_theme_maintenance_announcement_tags"); ?>
                    </div>
            </div>
            <input class='btn btn-primary' type='submit' value='<?php echo $h->lang('main_form_submit'); ?>' />

	</div>	
	<input type='hidden' name='page' value='maintenance'>
	<input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
	</form>
</div>