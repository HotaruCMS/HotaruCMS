<?php 
/**
 * Theme name: hotaru-light
 * Template name: index.php
 * Template author: carlo75
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
$result = $hotaru->plugins->pluginHook('theme_index_replace');
if (!isset($result) || !is_array($result)) {
?>
        <!-- HEADER-->
        <?php
            // plugin hook
            $result = $hotaru->plugins->pluginHook('theme_index_header');
            if (!isset($result) || !is_array($result)) {
                $hotaru->displayTemplate('header');
            }
        ?>
        <div id="ads160x600"><img src="<?php echo BASEURL . 'content/themes/' . THEME . '/images/ads160x600.jpg'; ?>" /></div>
        <div id="bd">
            <div id="yui-main"> 
            <?php if ($hotaru->sidebar) { // determines whether to show the sidebar or not ?>
                <div class='yui-gc'> 
                    <div class="yui-u first"'>
            <?php } else { ?>
                <div class='yui-g''>
                        <div class="yui-u first" style='width: 100%;'>
                <?php } ?>
                            <!-- MAIN -->
                            <?php     
                                // plugin hook
                            $result = $hotaru->plugins->pluginHook('theme_index_main');
                            if (!isset($result) || !is_array($result)) {
                                $page = $hotaru->getPageName();
                                $hotaru->displayTemplate($page); 
                            }
                        ?>
                        </div> <!-- close "yui-u first" -->
                    <?php if ($hotaru->sidebar) { ?>
                        <div class="yui-u">
                            
                            <!-- SIDEBAR -->
                            <?php
                                // plugin hook
                                $result = $hotaru->plugins->pluginHook('theme_index_sidebar');
                                if (!isset($result) || !is_array($result)) {
                                    $hotaru->displayTemplate('sidebar');
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
            $result = $hotaru->plugins->pluginHook('theme_index_footer');
            if (!isset($result) || !is_array($result)) {
                $hotaru->displayTemplate('footer');
            }
        ?>
<?php    } ?>
