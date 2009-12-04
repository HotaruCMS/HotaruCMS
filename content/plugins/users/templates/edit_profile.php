<?php
/**
 * Users Edit Profile
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
$profile = $hotaru->vars['profile']; // saved profile data

// get updated fields. 
if ($hotaru->cage->post->getAlpha('edited_profile') == 'true') {
    $profile['bio'] = sanitize($hotaru->cage->post->getHtmLawed('bio'), 1);
    
    // Add your own $profile['something'] stuff here. Use Inspekt: http://hotarucms.org/showpost.php?p=20&postcount=2
    
    $hotaru->vars['profile'] = $profile;
    $hotaru->plugins->pluginHook('user_edit_profile_pre_save'); 
    $settings = $hotaru->vars['profile'];
        
    // this hook does the actual saving. It can onlbe used by the Users plugin
    $hotaru->plugins->pluginHook('users_edit_profile_save', true, 'users', array($username, $profile));
} 

if (!isset($profile['bio'])) { $profile['bio'] = $hotaru->lang['users_profile_default_bio']; }

$hotaru->vars['profile'] = $profile;
$hotaru->plugins->pluginHook('user_edit_profile_fill_form'); 

?>
    
    <div id='breadcrumbs'><a href='<?php echo BASEURL; ?>'><?php echo $hotaru->lang["users_home"]; ?></a> 
        &raquo; <a href='<?php echo $hotaru->url(array('user' => $username)); ?>'><?php echo $username; ?></a> 
        &raquo; <?php echo $hotaru->lang["users_profile_edit"]; ?></div>
    
    <?php $hotaru->displayTemplate('user_tabs', 'users'); ?>
    
    <h2><?php echo $hotaru->lang["users_profile_edit"]; ?>: <?php echo $username; ?></h2>
    
    <?php echo $hotaru->showMessage(); ?>

    <form name='edit_profile_form' class='users_form' action='<?php echo $hotaru->url(array('page'=>'edit-profile', 'user'=>$username)); ?>' method='post'>    
    <table>
    
    <tr><td><?php echo $hotaru->lang["users_profile_edit_bio"]; ?>&nbsp; </td>
        <td><textarea cols=60 rows=5 name='bio'><?php echo $profile['bio']; ?></textarea></td>
    </tr>
    
    <?php // Add your own profile fields here. Use tr and td tags. ?>
    
    <?php $hotaru->plugins->pluginHook('user_edit_profile_extras'); ?>
    
    <tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' class='submit' value='<?php echo $hotaru->lang['users_profile_edit_update']; ?>' /></td></tr>
    </table>
    <input type='hidden' name='edited_profile' value='true' />
    </form>