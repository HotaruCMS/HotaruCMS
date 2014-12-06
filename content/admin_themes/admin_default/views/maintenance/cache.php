

<h2><?php //echo $h->lang("admin_theme_maintenance_cache"); ?></h2>
<ul>
	<li style="margin-bottom: 1em;">
            <a href="<?php echo SITEURL; ?>admin_index.php?page=maintenance&amp;action=clear_all_cache#tab_cache" class="btn btn-danger">
		<?php echo $h->lang("admin_theme_maintenance_all_cache"); ?>
            </a>&nbsp;<?php echo $h->lang("admin_theme_maintenance_all_cache_desc"); ?>
        </li>
	<li>
            <a href="<?php echo SITEURL; ?>admin_index.php?page=maintenance&amp;action=clear_db_cache#tab_cache">
		<?php echo $h->lang("admin_theme_maintenance_db_cache"); ?>
            </a> - <?php echo $h->lang("admin_theme_maintenance_db_cache_desc"); ?>
            <?php echo GetDirectorySize(CACHE . 'db_cache'); ?>
        </li>
	<li>
            <a href="<?php echo SITEURL; ?>admin_index.php?page=maintenance&amp;action=clear_css_js_cache#tab_cache">
		<?php echo $h->lang("admin_theme_maintenance_css_js_cache"); ?>
            </a> - <?php echo $h->lang("admin_theme_maintenance_css_js_cache_desc"); ?>
            <?php echo GetDirectorySize(CACHE . 'css_js_cache'); ?>
        </li>
	<li>
            <a href="<?php echo SITEURL; ?>admin_index.php?page=maintenance&amp;action=clear_html_cache#tab_cache">
		<?php echo $h->lang("admin_theme_maintenance_html_cache"); ?>
            </a> - <?php echo $h->lang("admin_theme_maintenance_html_cache_desc"); ?>
            <?php echo GetDirectorySize(CACHE . 'html_cache'); ?>
        </li>
	<li>
            <a href="<?php echo SITEURL; ?>admin_index.php?page=maintenance&amp;action=clear_lang_cache#tab_cache">
		<?php echo $h->lang("admin_theme_maintenance_lang_cache"); ?>
            </a> - <?php echo $h->lang("admin_theme_maintenance_lang_cache_desc"); ?>
            <?php echo GetDirectorySize(CACHE . 'lang_cache'); ?>
        </li>
	<li>
            <a href="<?php echo SITEURL; ?>admin_index.php?page=maintenance&amp;action=clear_rss_cache#tab_cache">
		<?php echo $h->lang("admin_theme_maintenance_rss_cache"); ?>
            </a> - <?php echo $h->lang("admin_theme_maintenance_rss_cache_desc"); ?>
            <?php echo GetDirectorySize(CACHE . 'rss_cache'); ?>
        </li>
</ul>
