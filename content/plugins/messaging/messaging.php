<?php
/**
 * name: Messaging
 * description: Enable users to send private messages to each other
 * version: 0.2
 * folder: messaging
 * class: Messaging
 * requires: users 1.5
 * hooks: install_plugin, theme_index_top, header_include, profile_navigation, breadcrumbs, theme_index_main, admin_maintenance_database, admin_maintenance_top, user_settings_pre_save, user_settings_fill_form, user_settings_extra_settings, userauth_checkcookie_success, hotaru_announcements, smart_cache_sql
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
 */

class Messaging
{
    /**
     * Default settings on install
     */
    public function install_plugin($h)
    {
         // Create a new empty table called "messaging"
        $exists = $h->db->table_exists('messaging');
        if (!$exists) {
            //echo "table doesn't exist. Stopping before creation."; exit;
            $sql = "CREATE TABLE `" . DB_PREFIX . "messaging` (
              `message_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `message_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `message_archived` enum('Y','N') NOT NULL DEFAULT 'N',
              `message_from` int(20) NOT NULL DEFAULT 0,
              `message_to` int(20) NOT NULL DEFAULT 0,
              `message_date` timestamp NOT NULL,
              `message_subject` varchar(255) NOT NULL DEFAULT '',
              `message_content` text NULL,
              `message_read` tinyint(1) NOT NULL DEFAULT '0',
              `message_inbox` tinyint(1) NOT NULL DEFAULT '1',
              `message_outbox` tinyint(1) NOT NULL DEFAULT '1',
              `message_updateby` int(20) NOT NULL DEFAULT 0
            ) ENGINE=" . DB_ENGINE . " DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . " COMMENT='Messaging';";
            $h->db->query($sql); 
        }
        
        // Permissions
        $site_perms = $h->getDefaultPermissions('all');
        if (!isset($site_perms['can_do_messaging'])) { 
            $perms['options']['can_do_messaging'] = array('yes', 'no');
            $perms['can_do_messaging']['admin'] = 'yes';
            $perms['can_do_messaging']['supermod'] = 'yes';
            $perms['can_do_messaging']['moderator'] = 'yes';
            $perms['can_do_messaging']['member'] = 'yes';
            $perms['can_do_messaging']['undermod'] = 'yes';
            $perms['can_do_messaging']['default'] = 'no';
            $h->updateDefaultPermissions($perms);
        }
        
