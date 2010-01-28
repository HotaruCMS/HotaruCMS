<?php 
/**
 * Plugin name: Widgets
 * Template name: plugins/widgets/widget_ordering.php
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

$widgets = $h->vars['widgets']->getArrayWidgets($h);    // gets and sorts plugins by "order"
$blocks = $h->vars['widgets']->getLastWidgetBlock($widgets);

for ($i=1; $i<=$blocks; $i++) {
?>
    <div id="table_list">
    
    <table>
    
    <tr class='table_a'><td colspan=6><?php echo $h->lang["widgets_ordering_title"] . " " . $i; ?> </td></tr>
    <tr class='table_headers'>
    <td><?php echo $h->lang["widgets_ordering_block_enabled"]; ?></td>
    <td><?php echo $h->lang["widgets_ordering_block_name"]; ?></td>
    <td><?php echo $h->lang["widgets_ordering_block_order"]; ?></td>
    </tr>
    
    <?php
        $alt = 0;
        if ($widgets) {
            foreach ($widgets as $widget => $details) {
                if ($details['block'] == $i) {
                    // For the enabled button...
                    if ($details['enabled']) { $img_src = 'active.png'; } else {$img_src = 'inactive.png';}
                    $enabled_output  = '<div id="widget_' . $widget . '" class="widget_onoff">' ;
                    $enabled_output .= '<img src="' . BASEURL . 'content/admin_themes/' . ADMIN_THEME . 'images/' . $img_src . '"></div>';
                    
                    // For the up and down arrows...
                    $order_output = '<div class="widget_order_container">';
                    $order_output .= "<a href='" . BASEURL;
                    $order_output .= "admin_index.php?page=plugin_settings&amp;plugin=widgets&amp;";
                    $order_output .= "action=orderup&amp;widget=". $widget . "&amp;args=". $details['args'] . "&amp;block=" . $details['block'] . "&amp;order=" . $details['order'] . "'>";
                    $order_output .= '<span id="widget_' . $widget . '" class="widget_order_up">' ;
                    $order_output .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/up.png'></span>";
                    $order_output .= "</a> \n<a href='" . BASEURL;
                    $order_output .= "admin_index.php?page=plugin_settings&amp;plugin=widgets&amp;";
                    $order_output .= "action=orderdown&amp;widget=". $widget . "&amp;args=". $details['args'] . "&amp;block=" . $details['block'] . "&amp;order=" . $details['order'] . "'>";
                    $order_output .= '<span id="widget_' . $widget . '" class="widget_order_down">' ;
                    $order_output .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/down.png'></span>";
                    $order_output .= "</a></div>\n";
                                
                    $alt++;
                    echo "<tr id='table_tr' class='table_row_" . $alt % 2 . "'>\n";
                        echo "<td class='plugins_active widgets_active'>" . $enabled_output . "</td>\n";
                        echo "<td class='table_text'>" . make_name($widget) . " </td>\n";
                        echo "<td class='plugins_order widgets_order'>" . $order_output . "</td>\n";
                    echo "</tr>\n";
                }
            }
        }
    ?>
    </table>
    <br />
    </div>
    
<?php } // End of for loop ?>

<div class="clear"></div>
<div id="plugin_management_notice" class="info_box gray_box" style="margin-top: 2.0em";>
    <p class="info_header"><?php echo $h->lang["widgets_ordering_guide"]; ?></p>
    &raquo; <?php echo $h->lang["widgets_ordering_guide_1"]; ?><br />
</div>