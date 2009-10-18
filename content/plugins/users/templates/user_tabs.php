<?php
/**
 * User Tabs
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
$username = $hotaru->vars['username'];
?>

<ul class='user_tabs'>
<li>[<a href='<?php echo $hotaru->url(array('page'=>'account', 'user'=>$username)); ?>'><?php echo $hotaru->lang["users_account_account"]; ?></a>]</li>
<li>[<a href='<?php echo $hotaru->url(array('page'=>'permissions', 'user'=>$username)); ?>'><?php echo $hotaru->lang["users_account_permissions"]; ?></a>]</li>
<?php if (($hotaru->current_user->getPermission('can_access_admin') == 'yes') && $hotaru->plugins->isActive('user_manager')) { ?>
    <li>[<a href="<?php echo BASEURL; ?>admin_index.php?page=plugin_settings&plugin=user_manager"><?php echo $hotaru->lang['user_man_link']; ?></a>]</li>
<?php } ?>
</ul>

