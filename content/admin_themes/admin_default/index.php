<?php 
/**
 * Theme name: admin_default
 * Template name: index.php
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
        
// plugin hook
$result = $admin->plugins->pluginHook('admin_theme_index_replace');
if (!isset($result) || !is_array($result)) {
?>
        <!-- HEADER-->
        <?php
            // plugin hook
            $result = $admin->plugins->pluginHook('admin_theme_index_header');
            if (!isset($result) || !is_array($result)) {
                $admin->displayAdminTemplate('header', $admin);
            }
        ?>
    
        <div id="bd" role="main">              
            <?php if ($admin->sidebar) { ?>
                <div class='yui-gf'> 
                <div class="yui-u"'>
            <?php } else { ?>
                <div class='yui-g''>
                    <div class="yui-u" style='width: 100%;'>
                <?php } ?>
                        <!-- MAIN -->
                        <div id="main">
                        <?php
                            // plugin hook
                        $result = $admin->plugins->pluginHook('admin_theme_index_main');
                        if (!isset($result) || !is_array($result)) {
                                $page = $admin->hotaru->getPageName();
                                if ($page == 'admin_login') {
                                    if ($admin->current_user->loggedIn) {
                                        $admin->displayAdminTemplate('main', $admin);
                                    } else {
                                        $admin->adminLoginForm();
                                    }
                                } else {
                                $admin->displayAdminTemplate($page, $admin);
                            } 
                        }     
                    ?>    
                    </div>        
                    </div>
                    <?php if ($admin->hotaru->sidebar) { ?>
                        <div class="yui-u first">
                        <!-- SIDEBAR -->
                        <?php
                            // plugin hook
                            $result = $admin->plugins->pluginHook('admin_theme_index_sidebar');
                            if (!isset($result) || !is_array($result)) {
                                $admin->displayAdminTemplate('sidebar', $admin);
                            }
                        ?>
                        </div>
                    <?php } ?>
            </div>
        </div>
        <!-- FOOTER -->
        <?php
            // plugin hook
            $result = $admin->plugins->pluginHook('admin_theme_index_footer');
            if (!isset($result) || !is_array($result)) {
                $admin->displayAdminTemplate('footer', $admin);
            }
        ?>
<?php    } ?>
