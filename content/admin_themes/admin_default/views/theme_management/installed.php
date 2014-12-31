<?php 
/**
 * Theme name: admin_default
 * Template name: installed.php
 * Template author: shibuya246
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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

$themes = $h->getFiles(THEMES, array('404error.php', 'pages'));
?>

<p> 
    <br/>
    You can download the latest versions of themes here if your server permissions line up nicely with the web.<br/>
    <strong>Note:</strong> Servers without SuExec enabled may not work. Future versions will additionally include FTP access to solve this.
</p>

<table class="table table-bordered">
    <tr class="info">
        <td>Plugin</td>
        <td>Version</td>
        <td>Activate</td>
        <td>Update</td>
    </tr>
    
    <?php 
    if ($themes) {
        foreach ($themes as $theme) {             
            
                //$href= SITEURL . "admin_index.php?page=plugin_management&action=update&plugin=" . strtolower($plugin['folder']) . "&resourceId=" . $plugin['resourceId'] . "&versionId=" . $plugin['resourceVersionId'] . "#tab_updates";
                ?>
                    <tr>
                        <td>
                            <?php
                        if ($theme == rtrim(THEME, '/')) { $active = ' <i><small>(current)</small></i>'; } else { $active = ''; } 
                                                echo "<a href='" . SITEURL . "admin_index.php?page=theme_settings&amp;theme=" . $theme . "'>" . make_name($theme, '-') . "</a>" . $active . "\n";
                        ?>
                        </td>
                        <td><?php //echo $plugin['version']; ?></td>
                        <td><a href="admin_index.php?page=theme_settings&theme=<?php echo $theme; ?>" class="btn btn-primary btn-xs">Settings</button></a></td>
                        <td><!--<a href="<?php //echo $href; ?>" class="btn btn-warning btn-xs">Update</button></a>--></td>
                    </tr>
                
        <?php } 
    }
    ?>
</table>
