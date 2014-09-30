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
<div id="admin-sidebar-menu" class="sidebar-nav" role="navigation">
    
    <div id="admin-sidebar-nav">	
	<a class="btn btn-default" href="<?php echo $h->url(array(), 'admin'); ?>" title="<?php echo $h->lang("admin_theme_navigation_home"); ?>"><i class="fa fa-home"></i></a>
	<a class="btn btn-default" href="<?php echo SITEURL; ?>admin_index.php?page=settings" title="<?php echo $h->lang("admin_theme_settings"); ?>"><i class="fa fa-cog"></i></a>
	<a class="btn btn-default" href="<?php echo SITEURL; ?>admin_index.php?page=maintenance" title="<?php echo $h->lang("admin_theme_maintenance"); ?>"><i class="fa fa-wrench"></i></a>
	<a class="btn btn-default" href="<?php echo SITEURL; ?>admin_index.php?page=blocked" title="<?php echo $h->lang("admin_theme_blocked_list"); ?>"><i class="fa fa-flag"></i></a>
    </div>
    
    <h5 class="sidebar-title">Navigation</h5>
    <ul class='nav nav-pills nav-stacked'>	          
	
	<li role="presentation"><a href="<?php echo SITEURL; ?>admin_index.php?page=pages_management"><i class="menu-icon fa fa-file"></i><span class="menu-text"><?php echo $h->lang("admin_theme_pages"); ?></span></a></li>	
        <li role="presentation"><a href="<?php echo SITEURL; ?>admin_index.php?page=plugin_management"><i class="menu-icon fa fa-check"></i><span class="menu-text"><?php echo $h->lang("admin_theme_plugins"); ?></span></a></li>
	
        <?php 
        $pluginFunc = new PluginFunctions();
        $sb_links = $pluginFunc->getAllActivePluginNames($h);
        ?>        
        
	<!-- Plugins -->       
        
        <?php
        if ($h->isActive('user_manager')) {
            echo '<li role="presentation" class="nav-header" style="cursor:pointer;" data-toggle="collapse" data-target="#admin_users_list"><a href="#"><i class="menu-icon fa fa-user"></i><span class="menu-text">' . $h->lang("admin_theme_users") . '</span></a>';
                echo '<div id="admin_users_list" class="collapse out">';  
                    echo '<ul id="users_list">';
                    
                        $pluginResult = $h->pluginHook('admin_sidebar_users');
                        
                        $adminPages = new AdminPages();
                        echo $adminPages->sidebarPluginsList($h, $pluginResult);                        
                        
                    echo '</ul>';
                echo '</div>';
            echo '</li>';
            
        }
        ?>
        
        
        <?php
        if ($h->isActive('post_manager')) {
            echo '<li role="presentation" class="nav-header" style="cursor:pointer;" data-toggle="collapse" data-target="#admin_posts_list"><a href="#"><i class="menu-icon fa fa-edit"></i><span class="menu-text">' . $h->lang("admin_theme_posts") . '</span></a>';
                echo '<div id="admin_posts_list" class="collapse out">';  
                    echo '<ul id="posts_list">';
                    
                        $pluginResult = $h->pluginHook('admin_sidebar_posts');
                        
                        $adminPages = new AdminPages();
                        echo $adminPages->sidebarPluginsList($h, $pluginResult);                        
                        
                    echo '</ul>';
                echo '</div>';
            echo '</li>';
            
        }
        ?>
	
	<?php
        if ($h->isActive('category_manager')) {
            echo '<li role="presentation" class="nav-header" style="cursor:pointer;" data-toggle="collapse" data-target="#admin_categories_list"><a href="#"><i class="menu-icon fa fa-bars"></i><span class="menu-text">' . $h->lang("admin_theme_categories") . '</span></a>';
                echo '<div id="admin_categories_list" class="collapse out">';  
                    echo '<ul id="categories_list">';
                    
                        $pluginResult = $h->pluginHook('admin_sidebar_categories');
                        
                        $adminPages = new AdminPages();
                        echo $adminPages->sidebarPluginsList($h, $pluginResult);                        
                        
                    echo '</ul>';
                echo '</div>';
            echo '</li>';
            
        }
        ?>
	
	<?php
        if ($h->isActive('widgets')) {
            echo '<li role="presentation"><a href="' . SITEURL . 'admin_index.php?page=plugin_settings&plugin=widgets#tab_settings"><i class="menu-icon fa fa-square-o"></i><span class="menu-text">' . $h->lang("admin_theme_widgets") . '</span></a></li>';            
        }
        ?>
        
        <?php $h->pluginHook('admin_sidebar_stats'); ?>
        
        <?php $pluginsCount = ($sb_links) ? count($sb_links) : 0; ?>

        <li role="presentation" class="nav-header" style="cursor:pointer;" data-toggle="collapse" data-target="#admin_plugins_list">
            <a href="#">
                <?php echo $h->lang("admin_theme_plugin_settings"); ?>
                <span class="label label-success pull-right"><?php echo $pluginsCount; ?></span>
            </a>
            

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
	
        
            
        <!-- Themes -->	
        <?php $themes = $h->getFiles(THEMES, array('404error.php', 'pages')); ?>
        <?php $themesCount = ($themes) ? count($themes) : 0; ?>
        <li role="presentation" class="nav-header" style="cursor:pointer;" data-toggle="collapse" data-target="#themes_list"><a href="#"><?php echo $h->lang("admin_theme_theme_settings"); ?>
            &nbsp;&nbsp;<span class="badge badge-info pull-right"><?php echo $themesCount; ?></span>
            </a>
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
