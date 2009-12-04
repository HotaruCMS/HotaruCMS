<?php
/**
 * name: Admin Email
 * description: Send email to all members, groups or users
 * version: 0.2
 * folder: admin_email
 * class: adminEmail
 * requires: users 1.0
 * hooks: install_plugin, admin_header_include, admin_plugin_settings, admin_sidebar_plugin_settings, admin_theme_index_replace, user_settings_pre_save, user_settings_fill_form, user_settings_extra_settings
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

class adminEmail extends PluginFunctions
{
    /**
     * Default settings on install
     */
    public function install_plugin()
    {
        // Default settings
        $admin_email_settings = $this->getSerializedSettings();
        
        if (!isset($admin_email_settings['admin_email_batch_size'])) { $admin_email_settings['admin_email_batch_size'] = 20; }
        if (!isset($admin_email_settings['admin_email_pause'])) { $admin_email_settings['admin_email_pause'] = 10; }
        if (!isset($admin_email_settings['admin_email_recipients'])) { $admin_email_settings['admin_email_recipients'] = serialize(array()); }
        if (!isset($admin_email_settings['admin_email_subject'])) { $admin_email_settings['admin_email_subject'] = ''; }
        if (!isset($admin_email_settings['admin_email_body'])) { $admin_email_settings['admin_email_body'] = ''; }
        if (!isset($admin_email_settings['admin_email_send_self'])) { $admin_email_settings['admin_email_send_self'] = ''; }
        if (!isset($admin_email_settings['admin_email_send_opted_out'])) { $admin_email_settings['admin_email_send_opted_out'] = ''; }
        if (!isset($admin_email_settings['admin_email_simulation'])) { $admin_email_settings['admin_email_simulation'] = ''; }
        if (!isset($admin_email_settings['admin_email_id_list'])) { $admin_email_settings['admin_email_id_list'] = serialize(array()); }
        
        $this->updateSetting('admin_email_settings', serialize($admin_email_settings));
        
        // Add "admin notify" option to the default user settings
        require_once(PLUGINS . 'users/libs/UserFunctions.php');
        $uf = new UserFunctions($this->hotaru);
        $base_settings = $uf->getDefaultSettings('base'); // originals from plugins
        $site_settings = $uf->getDefaultSettings('site'); // site defaults updated by admin
        if (!isset($base_settings['admin_notify'])) { 
            $base_settings['admin_notify'] = "checked";
            $site_settings['admin_notify'] = "checked";
            $uf->updateDefaultSettings($base_settings, 'base');
            $uf->updateDefaultSettings($site_settings, 'site');
        }
    }
    
    
    /**
     * Disable sidebar
     */
    public function admin_theme_index_replace()
    {
        if (($this->cage->get->testPage('plugin') == 'admin_email') && $this->cage->get->testInt('mailing') == 1) {
            $this->includeLanguage();
            $this->sendAdminEmail();
        }
    }
    
    /**
     * Do admin email
     */
    public function doAdminEmail()
    {
        // get extra functions from Users plugin
        require_once(PLUGINS . 'users/libs/UserFunctions.php');
        $uf = new UserFunctions($this->hotaru);
        
        // get latest changes:
        $admin_email_settings = $this->getSerializedSettings();

        $recipients = unserialize($admin_email_settings['admin_email_recipients']);
        $send_self = $admin_email_settings['admin_email_send_self'];
        $send_opted_out = $admin_email_settings['admin_email_send_opted_out'];
        
        // make recipients an array:
        if (is_object($recipients)) { 
            $recipients_array = array();
            foreach ($recipients as $recip) {
                array_push($recipients_array, $recip);
            }
            $recipients = $recipients_array;
        }
        
        $id_list = array(); // this will store the ids of all email recipients
        
        $groups = array('admin', 'supermod', 'moderator', 'member', 'undermod', 'banned', 'killspammed');
        foreach ($groups as $gname) {
            if (in_array($gname, $recipients)) {
                $group_members = $uf->userIdNameList($gname);
                if (!$group_members) { continue; }
                foreach ($group_members as $gm) {
                    array_push($id_list, $gm->user_id);
                }
            }
        }
        
        // strip groups from $recipients because we've just put them in $id_list 
        $recipients = array_diff($recipients, $groups);
        
        // complete list of user ids for each recipient
        $id_list = array_merge($id_list, $recipients);
        
        // if not force sending to users who have opted out of getting admin emails, remove them now:
        if (!$send_opted_out) {
            // we need to remove anyone who has opted out of emails from admin.
            // First get the user_settings for every user on the site with SAVED settings:
            $all_settings = $uf->userSettingsList();
            $default_settings = $uf->getDefaultSettings();
            
            // Next, make a list of opted out users (by id)
            $opted_out = array();
            if ($all_settings) {
                foreach ($all_settings as $set) {
                    $u_settings = unserialize($set->usermeta_value);
                    $merged_settings = array_merge($default_settings, $u_settings);
                    if (!$merged_settings['admin_notify']) {
                        array_push($opted_out, $set->usermeta_userid);
                    }
                }
            }
            
            // strip opted out users from $recipients
            $id_list = array_diff($id_list, $opted_out);
        }
        
        // if sending a copy to yourself, add your id to the list
        if ($send_self) { array_push($id_list, $this->current_user->id); }
        
        // remove any duplicates:
        $id_list = array_unique($id_list);
        
        // Save the id list to the database.
        $admin_email_settings['admin_email_id_list'] = serialize($id_list);
        $this->updateSetting('admin_email_settings', serialize($admin_email_settings));
        
        // reload the page, without any html...
        $url = BASEURL . "admin_index.php?page=plugin_settings&plugin=admin_email&mailing=1";
        echo "<meta http-equiv='Refresh' content='0; URL=" . $url . "'>";
        echo $this->lang["admin_email_redirecting"];
        ob_flush();
        flush();
        exit;
    }
    
    
    /**
     * Send emails
     *
     * @param object $recipients
     */
    public function sendAdminEmail()
    {
        // get extra functions from Users plugin
        require_once(PLUGINS . 'users/libs/UserFunctions.php');
        $uf = new UserFunctions($this->hotaru);
        
        // get latest changes:
        $admin_email_settings   = $this->getSerializedSettings();
        $id_list                = unserialize($admin_email_settings['admin_email_id_list']);
        $batch_size             = $admin_email_settings['admin_email_batch_size'];
        $pause                  = $admin_email_settings['admin_email_pause'];
        $orig_subject                = $admin_email_settings['admin_email_subject'];
        $orig_body                   = $admin_email_settings['admin_email_body'];
        $simulation             = $admin_email_settings['admin_email_simulation'];
        $send_self              = $admin_email_settings['admin_email_send_self'];
        $delimiter              = "\r\n";
        $start                  = 0;    // how many rows down the resulting users table do we start our batch from
        
        $batch = $this->cage->get->testInt('batch');
        if ($batch) { $start = ($batch-1) * $batch_size; } else { $batch = 1; }
        
        // get information for each user here:
        if ($id_list) {
            $batches = ceil(count($id_list) / $batch_size);
            $users = $uf->userListFull($id_list, $start, $batch_size); // batches returned alphabetically
        }
                
        if ($simulation) { 
            echo "<p style='color: red;'><b>" . $this->lang["admin_email_simulation_mode"] . "</b></p>\n";
        } else {
            echo "<p style='color: blue;'><b>" . $this->lang["admin_email_real_mode"] . "</b></p>\n";
        }
        
        if(!$users) { 
            echo "<p>" . $this->lang["admin_email_no_recipients"] . "</p>\n"; 
            echo "<p><a href='" . BASEURL . "admin_index.php?page=plugin_settings&plugin=admin_email&status=done'>";
            echo $this->lang['admin_email'] . "</a></p>\n"; 
            exit; 
        }
        
        echo "<p><b>" . $this->lang["admin_email_email_batch"] . $batch . "/" . $batches . "</b></p>\n";
                
        foreach($users as $recipient) {

            $subject = preg_replace('/\{username\}/i', $recipient->user_username, trim(html_entity_decode($orig_subject, ENT_QUOTES,'UTF-8')));
            $body = preg_replace('/\{username\}/i', $recipient->user_username, trim(html_entity_decode($orig_body, ENT_QUOTES,'UTF-8')));
            $message = preg_replace("#(\r\n|\r|\n)#s", $delimiter, $body);
            $message .= " \r\n\r\n";
            $message .= $this->lang['admin_email_pre_remove'] . "\r\n";
            $message .= $this->lang['admin_email_remove'] . "\r\n";
            $message .= BASEURL . "index.php?page=user-settings&user=" . $recipient->user_username;
            $from = SITE_EMAIL; // Send_From_Email is admin
            $to = $recipient->user_email;  
            $headers = "From: " . $from . "\r\nReply-To: " . $from . "\r\nX-Priority: 3\r\n";
            
            if ($send_self && ($this->current_user->id == $recipient->user_id)) {
                echo $this->lang["admin_email_sent_to"] . "<i>" . $to . "</i><br />\n";
                @mail($to, $subject, $message, $headers);   // This sends an email to the requesting admin
            } elseif ($simulation) {
                echo $this->lang["admin_email_fake_sending"] . $to . "<br />\n";
            } else {
                echo $this->lang["admin_email_sent_to"] . $to . "<br />\n";
                @mail($to, $subject, $message, $headers);   // This does the actual sending!
            }
            ob_flush();
            flush();
            sleep(1); // one second pause between sending emails
        }
        
        if ($batch < $batches) {
            $batch++;
            echo "<p>" . $this->lang["admin_email_waiting"] . $pause . $this->lang["admin_email_before_next_batch"] . "</p>\n";
            echo "<p><a href='" . $this->hotaru->url(array('page'=>'plugin_settings', 'plugin'=>'admin_email'), 'admin') . "'>";
            echo "<span style='color: red;'>" . $this->lang['admin_email_abort'] . "</span></a></p>\n";
            // reload the page, without any html...
            $url = BASEURL . "admin_index.php?page=plugin_settings&plugin=admin_email&mailing=1&batch=" . $batch;
            echo "<meta http-equiv='Refresh' content='" . $pause . "; URL=" . $url . "'>";
        } else {
            if ($simulation) {
                if ($send_self) { echo "<p>" . $this->lang["admin_email_sent_to_self"] . "</p>\n"; }
                echo "<p>" . $this->lang["admin_email_after_simulation"] . "\n";
                echo "<a href='" . BASEURL . "admin_index.php?page=plugin_settings&plugin=admin_email&status=simulated'>";
                echo $this->lang['admin_email'] . "</a>\n";
                echo $this->lang["admin_email_after_simulation2"] . "</p>\n";
            } else {
                echo "<p>" . $this->lang["admin_email_after_real"] . "\n";
                echo "<a href='" . BASEURL . "admin_index.php?page=plugin_settings&plugin=admin_email&status=done'>";
                echo $this->lang['admin_email'] . "</a>\n";
                echo $this->lang["admin_email_after_real2"] . "</p>\n";
            }
        }
        
        exit;
    }

    
    /**
     * User Settings - before saving
     */
    public function user_settings_pre_save()
    {
        // Emails from Admins:
        if ($this->cage->post->getAlpha('admin_notify') == 'yes') { 
            $this->hotaru->vars['settings']['admin_notify'] = "checked"; 
        } else { 
            $this->hotaru->vars['settings']['admin_notify'] = "";
        }
    }
    
    
    /**
     * User Settings - fill the form
     */
    public function user_settings_fill_form()
    {
        if ($this->hotaru->vars['settings']['admin_notify']) { 
            $this->hotaru->vars['admin_notify_yes'] = "checked"; 
            $this->hotaru->vars['admin_notify_no'] = ""; 
        } else { 
            $this->hotaru->vars['admin_notify_yes'] = ""; 
            $this->hotaru->vars['admin_notify_no'] = "checked"; 
        }
    }
    
    
    /**
     * User Settings - html for form
     */
    public function user_settings_extra_settings()
    {
        $this->includeLanguage();
        echo "<tr>\n";
            // ACCEPT EMAIL FROM ADMINS?
        echo "<td>" . $this->lang['users_settings_email_from_admin'] . "</td>\n";
        echo "<td><input type='radio' name='admin_notify' value='yes' " . $this->hotaru->vars['admin_notify_yes'] . "> " . $this->lang['users_settings_yes'] . " &nbsp;&nbsp;\n";
        echo "<input type='radio' name='admin_notify' value='no' " . $this->hotaru->vars['admin_notify_no'] . "> " . $this->lang['users_settings_no'] . "</td>\n";
        echo "</tr>\n";
    }
}
?>