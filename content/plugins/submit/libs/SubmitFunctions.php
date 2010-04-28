<?php
/**
 * Submit functions
 * Notes: This file is part of the Submit plugin.
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

class SubmitFunctions
{
     /**
     * Check if a form has been submitted
     *
     * @param $step - submit step
     * @return bool
     */
    public function checkSubmitted($h, $step = 'submit1')
    {
        switch ($step) {
        
            case 'submit1':
            
                // set defaults:
                $h->vars['submitted_data']['submit_editorial'] = false;
                $h->vars['submitted_data']['submit_orig_url'] = '';
                
                // no link necessary?
                if ($h->cage->post->keyExists('no_link')) { return true; }
                
                // form 1 submitted?
                if ($h->cage->post->getAlpha('submit1') == 'true') { return true; }
                
                // EVB / Bookmarklet, etc?
                if ($h->cage->get->keyExists('url')) { return true; }
                
                return false;
                break;
                
            case 'submit2':

                // set defaults:
                $h->vars['submitted_data']['submit_editorial'] = false;
                $h->vars['submitted_data']['submit_orig_url'] = '';
                $h->vars['submitted_data']['submit_title'] = '';
                $h->vars['submitted_data']['submit_content'] = '';
                $h->vars['submitted_data']['submit_id'] = 0;
                $h->vars['submitted_data']['submit_category'] = 1;
                $h->vars['submitted_data']['submit_tags'] = '';
                
                // submit 2 form submitted?
                if ($h->cage->post->getAlpha('submit2') == 'true') { return true; }
                
                return false;
                break;
                
            case 'submit3': // for step 3 EDIT

                // set defaults:
                $h->vars['submitted_data']['submit_editorial'] = false;
                $h->vars['submitted_data']['submit_orig_url'] = '';
                $h->vars['submitted_data']['submit_title'] = '';
                $h->vars['submitted_data']['submit_content'] = '';
                $h->vars['submitted_data']['submit_id'] = 0;
                $h->vars['submitted_data']['submit_category'] = 1;
                $h->vars['submitted_data']['submit_tags'] = '';
                
                // submit 3 edit form submitted? If so, return false so we don't process the data
                if ($h->cage->post->getAlpha('submit3edit') == 'true') { return true; }
                
                return false;
                break;
                
            case 'edit_post':
            
                // set defaults:
                $editorial = (strpos($h->post->origUrl, BASEURL) !== false) ? true : false;
                $h->vars['submitted_data']['submit_editorial'] = $editorial;
                
                // for Post Manager...
                $h->vars['submitted_data']['submit_pm_from'] = '';
                $h->vars['submitted_data']['submit_pm_search'] = ''; 
                $h->vars['submitted_data']['submit_pm_filter'] = '';
                $h->vars['submitted_data']['submit_pm_page'] =  0;
            
                // edit_post form submitted?
                if ($h->cage->post->getAlpha('edit_post') == 'true') { return true; }
                
                return false;
                break;
                
            default:
                return false;
        }
    }
    
    
     /**
     * Get the submitted data and save it
     *
     * @param $step - submit step
     * @return bool
     */
    public function processSubmitted($h, $step = 'submit1')
    {
        switch ($step) {
        
            case 'submit1':
            
                // is "Post without URL" checked?
                if ($h->cage->post->keyExists('no_link')) {
                    $h->vars['submitted_data']['submit_orig_url'] = '';
                    $h->vars['submitted_data']['submit_editorial'] = true;
                    
                // is a url submitted via the form?
                } elseif ($h->cage->post->getAlpha('submit1') == 'true') { 
                    $url = $h->cage->post->testUri('submit_orig_url');
                    if (!$url) { break; }
                    $h->vars['submitted_data']['submit_orig_url'] = $url;
                    $h->vars['submitted_data']['submit_editorial'] = false;
                    $title = $this->fetchTitle($url);
                    if (!$title) { $title = $h->lang["submit_not_found"]; }
                    $h->vars['submitted_data']['submit_title'] = $title;
                    
                // is a url submitted via the url? (i.e. EVB or Bookmarklet)
                } elseif ($h->cage->get->keyExists('url')) { 
                    $url = $h->cage->get->testUri('url');
                    if (!$url) { break; }
                    $h->vars['submitted_data']['submit_orig_url'] = $url;
                    $h->vars['submitted_data']['submit_editorial'] = false;
                    $title = $this->fetchTitle($url);
                    if (!$title) { $title = $h->lang["submit_not_found"]; }
                    $h->vars['submitted_data']['submit_title'] = $title;
                }
                break;
                
            case 'submit2': // also used for coming back from Step 3 to edit the post
                
                if (($h->cage->post->getAlpha('submit2') == 'true') 
                    || ($h->cage->post->getAlpha('submit3edit') == 'true')) { 
                    // get previously submitted_data:
                    $key = $h->cage->post->testAlnum('submit_key'); // from the form
                    $h->vars['submitted_data'] = $this->loadSubmitData($h, $key);
                    // get new (edited) title:
                    $title = $h->cage->post->sanitizeAll('post_title');
                    $h->vars['submitted_data']['submit_title'] = $title;
                    // get content:
                    $allowable_tags = $h->vars['submit_settings']['allowable_tags'];
                    $content = sanitize($h->cage->post->getHtmLawed('post_content'), 'tags', $allowable_tags);
                    $h->vars['submitted_data']['submit_content'] = $content;
                    // get category:
                    $category = $h->cage->post->getInt('post_category');
                    $h->vars['submitted_data']['submit_category'] = $category;
                    // get tags:
                    $tags = sanitize($h->cage->post->noTags('post_tags'), 'tags');
                    $h->vars['submitted_data']['submit_tags'] = $tags;
                    // get post id (if editing)
                    $post_id = $h->cage->post->testInt('submit_post_id');
                    $h->vars['submitted_data']['submit_id'] = $post_id;
                    
                    /* if submitting an editorial, the "self" used instead of a url got changed to the 
                       real url after the addPost function. BUT! When editing, we load the previously saved 
                       temp data, which wipes that url! So, we get it again with its post id */ 
                    if ($post_id && !$h->vars['submitted_data']['submit_orig_url']) {
                        $h->vars['submitted_data']['submit_orig_url'] = $h->url(array('page'=>$post_id));
                    }
                }
                break;
                
            case 'submit3': // when EDIT is clicked 
                
                if ($h->cage->post->getAlpha('submit3edit') == 'true') { 
                    // get previously submitted_data:
                    $key = $h->cage->post->testAlnum('submit_key'); // from the form
                    $h->vars['submitted_data'] = $this->loadSubmitData($h, $key);
                    // get post id (if editing)
                    $post_id = $h->cage->post->testInt('submit_post_id');
                    $h->vars['submitted_data']['submit_id'] = $post_id;
                }
                break;
                
                
            case 'edit_post': // from Edit post page
                
                if ($h->cage->post->getAlpha('edit_post') == 'true') {
                    // get id:
                    $h->vars['submitted_data']['submit_id'] = $h->cage->post->testInt('submit_post_id');
                    
                    // get status
                    if ($h->cage->post->testAlnumLines('post_status')) {
                        $h->vars['submitted_data']['submit_status'] = $h->cage->post->testAlnumLines('post_status');
                    } else {
                        $h->vars['submitted_data']['submit_status'] = $h->post->status;
                    }
                    
                    // get new (edited) title:
                    $title = $h->cage->post->sanitizeAll('post_title');
                    $h->vars['submitted_data']['submit_title'] = $title;
                    
                    // get content:
                    $allowable_tags = $h->vars['submit_settings']['allowable_tags'];
                    $content = sanitize($h->cage->post->getHtmLawed('post_content'), 'tags', $allowable_tags);
                    $h->vars['submitted_data']['submit_content'] = $content;
                    
                    // get category:
                    $category = $h->cage->post->testInt('post_category');
                    $h->vars['submitted_data']['submit_category'] = $category;
                    
                    // get tags:
                    $tags = sanitize($h->cage->post->noTags('post_tags'), 'tags');
                    $h->vars['submitted_data']['submit_tags'] = $tags;
                    
                    // get url if present:
                    if( $url = $h->cage->post->testUri('post_orig_url')) {
                        $h->vars['submitted_data']['submit_orig_url'] = $url;
                    } else {
                        $h->vars['submitted_data']['submit_orig_url'] = $h->post->origUrl;
                    }
                    
                    // from Post Manager...
                    $h->vars['submitted_data']['submit_pm_from'] = $h->cage->post->testAlnumLines('from');
                    $h->vars['submitted_data']['submit_pm_search'] = $h->cage->post->sanitizeTags('search_value'); 
                    $h->vars['submitted_data']['submit_pm_filter'] = $h->cage->post->testAlnumLines('post_status_filter');
                    $h->vars['submitted_data']['submit_pm_page'] =  $h->cage->post->testInt('pg');
                }
                break;

            default:
                return false;
        }
        
        $h->pluginHook('submit_functions_process_submitted');
        
        // save submitted data...
        $key = $this->saveSubmitData($h);
        return $key;
        break;
    }
    
    
     /**
     * Save submission step data
     *
     * @return bool
     */
    public function saveSubmitData($h)
    {
        // delete everything in this table older than 30 minutes:
        $this->deleteTempData($h->db);
        
        $sid = preg_replace('/[^a-z0-9]+/i', '', session_id());
        $key = md5(microtime() . $sid . rand());
        $sql = "INSERT INTO " . TABLE_TEMPDATA . " (tempdata_key, tempdata_value, tempdata_updateby) VALUES (%s,%s, %d)";
        $h->db->query($h->db->prepare($sql, $key, serialize($h->vars['submitted_data']), $h->currentUser->id));
        return $key;
    }
    
    
     /**
     * Retrieve submission step data
     *
     * @param $key - empty when setting
     * @return bool
     */
    public function loadSubmitData($h, $key = '')
    {
        // delete everything in this table older than 30 minutes:
        $this->deleteTempData($h->db);
        
        if (!$key) { return false; }
        
        $cleanKey = preg_replace('/[^a-z0-9]+/','',$key);
        if (strcmp($key,$cleanKey) != 0) {
            return false;
        } else {
            $sql = "SELECT tempdata_value FROM " . TABLE_TEMPDATA . " WHERE tempdata_key = %s ORDER BY tempdata_updatedts DESC LIMIT 1";
            $submitted_data = $h->db->get_var($h->db->prepare($sql, $key));
            if ($submitted_data) { return unserialize($submitted_data); } else { return false; } 
        }
    }
    
    
     /**
     * Delete temporary data older than 30 minutes
     */
    public function deleteTempData($db)
    {
        $exp = date('YmdHis', strtotime("-30 mins"));
        $sql = "DELETE FROM " . TABLE_TEMPDATA . " WHERE tempdata_updatedts < %s";
        $db->query($db->prepare($sql, $exp));
    }
    
    
     /**
     * Call the appropriate error checking function
     *
     * @param $step - submit step
     * @return bool
     */
    public function checkErrors($h, $step = 'submit1', $key = '')
    {
        switch ($step) {
            case 'submit1':
                return $this->checkErrors1($h, $key);
                break;
            case 'submit2':
                return $this->checkErrors2($h, $key);
                break;
            case 'edit_post':
                return $this->checkErrors2($h, $key);
                break;
            default:
                return false;
        }
    }
    
    
    /**
     * Checks submit_step1 for errors
     */
    public function checkErrors1($h, $key = '')
    {
    
        if (!$key) {
            // Nothing submitted
            $h->message = $h->lang['submit_nothing_submitted'];
            $h->messageType = 'red';
            return true; // error found
        }
        
        // check user has permission to post
        if ($h->currentUser->getPermission('can_submit') == 'no') {
            // No permission to submit
            $h->message = $h->lang["submit_no_permission"];
            $h->messageType = 'red';
            return true; //error found
        }
        
        // get the settings we need:
        $submit_settings = $h->getSerializedSettings('submit');
        $daily_limit = $submit_settings['daily_limit'];
        $freq_limit = $submit_settings['freq_limit'];
        
        // get the last submitted data by this user:
        $submitted_data = $this->loadSubmitData($h, $key);
        $url = urldecode($submitted_data['submit_orig_url']);
        $editorial = $submitted_data['submit_editorial'];

        // allow submission to continue without a link
        if ($editorial && ($h->currentUser->getPermission('can_post_without_link') == 'yes')) { 
            $h->vars['submit_editorial'] = true; 
            return false; //no error
        }
        
        if (!$submitted_data) {
            // Nothing submitted
            $h->message = $h->lang['submit_nothing_submitted'];
            $h->messageType = 'red';
            return true; // error found
        }
                    
        // ******** CHECK URL ********

        if (!$url) {
            // No url present...
            $h->message = $h->lang['submit_url_not_present_error'];
            $h->messageType = 'red';
            $error = 1;
        } elseif ($existing = $h->urlExists($url)) {
            // URL already exists...
            if (($existing->post_status == 'new') || ($existing->post_status == 'top'))
            {
            	// redirect to the existing post unless you 
            	header("Location: " . $h->url(array('page'=>$existing->post_id)));
            	exit;
            }
            $h->message = $h->lang['submit_url_already_exists_error'];
            $h->messageType = 'red';
            $error = 1;
        } elseif ($h->currentUser->getPermission('can_submit') == 'no') {
            // No permission to submit posts
            $h->message = $h->lang['submit_no_permission'];
            $h->messageType = 'red';
            $error = 1;
        } elseif ($this->checkBlocked($h, $url)) {
            // URL is blocked
            $h->message = $h->lang['submit_url_blocked'];
            $h->messageType = 'red';
            $error = 1;
        } elseif (($h->currentUser->role == 'member' || $h->currentUser->role == 'undermod')
                   && $daily_limit && ($daily_limit < $h->countPosts(24))) { 
            // exceeded daily limit
            $h->message = $h->lang['submit_daily_limit_exceeded'];
            $h->messageType = 'red';
            $error = 1;
        } elseif (($h->currentUser->role == 'member' || $h->currentUser->role == 'undermod')
                   && $freq_limit && ($h->countPosts(0, $freq_limit) > 0)) { 
            // already submitted a post in the last X minutes. Needs to wait before submitting again
            $h->message = $h->lang['submit_freq_limit_error'];
            $h->messageType = 'red';
            $error = 1;
        } else {
            // URL is okay.
            $error = 0;
        }
        
        // allow plugins to add their own checks
        $h->vars['submit_1_check_error'] = $error;
        $h->pluginHook('submit_1_check_errors', '', array('url'=>$url));
        $error = $h->vars['submit_1_check_error'];
        
        // Return true if error is found
        if ($error == 1) { return true; } else { return false; }
    }
    
    
    /**
     * Check for errors in submit 2
     *
     * @return bool
     */
    public function checkErrors2($h, $key = '')
    {
        // check user has permission to post
        if ($h->currentUser->getPermission('can_submit') == 'no') {
            // No permission to submit
            $h->messages[$h->lang['submit_no_permission']] = "red";
            return true; //error found
        }
        
        $post_id = $h->cage->post->testInt('submit_post_id'); // 0 unless come back from step 3.
        
        // for editing posts only (not from step 3)
        if ($h->cage->post->keyExists('edit_post')) { $edit = true; } else {$edit = false; }
        
        // get the settings we need:
        $submit_settings = $h->getSerializedSettings('submit');
        $min_content_length = $submit_settings['content_length'];
        $url_limit = $submit_settings['url_limit'];
        
        // get the last submitted data by this user:
        $submitted_data = $this->loadSubmitData($h, $key);
        
        $editorial = $submitted_data['submit_editorial'];
        $title = $submitted_data['submit_title'];
        $content = $submitted_data['submit_content'];
        
        if ($edit) {
            $orig_url = $submitted_data['submit_orig_url'];
        }
    
        if (!$submitted_data) {
            // Nothing submitted
            $h->messages[$h->lang['submit_nothing_submitted']] = "red";
            return true; // error found
        }
        
        // defaults:
        $error_csrf = 0;
        $error_url = 0;
        $error_title = 0;
        $error_content = 0;
        $error_category = 0;
        $error_tags = 0;
        $error_hooks = 0;
        
        // ******** CHECK CSRF *******
        
        if (!$h->csrf('check', 'submit2') 
            && !$h->csrf('check', 'submit3')
            && !$h->csrf('check', 'edit_post')) {
            $h->messages[$h->lang['error_csrf']] = "red";
            $error_csrf = 1;
        }
        
        // ******** CHECK URL ********
        $error_url = 0;
        if ($edit && !$editorial && !$orig_url) {
            // No url present...
            $h->messages[$h->lang['submit_url_not_present_error']] = "red";
            $error_url = 1;
        }
        
        // ******** CHECK TITLE ********
            
        if (!trim($title)) {
            // No title present...
            $h->messages[$h->lang['submit_title_not_present_error']] = "red";
            $error_title= 1;
        } elseif (!$post_id && $h->titleExists($title)) {
            // title already exists...
            if ($post_id != $h->titleExists($title)) {
                $h->messages[$h->lang['submit_title_already_exists_error']] = "red";
                $error_title = 1;
            } else {
                // the matching title is for the post we're currently modifying so no error...
                $error_title = 0;
            }
        } else {
            // title is okay.
            $error_title = 0;
        }
            
        // ******** CHECK DESCRIPTION ********
        if ($submit_settings['content']) { // if using the content field
            if (!trim($content)) {
                // No content present...
                $h->messages[$h->lang['submit_content_not_present_error']] = "red";
                $error_content = 1;
            } elseif (isset($min_content_length) && (strlen($content) < $min_content_length)) {
                // content is too short
                $h->messages[$h->lang['submit_content_too_short_error']] = "red";
                $error_content = 1;
            } elseif (($h->currentUser->role == 'member' || $h->currentUser->role == 'undermod')
                        && $url_limit && ($url_limit < countUrls($content))) { 
                // content contains too many links
                $h->messages[$h->lang['submit_content_too_many_links']] = "red";
                $error_content = 1;
            } else {
                // content is okay.
                $error_content = 0;
            }
        }
        
        // ******** CHECK CATEGORY ********
        if ($submit_settings['categories']) {
            $category = $h->cage->post->getInt('post_category');    
            if ($category > 1) {
                // category is okay.
                $error_category = 0;
            } else {
                // No category present...
                $h->messages[$h->lang['submit_category_error']] = "red";
                $error_category = 1;
            }
        }
        
        // ******** CHECK TAGS ********
        if ($submit_settings['tags']) {
            $tags = sanitize($h->cage->post->noTags('post_tags'), 'tags');
            
            if (!trim($tags)) {
                // No tags present...
                $h->messages[$h->lang['submit_tags_not_present_error']] = "red";
                $error_tags = 1;
            } elseif (strlen($tags) > $submit_settings['max_tags']) {
                // total tag length is too long
                $h->messages[$h->lang['submit_tags_length_error']] = "red";
                $error_tags = 1;
            } else {
                // tags are okay.
                $error_tags = 0;
            }
        }
        
        
        // Check for errors from plugin fields
        $error_hooks = 0;
        $error_array = $h->pluginHook('submit_2_check_errors');
        if (is_array($error_array)) {
            foreach ($error_array as $err) { if ($err == 1) { $error_hooks = 1; } }
        }
        
        // Return true if error is found
        if ($error_csrf == 1 || $error_url == 1 || $error_title == 1 || $error_content == 1 
            || $error_category == 1 || $error_tags == 1 || $error_hooks == 1)
        { 
            return true; 
        } else { 
            return false; 
        }
    }
    
    
    /**
     * Saves the submitted story to the database
     */
    public function processSubmission($h, $key)
    {
        $h->post->id = $h->cage->post->getInt('submit_post_id');
        if ($h->post->id) { $h->readPost(); } // read what we've already got for this post
        
        // get the last submitted data by this user:
        $submitted_data = $this->loadSubmitData($h, $key);
        $editorial = $submitted_data['submit_editorial'];
        
        /*  MOST PROBLEMS ARE CAUSED BY THESE LINES: BE VERY CAREFUL HERE BECAUSE WHAT MIGHT 
            WORK FOR POST SUBMISSION COULD SCREW UP EDIT POST OR WHAT MIGHT WORK FOR EDITORIALS 
            MIGHT SCREW UP NON-EDITORIALS AND VICE-VERSA. THE FOLLOWING WORKS FOR ALL (I think) */
        if ($editorial) {
            $h->post->origUrl = "self";
        }
        
        if ($submitted_data['submit_orig_url']) {
            $h->post->origUrl = $submitted_data['submit_orig_url'];
        }
        /* MOST PROBLEMS ARE CAUSED BY THE ABOVE LINES: */
        
        if ($h->post->origUrl == "self") {
            $h->post->domain = get_domain(urldecode(BASEURL)); // returns domain including http:// 
        } else {
            $h->post->domain = get_domain(urldecode($h->post->origUrl)); // returns domain including http:// 
        }
        
        $h->post->title = $submitted_data['submit_title'];
        $title = html_entity_decode($h->post->title, ENT_QUOTES, 'UTF-8');
        $h->post->url = make_url_friendly($title);
        $h->post->content = $submitted_data['submit_content'];
        $h->post->type = 'news';    // This is the type we use to distinguish social bookmarking from forums, blogs, etc.
        if (!$h->post->id) { $h->post->author = $h->currentUser->id; } // no author yet

        if (isset($submitted_data['submit_status'])) {
            $h->post->status = $submitted_data['submit_status'];
        } else {
            $h->post->status = 'processing';
        }
        
        if (isset($submitted_data['submit_category'])) {
            $h->post->category = $submitted_data['submit_category'];
        }
        
        if (isset($submitted_data['submit_tags'])) {
            $h->post->tags = $submitted_data['submit_tags'];
        } 

        $h->vars['submitted_data'] = $submitted_data;
        $h->pluginHook('submit_2_process_submission');
        
        if ($h->post->id != 0) {
            $h->updatePost();    // Updates an existing post (e.g. returning to step 2 from step 3 to modify it)
        } else {
            $h->addPost();    // Adds a new post
            // Now that the post is in the database with an ID and category assigned, we can get its url and update that field: 
            if ($h->post->origUrl == "self") {
                $post_id = $h->post->vars['last_insert_id'];
                $h->post->origUrl = $h->url(array('page'=>$post_id)); // update the url with the real one
                $sql = "UPDATE " . TABLE_POSTS . " SET post_orig_url = %s WHERE post_id = %d";
                $query = $h->db->prepare($sql, urlencode($h->post->origUrl), $post_id);
                $h->db->query($query);
            }
            
            // tidy up by deleting all processing posts older than 30 minutes:
            $h->deleteProcessingPosts();
        }
    }
    
    
    /**
     * Check if url or domain is on the blocked list
     *
     * @param string $url
     * @return bool - true if blocked
     */
    public function checkBlocked($h, $url)
    {
        // Is url blocked?
        if ($h->isBlocked('url', $url)) {
            return true;
        }
        
        // Is domain blocked?
        $domain = get_domain($url); // returns the domain including http 
        if ($h->isBlocked('url', $domain)) {
            return true;
        }
        
        // Is domain extension blocked?
        $host = parse_url($url, PHP_URL_HOST); // returns www.google.com
        $ext = substr(strrchr($host, '.'), 1); 
        if ($h->isBlocked('url', '.' . $ext)) { // dot added here
            return true;
        } 
                        
        return false;   // not blocked
    }
    
    
    /**
     * Scrapes the title from the page being submitted
     *
     * @param string $url
     * @link http://www.phpfour.com/blog/2008/01/php-http-class/
     */
    public function fetchTitle($url)
    {
        require_once(EXTENSIONS . 'SWCMS/HotaruHttpRequest.php');
        
        if ($url != 'http://' && $url != ''){
            $r = new HotaruHttpRequest($url);
            $string = $r->DownloadToString();
        } else {
            $string = '';
        }
        
        if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $string , $matches)) {
            $encoding=trim($matches[1]);

            //you need iconv to encode to utf-8 (if not, use custom iconv in funcs.strings.php)
            if (strcasecmp($encoding, 'utf-8') != 0) {
                //convert the html code into utf-8 whatever encoding it is using
                $string=iconv($encoding, 'UTF-8//IGNORE', $string);
            }
        }
        
        if (preg_match("'<title>([^<]*?)</title>'", $string, $matches)) {
            $title = trim($matches[1]);
        } else {
            $title = '';
        }
        
        return $title;
    }
    
}
?>
