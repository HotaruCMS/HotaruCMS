<?php 
/**
 * Theme name: admin_default
 * Template name: main.php
 * Template author: shibuya246
 *
 * PHP version 5
 *
 * LICENSE: Hotaru CMS is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version. 
 *
 * Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE. 
 *
 * You should have received a copy of the GNU General Public License along 
 * with Hotaru CMS. If not, see http://www.gnu.org/licenses/.
 * 
 * @category  Content Management System
 * @package   HotaruCMS
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

?>

<div class="span9">
	 
<!-- TITLE FOR ADMIN NEWS -->
	<h2>
		<a href="http://feeds2.feedburner.com/hotarucms"><img src="<?php echo SITEURL; ?>content/admin_themes/<?php echo ADMIN_THEME; ?>images/rss_16.png" width="16" height="16" alt="rss" /></a>
		&nbsp;<?php echo $h->lang("admin_theme_main_latest"); ?>
	</h2>
	
	<h3><?php echo $h->lang("admin_theme_main_help"); ?></h3>
	
	<!-- Feed items, number to show content for, max characters for content -->
        <div id="adminNews" style="display:none;"></div>
        <div id="hotaruImg">&nbsp;</div>
	<?php //echo $h->adminNews(10, 3, 300); ?>
	
	<br/>
        <div class="">
            <h2><?php //echo $h->lang("admin_theme_main_join_us"); ?></h2>
         </div>
</div>

<div class="span3">

    <div class="well sidebar-nav">
	
	<ul id="site-stats" class="nav nav-list">
            <li class="nav-header"><?php echo SITE_NAME . " " . $h->lang("admin_theme_main_stats"); ?></li>
		<li>Hotaru CMS <?php echo $h->version; ?></li>   
                               
		<?php                
                        $hotaru_latest_version = $h->miscdata('hotaru_latest_version');                
			if (version_compare($hotaru_latest_version, $h->version) == 1) {
			    //echo "<li><a href='http://hotarucms.org/forumdisplay.php?23-Download-Hotaru-CMS'>" . $h->lang('admin_theme_version_update_to') .  $hotaru_latest_version . "</a></li>";
                            $h->showMessage('A newer version of Hotaru CMS is available, v.' . $hotaru_latest_version . '. <a href="#">upgrade now</a>', 'alert-info');                                                 
                        } else {
                            echo $h->lang("admin_theme_version_latest_version_installed");
                        }
		?>       

		<?php $h->pluginHook('admin_theme_main_stats_post_version'); ?>
		<?php $h->pluginHook('admin_theme_main_stats', 'users', array('users' => array('all', 'admin', 'supermod', 'moderator', 'member', 'undermod', 'pending', 'banned', 'killspammed'))); ?>
		<?php $h->pluginHook('admin_theme_main_stats', 'post_manager', array('posts' => array('all', 'approved', 'pending', 'buried', 'archived'))); ?>
		<?php $h->pluginHook('admin_theme_main_stats', 'comments', array('comments' => array('all', 'approved', 'pending', 'archived'))); ?>
	</ul>
    </div>
</div>

<script type='text/javascript'>
jQuery(window).load(function() {        
        
        var sendurl = "<?php echo SITEURL; ?>admin_index.php?page=admin_news";
        
        $.ajax(
            {
            type: 'get',
                    url: sendurl,
                    cache: false,
                    //data: formdata,
                    beforeSend: function () {
                                    //$('#adminNews').html('<img src="' + SITEURL + "content/admin_themes/" + ADMIN_THEME + 'images/ajax-loader.gif' + '"/>&nbsp;Loading latest news.<br/>');
                            },
                    error: 	function(XMLHttpRequest, textStatus, errorThrown) {
                                    $('#adminNews').html('ERROR');
                                    $('#adminNews').removeClass('power_on').addClass('warning_on');
                    },
                    success: function(data) { // success means it returned some form of json code to us. may be code with custom error msg                                                                               
                                    $('#adminNews').html(data).fadeIn("fast");
                                    $('#hotaruImg').fadeOut("slow");
                                     
                    },
                    dataType: "html"
    });
});
</script>