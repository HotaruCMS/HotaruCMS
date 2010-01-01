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
?>

<?php $h->displayTemplate('sidebar'); ?>

<?php $h->showMessages(); ?>

<div id="plugin_management">

<?php $h->pluginHook('plugins_top'); ?>

<table>
<tr class='table_a'><td colspan=3>
    <?php echo $h->lang["admin_theme_plugins_installed"]; ?>
    <span class='table_key'>
    &nbsp;&nbsp;
    <img src='<?php echo BASEURL; ?>content/admin_themes/<?php echo ADMIN_THEME; ?>images/active_16.png'>
    <?php echo $h->lang["admin_theme_plugins_active"]; ?>
    &nbsp;&nbsp;
    <img src='<?php echo BASEURL; ?>content/admin_themes/<?php echo ADMIN_THEME; ?>images/inactive_16.png'>
    <?php echo $h->lang["admin_theme_plugins_inactive"]; ?>
    &nbsp;&nbsp;
    <img src='<?php echo BASEURL; ?>content/admin_themes/<?php echo ADMIN_THEME; ?>images/up_12.png'>
    <?php echo $h->lang["admin_theme_plugins_order_up"]; ?>
    &nbsp;&nbsp;
    <img src='<?php echo BASEURL; ?>content/admin_themes/<?php echo ADMIN_THEME; ?>images/down_12.png'>
    <?php echo $h->lang["admin_theme_plugins_order_down"]; ?>
    &nbsp;&nbsp;
    <img src='<?php echo BASEURL; ?>content/admin_themes/<?php echo ADMIN_THEME; ?>images/info_16.png'>
    <?php echo $h->lang["admin_theme_plugins_details"]; ?>
    &nbsp;&nbsp;
    <img src='<?php echo BASEURL; ?>content/admin_themes/<?php echo ADMIN_THEME; ?>images/uninstall_16.png'>
    <?php echo $h->lang["admin_theme_plugins_uninstall"]; ?>
    </span>
</td></tr>

<?php
    $the_plugins = $h->vars['installed_plugins']; 
    $per_column = count($the_plugins)/3;
    for($i=0; $i<3; $i++) { 
?>

<td style='width: 33%; vertical-align: top;'>

<table>    

<?php
    $alt = 0;
    if (!$the_plugins) { $the_plugins = array(); }
    foreach ($the_plugins as $plug) {
        $alt++;
        echo "<tr class='table_tr table_row_" . $alt % 2 . "'>\n";
        echo "<td class='table_active'>" . $plug['active'] . "</td>\n";
        echo "<td class='table_installed_plugin'>";
        if ($plug['settings']) {
            echo "<a href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=" . $plug['folder'] . "' title='" . $h->lang["admin_theme_plugins_settings"] . "'>";
            echo $plug['name'] . " " . $plug['version'] . "</a></td>\n";
        } else {
            echo $plug['name'] . " " . $plug['version'] . "</td>\n";
        }
        echo "<td class='table_order'>" . $plug['order_output'] . "</td>\n";
        echo "<td class='table_uninstall'>\n";
        echo "<a class='table_drop_down' href='#'><img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/info_16.png'></a>\n";
        echo "&nbsp;" . $plug['install'] . "</td>\n";
        echo "</tr>\n";
        echo "<tr class='table_tr_details' style='display:none;'><td colspan=3 class='table_description'>\n";
        echo $plug['description'] . "<br />";
        $requires = "";
        foreach ($plug['requires'] as $key=>$value) {
            $requires .= $key . " " . $value . ", ";
        }
        if ($requires != "") { echo $h->lang["admin_theme_plugins_requires"] . " " . rstrtrim($requires, ", "); } else { echo $h->lang["admin_theme_plugins_no_plugins"]; }
        if (isset($plug['author'])) { echo "<br />" . $h->lang["admin_theme_plugins_author"] . ": \n"; }
        if (isset($plug['authorurl'])) { echo "<a href='" . $plug['authorurl'] . "' title='" . $plug['authorurl'] . "'>"; }
        if (isset($plug['author'])) { echo $plug['author']; }
        if (isset($plug['authorurl'])) { echo "</a>\n"; }
        if (file_exists(PLUGINS . $plug['folder'] . "/readme.txt")) {
            echo "<br />" . $h->lang["admin_theme_plugins_more_info"];
            echo ": <a href='" . BASEURL . "content/plugins/" . $plug['folder'] . "/readme.txt' title='" . $h->lang["admin_theme_plugins_readme"] . "'>";
            echo $h->lang["admin_theme_plugins_readmetxt"] . "</a>";
        }
        echo "</td>";
        echo "<td class='table_description_close'><a class='table_hide_details' href='#'>";
        echo $h->lang["admin_theme_plugins_close"] . "</a></td></tr>\n";
        array_shift($the_plugins);
        if ($alt >= $per_column) { break; }
    }
?>

</table> <!-- close table which contains one column of plugins -->

</td>   <!-- close cell which contains one of three columns of smaller tables -->
<?php } ?>

