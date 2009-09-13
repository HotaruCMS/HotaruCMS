<?php
/**
 * Users Register
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
 
global $hotaru, $cage, $lang, $plugins, $userbase;
    
if ($cage->post->getAlpha('users_type') == 'register') {
    $username_check = $cage->post->testUsername('username');
    $password_check = "";
    $password2_check = "";
    $email_check = $cage->post->testEmail('email');    
} else {
    $username_check = "";
    $password_check = "";
    $password2_check = "";
    $email_check = "";
}
?>

    <div id='main'>
        <div id='breadcrumbs'><a href='<?php echo BASEURL; ?>'><?php echo $lang["users_home"]; ?></a> &raquo; <?php echo $lang["users_register"]; ?></div>
            
        <h2><?php echo $lang["users_register"]; ?></h2>
        
        <?php echo $hotaru->showMessages(); ?>
            
        <div class='main_inner'>
        <?php echo $lang["users_register_instructions"]; ?>
                
            <form name='register_form' action='<?php echo BASEURL; ?>index.php?page=register' method='post'>    
            <table>
            <tr><td><?php echo $lang["users_register_username"]; ?>&nbsp; </td><td><input type='text' size=30 name='username' value='<?php echo $username_check; ?>' /></td></tr>
            <tr><td><?php echo $lang["users_register_email"]; ?>&nbsp; </td><td><input type='text' size=30 name='email' value='<?php echo $email_check; ?>' /></td></tr>
            <tr><td><?php echo $lang["users_register_password"]; ?>&nbsp; </td><td><input type='password' size=30 name='password' value='<?php echo $password_check; ?>' /></td></tr>
            <tr><td><?php echo $lang["users_register_password_verify"]; ?>&nbsp; </td><td><input type='password' size=30 name='password2' value='<?php echo $password2_check; ?>' /></td></tr>
            
            <?php 
                if ($userbase->vars['usersRecaptchaEnabled']) { 
                    $recaptcha_pubkey = $plugins->getSetting('users_recaptcha_pubkey', 'users');
                    echo "<tr><td colspan=2>" . recaptcha_get_html($recaptcha_pubkey) . "</td></tr>";
                }
            ?>
            
            <input type='hidden' name='users_type' value='register' />
            <tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' value='<?php echo $lang['users_register_form_submit']; ?>' /></td></tr>            
            </table>
            </form>
        </div>
    </div>    
