<?php
/**
 * name: Contact Us
 * description: Contact form
 * version: 0.1
 * folder: contact_us
 * type: contact_form
 * class: ContactUs
 * hooks: install_plugin, header_include, admin_sidebar_plugin_settings, admin_plugin_settings, theme_index_top, theme_index_main
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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

class ContactUs
{
    /**
     * Install plugin
     */
    public function install_plugin($h)
    {

        // Plugin settings
        $contact_us_settings = $h->getSerializedSettings();
        if (!isset($contact_us_settings['recaptcha'])) { $contact_us_settings['recaptcha'] = ""; }
        
        $h->updateSetting('contact_us_settings', serialize($contact_us_settings));
    }
    

    /**
     * Prepare page
     */
    public function theme_index_top($h)
    {
        if (($h->pageName == 'contact') && $h->cage->post->testAlpha('submitted') == 'contact')
        {
            // check CSRF key
            if (!$h->csrf()) {
                $h->messages[$h->lang['error_csrf']] = 'red';
                return false;
            }
        
            // get settings
            $contact_us_settings = $h->getSerializedSettings();
            
            // put recaptcha setting in $h so we can use it in the contact form:
            $h->vars['contact_us_recaptcha'] = $contact_us_settings['recaptcha'];
            
            // assume no errors to start with
            $error = false;
            
            // get submitted form data
            $name = $h->cage->post->getHtmLawed('name');
            $email = $h->cage->post->testEmail('email');
            $body = $h->cage->post->getHtmLawed('body');
            if ($h->cage->post->keyExists('self')) {
                $self = 'checked';
            } else {
                $self = '';
            }
            
            // check errors
            if (!$name) { $h->messages[$h->lang['contact_us_error_name']] = 'red'; $error = true; }
            if (!$email) { $h->messages[$h->lang['contact_us_error_email']] = 'red'; $error = true; }
            if (!$body) { $h->messages[$h->lang['contact_us_error_body']] = 'red'; $error = true; }
            
            // if using reCaptcha...
            if ($contact_us_settings['recaptcha']) {
                $result = $h->pluginHook('check_recaptcha');
                
                // recaptcha errors
                if ($result['ReCaptcha_check_recaptcha'] == 'empty')
                {
                    $h->messages[$h->lang["contact_us_error_recaptcha_empty"]] = 'red';
                    $error = true; 
                } 
                elseif ($result['ReCaptcha_check_recaptcha'] == 'error')
                {
                    $h->messages[$h->lang["contact_us_error_recaptcha_wrong"]] = 'red';
                    $error = true; 
                }
            }
            
            // no errors, send email!
            if (!$error) 
            {
                if (SMTP == 'true') {
                    $headers = array ('From' => $email, 'To' => SITE_EMAIL, 'Subject' => SITE_NAME . ' Contact Form');
                    $recipients['To'] = SITE_EMAIL;
                    if ($self) { $recipients['Cc'] = $email; }
                } else {
                    $headers = "From: " . $email . "\r\n";
                    if ($self) { $headers .= "Cc: " . $email . "\r\n"; }
                    $headers .= "Reply-To: " . $email . "\r\n";
                    $recipients = SITE_EMAIL;
                }
                
                $subject = SITE_NAME . ' Contact Form';
                $h->email($recipients, $subject, $body, $headers);
                
                // sent message
                $h->messages[$h->lang["contact_us_success"]] = 'green';
            } 
            else 
            {
                // fill $h so the user doesn't have to re-enter their message, etc. when correcting their error
                $h->vars['contact_us_name'] = $name;
                $h->vars['contact_us_email'] = $email;
                $h->vars['contact_us_body'] = $body;
                $h->vars['contact_us_self'] = $self;
            }
            
        }
    }
    
    
    /**
     * Show page
     */
    public function theme_index_main($h)
    {
        //if not the contact page, get out of here
        if ($h->pageName != 'contact') { return false; }
        
        // set default values
        if (!isset($h->vars['contact_us_name'])) { $h->vars['contact_us_name'] = ''; }
        if (!isset($h->vars['contact_us_email'])) { $h->vars['contact_us_email'] = ''; }
        if (!isset($h->vars['contact_us_body'])) { $h->vars['contact_us_body'] = ''; }
        if (!isset($h->vars['contact_us_self'])) { $h->vars['contact_us_self'] = ''; }
        if (!isset($h->vars['contact_us_recaptcha'])) { 
            // get recaptcha setting
            $contact_us_settings = $h->getSerializedSettings();
            $h->vars['contact_us_recaptcha'] = $contact_us_settings['recaptcha'];
        }
        
        
        // display contact form
        $h->displayTemplate('contact_form');
        return true;
    }
}

?>
