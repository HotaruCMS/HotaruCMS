<?php 
/**
 * Theme name: Cut Here
 * Template name: index.php
 * Original Template author: Nick Ramsay
 * Original Design: Carlo Armanni
 * Original Author URI: www.tr3ndy.com
 * Template Author: Carlo Armanni
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
 * @author    Carlo Armanni <admin@tr3ndy.com>
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.tr3ndy.com/
 */

// plugin hook
$result = $h->pluginHook('theme_index_top');
if (!$result) {
?>
        <!-- HEADER-->
        <?php
            // plugin hook
            $result = $h->pluginHook('theme_index_header');
            if (!$result) {
                $h->displayTemplate('header');
            }
        ?>
        <div id="breaker"></div>
        <div id="bd">
            <div> 
            <?php if ($h->sidebars) { // determines whether to show the sidebar or not ?>
                <div> 
                    <div id="bdpost" class="grid_9">
            <?php } else { ?>
                <div>
                        <div class="yui-u first" style='width: 100%;'>
                <?php } ?>
                            <!-- BREADCRUMBS -->
                            <div id='breadcrumbs'>
                                <?php echo $h->breadcrumbs(); ?>
                            </div>
                            
                            <!-- USER TABS -->
                            <?php 
                                // plugin hook
                                $result = $h->pluginHook('theme_index_post_breadcrumbs');
                            ?>
                            
                            <!-- FILTER TABS -->
                            <?php 
                                // plugin hook
                                $result = $h->pluginHook('theme_index_pre_main');
                            ?>
                            
                            <!-- MAIN -->
                            <?php     
                                // plugin hook
                            $result = $h->pluginHook('theme_index_main');
                            if (!$result) {
                                $h->displayTemplate($h->pageName); 
                            }
                            ?>
                        </div> <!-- close "" -->
                    <?php if ($h->sidebars) { ?>
                        <div id="sdbar" class="grid_3">
                            
                            <!-- SIDEBAR -->
                            <?php
                                // plugin hook
                                $result = $h->pluginHook('theme_index_sidebar');
                                if (!$result) {
                                    $h->displayTemplate('sidebar');
                                }                                
                            ?>
                        </div> <!-- close "sdbar" -->
                    <?php } ?>
            </div> 
            </div> <!-- close "bdpost" -->
        </div> <!-- close "bd" -->
        <!-- FOOTER -->
        <?php
            // plugin hook
            $result = $h->pluginHook('theme_index_footer');
            if (!$result) {
                $h->displayTemplate('footer');
            }
        ?>
<?php    } ?>
