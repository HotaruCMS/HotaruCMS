<?php 
/**
 * Theme name: admin_default
 * Template name: admin_login.php
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

global $hotaru, $lang; // don't remove
?>

<p class="breadcrumbs">
    <a href="<?php echo BASEURL; ?>"><?php echo SITE_NAME; ?></a> 
    &raquo; <a href="<?php echo url(array(), 'admin'); ?>"><?php echo $lang["admin_theme_main_admin_cp"]; ?></a> 
    &raquo; <?php echo $lang["admin_theme_login"]; ?>
</p>
        
<?php $hotaru->showMessage(); ?>
        
<div class='main_inner'>
    <?php echo $lang["admin_theme_login_instructions"]; ?>
    
    <form name='login_form' action='<?php echo BASEURL; ?>admin_index.php' method='post'>    
    <table>
        <tr>
        <td><?php echo $lang["admin_theme_login_username"]; ?>:&nbsp; </td>
        <td><input type='text' size=30 name='username' value='<?php echo $username_check; ?>' /></td>
        </tr>
        <tr>
        <td><?php echo $lang["admin_theme_login_password"]; ?>:&nbsp; </td>
        <td><input type='password' size=30 name='password' value='<?php echo $password_check; ?>' /></td>
        </tr>
        <tr>
        <td>&nbsp; </td>
        <td style='text-align:right;'><input type='submit' value='<?php echo $lang['admin_theme_login_form_submit']; ?>' /></td>
        </tr>            
    </table>
    <input type='hidden' name='login_attempted' value='true'>
    <input type='hidden' name='page' value='admin_login'>
    </form>
    
    <a href="#" class="forgot_password"><?php echo $lang["admin_theme_login_forgot_password"]; ?></a>
    
    <form style="display: none;" name='forgot_password_form' action='<?php echo BASEURL; ?>admin_index.php' method='post'>    
        <?php echo $lang['admin_theme_login_forgot_password_submit_instruct_1']; ?>
    <table>
        <tr>
        <td><?php echo $lang["admin_theme_update_email"]; ?>&nbsp; </td>
        <td><input type='text' size=30 name='email' value='<?php echo $email_check; ?>' /></td>
        <td><input type='submit' value='<?php echo $lang['admin_theme_login_forgot_password_submit']; ?>' /></td>
        </tr>            
    </table>
    <input type='hidden' name='forgotten_password' value='true'>
    <input type='hidden' name='page' value='admin_login'>
        <?php echo $lang['admin_theme_login_forgot_password_submit_instruct_2']; ?>
    </form>

</div>
