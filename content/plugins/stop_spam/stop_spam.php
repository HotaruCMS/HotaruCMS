<?php
/**
 * name: StopSpam
 * description: Checks new users against the StopForumSpam.com blacklist
 * version: 0.4
 * folder: stop_spam
 * class: StopSpam
 * type: antispam
 * requires: users 1.1
 * hooks: install_plugin, users_register_check_blocked, users_register_pre_add_user, users_register_post_add_user, users_email_conf_post_role, user_manager_role, user_manager_details, user_manager_pre_submit_button, user_man_killspam_delete, admin_sidebar_plugin_settings, admin_plugin_settings
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

class StopSpam
{
    protected $ssType   =   'go_pending';   // otherwise 'block_reg'
    /**
     * Default settings on install
     */
    public function install_plugin($h)
    {
        // Default settings 
        if (!$h->getSetting('stop_spam_key')) { $h->updateSetting('stop_spam_key', ''); }
        if (!$h->getSetting('stop_spam_type')) { $h->updateSetting('stop_spam_type', 'go_pending'); }
    }
    
    
    /**
     * Checks user against the StopForumSpam.com blacklist
     */
    public function users_register_check_blocked($h)
    {
        $this->ssType = $h->getSetting('stop_spam_type');
        
        // get user info:
        $username = $h->currentUser->name;
        $email = $h->currentUser->email;
        $ip = $h->cage->server->testIp('REMOTE_ADDR');
        
        // If any variable is empty or the IP is "localhost", skip using this plugin.
        if (!$username || !$email || !$ip || ($ip == '127.0.0.1')) { return false; }

        // Include our StopSpam class:
        require_once(PLUGINS . 'stop_spam/libs/StopSpam.php');
        $spam = new StopSpamFunctions();
        $ip_blacklisted = $spam->isSpammer('ip', $ip);
        $username_blacklisted = $spam->isSpammer('username', $username);
        $email_blacklisted = $spam->isSpammer('email', $email);
        
        $spammer = false;
        $flags = array();
        
        if ($ip_blacklisted) {
            array_push($flags, 'IP address');
            $spammer = true;
        }
        
        if ($username_blacklisted) {
            array_push($flags, 'username');
            $spammer = true;
        }
        
        if ($email_blacklisted) {
            array_push($flags, 'email address');
            $spammer = true;
        }
        
        if ($spammer)
        { 
            // store flags - used when type is "go_pending"
            $h->vars['reg_flags'] = $flags;
            
            // if type is "block_reg", provide a way to tell the Users plugin:
            if ($this->ssType == 'block_reg') {
                $h->vars['block'] = true;
            }
        } 
        else 
        {
            // safe user, do nothing...
        }

    }
    
    
    /**
     * Set a spammer's role to "pending"
     */
    public function users_register_pre_add_user($h)
    {
        if ($h->vars['reg_flags']) {
            $h->currentUser->role = 'pending';
        }
    }
    
    
    /**
     * Adds any spam details to the usermeta table
     *
     * @param array $vars - contains the last insert id
     */
    public function users_register_post_add_user($h, $vars)
    {
        $last_insert_id = $vars[0];
        
        if ($h->currentUser->vars['reg_flags']) {
            $sql = "INSERT INTO " . TABLE_USERMETA . " (usermeta_userid, usermeta_key, usermeta_value, usermeta_updateby) VALUES(%d, %s, %s, %d)";
            $h->db->query($h->db->prepare($sql, $last_insert_id, 'stop_spam_flags', serialize($h->currentUser->vars['reg_flags']), $last_insert_id));
        }
        
        /* Registration continues as normal, so the user may have to validate their email address. */
    }
    
    
    /**
     * This function is called after the email confirmtaion function assigns the user a new role.
     * We want to override the role, forcing the user to be "pending";
     */
    public function users_email_conf_post_role($h)
    {
        // Check to see if this user has any stop_spam_flags:
        $sql = "SELECT usermeta_value FROM " . TABLE_USERMETA . " WHERE usermeta_userid = %d AND usermeta_key = %s";
        $flags = $h->db->get_var($h->db->prepare($sql, $h->currentUser->id, 'stop_spam_flags'));
        
        if ($flags) {  $h->currentUser->role = 'pending'; }
    }
    
    
    /**
     * Adds an icon in User Manager about the user being flagged
     */
    public function user_manager_role($h)
    {
        list ($icons, $user_role, $user) = $h->vars['user_manager_role'];
        
        // Check to see if this user has any stop_spam_flags:
        $sql = "SELECT usermeta_value FROM " . TABLE_USERMETA . " WHERE usermeta_userid = %d AND usermeta_key = %s";
        $flags = $h->db->get_var($h->db->prepare($sql, $user->user_id, 'stop_spam_flags'));
        $h->vars['stop_spam_flags'] = $flags;
        
        if ($flags) {
            $flags = unserialize($flags);
            $title = $h->lang['stop_spam_flagged_reasons'];
            foreach ($flags as $flag) {
                $title .= $flag . ", ";
            }
            $title = rstrtrim($title, ", ");
            $icons .= " <img src = '" . BASEURL . "content/plugins/user_manager/images/flag_red.png' title='" . $title . "'>";
            $h->vars['user_manager_role'] = array($icons, $user_role, $user);
        }
    }
    
    
    /**
     * Adds a note in User Manager about the user being flagged
     */
    public function user_manager_details($h)
    {
        list ($output, $user) = $h->vars['user_manager_details'];
        
        // Check to see if this user has any stop_spam_flags:
        $sql = "SELECT usermeta_value FROM " . TABLE_USERMETA . " WHERE usermeta_userid = %d AND usermeta_key = %s";
        
        if (!isset($h->vars['stop_spam_flags'])) {
            $flags = $h->db->get_var($h->db->prepare($sql, $user->user_id, 'stop_spam_flags'));
        } else {
            $flags = $h->vars['stop_spam_flags']; // retrieve from memory
        }
        
        if ($flags) {
            $flags = unserialize($flags);  
            $output .= "<br /><b>" . $h->lang['stop_spam_flagged_reasons'] . "</b><span style='color: red;'>";
            foreach ($flags as $flag) {
                $output .= $flag . ", ";
            }
            $output = rstrtrim($output, ", ");
            $output .= "</span>";
            $h->vars['user_manager_details'] = array($output);
        }
    }
    
    
    /**
     * Option to add deleted or killspammed users to the StopForumSpam.com database
     */
    public function user_manager_pre_submit_button($h)
    {
        echo "&nbsp;&nbsp;&nbsp;&nbsp; <input type='checkbox' name='stopspam'> ";
        echo $h->lang['stop_spam_add_database'] . "<br />";
    }
    
    /**
     * Add deleted or killspammed user to the StopForumSpam.com database
     */
    public function user_man_killspam_delete($h, $vars)
    {
        if (!$h->cage->get->keyExists('stopspam')) { return false; }
        
        $key = $h->getSetting('stop_spam_key', 'stop_spam'); // used for reporting spammers
        
        if (!$key) { return false; } // can't use this plugin without an API key from StopForumSpam.com
        
        // Include our StopSpam class:
        require_once(PLUGINS . 'stop_spam/libs/StopSpam.php');
        $spam = new StopSpamFunctions();
        $user = $vars[0];
        $spam->addSpammer($user->ip, $user->name, $user->email, $key);
        
        // known spammer, already in database, use for testing:
        //$spam->addSpammer('188.92.76.35', 'Priesseap', 'turkish.zashek.an@gmail.com', $key);
    }

}

?>