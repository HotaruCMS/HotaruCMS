<?php 
/**
 * Theme name: default
 * Template name: navigation.php
 * Template author: Nick Ramsay
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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

global $hotaru, $plugins, $current_user, $lang; // don't remove
?>

<!-- Navigation Bar -->
<ul id="navigation">
    <?php $plugins->checkActions('navigation_first'); ?>
    
    <?php if ($hotaru->getTitle() == 'top') { $status = "id='navigation_active'"; } else { $status = ""; } ?>
    <li><a <?php echo $status; ?> href="<?php echo BASEURL; ?>"><?php echo $lang["main_theme_navigation_home"]; ?></a></li>
    <?php $plugins->checkActions('navigation'); ?>
    <?php 
        if (!$plugins->pluginActive('users')) { 

            if ($current_user->loggedIn == true) { 
            
                if ($hotaru->getTitle() == 'admin') { $status = "id='navigation_active'"; } else { $status = ""; }
                echo "<li><a " . $status . " href='" . url(array(), 'admin') . "'>" . $lang["main_theme_navigation_admin"] . "</a></li>"; 
                
                if ($hotaru->getTitle() == 'logout') { $status = "id='navigation_active'"; } else { $status = ""; }
                echo "<li><a " . $status . " href='" . url(array('page'=>'admin_logout'), 'admin') . "'>" . $lang["main_theme_navigation_logout"] . "</a></li>";
            } else { 
                if ($hotaru->getTitle() == 'login') { $status = "id='navigation_active'"; } else { $status = ""; }
                echo "<li><a " . $status . " href='" . url(array(), 'admin') . "'>" . $lang["main_theme_navigation_login"] . "</a></li>"; 
            }
        } else {
            $plugins->checkActions('navigation_users', true, 'users'); // ensures login/logout/register are last.
        }
    ?>
    
    <?php     // RSS Link and icon if Submit plugin is active
        if ($plugins->getPluginStatus('submit') == 'active') { ?>
        <li>
        <a href="<?php echo url(array('page'=>'rss')); ?>">RSS 
            <img src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/rss_16.png">
        </a>
        </li>
    <?php } ?>
            
</ul>

<?php $plugins->checkActions('navigation_last'); ?>