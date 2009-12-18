<?php
/**
 * name: SB Submit
 * description: Social Bookmarking submit - Enables post submission
 * version: 0.1
 * folder: sb_submit
 * class: SbSubmit
 * type: addpost
 * hooks: install_plugin, theme_index_top, header_include, header_include_raw, navigation, admin_header_include_raw, theme_index_main, admin_plugin_settings, admin_sidebar_plugin_settings
 * requires: sb_base 0.1
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

class SbSubmit
{
    public $hotaru = '';   // access Hotaru functions using $this->hotaru
    
    /**
     * Install Submit settings if they don't already exist
     */
    public function install_plugin()
    {
        // Permissions
        $site_perms = $this->hotaru->getDefaultPermissions('all');
        if (!isset($site_perms['can_submit'])) { 
            $perms['options']['can_submit'] = array('yes', 'no', 'mod');
            //$perms['options']['can_edit_posts'] = array('yes', 'no', 'own');
            //$perms['options']['can_delete_posts'] = array('yes', 'no');
            $perms['options']['can_post_without_link'] = array('yes', 'no');
            
            $perms['can_submit']['admin'] = 'yes';
            $perms['can_submit']['supermod'] = 'yes';
            $perms['can_submit']['moderator'] = 'yes';
            $perms['can_submit']['member'] = 'yes';
            $perms['can_submit']['undermod'] = 'mod';
            $perms['can_submit']['default'] = 'no';
            
            /*
            $perms['can_edit_posts']['admin'] = 'yes';
            $perms['can_edit_posts']['supermod'] = 'yes';
            $perms['can_edit_posts']['moderator'] = 'yes';
            $perms['can_edit_posts']['member'] = 'own';
            $perms['can_edit_posts']['undermod'] = 'own';
            $perms['can_edit_posts']['default'] = 'no';
            
            $perms['can_delete_posts']['admin'] = 'yes';
            $perms['can_delete_posts']['supermod'] = 'yes';
            $perms['can_delete_posts']['default'] = 'no';
            */
            
            $perms['can_post_without_link']['admin'] = 'yes';
            $perms['can_post_without_link']['supermod'] = 'yes';
            $perms['can_post_without_link']['default'] = 'no';
            
            $this->hotaru->updateDefaultPermissions($perms);
        }
        

        // Default settings 
        $submit_settings = $this->hotaru->getSerializedSettings();
        
        //if (!isset($submit_settings['enabled'])) { $submit_settings['enabled'] = "checked"; }
        if (!isset($submit_settings['content_length'])) { $submit_settings['content_length'] = 50; }
        if (!isset($submit_settings['summary'])) { $submit_settings['summary'] = "checked"; }
        if (!isset($submit_settings['summary_length'])) { $submit_settings['summary_length'] = 200; }
        //if (!isset($submit_settings['posts_per_page'])) { $submit_settings['posts_per_page'] = 10; }
        if (!isset($submit_settings['allowable_tags'])) { $submit_settings['allowable_tags'] = "<b><i><u><a><blockquote><strike>"; }
        if (!isset($submit_settings['url_limit'])) { $submit_settings['url_limit'] = 0; }
        if (!isset($submit_settings['daily_limit'])) { $submit_settings['daily_limit'] = 0; }
        if (!isset($submit_settings['freq_limit'])) { $submit_settings['freq_limit'] = 0; }
        if (!isset($submit_settings['set_pending'])) { $submit_settings['set_pending'] = ""; } // sets all new posts to pending 
        if (!isset($submit_settings['x_posts'])) { $submit_settings['x_posts'] = 1; }
        if (!isset($submit_settings['email_notify'])) { $submit_settings['email_notify'] = ""; }
        if (!isset($submit_settings['email_notify_mods'])) { $submit_settings['email_notify_mods'] = array(); }
        //if (!isset($submit_settings['archive'])) { $submit_settings['archive'] = "no_archive"; }
        
        $this->hotaru->updateSetting('sb_submit_settings', serialize($submit_settings));
        
        // Add "open in new tab" option to the default user settings
        $base_settings = $this->hotaru->getDefaultSettings('base'); // originals from plugins
        $site_settings = $this->hotaru->getDefaultSettings('site'); // site defaults updated by admin
        if (!isset($base_settings['new_tab'])) { 
            $base_settings['new_tab'] = ""; $site_settings['new_tab'] = "";
            $this->hotaru->updateDefaultSettings($base_settings, 'base'); 
            $this->hotaru->updateDefaultSettings($site_settings, 'site');
        }
        if (!isset($base_settings['link_action'])) { 
            $base_settings['link_action'] = ""; $site_settings['link_action'] = "";
            $this->hotaru->updateDefaultSettings($base_settings, 'base'); 
            $this->hotaru->updateDefaultSettings($site_settings, 'site');
        }
    }
    
    
    /**
     * Determine the pageType
     */
    public function theme_index_top()
    {
        // Include SbSubmitFunctions if this is page name contains 'submit' 
        if (strpos($this->hotaru->pageName, 'submit') !== false) {
            include_once(PLUGINS . 'sb_submit/libs/SbSubmitFunctions.php'); // used for submit functions
        }
        
        switch ($this->hotaru->pageName)
        {
            // Submit Step 1
            case 'submit':
            case 'submit1':
                $this->hotaru->pageName = 'submit1';
                $this->hotaru->pageType = 'submit';
                $this->hotaru->pageTitle = $this->hotaru->lang["submit_step1"];
                $funcs = new SbSubmitFunctions();
                $submitted = $funcs->checkSubmitted($this->hotaru, 'submit1');
                if ($submitted) {
                    $key = $funcs->saveSubmitted($this->hotaru, 'submit1');
                    $errors = $funcs->checkErrors($this->hotaru, 'submit1', $key);
                    if (!$errors) {
                        header("Location: " . $this->hotaru->url(array('page'=>'submit2', 'key'=>$key)));
                        exit;
                    }
                }
                break;
                
            // Submit Step 2 - checks the results of step 1 and prepares the step 2 form:
            case 'submit2':
                $this->hotaru->pageType = 'submit';
                $this->hotaru->pageTitle = $this->hotaru->lang["submit_step2"];
                break;
                
            // Submit Step 3
            case 'submit3':
                $this->hotaru->pageType = 'submit';
                $this->hotaru->pageTitle = $this->hotaru->lang["submit_step3"];
                break;
                
            // Submit Confirm
            case 'submit_confirm':
                // redirect to Latest page
                break;
        }
        
        if ($this->hotaru->pageType != 'submit') { return false; }
        
        // If the user is not logged in...
        if (!$this->hotaru->currentUser->loggedIn) {
            $return = urlencode($this->hotaru->url(array('page'=>'submit'))); // return user here after login
            header("Location: " . $this->hotaru->url(array('page'=>'login', 'return'=>$return)));
        }
    }


    /**
     * Include jQuery for hiding and showing email options in plugin settings
     */
    public function admin_header_include_raw()
    {
        if ($this->hotaru->isSettingsPage('submit')) {
            echo "<script type='text/javascript'>\n";
            echo "$(document).ready(function(){\n";
                echo "$('#email_notify').click(function () {\n";
                echo "$('#email_notify_options').slideToggle();\n";
                echo "});\n";
            echo "});\n";
            echo "</script>\n";
        }
    }
    
    
    /**
     * Output raw javascript directly to the header (instead of caching a .js file)
     */
    public function header_include_raw()
    {
        /* This code (courtesy of Pligg.com and SocialWebCMS.com) pops up a 
           box asking the user of they are sure they want to leave the page
           without submitting their post. */
           
        if ($this->hotaru->pageName == 'submit2' || $this->hotaru->pageName == 'submit3') {
            echo '
                <script type="text/javascript">
        
                var safeExit = false;
            
                window.onbeforeunload = function (event) 
                {
                    if (safeExit)
                        return;
        
                    if (!event && window.event) 
                              event = window.event;
                              
                       event.returnValue = "' . $this->hotaru->lang['submit_accidental_click'] . '";
                }
                
                </script>
            ';
        }
    }
    
    
    /**
     * Add "Submit" to the navigation bar
     */
    public function navigation()
    {
        // return false if not logged in or submission disabled
        if (!$this->hotaru->currentUser->loggedIn) { return false; }
        //if (!$this->hotaru->post->useSubmission) { return false; }
        
        // highlight "Submit" as active tab
        if ($this->hotaru->pageType == 'submit') { $status = "id='navigation_active'"; } else { $status = ""; }
        
        // display the link in the navigation bar
        echo "<li><a  " . $status . " href='" . $this->hotaru->url(array('page'=>'submit')) . "'>" . $this->hotaru->lang['submit_submit_a_story'] . "</a></li>\n";
    }
    
    
    /**
     * Determine which template to show and do preparation of variables, etc.
     */
    public function theme_index_main()
    {
        switch ($this->hotaru->pageName)
        {
            // Submit Step 1
            case 'submit1':
                $this->hotaru->displayTemplate('submit_step1');
                return true;
                break;
                
            // Submit Step 2
            case 'submit2':
                $this->hotaru->displayTemplate('submit_step2');
                return true;
                break;
                
            // Submit Step 3
            case 'submit3':
                $this->hotaru->displayTemplate('submit_step3');
                return true;
                break;
        }
    }


}
?>