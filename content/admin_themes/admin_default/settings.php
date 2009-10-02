<?php 
/**
 * Theme name: admin_default
 * Template name: settings.php
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

<p class="breadcrumbs">
    <a href="<?php echo BASEURL; ?>"><?php echo SITE_NAME; ?></a> 
    &raquo; <a href="<?php echo url(array(), 'admin'); ?>"><?php echo $admin->lang["admin_theme_main_admin_cp"]; ?></a> 
    &raquo; <?php echo $admin->lang["admin_theme_settings"]; ?>
</p>

<?php $admin->plugins->pluginHook('admin_settings_top'); ?>
    
    <h2><?php echo $admin->lang["admin_theme_settings_title"]; ?></h2>
    
    <?php $loaded_settings = $admin->settings();    // Prepare or process the form ?>
    
    <form id='settings_form' name='settings_form' action='<?php echo BASEURL; ?>admin_index.php?page=settings' method='post'>
    
    <table id="settings">    
    <tr>
        <td><b><u><?php echo $admin->lang["admin_theme_settings_setting"]; ?></u></b></td>
        <td><b><u><?php echo $admin->lang["admin_theme_settings_value"]; ?></u></b></td>
        <td><b><u><?php echo $admin->lang["admin_theme_settings_default"]; ?></u></b></td>
        <td><b><u><?php echo $admin->lang["admin_theme_settings_notes"]; ?></u></b></td>
    </tr>
    
    <?php     // **********************************************************
    
        // Loop through the settings, displaying each one as a row...    
        foreach ($loaded_settings as $ls) { 
        
            // replace underscores with spaces and make the first character of the setting name uppercase.
            $name = ucfirst(preg_replace('/_/', ' ', $ls->settings_name));    
        ?>
            <tr>
            <td><?php echo $name; ?>: </td>
            <td><input type='text' size=20 name='<?php echo $ls->settings_name; ?>' value='<?php echo $ls->settings_value; ?>' /></td>
            <td><?php echo $ls->settings_default; ?></td>
            <td><i><?php echo $ls->settings_note; ?></i></td>
            </tr>
     
    <?php     } // End loop **********************************************************     ?>
    
    <br />
    <input type='hidden' name='settings_update' value='true' />
    </table>
    <input id='settings_submit' type='submit' value='Save' />
    </form>
    
    
    
<?php $admin->plugins->pluginHook('admin_settings_bottom'); ?>
