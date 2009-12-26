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

// get updated fields. 
if ($hotaru->cage->post->getAlpha('updated_settings') == 'true') {
    
    // plugin hook
    $hotaru->plugins->pluginHook('user_settings_pre_save'); 
        
    // this hook does the actual saving. It can only be used by the Users plugin
    $hotaru->plugins->pluginHook('user_settings_save', true, 'users', array($username, $hotaru->vars['settings'])); 
} 

// set radio buttons plugin hook
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

        <?php $hotaru->plugins->pluginHook('user_settings_extra_settings'); ?>
        
    <tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' class='submit' value='<?php echo $hotaru->lang['users_settings_update']; ?>' /></td></tr>
    </table>
    <input type='hidden' name='updated_settings' value='true' />
    <input type='hidden' name='token' value='<?php echo $hotaru->token; ?>' />
    </form>