</tr></table> <!-- close table which contains three columns of smaller tables -->

<table>
<tr>
<td colspan=3><small>
    <a href="<?php echo BASEURL; ?>admin_index.php?page=plugin_management&amp;action=deactivate_all">
        <?php echo $h->lang["admin_theme_plugins_deactivate_all"]; ?></a>
         | 
    <a href="<?php echo BASEURL; ?>admin_index.php?page=plugin_management&amp;action=activate_all">
        <?php echo $h->lang["admin_theme_plugins_activate_all"]; ?></a>
</small></td>
<td colspan=2 style='text-align: right;'><small>
    <a href="<?php echo BASEURL; ?>admin_index.php?page=plugin_management&amp;action=uninstall_all">
        <?php echo $h->lang["admin_theme_plugins_uninstall_all"]; ?></a>
</small></td>
</tr>
</table>

<table><tr>
<tr><td colspan=3>&nbsp;</td></tr>
<tr class='table_b'><td colspan=3>
    <?php echo $h->lang["admin_theme_plugins_not_installed"]; ?>
    <span class='table_key'>
    &nbsp;&nbsp;
    <img src='<?php echo BASEURL; ?>content/admin_themes/<?php echo ADMIN_THEME; ?>images/info_16.png'>
    <?php echo $h->lang["admin_theme_plugins_details"]; ?>
    &nbsp;&nbsp;
    <img src='<?php echo BASEURL; ?>content/admin_themes/<?php echo ADMIN_THEME; ?>images/install_16.png'>
    <?php echo $h->lang["admin_theme_plugins_install"]; ?>
    </span>
</td></tr>


<?php
    $the_plugins = $h->vars['uninstalled_plugins']; // don't remove
    $per_column = count($the_plugins)/3;
    for($i=0; $i<3; $i++) {
?>

<td style='width: 33%; vertical-align: top;'>

<table>    

<?php
    $alt = 0;
    if (!$the_plugins) { $the_plugins = array(); }
    foreach ($the_plugins as $plug) {
        $alt++;
        echo "<tr id='table_tr' class='table_row_" . $alt % 2 . "'>\n";
        echo "<td class='table_uninstalled_plugin'>" . $plug['name'] . " " . $plug['version'] . "<br />\n";
        echo "<span class='table_requires'>";
        $requires = '';
        foreach ($plug['requires'] as $key=>$value) {
            $requires .= make_name($key) . " " . $value . ", ";
        }
        echo rtrim($requires, ', ') . "</span></td>\n";
        echo "<td class='table_install'>\n";
        echo "<a class='table_drop_down' href='#'><img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/info_16.png'></a>\n";
        echo "&nbsp;" . $plug['install'] . "</td>\n";
        echo "</tr>\n";
        echo "<tr class='table_tr_details' style='display:none;'><td class='table_description'>\n";
        echo $plug['description'];
        if (isset($plug['author'])) { echo "<br />" . $h->lang["admin_theme_plugins_author"] . ": \n"; }
        if (isset($plug['authorurl'])) { echo "<a href='" . $plug['authorurl'] . "' title='" . $plug['authorurl'] . "'>"; }
        if (isset($plug['author'])) { echo $plug['author']; }
        if (isset($plug['authorurl'])) { echo "</a>\n"; }
        if (file_exists(PLUGINS . $plug['folder'] . "/readme.txt")) {
            echo "<br />" . $h->lang["admin_theme_plugins_more_info"];
            echo ": <a href='" . BASEURL . "content/plugins/" . $plug['folder'] . "/readme.txt' title='" . $h->lang["admin_theme_plugins_readme"] . "'>";
            echo $h->lang["admin_theme_plugins_readmetxt"] . "</a>";
        }
        echo "</td>\n";
        echo "<td class='table_description_close'><a class='table_hide_details' href='#'>";
        echo $h->lang["admin_theme_plugins_close"] . "</a></td></tr>\n";
        array_shift($the_plugins);
        if ($alt >= $per_column) { break; }
    }

?>
</table> <!-- close table which contains one column of plugins -->

</td>   <!-- close cell which contains one of three columns of smaller tables -->
<?php } ?>

</tr></table> <!-- close table which contains three columns of smaller tables -->


</div>
<div class="clear"></div>
<div id="plugin_management_notice" class="info_box gray_box" style="margin-top: 2.0em";>
    <p class="info_header"><?php echo $h->lang["admin_theme_plugins_guide"]; ?></p>
    <?php $h->pluginHook('plugins_guide_top'); ?>
    &raquo; <?php echo $h->lang["admin_theme_plugins_guide1"]; ?><br />
    &raquo; <?php echo $h->lang["admin_theme_plugins_guide2"]; ?><br />
    &raquo; <?php echo $h->lang["admin_theme_plugins_guide3"]; ?><br />
    &raquo; <?php echo $h->lang["admin_theme_plugins_guide4"]; ?><br />
    <?php $h->pluginHook('plugins_guide_bottom'); ?>
</div>


<?php $h->pluginHook('plugins_bottom'); ?>
