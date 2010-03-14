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
$loaded_settings = $h->vars['admin_settings'];
?>

<?php $h->pluginHook('admin_settings_top'); ?>
    
    <h2><?php echo $h->lang["admin_theme_settings_title"]; ?></h2>
    
    <?php $h->showMessage(); ?>
    
    <form id='settings_form' name='settings_form' action='<?php echo BASEURL; ?>admin_index.php?page=settings' method='post'>
    
    <table id="settings">    
    <tr>
        <td><b><u><?php echo $h->lang["admin_theme_settings_setting"]; ?></u></b></td>
        <td><b><u><?php echo $h->lang["admin_theme_settings_value"]; ?></u></b></td>
        <td><b><u><?php echo $h->lang["admin_theme_settings_default"]; ?></u></b></td>
        <td><b><u><?php echo $h->lang["admin_theme_settings_notes"]; ?></u></b></td>
    </tr>
    
    <?php     // **********************************************************
    
        // Loop through the settings, displaying each one as a row...    
        foreach ($loaded_settings as $ls) { 
            
            // replace underscores with spaces and make the first character of the setting name uppercase.
            $name = ucfirst(preg_replace('/_/', ' ', $ls->settings_name));

            // get settings_names that need warning for '/' character being attached to text
            if ($ls->settings_name == 'THEME' || $ls->settings_name == 'ADMIN_THEME') { $css_class = ' class="warning_slash"'; } else {$css_class = ''; }

        ?>
            <tr>
            <td><?php echo $name; ?>: </td>
            <td>
                <?php
                if ( $ls->settings_value == 'true' || $ls->settings_value == 'false' ) {
                    echo '<input type="radio" name="' . $ls->settings_name .'" value="true" ';
                    if ($ls->settings_value == 'true') { echo ' checked'; }
                    echo ' >&nbsp;ON&nbsp;&nbsp;';
                    echo '<input type="radio" name="' . $ls->settings_name .'" value="false" ';
                    if ($ls->settings_value == 'false') { echo ' checked'; }
                    echo ' >&nbsp;OFF';
                }
                else {
                    if ($ls->settings_name == 'SMTP_PASSWORD') { $type = 'password'; } else { $type = 'text'; }
                    echo '<input type="' . $type . '" size=20 name="' . $ls->settings_name .'" value="' . $ls->settings_value . '" ' . $css_class . ' />';
                }
                ?>
            </td>
            <td>
                <?php 
                    if ($ls->settings_default == 'true') {
                        echo "ON"; 
                    } elseif($ls->settings_default == 'false') {
                        echo "OFF"; 
                    } else {
                        echo $ls->settings_default; 
                    }
                ?>
            </td>
            <td><i><?php echo $ls->settings_note; ?></i></td>
            </tr>
     
    <?php     } // End loop **********************************************************     ?>
    
    <br />
    <input type='hidden' name='settings_update' value='true' />
    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
    </table>
    <input id='settings_submit' type='submit' value='Save' />
    </form>
    
    
    
<?php $h->pluginHook('admin_settings_bottom'); ?>
