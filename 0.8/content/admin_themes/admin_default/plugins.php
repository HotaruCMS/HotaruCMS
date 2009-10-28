<?php 
/**
 * Theme name: admin_default
 * Template name: plugins.php
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

$the_plugins = $admin->plugins->getPlugins(); // don't remove
if($the_plugins) { $the_plugins = sksort($the_plugins, "order", "int", true); }    // sorts plugins by "order"
?>

<p class="breadcrumbs">
    <a href="<?php echo BASEURL; ?>"><?php echo SITE_NAME; ?></a> 
    &raquo; <a href="<?php echo $admin->hotaru->url(array(), 'admin'); ?>"><?php echo $admin->lang["admin_theme_main_admin_cp"]; ?></a> 
    &raquo; <?php echo $admin->lang["admin_theme_plugins"]; ?>
</p>
    
<?php $admin->hotaru->showMessages(); ?>

<div id="table_list">

<?php $admin->plugins->pluginHook('plugins_top'); ?>

<table>

<tr class='table_a'><td colspan=6><?php echo $admin->lang["admin_theme_plugins_installed"]; ?></td></tr>
<tr class='table_headers'>
<td><?php echo $admin->lang["admin_theme_plugins_on_off"]; ?></td>
<td><?php echo $admin->lang["admin_theme_plugins_plugin"]; ?></td>
<td><?php echo $admin->lang["admin_theme_plugins_order"]; ?></td>
<td><?php echo $admin->lang["admin_theme_plugins_details"]; ?></td>
<td><?php echo $admin->lang["admin_theme_plugins_uninstall"]; ?></td>
</tr>

<?php
    $alt = 0;
    foreach ($the_plugins as $plug) {
        $alt++;
        if ($plug['location'] == 'database') {        
            echo "<tr class='table_tr table_row_" . $alt % 2 . "'>\n";
            echo "<td class='table_active'>" . $plug['active'] . "</td>\n";
            echo "<td class='table_text'>" . $plug['name'] . " " . $plug['version'] . "</td>\n";
            echo "<td class='table_order'>" . $plug['order_output'] . "</td>\n";
            echo "<td class='table_details'><a class='table_drop_down' href='#'><img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/info.png'></a></td>\n";
            echo "<td class='table_install'>" . $plug['install'] . "</td>\n";
            echo "</tr>\n";
            echo "<tr class='table_tr_details' style='display:none;'><td colspan=4 class='table_description'>";
            echo "<b>" . $admin->lang["admin_theme_plugins_description"] . ":</b> " . $plug['description'] . "<br />";
            echo "<b>" . $admin->lang["admin_theme_plugins_requires"] . ":</b> ";
            $requires = "";
            foreach ($plug['requires'] as $key=>$value) {
                $requires .= $key . " " . $value . ", ";
            }
            if ($requires != "") { echo rstrtrim($requires, ", "); } else { echo $admin->lang["admin_theme_plugins_no_plugins"]; }
            echo "</td>";
            echo "<td class='table_description_close'><a class='table_hide_details' href='#'>";
            echo $admin->lang["admin_theme_plugins_close"] . "</a></td></tr>\n";
        }
    }
?>
</table>

<table>    
<tr><td colspan=5>&nbsp;</td></tr>
<tr class='table_b'><td colspan=6><?php echo $admin->lang["admin_theme_plugins_not_installed"]; ?></td></tr>
<tr class='table_headers'>
<td><?php echo $admin->lang["admin_theme_plugins_off"]; ?></td>
<td colspan=2><?php echo $admin->lang["admin_theme_plugins_plugin"]; ?></td>
<td><?php echo $admin->lang["admin_theme_plugins_requires"]; ?></td>
<td><?php echo $admin->lang["admin_theme_plugins_details"]; ?></td>
<td><?php echo $admin->lang["admin_theme_plugins_install"]; ?></td>
</tr>

<?php
    $alt = 0;
    foreach ($the_plugins as $plug) {
        $alt++;
        if ($plug['location'] == 'folder') {        
            echo "<tr id='table_tr' class='table_row_" . $alt % 2 . "'>\n";
            echo "<td class='table_active'>" . $plug['active'] . "</td>\n";
            echo "<td class='table_text' colspan=2>" . $plug['name'] . " " . $plug['version'] . "</td>\n";
            echo "<td class='table_requires'>";
            foreach ($plug['requires'] as $key=>$value) {
                echo $key . " " . $value . "<br />";
            }
            echo "</td>\n";
            echo "<td class='table_details'><a class='table_drop_down' href='#'><img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/info.png'></a></td>\n";
            echo "<td class='table_install'>" . $plug['install'] . "</td>\n";
            echo "</tr>\n";
            echo "<tr id='tr_details' style='display:none;'><td colspan=5 class='table_description'>";
            echo "<b>" . $admin->lang["admin_theme_plugins_description"] . ":</b> " . $plug['description'] . "</td>";
            echo "<td class='table_description_close'><a class='table_hide_details' href='#'>";
            echo $admin->lang["admin_theme_plugins_close"] . "</a></td></tr>\n";
        }
    }

?>
</table>
</div>
<div class="clear"></div>
<div id="plugin_management_notice" class="info_box gray_box" style="margin-top: 2.0em";>
    <p class="info_header"><?php echo $admin->lang["admin_theme_plugins_guide"]; ?></p>
    <?php $admin->plugins->pluginHook('plugins_guide_top'); ?>
    &raquo; <?php echo $admin->lang["admin_theme_plugins_guide1"]; ?><br />
    &raquo; <?php echo $admin->lang["admin_theme_plugins_guide2"]; ?><br />
    &raquo; <?php echo $admin->lang["admin_theme_plugins_guide3"]; ?><br />
    <?php $admin->plugins->pluginHook('plugins_guide_bottom'); ?>
</div>


<?php $admin->plugins->pluginHook('plugins_bottom'); ?>
