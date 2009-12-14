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
$result = $hotaru->pluginHook('admin_theme_index_top');
if (!$result) {
?>
        <!-- HEADER-->
        <?php
            // plugin hook
            $result = $hotaru->pluginHook('admin_theme_index_header');
            if (!$result) {
                $hotaru->displayTemplate('header');
            }
        ?>
    
        <div id="bd" role="main">              
            <?php if ($hotaru->sidebars) { ?>
                <div class='yui-gf'> 
                <div class="yui-u">
            <?php } else { ?>
                <div class='yui-g'>
                    <div class="yui-u" style='width: 100%;'>
            <?php } ?>
                        <!-- BREADCRUMBS -->
                        <div id='breadcrumbs'>
                            <?php echo $hotaru->breadcrumbs(); ?>
                        </div>
                            
                        <!-- MAIN -->
                        <div id="main">
                        <?php
                            // plugin hook
                        $result = $hotaru->pluginHook('admin_theme_index_main');
                        if (!$result) {
                                $page = $hotaru->getPageName();
                                if ($page == 'admin_login') {
                                    if ($hotaru->currentUser->loggedIn) {
                                        $hotaru->displayTemplate('admin_home');
                                    } else {
                                        $hotaru->adminLoginForm();
                                    }
                                } else {
                                    $hotaru->displayTemplate($page);
                                } 
                        }
                    ?>    
                    </div>        
                    </div>
                    <?php if ($hotaru->sidebars) { ?>
                        <div class="yui-u first">
                        <!-- SIDEBAR -->
                        <?php
                            // plugin hook
                            $result = $hotaru->pluginHook('admin_theme_index_sidebar');
                            if (!$result) {
                                $hotaru->displayTemplate('sidebar');
                            }
                        ?>
                        </div>
                    <?php } ?>
            </div>
        </div>
        <!-- FOOTER -->
        <?php
            // plugin hook
            $result = $hotaru->pluginHook('admin_theme_index_footer');
            if (!$result) {
                $hotaru->displayTemplate('footer');
            }
        ?>
<?php    } ?>