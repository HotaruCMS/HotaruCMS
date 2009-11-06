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
?>

<div id="profile">

    <?php 
        if ($hotaru->plugins->isActive('gravatar')) { 
            echo "<div id='profile_avatar'>";
            $avatar = new Gravatar('', $hotaru);
            $hotaru->vars['gravatar_size'] = 80;
            $avatar->showGravatarLink($hotaru->user->name, $hotaru->user->email);
            echo "</div>";
        }
    ?>
    
    <div id="profile_bio">
        Users aren't able to add a bio to their profiles yet, but they will soon!
    </div>
    
    <div class="clear"></div>
    
    <div id="profile_usage">
        <?php echo $hotaru->lang['users_profile_usage']; ?>
        <?php $hotaru->plugins->pluginHook('profile_usage'); ?>
    </div>
    
    <?php $hotaru->plugins->pluginHook('profile'); ?>
    
</div>

