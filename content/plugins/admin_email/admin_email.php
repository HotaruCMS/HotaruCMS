<?php
/**
 * name: Admin Email
 * description: Send email to all members, groups or users
 * version: 0.1
 * folder: admin_email
 * class: adminEmail
 * requires: submit 1.4, users 0.8
 * hooks: install_plugin, admin_header_include, admin_plugin_settings, admin_sidebar_plugin_settings, admin_theme_index_replace
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
        if (!isset($admin_email_settings['admin_email_simulation'])) { $admin_email_settings['admin_email_simulation'] = ''; }
        if (!isset($admin_email_settings['admin_email_id_list'])) { $admin_email_settings['admin_email_id_list'] = serialize(array()); }
        
        $this->updateSetting('admin_email_settings', serialize($admin_email_settings));
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
        
        if(!$users) { echo "FINISHED!"; exit; }
        
        if ($simulation) { 
            echo "<p style='color: red;'><b>" . $this->lang["admin_email_simulation_mode"] . "</b></p>\n";
        } else {
            echo "<p style='color: blue;'><b>" . $this->lang["admin_email_real_mode"] . "</b></p>\n";
        }
        
        echo "<p><b>" . $this->lang["admin_email_email_batch"] . $batch . "/" . $batches . "</b></p>\n";
                
        foreach($users as $recipient) {

            $subject = preg_replace('/\{username\}/i', $recipient->user_username, trim(html_entity_decode($orig_subject, ENT_QUOTES,'UTF-8')));
            $body = preg_replace('/\{username\}/i', $recipient->user_username, trim(html_entity_decode($orig_body, ENT_QUOTES,'UTF-8')));
            $message = preg_replace("#(\r\n|\r|\n)#s", $delimiter, $body);
            $message .= " \r\n\r\n";
            $message .= $this->lang['admin_email_pre_remove'] . "\r\n";
            $message .= $this->lang['admin_email_remove'];
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
}
?>