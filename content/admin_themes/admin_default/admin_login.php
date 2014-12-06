<?php 
/**
 * Theme name: admin_default
 * Template name: admin_login.php
 * Template author: shibuya246
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

<div class="signin-container" style="width:650px; margin: 40px auto;">
    <div class="row">
        <?php $h->showMessage(); ?>
        
        <!-- Right side -->
        <div class="col-md-9 signin-form">

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Hotaru CMS
                    </div>
                    <div class="panel-body">
                        
                        <div class="row" style="margin-bottom: 30px;">
                            <div class="col-md-3">
                                <img src="<?php echo BASEURL; ?>content/admin_themes/admin_default/images/hotaru-80px.png" alt=""/>
                            </div>
                            <div class="col-md-8">
                                <h2>Admin Login</h2>
                            </div>
                        </div>
                        
                <!-- Form -->
                <form role="form" name='login_form' action='<?php echo SITEURL; ?>admin_index.php' method='post'>

                    <div class="form-group" style="">
                        <div class="input-group">
                            <div class="input-group-addon">&nbsp;<span class="fa fa-user signin-form-icon"></span>&nbsp;</div>
                            <input type='text' name='username' id='admin_login_name' class="form-control input-lg" placeholder="<?php echo $h->lang("admin_theme_login_username"); ?>" value='<?php echo $username_check; ?>' />
                        </div>
                    </div> <!-- / Username -->

                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">&nbsp;<span class="fa fa-lock signin-form-icon"></span>&nbsp;</div>
                            <input type='password' name='password' id='admin_login_password' class="form-control input-lg" placeholder="<?php echo $h->lang("admin_theme_login_password"); ?>" value='<?php echo $password_check; ?>' />
                        </div>
                    </div> <!-- / Password -->
<hr>
                    <div class="form-actions text-center">
                        <button type="submit" class="btn btn-primary"><?php echo $h->lang('admin_theme_login_form_submit'); ?></button>
                    </div>
                    
                    <input type='hidden' name='login_attempted' value='true'>
                    <input type='hidden' name='page' value='admin_login'>
                    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
        
                </form>
                <!-- / Form -->

                <!-- "Sign In with" block -->
<!--			<div class="signin-with">
                         Facebook 
                        <a href="index.html" class="signin-with-btn" style="background:#4f6faa;background:rgba(79, 111, 170, .8);">Sign In with <span>Facebook</span></a>
                </div>-->
                <!-- / "Sign In with" block -->
                    </div>
                </div>

    <center>
        <p><a href="<?php echo SITEURL; ?>">Back to Site</a></p>
        <a href="#" class="forgot_password"><?php echo $h->lang("admin_theme_login_forgot_password"); ?></a>
    </center>
    <br/><br/>
        <form style="display: none;" id='forgot_password_form' name='forgot_password_form' action='<?php echo SITEURL; ?>admin_index.php' method='post'>    
            <?php echo $h->lang('admin_theme_login_forgot_password_submit_instruct_1'); ?>
            
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon">&nbsp;<span class="fa fa-envelope"></span>&nbsp;</div>
                    <input type='text' name='email' id='admin_login_password' class="form-control input-lg" placeholder="<?php echo $h->lang("admin_theme_update_email"); ?>" value='<?php echo $email_check; ?>' />
                </div>
            </div>
            
            <input type='submit' class="btn btn-primary" value='<?php echo $h->lang('admin_theme_login_forgot_password_submit'); ?>' />
            
            <input type='hidden' name='forgotten_password' value='true'>
            <input type='hidden' name='page' value='admin_login'>
            <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
            <br/>
            <hr/>
            <?php echo $h->lang('admin_theme_login_forgot_password_submit_instruct_2'); ?>
        </form>
    
    </div>
        <!-- Right side -->
    </div>
</div>
