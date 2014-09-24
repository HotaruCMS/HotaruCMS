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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

$profile = $h->vars['profile']; // saved profile data

// get updated fields. 
if ($h->cage->post->getAlpha('edited_profile') == 'true') {
    $profile['bio'] = sanitize($h->cage->post->getHtmLawed('bio'), 'all');
    
    // Add your own $profile['something'] stuff here. Use Inspekt: http://hotarucms.org/showpost.php?p=20&postcount=2
    
    $h->vars['profile'] = $profile;
    $h->pluginHook('user_edit_profile_pre_save'); 
    $settings = $h->vars['profile'];
        
    // this hook does the actual saving. It can only be used by the Users plugin
    $h->pluginHook('users_edit_profile_save', 'users', array($h->vars['user']->name, $profile));
} 

if (!isset($profile['bio'])) { $profile['bio'] = $h->lang['users_profile_default_bio']; }

$h->vars['profile'] = $profile;
$h->pluginHook('user_edit_profile_fill_form'); 

?>
<div id="users_edit_profile" class="users_content">

    <h2><?php echo $h->lang["users_profile_edit"]; ?>: <?php echo $h->vars['user']->name; ?></h2>
    
    <?php echo $h->showMessage(); ?>

    <form name='edit_profile_form' class='users_form' action='<?php echo $h->url(array('page'=>'edit-profile', 'user'=>$h->vars['user']->name)); ?>' method='post'>    
    <table>
    
    <tr><td><?php echo $h->lang["users_profile_edit_bio"]; ?>&nbsp; </td>
        <td><textarea rows=5 name='bio'><?php echo $profile['bio']; ?></textarea></td>
    </tr>
    
    <?php // Add your own profile fields here. Use tr and td tags. ?>
    
    <?php $h->pluginHook('user_edit_profile_extras'); ?>
    
    <tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' class='submit' value='<?php echo $h->lang['users_profile_edit_update']; ?>' /></td></tr>
    </table>
    <input type='hidden' name='edited_profile' value='true' />
    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
    </form>
</div>