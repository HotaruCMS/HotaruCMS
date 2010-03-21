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

// merge custom admin_language.php if exists in admin theme's languages folder
// can be overridden by an admin_languages.php in a user theme's languages folder
$h->includeThemeLanguage('admin');

// plugin hook
$result = $h->pluginHook('admin_theme_index_top');
if (!$result) {
?>
        <!-- HEADER-->
        <?php
            // plugin hook
            $result = $h->pluginHook('admin_theme_index_header');
            if (!$result) {
                $h->displayTemplate('header');
            }
        ?>
    
        <div id="bd" role="main">              
            <?php if ($h->sidebars) { ?>
                <div class='yui-gf'> 
                <div class="yui-u">
            <?php } else { ?>
                <div class='yui-g'>
                    <div class="yui-u" style='width: 100%;'>
            <?php } ?>
                        <!-- BREADCRUMBS -->
                        <div id='breadcrumbs'>
                            <?php echo $h->breadcrumbs(); ?>
                        </div>
                            
                        <!-- MAIN -->
                        <div id="main">
                        <?php
                            // plugin hook
                        $result = $h->pluginHook('admin_theme_index_main');
                        if (!$result) {
                                if ($h->pageName == 'admin_login') {
                                    if ($h->currentUser->loggedIn) {
                                        $h->displayTemplate('admin_home');
                                    } else {
                                        $h->adminLoginForm();
                                    }
                                } else {
                                    $h->displayTemplate($h->pageName);
                                } 
                        }
                    ?>    
                    </div>        
                    </div>
                    <?php if ($h->sidebars) { ?>
                        <div class="yui-u first">
                        <!-- SIDEBAR -->
                        <?php
                            // plugin hook
                            $result = $h->pluginHook('admin_theme_index_sidebar');
                            if (!$result) {
                                $h->displayTemplate('sidebar');
                            }
                        ?>
                        </div>
                    <?php } ?>
            </div>
        </div>
        <!-- FOOTER -->
        <?php
            // plugin hook
            $result = $h->pluginHook('admin_theme_index_footer');
            if (!$result) {
                $h->displayTemplate('footer');
            }
        ?>
<?php    } ?>
