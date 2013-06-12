<?php 
/**
 * Theme name: admin_default
 * Template name: sidebar.php
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


<div class="well sidebar-nav">
<ul class='nav nav-list'>
	<li>
            <span>
            <?php
	     if($h->isActive('avatar')) {
                    $h->setAvatar($h->currentUser->id, 24, 'g', 'img-polaroid left');
                    echo  $h->linkAvatar();
            }
            ?>
               &nbsp;<a style="vertical-align:bottom;" href="<?php echo SITEURL; ?>admin_index.php?page=admin_account"><?php echo $h->currentUser->name; ?></a>
 
                </span>
            </li>
            <hr style="margin:10px 0;"/>

	
        <li><a href="<?php echo $h->url(array(), 'admin'); ?>"><i class="icon-home"></i> <?php echo $h->lang("admin_theme_navigation_home"); ?></a></li>
	
	<li><a href="<?php echo SITEURL; ?>admin_index.php?page=settings"><i class="icon-wrench"></i> <?php echo $h->lang("admin_theme_settings"); ?></a></li>
	<li><a href="<?php echo SITEURL; ?>admin_index.php?page=maintenance"><i class="icon-pencil"></i> <?php echo $h->lang("admin_theme_maintenance"); ?></a></li>
	<li><a href="<?php echo SITEURL; ?>admin_index.php?page=blocked"><i class="icon-flag"></i> <?php echo $h->lang("admin_theme_blocked_list"); ?></a></li>
	<li><a href="<?php echo SITEURL; ?>admin_index.php?page=pages_management"><i class="icon-file"></i> <?php echo $h->lang("admin_theme_pages"); ?></a></li>	
        <li><a href="<?php echo SITEURL; ?>admin_index.php?page=plugin_management"><i class="icon-check"></i> <?php echo $h->lang("admin_theme_plugins"); ?></a></li>
	
        <?php 
        $pluginFunc = new PluginFunctions();
        $sb_links = $pluginFunc->getAllActivePluginNames($h);
        ?>        
        
        <hr style="margin:10px 0;"/>	         
        
	<!-- Plugins -->       
        
        <?php
        if ($h->isActive('user_manager')) {
            echo '<li class="nav-header" style="cursor:pointer;" data-toggle="collapse" data-target="#admin_users_list">' . $h->lang("admin_theme_users");
                echo '<div id="admin_users_list" class="collapse out">';  
                    echo '<ul id="users_list">';
                    
                        $pluginResult = $h->pluginHook('admin_sidebar_users');
                        
                        $adminPages = new AdminPages();
                        echo $adminPages->sidebarPluginsList($h, $pluginResult);                        
                        
                    echo '</ul>';
                echo '</div>';
            echo '</li>';
            echo '<hr style="margin:10px 0;"/>';
        }
        ?>
        
        
        <?php
        if ($h->isActive('post_manager')) {
            echo '<li class="nav-header" style="cursor:pointer;" data-toggle="collapse" data-target="#admin_posts_list">' . $h->lang("admin_theme_posts");
                echo '<div id="admin_posts_list" class="collapse out">';  
                    echo '<ul id="posts_list">';
                    
                        $pluginResult = $h->pluginHook('admin_sidebar_posts');
                        
                        $adminPages = new AdminPages();
                        echo $adminPages->sidebarPluginsList($h, $pluginResult);                        
                        
                    echo '</ul>';
                echo '</div>';
            echo '</li>';
            echo '<hr style="margin:10px 0;"/>';
        }
        ?>
        
        <?php $h->pluginHook('admin_sidebar_stats'); ?>
        
        <?php $pluginsCount = ($sb_links) ? count($sb_links) : 0; ?>

        <li class="nav-header" style="cursor:pointer;" data-toggle="collapse" data-target="#admin_plugins_list"><?php echo $h->lang("admin_theme_plugin_settings"); ?>
            &nbsp;&nbsp;<span class="badge badge-info"><?php echo $pluginsCount; ?></span>

            <div id="admin_plugins_list" class="collapse out">    
                <ul id="plugin_settings_list">
                        <?php                                     
                                if ($sb_links) {
                                        //$sb_links = sksort($sb_links, $subkey="name", $type="char", true);
                                        foreach ($sb_links as $plugin) { 
                                                echo "<li><a href='" . SITEURL . "admin_index.php?page=plugin_settings&amp;plugin=" . $plugin->plugin_folder . "#tab_settings'>" . $plugin->plugin_name . "</a></li>\n";
                                        }
                                }
                        ?>
                </ul>
            </div> 
        </li>
	
        <hr style="margin:10px 0;"/>
            
        <!-- Themes -->	
        <?php $themes = $h->getFiles(THEMES, array('404error.php', 'pages')); ?>
        <?php $themesCount = ($themes) ? count($themes) : 0; ?>
        <li class="nav-header" style="cursor:pointer;" data-toggle="collapse" data-target="#themes_list"><?php echo $h->lang("admin_theme_theme_settings"); ?>
            &nbsp;&nbsp;<span class="badge badge-info"><?php echo $themesCount; ?></span>
            <div id="themes_list" class="collapse out">
                <ul id="plugin_settings_list">
                <?php 

                        if ($themes) {
                                sort($themes); // sort alphabetically
                                foreach ($themes as $theme) { 
                                        if ($theme == rtrim(THEME, '/')) { $active = ' <i><small>(current)</small></i>'; } else { $active = ''; } 
                                                echo "<li><a href='" . SITEURL . "admin_index.php?page=theme_settings&amp;theme=" . $theme . "'>" . make_name($theme, '-') . "</a>" . $active . "</li>\n";
                                }
                        }
                ?>
                </ul>
            </div>
        </li>	
	
	<?php $h->pluginHook('admin_sidebar'); ?>
</ul>
</div>
