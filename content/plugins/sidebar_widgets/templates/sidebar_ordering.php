<?php 
/**
 * Plugin name: Sidebar
 * Template name: plugins/sidebar/sidebar_ordering.php
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

global $hotaru, $plugins, $lang, $sidebar; // don't remove
$widgets = $sidebar->getSidebarWidgets();    // gets and sorts plugins by "order"
$sidebars = $sidebar->getLastSidebar($widgets);

for ($i=1; $i<=$sidebars; $i++) {
?>

    <div id="table_list">
    
    <table>
    
    <tr class='table_a'><td colspan=6><?php echo $lang["sidebar_ordering_title"] . " " . $i; ?> </td></tr>
    <tr class='table_headers'>
    <td><?php echo $lang["sidebar_ordering_block_enabled"]; ?></td>
    <td><?php echo $lang["sidebar_ordering_block_name"]; ?></td>
    <td><?php echo $lang["sidebar_ordering_block_order"]; ?></td>
    </tr>
    
    <?php
        $alt = 0;
        foreach ($widgets as $widget => $details) {
        
            if ($details['sidebar'] == $i) {
                // For the enabled button...
                if ($details['enabled']) {
                    $enabled_output = "<a href='" . BASEURL;
                    $enabled_output .= "admin_index.php?page=plugin_settings&amp;plugin=sidebar_widgets&amp;";
                    $enabled_output .= "action=disable&amp;widget=". $widget . "'>";
                    $enabled_output .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/active.png'></a>";
                } else {
                    $enabled_output = "<a href='" . BASEURL;
                    $enabled_output .= "admin_index.php?page=plugin_settings&amp;plugin=sidebar_widgets&amp;";
                    $enabled_output .= "action=enable&amp;widget=". $widget . "'>";
                    $enabled_output .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/inactive.png'></a>";
                }
                
                // For the up and down arrows...
                $order_output = "<a href='" . BASEURL;
                $order_output .= "admin_index.php?page=plugin_settings&amp;plugin=sidebar_widgets&amp;";
                $order_output .= "action=orderup&amp;widget=". $widget . "&amp;args=". $details['args'] . "&amp;sidebar=" . $details['sidebar'] . "&amp;order=" . $details['order'] . "'>";
                $order_output .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/up.png'>";
                $order_output .= "</a> \n<a href='" . BASEURL;
                $order_output .= "admin_index.php?page=plugin_settings&amp;plugin=sidebar_widgets&amp;";
                $order_output .= "action=orderdown&amp;widget=". $widget . "&amp;args=". $details['args'] . "&amp;sidebar=" . $details['sidebar'] . "&amp;order=" . $details['order'] . "'>";
                $order_output .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/down.png'>";
                $order_output .= "</a>\n";
                            
                $alt++;
                echo "<tr id='table_tr' class='table_row_" . $alt % 2 . "'>\n";
                    echo "<td class='plugins_active sidebar_active'>" . $enabled_output . "</td>\n";
                    echo "<td class='table_text'>" . $hotaru->pageToTitleCaps($widget) . " </td>\n";
                    echo "<td class='plugins_order sidebar_order'>" . $order_output . "</td>\n";
                echo "</tr>\n";
            }
        }
    ?>
    </table>
    <br />
    </div>
    
<?php } // End of for loop ?>

<div class="clear"></div>
<div id="plugin_management_notice" class="info_box gray_box" style="margin-top: 2.0em";>
    <p class="info_header"><?php echo $lang["sidebar_ordering_guide"]; ?></p>
    <?php $plugins->pluginHook('sidebar_ordering_guide_top'); ?>
    &raquo; <?php echo $lang["sidebar_ordering_guide_1"]; ?><br />
    <?php $plugins->pluginHook('sidebar_ordering_guide_bottom'); ?>
</div>
