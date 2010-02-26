<?php 
/**
 * Theme name: default
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
        
        <div id="bd">
            <div id="yui-main"> 
            <?php if ($h->sidebars) { // determines whether to show the sidebar or not ?>
                <div class='yui-gc'> 
                    <div class="yui-u first">
            <?php } else { ?>
                <div class='yui-g'>
                        <div class="yui-u first" style='width: 100%;'>
                <?php } ?>
                            <!-- BREADCRUMBS -->
                            <div id='breadcrumbs'>
                                <?php echo $h->breadcrumbs(); ?>
                            </div>
                            
                            <!-- POST BREADCRUMBS -->
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
                        </div> <!-- close "yui-u first" -->
                    <?php if ($h->sidebars) { ?>
                        <div class="yui-u">
                            
                            <!-- SIDEBAR -->
                            <?php
                                // plugin hook
                                $result = $h->pluginHook('theme_index_sidebar');
                                if (!$result) {
                                    $h->displayTemplate('sidebar');
                                }                                
                            ?>
                        </div> <!-- close "yui-u" -->
                    <?php } ?>
            </div> <!-- close "yui-gc" or "yui-g" -->
            </div> <!-- close "yui-main" -->
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
