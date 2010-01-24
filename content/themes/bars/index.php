<?php
/**
 * name: Bars
 * description: Flexible two or three column theme
 * version: 0.1
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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

// get settings:
$bars = $h->getThemeSettings();

// get language
$h->includeThemeLanguage();



// set widths and widget block numbers
if ($bars['left'] && $bars['right']) { 
    $bar_width = 6; $content_width = 11;            // BOTH BARS: total 24 (6x2 + 12)
    $left = 1; $right = 2;
} elseif (!$bars['left'] && !$bars['right']) {
    $h->sidebars = false;
    $content_width = 24;      // NO BARS: total 24 
    $left = 0; $right = 0;
} else {
    $bar_width = 8; $content_width = 15;            // ONE BAR: total 24 (8 + 16)
    $left = 1; $right = 1;
}

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
    <div class="container">
        <div id="main" class="span-24 last">
            <?php if (!$h->sidebars) { ?>
                    <div id="content" class="span-24 last">
            <?php } elseif ($h->sidebars && $bars['left']) { ?>
                    <div id="left_sidebar" class="span-<?php echo $bar_width; ?> last">
                            <!-- SIDEBAR -->
                            <?php
                                // plugin hook
                                $result = $h->pluginHook('theme_index_sidebar');
                                if (!$result) { ?>
                                    <div id="sidebar">
                                        <?php $h->pluginHook('widget_block', '', array($left)); ?>
                                    </div>
                            <?php } ?>
                    </div>
                    <div id="content" class="span-<?php echo $content_width; ?> append-1">
            <?php } else {?>
                    <div id="content" class="span-<?php echo $content_width; ?> append-1">
            <?php } ?>

                            <!-- BREADCRUMBS -->
                            <div id='breadcrumbs' class="span-<?php echo $content_width; ?> last">
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
                    </div> <!-- close "content" -->
            <?php if ($h->sidebars && $bars['right']) { ?>
                    <div id="right_sidebar" class="span-<?php echo $bar_width; ?> last">
                            <!-- SIDEBAR -->
                            <?php
                                // plugin hook
                                $result = $h->pluginHook('theme_index_sidebar_2');
                                if (!$result) { ?>
                                    <div id="sidebar">
                                        <?php $h->pluginHook('widget_block', '', array($right)); ?>
                                    </div>
                            <?php } ?>
                    </div>
            <?php } ?>
        </div> <!-- close "main" -->
    </div> <!-- close "container" -->
        <!-- FOOTER -->
        <?php
            // plugin hook
            $result = $h->pluginHook('theme_index_footer');
            if (!$result) {
                $h->displayTemplate('footer');
            }
        ?>
<?php    } ?>