        // Add "pm notify" option to the default user settings
        $base_settings = $h->getDefaultSettings('base'); // originals from plugins
        $site_settings = $h->getDefaultSettings('site'); // site defaults updated by admin
        if (!isset($base_settings['pm_notify'])) { 
            $base_settings['pm_notify'] = "checked";
            $site_settings['pm_notify'] = "checked";
            $h->updateDefaultSettings($base_settings, 'base');
            $h->updateDefaultSettings($site_settings, 'site');
        }
    }
    
    
    /**
     * Determine page and get user details
     */
    public function theme_index_top($h)
    {
        if ($h->currentUser->getPermission('can_do_messaging') == 'no') { return false; }

        $messaging = false;
        
        $user = $h->cage->get->testUsername('user');
        if (!$user) { $user = $h->currentUser->name; }
        
        switch ($h->pageName)
        {
            case 'inbox':
                $messaging = true;
                $h->pageTitle = $h->lang['messaging_inbox'] . "[delimiter]" . $user;
                break;
            case 'outbox':
                $messaging = true;
                $h->pageTitle = $h->lang['messaging_outbox'] . "[delimiter]" . $user;
                break;
            case 'compose':
                $messaging = true;
                $h->pageTitle = $h->lang['messaging_compose'] . "[delimiter]" . $user;
            case 'show_message':
                $messaging = true;
                $h->pageTitle = $h->lang['messaging_view_message'] . "[delimiter]" . $user;
        }
        
        // set page types & create UserAuth and MessagingFuncs objects
        if ($messaging) { 
            $h->pageType = 'user';  // this setting hides the posts filter bar
            $h->subPage = 'user'; 
            
            include_once(PLUGINS . 'messaging/libs/MessagingFuncs.php');
            $msgFuncs = new MessagingFuncs();
            
            // create a user object and fill it with user info (user being viewed)
            $h->vars['user'] = new UserAuth();
            $h->vars['user']->getUserBasic($h, 0, $user);
            
            //defaults:
            $h->vars['message_to'] = '';
            $h->vars['message_from'] = '';
            $h->vars['message_subject'] = '';
            $h->vars['message_body'] = '';
            $h->vars['message_reply'] = false;
            $h->vars['message_id'] = 0;
        }
        
        switch ($h->pageName)
        {
            case 'inbox':

                // see if we need to delete any messages:
                if ($h->cage->post->keyExists('delete_selected') && $h->cage->post->keyExists('message')) {
                    foreach ($h->cage->post->keyExists('message') as $id => $checked) {
                        // delete checked message
                        $msgFuncs->deleteMessage($h, $id, 'inbox');
                    }
                }
                $count = $msgFuncs->getBoxCount($h, 'inbox');
                $query = $msgFuncs->getBoxQuery($h, 'inbox');
                $h->vars['messages_list'] = $h->pagination($query, $count, 20);
                break;
                
            case 'outbox':
                
                // see if we need to delete any messages:
                if ($h->cage->post->keyExists('delete_selected') && $h->cage->post->keyExists('message')) {
                    foreach ($h->cage->post->keyExists('message') as $id => $checked) {
                        // delete checked message
                        $msgFuncs->deleteMessage($h, $id, 'outbox');
                    }
                }
                
                $count = $msgFuncs->getBoxCount($h, 'outbox');
                $query = $msgFuncs->getBoxQuery($h, 'outbox');
                $h->vars['messages_list'] = $h->pagination($query, $count, 20);
                break;
                
            case 'compose':
                
                // when clicking "Send Message"...
                $h->vars['message_to'] = $h->cage->get->testUsername('message_to');
                
                // SENDING A NEW MESSAGE
                if ($h->cage->get->testAlpha('action') == 'send')
                {
                    // check CSRF key
                    if (!$h->csrf()) {
                        $h->messages[$h->lang['error_csrf']] = 'red';
                        return false;
                    }
            
                    // get submitted data
                    if (!$h->vars['message_to']) {
                        $h->vars['message_to'] = $h->cage->post->testUsername('message_to');
                    }
                    $h->vars['message_subject'] = $h->cage->post->getHtmLawed('message_subject');
                    $h->vars['message_body'] = $h->cage->post->getHtmLawed('message_body');
                    
                    // Create and fill MessagingFuncs object
                    $msgFuncs->to = $h->vars['message_to'];
                    $msgFuncs->from = $h->currentUser->name;
                    $msgFuncs->subject = $h->vars['message_subject'];
                    $msgFuncs->body = $h->vars['message_body'];
                    
                    // Attempt to send the message
                    $result = $msgFuncs->sendMessage($h);
                    if ($result) {
                        $h->messages[$h->lang['messaging_sent']] = 'green';
                    } else {
                        foreach ($msgFuncs->errors as $err) {
                            $error_message = "messaging_" . $err;
                            if (isset($h->lang[$error_message])) {
                                $h->messages[$h->lang[$error_message]] = 'red';
                            }
                        }
                    }
                } 
                
                // REPLYING TO A MESSAGE
                elseif ($h->cage->get->testInt('reply'))
                {
                    $h->vars['message_id'] = $h->cage->get->testInt('reply');
                    $original_message = $msgFuncs->getMessage($h, $h->vars['message_id']);
                    
                    if (!$original_message) { return false; }
                    $h->vars['message_to'] = $h->getUserNameFromId($original_message->message_from);
                    $h->vars['message_subject'] = urldecode($original_message->message_subject);
                    $h->vars['message_body'] = "";
                    $h->vars['message_reply'] = true;
                    
                    // mark this message as read
                    $msgFuncs->markRead($h, $h->vars['message_id']);
                }
                break;
                
            case 'show_message':
                
                $h->vars['message_id'] = $h->cage->get->testInt('id');
                $original_message = $msgFuncs->getMessage($h, $h->vars['message_id']);
                
                if (!$original_message) { $h->vars['message_id'] = 0; return false; }
                $h->vars['message_from_name'] = $h->getUserNameFromId($original_message->message_from);
                $h->vars['message_from_id'] = $original_message->message_from;
                $h->vars['message_date'] = $original_message->message_date;
                $h->vars['message_subject'] = sanitize(urldecode($original_message->message_subject), 'all');
                $h->vars['message_body'] = sanitize(urldecode($original_message->message_content), 'all');
                
                // mark this message as read
                $msgFuncs->markRead($h, $h->vars['message_id']);
        }
    }
    
    
    /**
     * Profile menu link on other people's profiles for sending a message
     */
    public function profile_navigation($h)
    {
        if ($h->currentUser->getPermission('can_do_messaging') == 'no') { return false; }
        
        if ($h->currentUser->name != $h->vars['user']->name) {
            echo "<li><a href='" . $h->url(array('page'=>'compose', 'user'=>$h->currentUser->name, 'message_to'=>$h->vars['user']->name)) . "'>" . $h->lang['messaging_send_message'] . "</a></li>\n";
        } else {
            echo "<li><a href='" . $h->url(array('page'=>'inbox', 'user'=>$h->vars['user']->name)) . "'>" . $h->lang['messaging_inbox'] . "</a></li>\n";
            echo "<li><a href='" . $h->url(array('page'=>'outbox', 'user'=>$h->vars['user']->name)) . "'>" . $h->lang['messaging_outbox'] . "</a></li>\n";
            echo "<li><a href='" . $h->url(array('page'=>'compose', 'user'=>$h->vars['user']->name)) . "'>" . $h->lang['messaging_compose'] . "</a></li>\n";
        }
    }
    
    
    /**
     * Breadcrumbs for messaging pages
     */
    public function breadcrumbs($h)
    {
        $user = $h->cage->get->testUsername('user');
        if (!$user) { $user = $h->currentUser->name; }
        
        switch ($h->pageName)
        {
            case 'inbox':
                return "<a href='" . $h->url(array('user'=>$user)) . "'>" . $user . "</a> &raquo; " . $h->lang['messaging_inbox'];
                break;
            case 'outbox':
                return "<a href='" . $h->url(array('user'=>$user)) . "'>" . $user . "</a> &raquo; " . $h->lang['messaging_outbox'];
                break;
            case 'compose':
                return "<a href='" . $h->url(array('user'=>$user)) . "'>" . $user . "</a> &raquo; " . $h->lang['messaging_compose'];
                break;
            case 'show_message':
                return "<a href='" . $h->url(array('user'=>$user)) . "'>" . $user . "</a> &raquo; " . $h->lang['messaging_view_message'];
                break;
        }
    }
    
    
    /**
     * Display pages
     */
    public function theme_index_main($h)
    {
        if (isset($h->vars['user']->id) && ($h->currentUser->id != $h->vars['user']->id)) { return false; }
            
        switch ($h->pageName)
        {
            case 'inbox':
                $h->displayTemplate('messaging_inbox');
                return true;
                break;
            case 'outbox':
                $h->displayTemplate('messaging_outbox');
                return true;
                break;
            case 'compose':
                $h->displayTemplate('messaging_compose');
                return true;
                break;
            case 'show_message':
                if (!$h->vars['message_id']) { return false; }
                $h->displayTemplate('messaging_show_message');
                return true;
                break;
        }
    }
    
    
    /**
     * Clear deleted messages on Maintenance page
     */
    public function admin_maintenance_database($h)
    {
        echo "<li><a href='" . BASEURL . "admin_index.php?page=maintenance&amp;action=clear_messages'>";
        echo $h->lang["messaging_maintenance_clear_messages"] . "</a> - ";
        echo $h->lang["messaging_maintenance_clear_messages_desc"];
        echo "</li>";
    }
    
    
    /**
     * Delete messages that are no longer shown in either inbox or outbox
     */
    public function admin_maintenance_top($h)
    {
        if (($h->pageName == 'maintenance') && ($h->cage->get->testAlnumLines('action') == 'clear_messages')) {
            $sql = "DELETE FROM " . DB_PREFIX . "messaging WHERE message_inbox = %d AND message_outbox = %d";
            $h->db->query($h->db->prepare($sql, 0, 0));
            
            // optimize the table
            $h->db->query("OPTIMIZE TABLE " . DB_PREFIX . "messaging");
            
            $h->message = $h->lang['messaging_maintenance_table_cleaned'];
            $h->messageType = 'green';
            $h->showMessage();
            return true;
        }
    }
    
    
    /**
     * User Settings - before saving
     */
    public function user_settings_pre_save($h)
    {
        if ($h->currentUser->getPermission('can_do_messaging') == 'no') { return false; }
        
        // Email notification of private messages:
        if ($h->cage->post->getAlpha('pm_notify') == 'yes') { 
            $h->vars['settings']['pm_notify'] = "checked"; 
        } else { 
            $h->vars['settings']['pm_notify'] = "";
        }
    }
    
    
    /**
     * User Settings - fill the form
     */
    public function user_settings_fill_form($h)
    {
        if ($h->currentUser->getPermission('can_do_messaging') == 'no') { return false; }

        if (!isset($h->vars['settings']) || !$h->vars['settings']) { return false; }
        
        if ($h->vars['settings']['pm_notify']) { 
            $h->vars['pm_notify_yes'] = "checked"; 
            $h->vars['pm_notify_no'] = ""; 
        } else { 
            $h->vars['pm_notify_yes'] = ""; 
            $h->vars['pm_notify_no'] = "checked"; 
        }
    }
    
    
    /**
     * User Settings - html for form
     */
    public function user_settings_extra_settings($h)
    {
        if ($h->currentUser->getPermission('can_do_messaging') == 'no') { return false; }

        if (!isset($h->vars['settings']) || !$h->vars['settings']) { return false; }
        
        echo "<tr>\n";
            // Allow email notification of private messages?
        echo "<td>" . $h->lang['users_settings_pm_notification_by_email'] . "</td>\n";
        echo "<td><input type='radio' name='pm_notify' value='yes' " . $h->vars['pm_notify_yes'] . "> " . $h->lang['users_settings_yes'] . " &nbsp;&nbsp;\n";
        echo "<input type='radio' name='pm_notify' value='no' " . $h->vars['pm_notify_no'] . "> " . $h->lang['users_settings_no'] . "</td>\n";
        echo "</tr>\n";
    }
    
    
    /**
     * Check for waiting messages
     */
    public function userauth_checkcookie_success($h)
    {
        if ($h->currentUser->getPermission('can_do_messaging') == 'no') { return false; }
        
        $sql = "SELECT count(*) FROM " . DB_PREFIX . "messaging WHERE message_archived = %s AND message_to = %d AND message_read = %d AND message_inbox = %d";
        $query = $h->db->prepare($sql, 'N', $h->currentUser->id, 0, 1);

        $h->smartCache('on', 'messaging', 10, $query); // start using cache
        $h->vars['messages_waiting'] = $h->db->get_var($query);
        $h->smartCache('off'); // stop using cache  
    }
    
    
    /**
     * Show "You have unread messages in your inbox!" as an announcement
     */
    public function hotaru_announcements($h)
    {
        if ($h->currentUser->getPermission('can_do_messaging') == 'no') { return false; }

        if (isset($h->vars['messages_waiting']) && $h->vars['messages_waiting'] > 0) {
            $inbox_link = "<a href='" . $h->url(array('page'=>'inbox', 'user'=>$h->currentUser->name)) . "'>";
            $inbox_link .= $h->lang['messaging_unread_messages_announcement'] . "</a>";
            array_push($h->vars['hotaru_announcements'], $inbox_link);
        }
    }
    
    
    /**
     * Check for updates to the messages table
     */
    public function smart_cache_sql($h)
    {
        $h->vars['smart_cache_sql'] = "SELECT message_updatedts FROM " . DB_PREFIX . "messaging ORDER BY message_updatedts DESC";
    }
}
?>