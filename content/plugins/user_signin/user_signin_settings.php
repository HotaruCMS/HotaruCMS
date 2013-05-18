<?php
/**
 * User Signin Settings
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
 
class UserSigninSettings
{
     /**
     * Admin settings for the Users plugin
     */
    public function settings($h)
    {
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }    
        
        echo "<h1>" . $h->lang["user_signin_settings_header"] . "</h1>\n";
        
        // Get settings from database if they exist...
        $user_signin_settings = $h->getSerializedSettings();
        
        $recaptcha_enabled = $user_signin_settings['recaptcha_enabled'];
        $emailconf_enabled = $user_signin_settings['emailconf_enabled'];
        $reg_status = $user_signin_settings['registration_status'];
        $email_notify = $user_signin_settings['email_notify'];
        $email_mods = $user_signin_settings['email_notify_mods'];
    
        $h->pluginHook('user_signin_settings_get_values');
        
        //...otherwise set to blank:
        if (!$recaptcha_enabled) { $recaptcha_enabled = ''; }
        if (!$emailconf_enabled) { $emailconf_enabled = ''; }
        if (!$reg_status) { $reg_status = 'member'; }
        if (!$email_notify) { $email_notify = ''; }
        if (!$email_mods) { $email_mods = array(); }
        
        echo "<form name='user_signin_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=user_signin' method='post'>\n";
        
        echo "<p>" . $h->lang["user_signin_settings_instructions"] . "</p><br />";
        
        echo "<b>" . $h->lang["user_signin_settings_registration"] . "</b><br /><br />";
        
        $thisdomain =  rstrtrim(str_replace("http://", "", BASEURL), '/');
        echo "<input type='checkbox' name='rc_enabled' value='enabled' " . $recaptcha_enabled . " >&nbsp;&nbsp;" . $h->lang["user_signin_settings_recaptcha_enable"] . "<br /><br />\n";    

        echo "<input type='checkbox' name='emailconf' value='emailconf' " . $emailconf_enabled . ">&nbsp;&nbsp;" . $h->lang["user_signin_settings_email_conf"] . "<br /><br />\n";

        // reg_status radio buttons:
        switch ($reg_status) {
            case 'pending':
                $checked_rs_pend = 'checked'; $checked_rs_undermod = ''; $checked_rs_member = '';
                break;
            case 'undermod':
                $checked_rs_pend = ''; $checked_rs_undermod = 'checked'; $checked_rs_member = '';
                break;
            default: 
                $checked_rs_pend = ''; $checked_rs_undermod = ''; $checked_rs_member = 'checked';
        }

            echo $h->lang["user_signin_settings_reg_status"] . "\n";
            
            echo "<input type='radio' name='regstatus' value='pending' " . $checked_rs_pend . ">";
            echo " " . $h->lang["user_signin_settings_reg_status_pending"] . " &nbsp;\n";
            
            echo "<input type='radio' name='regstatus' value='undermod' " . $checked_rs_undermod . ">";
            echo " " . $h->lang["user_signin_settings_reg_status_undermod"] . " &nbsp;\n";
            
            echo "<input type='radio' name='regstatus' value='member' " . $checked_rs_member . ">";
            echo " " . $h->lang["user_signin_settings_reg_status_member"] . " &nbsp;<br /><br />\n";
                
        // email_notify:
        echo "<input type='checkbox' name='email_notify' value='email_notify' id='email_notify' " . $email_notify . ">&nbsp;&nbsp;" . $h->lang["user_signin_settings_email_notify"] . "<br /><br />\n";
    
        $admins = $h->getMods('can_access_admin', 'yes');
        if (!$email_notify) { $show_admins = 'display: none;'; } else { $show_admins = ''; }
        echo "<div id='email_notify_options' style='margin-left: 2.0em; " . $show_admins . "'>"; 
        
        if ($admins) {
            echo "<table>\n";
            foreach ($admins as $ad) {
                if (array_key_exists($ad['id'], $email_mods)) { 
                    switch ($email_mods[$ad['id']]['type']) {
                        case 'all':
                            $checked_all = 'checked'; $checked_pend = ''; $checked_none = '';
                            break;
                        case 'pending':
                            $checked_all = ''; $checked_pend = 'checked'; $checked_none = '';
                            break;
                        default:
                            $checked_all = ''; $checked_pend = ''; $checked_none = 'checked';
                    }
                }
                else
                {
                    $checked_all = ''; $checked_pend = ''; $checked_none = 'checked';
                }
                
                echo "<tr>\n";
                echo "<td><b>" . ucfirst($ad['name']) . "</b></td>\n";
                
                echo "<td><input type='radio' name='emailmod[" . $ad['id'] . "][" . $ad['email'] . "]' value='all' " . $checked_all . ">";
                echo " " . $h->lang["user_signin_settings_email_notify_all"] . "</td>\n";
                
                echo "<td><input type='radio' name='emailmod[" . $ad['id'] . "][" . $ad['email'] . "]' value='pending' " . $checked_pend . ">";
                echo " " . $h->lang["user_signin_settings_email_notify_pending"] . "</td>\n";
                
                echo "<td><input type='radio' name='emailmod[" . $ad['id'] . "][" . $ad['email'] . "]' value='none' " . $checked_none . ">";
                echo " " . $h->lang["user_signin_settings_email_notify_none"] . "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n";
        }
        echo "</div>";
                
        $h->pluginHook('user_signin_settings_form');
                
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["user_signin_settings_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Save Users Settings
     */
    public function saveSettings($h)
    {
        // Recaptcha Enabled
        if ($h->cage->post->keyExists('rc_enabled')) { 
            $recaptcha_enabled = 'checked'; 
        } else { 
            $recaptcha_enabled = ''; 
        }
        
        // Email Confirmation Enabled
        if ($h->cage->post->keyExists('emailconf')) { 
            $emailconf_enabled = 'checked'; 
            // update all users who have previously logged in:
            $sql = "UPDATE " . TABLE_USERS . " SET user_email_valid = %d WHERE user_lastlogin > %d";
            $h->db->query($h->db->prepare($sql, 1, 0));
        } else { 
            $emailconf_enabled = ''; 
        }
        
        // Registration auto-pending
        if ($h->cage->post->keyExists('regstatus')) { 
            $reg_status = $h->cage->post->getAlpha('regstatus');
        }         
        
        // Send email notification about newly registered users
        if ($h->cage->post->keyExists('email_notify')) { 
            $email_notify = 'checked'; 
        } else { 
            $email_notify = ''; 
        }
        
        
        // admins to receive above email notification
        if ($h->cage->post->keyExists('emailmod')) 
        {
            $email_mods = array();
            foreach ($h->cage->post->keyExists('emailmod') as $id => $array) {
                $email_mods[$id]['id'] = $id;
                $email_mods[$id]['email'] = key($array);
                $email_mods[$id]['type'] = $array[$email_mods[$id]['email']];
            }
        }
                
                
        $h->pluginHook('users_save_settings');
        
        $user_signin_settings['recaptcha_enabled'] = $recaptcha_enabled;
        $user_signin_settings['emailconf_enabled'] = $emailconf_enabled;
        $user_signin_settings['registration_status'] = $reg_status;
        $user_signin_settings['email_notify'] = $email_notify;
        $user_signin_settings['email_notify_mods'] = $email_mods; //array
        
        $h->updateSetting('user_signin_settings', serialize($user_signin_settings));
        
        $h->message = $h->lang["user_signin_settings_saved"];
        $h->messageType = "green";
        $h->showMessage();
        
        return true;    
    }
}
?>
