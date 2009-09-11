<?php
/**
 * Users Login
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
 
global $hotaru, $cage, $lang;
if (!$username_check = $cage->post->testUsername('username')) { $username_check = ""; } 
if (!$password_check = $cage->post->testPassword('password')) { $password_check = ""; }
if ($cage->post->getInt('remember') == 1){ $remember_check = "checked"; } else { $remember_check = ""; }
 
?>
    
    <div id='main'>
        <div id='breadcrumbs'><a href='<?php echo BASEURL; ?>'><?php echo $lang["users_home"]; ?></a> &raquo; <?php echo $lang["users_login"]; ?></div>
        
        <h2>Login</h2>
        
        <?php echo $hotaru->showMessages(); ?>
        
        <div class='main_inner'>
        <?php echo $lang["users_login_instructions"]; ?>
        
            <form name='login_form' action='<?php echo BASEURL; ?>index.php?page=login' method='post'>
            <table>
                <tr><td><?php echo $lang["users_login_form_submit_username"]; ?>&nbsp; </td><td><input type='text' size=30 name='username' value='<?php echo $username_check; ?>' /></td></tr>
                <tr><td><?php echo $lang["users_login_form_submit_password"]; ?>&nbsp; </td><td><input type='password' size=30 name='password' value='<?php echo $password_check; ?>' /></td></tr>
                <tr><td><?php echo $lang["users_login_form_submit_remember"]; ?> </td><td><input type='checkbox' name='remember' value='1' <?php echo $remember_check; ?> /></td></tr>
                <tr><td>&nbsp; </td><td style='text-align:right;'><input type='submit' value='<?php echo $lang['users_login_form_submit']; ?>' /></td></tr>
                            
            </table>
            </form>
        </div>
    </div>    