<?php
/**
 * Users Settings
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

$username = $hotaru->vars['username']; // username
$settings = $hotaru->vars['settings']; // saved settings data

// get updated fields. 
if ($hotaru->cage->post->getAlpha('updated_settings') == 'true') {

    // Emails from Admins:
    if ($hotaru->cage->post->getAlpha('admin_notify') == 'yes') { 
        $settings['admin_notify'] = "checked"; 
    } else { 
        $settings['admin_notify'] = "";
    }
    
    // Open posts in a new tab:
    if ($hotaru->cage->post->keyExists('new_tab') == 'yes') { 
        $settings['new_tab'] = "checked";
    } else { 
        $settings['new_tab'] = "";
    }
    
    // Add your own $profile['something'] stuff here. Use Inspekt: http://hotarucms.org/showpost.php?p=20&postcount=2
    
    $hotaru->vars['settings'] = $settings;
    $hotaru->plugins->pluginHook('user_settings_pre_save'); 
    $settings = $hotaru->vars['settings'];
        
    // this hook does the actual saving. It can onlbe used by the Users plugin
    $hotaru->plugins->pluginHook('user_settings_save', true, 'users', array($username, $settings)); 
} 

// set radio buttons:
if ($settings['admin_notify']) { $an_yes = "checked"; $an_no = ""; } else { $an_yes = ""; $an_no = "checked"; }
if ($settings['new_tab']) { $nt_yes = "checked"; $nt_no = ""; } else { $nt_yes = ""; $nt_no = "checked"; }

$hotaru->vars['settings'] = $settings;
$hotaru->plugins->pluginHook('user_settings_fill_form'); 

?>
    
    <div id='breadcrumbs'><a href='<?php echo BASEURL; ?>'><?php echo $hotaru->lang["users_home"]; ?></a> 
        &raquo; <a href='<?php echo $hotaru->url(array('user' => $username)); ?>'><?php echo $username; ?></a> 
        &raquo; <?php echo $hotaru->lang["users_settings"]; ?></div>
    
    <?php $hotaru->displayTemplate('user_tabs', 'users'); ?>
    
    <h2><?php echo $hotaru->lang["users_settings"]; ?>: <?php echo $username; ?></h2>
    
    <?php echo $hotaru->showMessage(); ?>

    <form name='user_settings_form' action='<?php echo $hotaru->url(array('page'=>'user-settings', 'user'=>$username)); ?>' method='post'>    
    <table>
        <tr>
            <!-- ACCEPT EMAIL FROM ADMINS? -->
            <td><?php echo $hotaru->lang['users_settings_email_from_admin']; ?></td>
            <td><input type="radio" name="admin_notify" value="yes" <?php echo $an_yes; ?>> <?php echo $hotaru->lang['users_settings_yes']; ?> &nbsp;&nbsp;
            <input type="radio" name="admin_notify" value="no" <?php echo $an_no; ?>> <?php echo $hotaru->lang['users_settings_no']; ?></td>
        </tr>

        <tr>
            <!-- OPEN POSTS IN A NEW TAB? -->
            <td><?php echo $hotaru->lang['users_settings_open_new_tab']; ?></td>
            <td><input type="radio" name="new_tab" value="yes" <?php echo $nt_yes; ?>> <?php echo $hotaru->lang['users_settings_yes']; ?> &nbsp;&nbsp;
            <input type="radio" name="new_tab" value="no" <?php echo $nt_no; ?>> <?php echo $hotaru->lang['users_settings_no']; ?></td>
        </tr>

        <?php $hotaru->plugins->pluginHook('user_settings_extra_settings'); ?>
        
    <tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' class='submit' value='<?php echo $hotaru->lang['users_settings_update']; ?>' /></td></tr>
    </table>
    <input type='hidden' name='updated_settings' value='true' />
    </form>
