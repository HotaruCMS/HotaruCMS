<?php
/**
 * name: Submit Light
 * description: Reduces Submit to two steps
 * version: 0.3
 * folder: submit_light
 * class: SubmitLight
 * hooks: theme_index_top
 * requires: submit 1.9
 * extends: Submit
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

class SubmitLight extends Submit
{
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
                        $post_id = $h->post->id; // got this from addPost in Post.php
                        $h->readPost($post_id);
                        $h->changePostStatus('new');
                        
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
                        
                        $h->pluginHook('submit_confirm_pre_trackback'); // Vote uses this to change pst status and redirection
        
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
                $h->readPost($post_id);
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

                $h->pluginHook('submit_confirm_pre_trackback'); // Vote uses this to change pst status and redirection

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
}
?>
