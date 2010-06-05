<?php
/**
 * User Profile
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
 
$username = $h->vars['user']->name;
?>

<div class="profile_navigation">

    <?php
     if ($h->isActive('avatar')) {
            echo "<div id='profile_avatar'>";
            $h->setAvatar($h->vars['user']->id, 80);
            echo $h->linkAvatar();
            echo "</div>";
    }
    ?>

    <ul>
    
    <li><a href='<?php echo $h->url(array('user'=>$username)); ?>'><?php echo $h->lang["users_profile"]; ?></a></li>
    
    <?php $h->pluginHook('profile_navigation'); ?>
    
    <?php // show account and profile links to owner or admin access users: 
        if (($h->currentUser->name == $username) || ($h->currentUser->getPermission('can_access_admin') == 'yes')) { ?>

    <li><a href='<?php echo $h->url(array('page'=>'account', 'user'=>$username)); ?>'><?php echo $h->lang["users_account"]; ?></a></li>
    <li><a href='<?php echo $h->url(array('page'=>'edit-profile', 'user'=>$username)); ?>'><?php echo $h->lang["users_profile_edit"]; ?></a></li>
    <li><a href='<?php echo $h->url(array('page'=>'user-settings', 'user'=>$username)); ?>'><?php echo $h->lang["users_settings"]; ?></a></li>

    <?php $h->pluginHook('profile_navigation_restricted'); ?>

    <?php // show permissions and User Manager links admin access users only: 
        if ($h->currentUser->getPermission('can_access_admin') == 'yes') { ?>
        <li><a href='<?php echo $h->url(array('page'=>'permissions', 'user'=>$username)); ?>'><?php echo $h->lang["users_permissions"]; ?></a></li>
    
        <?php // show User Manager link only if theplugin is active
            if ($h->isActive('user_manager')) { ?>
            <li><a href="<?php echo BASEURL; ?>admin_index.php?search_value=<?php echo $username; ?>&amp;plugin=user_manager&amp;page=plugin_settings&amp;type=search"><?php echo $h->lang['user_man_link']; ?></a></li>
        <?php } ?>
        
    <?php } ?>
    
    </ul>

<?php } ?>
</div>
