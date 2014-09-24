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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
?>
<div id="users_permissions" class="users_content">

    <h2><?php echo $h->lang["users_permissions"]; ?>: <?php echo $h->vars['user']->name; ?></h2>
    
    <?php $h->showMessages(); ?>
        
    <form name='permissions_form' action='<?php echo BASEURL; ?>index.php' method='post'>
    <table class='permissions'>
    <?php echo $h->vars['perm_options']; ?>

    
    </table>
    <input type='hidden' name='page' value='permissions' />
    <input type='hidden' name='permissions' value='updated' />
    <input type='hidden' name='userid' value='<?php echo $h->vars['user']->id; ?>' />
    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
    <div style='text-align: right'><input class='submit' type='submit' value='<?php echo  $h->lang['users_permissions_update']; ?>' /></div>
    </form>
</div>