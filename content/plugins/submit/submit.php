<?php
/**
 * name: Submit
 * description: Social Bookmarking submit - Enables post submission
 * version: 2.3
 * folder: submit
 * class: Submit
 * type: post
 * hooks: install_plugin, admin_theme_index_top, theme_index_top, header_include, header_include_raw, navigation, admin_header_include_raw, breadcrumbs, theme_index_main, admin_plugin_settings, admin_sidebar_plugin_settings
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

class Submit
{
    /**
     * Install Submit settings if they don't already exist
     */
    public function install_plugin($h)
    {
        // Permissions
        $site_perms = $h->getDefaultPermissions('all');
        if (!isset($site_perms['can_submit'])) { 
            $perms['options']['can_submit'] = array('yes', 'no', 'mod');
            $perms['options']['can_edit_posts'] = array('yes', 'no', 'own');
            $perms['options']['can_delete_posts'] = array('yes', 'no');
            $perms['options']['can_post_without_link'] = array('yes', 'no');
            
            $perms['can_submit']['admin'] = 'yes';
            $perms['can_submit']['supermod'] = 'yes';
            $perms['can_submit']['moderator'] = 'yes';
            $perms['can_submit']['member'] = 'yes';
            $perms['can_submit']['undermod'] = 'mod';
            $perms['can_submit']['default'] = 'no';
            
            $perms['can_edit_posts']['admin'] = 'yes';
            $perms['can_edit_posts']['supermod'] = 'yes';
            $perms['can_edit_posts']['moderator'] = 'yes';
            $perms['can_edit_posts']['member'] = 'own';
            $perms['can_edit_posts']['undermod'] = 'own';
            $perms['can_edit_posts']['default'] = 'no';
            
            $perms['can_delete_posts']['admin'] = 'yes';
            $perms['can_delete_posts']['supermod'] = 'yes';
            $perms['can_delete_posts']['default'] = 'no';
            
            $perms['can_post_without_link']['admin'] = 'yes';
            $perms['can_post_without_link']['supermod'] = 'yes';
            $perms['can_post_without_link']['default'] = 'no';
            
            $h->updateDefaultPermissions($perms);
        }
        

        // Default settings 
        $submit_settings = $h->getSerializedSettings();
        
        if (!isset($submit_settings['enabled'])) { $submit_settings['enabled'] = "checked"; }
        if (!isset($submit_settings['content'])) { $submit_settings['content'] = "checked"; }
        if (!isset($submit_settings['content_length'])) { $submit_settings['content_length'] = 50; }
        if (!isset($submit_settings['summary'])) { $submit_settings['summary'] = "checked"; }
        if (!isset($submit_settings['summary_length'])) { $submit_settings['summary_length'] = 200; }
        if (!isset($submit_settings['allowable_tags'])) { $submit_settings['allowable_tags'] = "<b><i><u><a><blockquote><del>"; }
        if (!isset($submit_settings['url_limit'])) { $submit_settings['url_limit'] = 0; }
        if (!isset($submit_settings['daily_limit'])) { $submit_settings['daily_limit'] = 0; }
        if (!isset($submit_settings['freq_limit'])) { $submit_settings['freq_limit'] = 0; }
        if (!isset($submit_settings['set_pending'])) { $submit_settings['set_pending'] = ""; } // sets all new posts to pending 
        if (!isset($submit_settings['x_posts'])) { $submit_settings['x_posts'] = 1; }
        if (!isset($submit_settings['email_notify'])) { $submit_settings['email_notify'] = ""; }
        if (!isset($submit_settings['email_notify_mods'])) { $submit_settings['email_notify_mods'] = array(); }
        if (!isset($submit_settings['categories'])) { $submit_settings['categories'] = 'checked'; }
        if (!isset($submit_settings['tags'])) { $submit_settings['tags'] = 'checked'; }
        if (!isset($submit_settings['max_tags'])) { $submit_settings['max_tags'] = 100; } // total length in characters
        
        $h->updateSetting('submit_settings', serialize($submit_settings));
    }
    
    
    /**
     * Determine whether or not to show "Submit" in the admin navigation bar
     */
    public function admin_theme_index_top($h)
    {
        /* get submit settings - so we can show or hide "Submit" in the Admin navigation bar. */
        $h->vars['submit_settings'] = $h->getSerializedSettings('submit');
        $h->vars['submission_closed'] = false;
        if (!$h->vars['submit_settings']['enabled']) { $h->vars['submission_closed'] = true; }
    }
    
    
    /**
     * Determine the submission step and perform necessary actions
     */
    public function theme_index_top($h)
    {
        /* get submit settings - available to all because we need to know if submission is 
           open or closed so we can show or hide the navigation bar "Submit" link. */
        $h->vars['submit_settings'] = $h->getSerializedSettings('submit');
        $h->vars['submission_closed'] = false;
        if (!$h->vars['submit_settings']['enabled']) { $h->vars['submission_closed'] = true; }
        
        // Exit if this page name does not contain 'submit' and isn't edit_post
        if ((strpos($h->pageName, 'submit') === false) && ($h->pageName != 'edit_post'))
        {
            return false;
        }
        
        // check user has permission to post. Exit if not.
        $h->vars['posting_denied'] = false;
        if ($h->currentUser->getPermission('can_submit') == 'no') {
            // No permission to submit
            $h->messages[$h->lang['submit_no_post_permission']] = "red";
            $h->vars['posting_denied'] = true;
            $h->vars['can_edit'] = false;
            $h->vars['post_deleted'] = false;
            return false;
        }
        
        // redirect to log in page if not logged in
        if (!$h->currentUser->loggedIn) { 
            $return = urlencode($h->url(array('page'=>'submit'))); // return user here after login
            header("Location: " . $h->url(array('page'=>'login', 'return'=>$return)));
            return false; 
        }
        
        // return false if submission is closed
        if ($h->vars['submission_closed']) {
            // Submission is closed
            $h->messages[$h->lang["submit_posting_closed"]] = "red";
            return false;
        }
        
        // Include SubmitFunctions
        include_once(PLUGINS . 'submit/libs/SubmitFunctions.php'); // used for submit functions

        // get functions
        $funcs = new SubmitFunctions();

        switch ($h->pageName)
        {
            // SUBMIT STEP 1
            case 'submit':
            case 'submit1':
            
                // set properties
                $h->pageName = 'submit1';
                $h->pageType = 'submit';
                $h->pageTitle = $h->lang["submit_step1"];
                
                // check if data has been submitted
                $submitted = $funcs->checkSubmitted($h, 'submit1');
                
                // save/reload data, then go to step 2 when no more errors
                if ($submitted) {
                    $key = $funcs->processSubmitted($h, 'submit1');
                    $errors = $funcs->checkErrors($h, 'submit1', $key);
                    if (!$errors) {
                        $redirect = htmlspecialchars_decode($h->url(array('page'=>'submit2', 'key'=>$key)));
                        header("Location: " . $redirect);
                        exit;
                    }
                }
                break;
                
            // SUBMIT STEP 2 
            case 'submit2':
            
                // set properties
                $h->pageType = 'submit';
                $h->pageTitle = $h->lang["submit_step2"];
                
                // check if data has been submitted
                $submitted = $funcs->checkSubmitted($h, 'submit2');
                
                // not submitted so reload data from step 1 (or step 2 if editing)
                if (!$submitted) {
                    // if coming from step 1, get the key from the url
                    $key = $h->cage->get->testAlnum('key');
                    
                    // use the key in the step 2 form
                    $h->vars['submit_key'] = $key; 
                    
                    // load submitted data:
                    $submitted_data = $funcs->loadSubmitData($h, $key);
                    
                    // merge defaults from "checkSubmitted" with $submitted_data...
                    $merged_data = array_merge($h->vars['submitted_data'], $submitted_data);
                    $h->vars['submitted_data'] = $merged_data;
                    
                    // not sure if this is completely necessary, but it's worth having...
                    if ($h->vars['submitted_data']['submit_id']) {
                        $h->post->id = $h->vars['submitted_data']['submit_id'];
                        $h->post->readPost($h);
                    }
                }
                
                // submitted so save data and proceed to step 3 when no more errors
                if ($submitted) {
                    $key = $funcs->processSubmitted($h, 'submit2');
                    $errors = $funcs->checkErrors($h, 'submit2', $key);
                    if (!$errors) {
                        $funcs->processSubmission($h, $key);
                        $postid = $h->post->id; // got this from addPost in Post.php
                        $link = $h->url(array('page'=>'submit3', 'postid'=>$postid,'key'=>$key));
                        $redirect = htmlspecialchars_decode($link);
                        header("Location: " . $redirect);
                        exit;
                    }
                    $h->vars['submit_key'] = $key; // used in the step 2 form
                }
                break;
                
            // SUBMIT STEP 3
            case 'submit3':
            
                $h->pageType = 'submit';
                $h->pageTitle = $h->lang["submit_step3"];
                
                // Check if the Edit button has been clicked
                $funcs = new SubmitFunctions();
                $submitted = $funcs->checkSubmitted($h, 'submit3');
                
                // Edit button pressed so save data with newly assigned post id and go back to step 2
                if ($submitted) {
                    $key = $funcs->processSubmitted($h, 'submit3');
                    $funcs->processSubmission($h, $key);
                    $link = $h->url(array('page'=>'submit2', 'key'=>$key));
                    $redirect = htmlspecialchars_decode($link);
                    header("Location: " . $redirect);
                    exit;
                }
                
                // get key from the url for the submit 3 form
                $key = $h->cage->get->testAlnum('key');
                $h->vars['submit_key'] = $key; 
                
                // get post id from the url and read the post for the preview
                $h->post->id = $h->cage->get->testInt('postid');
                $h->readPost();

                break;
                
            // SUBMIT CONFIRM
            case 'submit_confirm':
            
                $post_id = $h->cage->post->testInt('submit_post_id');
                $h->readPost($post_id); // be careful! The results are cached and returned on next readPost 
                $h->changePostStatus('new');
                $h->post->status = 'new'; // this fixes a caching-related problem by forcing the new status on the post property
                
                $return = 0; // will return false later if set to 1.
                
                $h->pluginHook('submit_step_3_pre_trackback'); // Akismet uses this to change the status
                
                // set to pending?
                $set_pending = $h->vars['submit_settings']['set_pending'];

                if ($set_pending == 'some_pending') {
                    $posts_approved = $h->postsApproved();
                    $x_posts_needed = $h->vars['submit_settings']['x_posts'];
                }


                // Set to pending is the user's permissions for "can_submit" are "mod" OR
                // if "Put all new posts in moderation" has been checked in Admin->Submit
                if (   ($h->currentUser->getPermission('can_submit') == 'mod')
                    || ($set_pending == 'all_pending')
                    || (($set_pending == 'some_pending') && ($posts_approved <= $x_posts_needed)))
                {
                // Submitted posts given 'pending' for this user
                    $h->changePostStatus('pending');
                    $h->messages[$h->lang['submit_moderation']] = 'green';
                    $return = 1; // will return false just after we notify admins of the post (see about 10 lines down)
                }

                $h->pluginHook('submit_confirm_pre_trackback'); // Vote uses this to change post status and redirection

                // notify chosen mods of new post by email if enabled and UserFunctions file exists
                if (($h->vars['submit_settings']['email_notify']) && (file_exists(PLUGINS . 'users/libs/UserFunctions.php')))
                {
                    require_once(PLUGINS . 'users/libs/UserFunctions.php');
                    $uf = new UserFunctions();
                    $uf->notifyMods($h, 'post', $h->post->status, $h->post->id);
                }
                
                if ($return == 1) { return false; } // post is pending so we don't want to send a trackback. Return now.
                
                $h->sendTrackback();
                
                if (isset($h->vars['submit_redirect'])) {
                    header("Location: " . $h->vars['submit_redirect']);
                } else {
                    header("Location: " . $h->url(array('page'=>'latest')));    // Go to the Latest page
                }
                break;
                
            // EDIT POST (after submission)
            case 'edit_post':
                $h->pageType = 'submit';
                $h->pageTitle = $h->lang["submit_edit_title"];
                
                // get the post id and read in the data
                if ($h->cage->get->keyExists('post_id')) { // first time from url
                    $h->post->id = $h->cage->get->testInt('post_id');
                    $h->readPost();
                } elseif($h->cage->post->keyExists('submit_post_id')) { // from submit form (used when errors)
                    $h->post->id = $h->cage->post->testInt('submit_post_id');
                    $h->readPost();
                }
                
                // authenticate...
                $can_edit = false;
                if ($h->currentUser->getPermission('can_edit_posts') == 'yes') { $can_edit = true; }
                if (($h->currentUser->getPermission('can_edit_posts') == 'own') && ($h->currentUser->id == $h->post->author)) { $can_edit = true; }
                $h->vars['can_edit'] = $can_edit; // used in theme_index_main()
                
                if (!$can_edit) {
                    $h->messages[$h->lang["submit_no_edit_permission"]] = "red";
                    return false;
                    exit;
                }

                // check if data has been submitted
                $submitted = $funcs->checkSubmitted($h, 'edit_post');
                
                // if being deleted...
                $h->vars['post_deleted'] = false;
                if ($h->cage->get->getAlpha('action') == 'delete') {
                    if ($h->currentUser->getPermission('can_delete_posts') == 'yes') { // double-checking
                        $post_id = $h->cage->get->testInt('post_id');
                        $h->readPost($post_id); 
                        $h->pluginHook('submit_edit_delete'); // Akismet uses this to report the post as spam
                        $h->deletePost(); 
                        $h->messages[$h->lang["submit_edit_deleted"]] = 'red';
                        $h->vars['post_deleted'] = true;
                        break;
                    }
                }
                
                // if form has been submitted...
                if ($submitted) {
                    $key = $funcs->processSubmitted($h, 'edit_post');
                    $errors = $funcs->checkErrors($h, 'edit_post', $key);
                    if (!$errors) {
                        $funcs->processSubmission($h, $key);
                        if ($h->cage->post->testAlnumLines('from') == 'post_man')
                        {
                            // Build the redirect link to send us back to Post Manager
                            
                            $redirect = BASEURL . "admin_index.php?page=plugin_settings&plugin=post_manager";
                            if ($h->cage->post->testAlnumLines('post_status_filter')) {
                                $redirect .= "&type=filter";
                                $redirect .= "&post_status_filter=" . $h->cage->post->testAlnumLines('post_status_filter');
                            }
                            if ($h->cage->post->sanitizeTags('search_value')) {
                                $redirect .= "&type=search";
                                $redirect .= "&search_value=" . $h->cage->post->sanitizeTags('search_value');
                            }
                            $redirect .= "&pg=" . $h->cage->post->testInt('pg');
                            header("Location: " . $redirect);    // Go back to where we were in Post Manager
                            exit;
                        }
                        else 
                        {
                            $redirect = htmlspecialchars_decode($h->url(array('page'=>$h->post->id)));
                            header("Location: " . $redirect);
                            exit;
                        }
                    }
                    // load submitted data:
                    $submitted_data = $funcs->loadSubmitData($h, $key);
                }
                
            break;
        }
    }


    /**
     * Include jQuery for hiding and showing email options in plugin settings
     */
    public function admin_header_include_raw($h)
    {
        if ($h->isSettingsPage('submit')) {
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
    public function header_include_raw($h)
    {
        /* This code (courtesy of Pligg.com and SocialWebCMS.com) pops up a 
           box asking the user of they are sure they want to leave the page
           without submitting their post. */
           
        if ($h->pageName == 'submit2' || $h->pageName == 'submit3') {
            echo '
                <script type="text/javascript">
        
                var safeExit = false;
            
                window.onbeforeunload = function (event) 
                {
                    if (safeExit)
                        return;
        
                    if (!event && window.event) 
                              event = window.event;
                              
                       event.returnValue = "' . $h->lang['submit_accidental_click'] . '";
                }
                
                </script>
            ';
        }
    }
    
    
    /**
     * Add "Submit" to the navigation bar
     */
    public function navigation($h)
    {
        // return false if not logged in or submission disabled
        if (!$h->currentUser->loggedIn) { return false; }
        if (isset($h->vars['submission_closed']) && $h->vars['submission_closed'] == true) { return false; }
        
        // highlight "Submit" as active tab
        if ($h->pageType == 'submit') { $status = "id='navigation_active'"; } else { $status = ""; }
        
        // display the link in the navigation bar
        echo "<li><a  " . $status . " href='" . $h->url(array('page'=>'submit')) . "'>" . $h->lang['submit_submit_a_story'] . "</a></li>\n";
    }
    
    
    /**
     * Replace the default breadcrumbs in Edit Post
     */
    public function breadcrumbs($h)
    {
        if ($h->pageName == 'edit_post') {
            $post_link = "<a href='" . $h->url(array('page'=>$h->post->id)) . "'>";
            $post_link .= $h->post->title . "</a>";
            $h->pageTitle = $h->pageTitle . " &raquo; " . $post_link;
        }
    }
    
    
    /**
     * Determine which template to show and do preparation of variables, etc.
     */
    public function theme_index_main($h)
    {
        // show message and exit if posting denied (determined in theme_index_top)
        if ($h->pageType == 'submit' && $h->vars['posting_denied']) {
            $h->showMessages();
            return true;
        }
        
        switch ($h->pageName)
        {
            // Submit Step 1
            case 'submit':
            case 'submit1':
            
                if ($h->vars['submission_closed'] || $h->vars['posting_denied']) {
                    $h->showMessages();
                    return true;
                }
            
                // display template
                $h->displayTemplate('submit1');
                return true;
                break;
                
            // Submit Step 2
            case 'submit2':
            
                if ($h->vars['submission_closed'] || $h->vars['posting_denied']) {
                    $h->showMessages();
                    return true;
                }
            
                // settings
                $h->vars['submit_use_content'] = $h->vars['submit_settings']['content'];
                $h->vars['submit_content_length'] = $h->vars['submit_settings']['content_length'];
                $h->vars['submit_use_categories'] = $h->vars['submit_settings']['categories'];
                $h->vars['submit_use_tags'] = $h->vars['submit_settings']['tags'];
                $allowable_tags = $h->vars['submit_settings']['allowable_tags'];
                $h->vars['submit_allowable_tags'] = htmlentities($allowable_tags);
                
                // submitted data
                $h->vars['submit_editorial'] = $h->vars['submitted_data']['submit_editorial'];
                $h->vars['submit_orig_url'] = urldecode($h->vars['submitted_data']['submit_orig_url']);
                $h->vars['submit_title'] = sanitize($h->vars['submitted_data']['submit_title'], 'all');
                $h->vars['submit_content'] = sanitize($h->vars['submitted_data']['submit_content'], 'tags', $allowable_tags);
                $h->vars['submit_post_id'] = $h->vars['submitted_data']['submit_id'];
                $h->vars['submit_category'] = $h->vars['submitted_data']['submit_category'];
                $h->vars['submit_tags'] = sanitize($h->vars['submitted_data']['submit_tags'], 'all');
                
                // strip htmlentities before showing in the form:
                $h->vars['submit_title'] = html_entity_decode($h->vars['submit_title']);
                $h->vars['submit_content'] = html_entity_decode($h->vars['submit_content']);
                $h->vars['submit_tags'] = html_entity_decode($h->vars['submit_tags']);
                
                // build category picker code
                if ($h->vars['submit_use_categories']) {
                    $h->vars['submit_category_picker'] = $this->categoryPicker($h);
                }
                
                // display template
                $h->displayTemplate('submit2');
                return true;
                break;
                
            // Submit Step 3
            case 'submit3':
            
                if ($h->vars['submission_closed'] || $h->vars['posting_denied']) {
                    $h->showMessages();
                    return true;
                }
            
                // need these for the post preview (which uses SB Base's sb_post.php template)
                $h->vars['use_content'] = $h->vars['submit_settings']['content'];
                $h->vars['summary_length'] = $h->vars['submit_settings']['summary_length'];
                $h->vars['editorial'] = true; // this makes the link unclickable
                
                // display template
                $h->displayTemplate('submit3');
                return true;
                break;
                
            // Edit Post
            case 'edit_post':
                if ($h->vars['post_deleted'] || !$h->vars['can_edit']) {
                    $h->showMessages();
                    return true;
                }
                
                // settings
                $h->vars['submit_use_content'] = $h->vars['submit_settings']['content'];
                $h->vars['submit_content_length'] = $h->vars['submit_settings']['content_length'];
                $h->vars['submit_use_categories'] = $h->vars['submit_settings']['categories'];
                $h->vars['submit_use_tags'] = $h->vars['submit_settings']['tags'];
                $allowable_tags = $h->vars['submit_settings']['allowable_tags'];
                $h->vars['submit_allowable_tags'] = htmlentities($allowable_tags);
                
                $h->vars['submit_orig_url'] = $h->post->origUrl;
                $h->vars['submit_title'] = $h->post->title;
                $h->vars['submit_content'] = $h->post->content;
                $h->vars['submit_post_id'] = $h->post->id;
                $h->vars['submit_status'] = $h->post->status;
                $h->vars['submit_category'] = $h->post->category;
                $h->vars['submit_tags'] = $h->post->tags;
                
                $h->vars['submit_editorial'] = $h->vars['submitted_data']['submit_editorial'];
                $h->vars['submit_pm_from'] = $h->vars['submitted_data']['submit_pm_from'];
                $h->vars['submit_pm_search'] = $h->vars['submitted_data']['submit_pm_search']; 
                $h->vars['submit_pm_filter'] = $h->vars['submitted_data']['submit_pm_filter'];
                $h->vars['submit_pm_page'] = $h->vars['submitted_data']['submit_pm_page'];
                
                // strip htmlentities before showing in the form:
                $h->vars['submit_title'] = $h->vars['submit_title'];
                $h->vars['submit_content'] = html_entity_decode($h->vars['submit_content']);
                $h->vars['submit_tags'] = html_entity_decode($h->vars['submit_tags']);
                
                // get status options for admin section
                $h->vars['submit_status_options'] = '';
                if ($h->currentUser->getPermission('can_edit_posts') == 'yes') {
                    $statuses = $h->post->getUniqueStatuses($h); 
                    if ($statuses) {
                        foreach ($statuses as $status) {
                            if ($status != 'unsaved' && $status != 'processing' && $status != $h->vars['submit_status']) { 
                                $h->vars['submit_status_options'] .= "<option value=" . $status . ">" . $status . "</option>\n";
                            }
                        }
                    }
                }
                
                // build category picker code
                if ($h->vars['submit_use_categories']) {
                    $h->vars['submit_category_picker'] = $this->categoryPicker($h);
                }
                
                // display template
                $h->displayTemplate('submit_edit');
                return true;
                break;
                
            // Submitted
            case 'submit_confirm':
                $h->showMessages();
                return true;
                break;
        }
    }

    /**
     * Build code for category picker in submit step 2 and edit post
     */
    public function categoryPicker($h)
    {
        $output = '';
        
        $sql = "SELECT category_name, category_safe_name FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
        $result = $h->db->get_row($h->db->prepare($sql, $h->vars['submit_category']));
        
        if($result) { 
            $category_safe_name = stripslashes(htmlentities(urldecode($result->category_safe_name), ENT_QUOTES,'UTF-8'));
            
            if ($category_safe_name == 'all') { 
                $output .= "<option value='1' selected>" . $h->lang['submit_category_select'] . "</option>\n";
            } else {
                $output .= "<option value=" . $h->vars['submit_category'] . " selected>" . urldecode($result->category_name) . "</option>\n";
            }
        } else {
            $output .= "<option value='1' selected>" . $h->lang['submit_category_select'] . "</option>\n";
        }
        
        $sql = "SELECT category_id, category_name FROM " . TABLE_CATEGORIES . " ORDER BY category_order ASC";
        $cats = $h->db->get_results($h->db->prepare($sql));
        
        if ($cats) {
            foreach ($cats as $cat) {
                if ($cat->category_id != 1) { 
                    $cat_name = stripslashes(htmlentities(urldecode($cat->category_name), ENT_QUOTES,'UTF-8'));
                    $output .= "<option value=" . $cat->category_id . ">" . $cat_name . "</option>\n";
                }
            }
        }
        
        return $output;
    }
}
?>
