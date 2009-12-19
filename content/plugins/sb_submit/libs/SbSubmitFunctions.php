<?php
/**
 * Submit functions
 * Notes: This file is part of the SB Submit plugin.
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

class SbSubmitFunctions
{
     /**
     * Check if a form has been submitted
     *
     * @param $step - submit step
     * @return bool
     */
    public function checkSubmitted($hotaru, $step = 'submit1')
    {
        switch ($step) {
        
            case 'submit1':
            
                // set defaults:
                $hotaru->vars['submitted_data']['submit_use_link'] = true;
                $hotaru->vars['submitted_data']['submit_orig_url'] = '';
                
                // no link necessary?
                if ($hotaru->cage->post->keyExists('no_link')) { return true; }
                
                // form 1 submitted?
                if ($hotaru->cage->post->getAlpha('submit1') == 'true') { return true; }
                
                // EVB / Bookmarklet, etc?
                if ($hotaru->cage->get->keyExists('url')) { return true; }
                
                return false;
                break;
                
            case 'submit2':

                // set defaults:
                $hotaru->vars['submitted_data']['submit_use_link'] = true;
                $hotaru->vars['submitted_data']['submit_orig_url'] = '';
                $hotaru->vars['submitted_data']['submit_title'] = '';
                $hotaru->vars['submitted_data']['submit_content'] = '';
                $hotaru->vars['submitted_data']['submit_id'] = 0;
                
                // submit 2 form submitted?
                if ($hotaru->cage->post->getAlpha('submit2') == 'true') { return true; }
                
                return false;
                break;
                
            case 'submit3': // for step 3 EDIT

                // set defaults:
                $hotaru->vars['submitted_data']['submit_use_link'] = true;
                $hotaru->vars['submitted_data']['submit_orig_url'] = '';
                $hotaru->vars['submitted_data']['submit_title'] = '';
                $hotaru->vars['submitted_data']['submit_content'] = '';
                $hotaru->vars['submitted_data']['submit_id'] = 0;
                
                // submit 3 edit form submitted? If so, return false so we don't process the data
                if ($hotaru->cage->post->getAlpha('submit3edit') == 'true') { return true; }
                
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
    public function processSubmitted($hotaru, $step = 'submit1')
    {
        switch ($step) {
        
            case 'submit1':
            
                // is "Post without URL" checked?
                if ($hotaru->cage->post->keyExists('no_link')) {
                    $hotaru->vars['submitted_data']['submit_orig_url'] = '';
                    $hotaru->vars['submitted_data']['submit_use_link'] = false;
                    
                // is a url submitted via the form?
                } elseif ($hotaru->cage->post->getAlpha('submit1') == 'true') { 
                    $url = $hotaru->cage->post->testUri('submit_orig_url');
                    if (!$url) { break; }
                    $hotaru->vars['submitted_data']['submit_orig_url'] = urlencode($url);
                    $hotaru->vars['submitted_data']['submit_use_link'] = true;
                    $title = $this->fetchTitle($url);
                    if (!$title) { $title = $hotaru->lang["submit_not_found"]; }
                    $hotaru->vars['submitted_data']['submit_title'] = $title;
                    
                // is a url submitted via the url? (i.e. EVB or Bookmarklet)
                } elseif ($hotaru->cage->get->keyExists('url')) { 
                    $url = $hotaru->cage->get->testUri('url');
                    if (!$url) { break; }
                    $hotaru->vars['submitted_data']['submit_orig_url'] = urlencode($url);
                    $hotaru->vars['submitted_data']['submit_use_link'] = true;
                    $title = $this->fetchTitle($url);
                    if (!$title) { $title = $hotaru->lang["submit_not_found"]; }
                    $hotaru->vars['submitted_data']['submit_title'] = $title;
                }
                break;
                
            case 'submit2': // also used for coming back from Step 3 to edit the post
                
                if (($hotaru->cage->post->getAlpha('submit2') == 'true') 
                    || ($hotaru->cage->post->getAlpha('submit3edit') == 'true')) { 
                    // get previously submitted_data:
                    $key = $hotaru->cage->post->testAlnum('submit_key'); // from the form
                    $hotaru->vars['submitted_data'] = $this->loadSubmitData($hotaru, $key);
                    // get new (edited) title:
                    $title = $hotaru->cage->post->getMixedString2('post_title');
                    $hotaru->vars['submitted_data']['submit_title'] = $title;
                    // get content:
                    $allowable_tags = $hotaru->vars['submit_settings']['allowable_tags'];
                    $content = sanitize($hotaru->cage->post->getHtmLawed('post_content'), 2, $allowable_tags);
                    $hotaru->vars['submitted_data']['submit_content'] = $content;
                    // get post id (if editing)
                    $post_id = $hotaru->cage->post->testInt('submit_post_id');
                    $hotaru->vars['submitted_data']['submit_id'] = $post_id;
                }
                break;
                
            case 'submit3': // when EDIT is clicked 
                
                if ($hotaru->cage->post->getAlpha('submit3edit') == 'true') { 
                    // get previously submitted_data:
                    $key = $hotaru->cage->post->testAlnum('submit_key'); // from the form
                    $hotaru->vars['submitted_data'] = $this->loadSubmitData($hotaru, $key);
                    // get post id (if editing)
                    $post_id = $hotaru->cage->post->testInt('submit_post_id');
                    $hotaru->vars['submitted_data']['submit_id'] = $post_id;
                }
                break;

            default:
                return false;
        }
        
        // save submitted data...
        $key = $this->saveSubmitData($hotaru);
        return $key;
        break;
    }
    
    
     /**
     * Save submission step data
     *
     * @return bool
     */
    public function saveSubmitData($hotaru)
    {
        // delete everything in this table older than 30 minutes:
        $this->deleteTempData($hotaru->db);
        
        $sid = preg_replace('/[^a-z0-9]+/i', '', session_id());
        $key = md5(microtime() . $sid . rand());
        $sql = "INSERT INTO " . TABLE_TEMPDATA . " (tempdata_key, tempdata_value, tempdata_updateby) VALUES (%s,%s, %d)";
        $hotaru->db->query($hotaru->db->prepare($sql, $key, serialize($hotaru->vars['submitted_data']), $hotaru->currentUser->id));
        return $key;
    }
    
    
     /**
     * Retrieve submission step data
     *
     * @param $key - empty when setting
     * @return bool
     */
    public function loadSubmitData($hotaru, $key = '')
    {
        // delete everything in this table older than 30 minutes:
        $this->deleteTempData($hotaru->db);
        
        if (!$key) { return false; }
        
        $cleanKey = preg_replace('/[^a-z0-9]+/','',$key);
        if (strcmp($key,$cleanKey) != 0) {
            return false;
        } else {
            $sql = "SELECT tempdata_value FROM " . TABLE_TEMPDATA . " WHERE tempdata_key = %s ORDER BY tempdata_updatedts DESC LIMIT 1";
            $submitted_data = $hotaru->db->get_var($hotaru->db->prepare($sql, $key));
            if ($submitted_data) { return unserialize($submitted_data); } else { return false; } 
        }
    }
    
    
     /**
     * Delete temporary data older than 30 minutes
     *
     * @return bool
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
    public function checkErrors($hotaru, $step = 'submit1', $key = '')
    {
        switch ($step) {
            case 'submit1':
                return $this->checkErrors1($hotaru, $key);
                break;
            case 'submit2':
                return $this->checkErrors2($hotaru, $key);
                break;
            default:
                return false;
        }
    }
    
    
    /**
     * Checks submit_step1 for errors
     */
    public function checkErrors1($hotaru, $key = '')
    {
    
        if (!$key) {
            // Nothing submitted
            $hotaru->message = $hotaru->lang['submit_nothing_submitted'];
            $hotaru->messageType = 'red';
            return true; // error found
        }
        
        // get the settings we need:
        $submit_settings = $hotaru->getSerializedSettings('sb_submit');
        $daily_limit = $submit_settings['daily_limit'];
        $freq_limit = $submit_settings['freq_limit'];
        
        // get the last submitted data by this user:
        $submitted_data = $this->loadSubmitData($hotaru, $key);
        $url = urldecode($submitted_data['submit_orig_url']);
        $use_link = $submitted_data['submit_use_link'];
        
        // allow submission to continue without a link
        if (!$use_link && ($hotaru->currentUser->getPermission('can_post_without_link') == 'yes')) { 
            $hotaru->vars['submit_use_link'] = false; 
            return false; //no error
        }
        
        if (!$submitted_data) {
            // Nothing submitted
            $hotaru->message = $hotaru->lang['submit_nothing_submitted'];
            $hotaru->messageType = 'red';
            return true; // error found
        }
        
        // ******** CHECK URL ********

        if (!$url) {
            // No url present...
            $hotaru->message = $hotaru->lang['submit_url_not_present_error'];
            $hotaru->messageType = 'red';
            $error = 1;
        } elseif ($hotaru->urlExists(urlencode($url))) {
            // URL already exists...
            $hotaru->message = $hotaru->lang['submit_url_already_exists_error'];
            $hotaru->messageType = 'red';
            $error = 1;
        } elseif ($hotaru->currentUser->getPermission('can_submit') == 'no') {
            // No permission to submit posts
            $hotaru->message = $hotaru->lang['submit_no_permission'];
            $hotaru->messageType = 'red';
            $error = 1;
        } elseif ($this->checkBlocked($hotaru, $url)) {
            // URL is blocked
            $hotaru->message = $hotaru->lang['submit_url_blocked'];
            $hotaru->messageType = 'red';
            $error = 1;
        } elseif (($hotaru->currentUser->role == 'member' || $hotaru->currentUser->role == 'undermod')
                   && $daily_limit && ($daily_limit < $hotaru->post->countPosts(24))) { 
            // exceeded daily limit
            $hotaru->message = $hotaru->lang['submit_daily_limit_exceeded'];
            $hotaru->messageType = 'red';
            $error = 1;
        } elseif (($hotaru->currentUser->role == 'member' || $hotaru->currentUser->role == 'undermod')
                   && $freq_limit && ($hotaru->post->countPosts(0, $freq_limit) > 0)) { 
            // already submitted a post in the last X minutes. Needs to wait before submitting again
            $hotaru->message = $hotaru->lang['submit_freq_limit_error'];
            $hotaru->messageType = 'red';
            $error = 1;
        } else {
            // URL is okay.
            $error = 0;
        }
        
        // Return true if error is found
        if ($error == 1) { return true; } else { return false; }
    }
    
    
    /**
     * Check for errors in submit 2
     *
     * @return bool
     */
    public function checkErrors2($hotaru, $key = '')
    {
        $post_id = $hotaru->cage->post->testInt('submit_post_id'); // 0 unless come back from step 3.
        
        // get the settings we need:
        $submit_settings = $hotaru->getSerializedSettings('sb_submit');
        $min_content_length = $submit_settings['content_length'];
        $url_limit = $submit_settings['url_limit'];
        
        // get the last submitted data by this user:
        $submitted_data = $this->loadSubmitData($hotaru, $key);
        $title = $submitted_data['submit_title'];
        $content = $submitted_data['submit_content'];
    
        if (!$submitted_data) {
            // Nothing submitted
            $hotaru->messages[$hotaru->lang['submit_nothing_submitted']] = "red";
            return true; // error found
        }
        
        // defaults:
        $error_csrf = 0;
        $error_title = 0;
        $error_content = 0;
        $error_hooks = 0;
        
        // ******** CHECK CSRF *******
        
        if (!$hotaru->csrf('check', 'submit2') && !$hotaru->csrf('check', 'submit3')) {
            $hotaru->messages[$hotaru->lang['error_csrf']] = "red";
            $error_csrf = 1;
        }
        
        // ******** CHECK TITLE ********
            
        if (!$title) {
            // No title present...
            $hotaru->messages[$hotaru->lang['submit_title_not_present_error']] = "red";
            $error_title= 1;
        } elseif (!$post_id && $hotaru->titleExists($title)) {
            // title already exists...
            if ($post_id != $hotaru->titleExists($title)) {
                $hotaru->messages[$hotaru->lang['submit_title_already_exists_error']] = "red";
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
        //if ($hotaru->post->useContent) {
            if (!$content) {
                // No content present...
                $hotaru->messages[$hotaru->lang['submit_content_not_present_error']] = "red";
                $error_content = 1;
            } elseif (isset($min_content_length) && (strlen($content) < $min_content_length)) {
                // content is too short
                $hotaru->messages[$hotaru->lang['submit_content_too_short_error']] = "red";
                $error_content = 1;
            } elseif (($hotaru->currentUser->role == 'member' || $hotaru->currentUser->role == 'undermod')
                        && $url_limit && ($url_limit < countUrls($content))) { 
                // content contains too many links
                $hotaru->messages[$hotaru->lang['submit_content_too_many_links']] = "red";
                $error_content = 1;
            } else {
                // content is okay.
                $error_content = 0;
            }
        //}
        
        
        // Check for errors from plugin fields, e.g. Tags
        $error_hooks = 0;
        $error_array = $hotaru->pluginHook('submit_2_check_errors');
        if (is_array($error_array)) {
            foreach ($error_array as $err) { if ($err == 1) { $error_hooks = 1; } }
        }
        
        // Return true if error is found
        if ($error_csrf == 1 || $error_title == 1 || $error_content == 1 || $error_hooks == 1) { return true; } else { return false; }
    }
    
    
    /**
     * Saves the submitted story to the database
     */
    public function processSubmission($hotaru, $key)
    {
        $hotaru->post->id = $hotaru->cage->post->getInt('submit_post_id');
        
        // get the last submitted data by this user:
        $submitted_data = $this->loadSubmitData($hotaru, $key);
        $use_link = $submitted_data['submit_use_link'];

        if ($use_link || ($hotaru->post->id != 0)) {
            $hotaru->post->origUrl = $submitted_data['submit_orig_url'];
        } else {
            $hotaru->post->origUrl = "self";
        }
        
        $hotaru->post->title = $submitted_data['submit_title'];
        $hotaru->post->url = make_url_friendly($hotaru->post->title);
        $hotaru->post->domain = get_domain(urldecode($hotaru->post->origUrl)); // returns domain including http:// 
        $hotaru->post->content = $submitted_data['submit_content'];
        $hotaru->post->status = 'processing';
        $hotaru->post->author = $hotaru->currentUser->id;
        
        $hotaru->pluginHook('submit_2_process_submission');
        
        if ($hotaru->post->id != 0) {
            $hotaru->updatePost();    // Updates an existing post (e.g. returning to step 2 from step 3 to modify it)
        } else {
            $hotaru->addPost();    // Adds a new post
        }
    }
    
    
    /**
     * Check if url or domain is on the blocked list
     *
     * @param string $url
     * @return bool - true if blocked
     */
    public function checkBlocked($hotaru, $url)
    {
        // Is url blocked?
        if ($hotaru->isBlocked('url', $url)) {
            return true;
        }
        
        // Is domain blocked?
        $domain = get_domain($url); // returns the domain including http 
        if ($hotaru->isBlocked('url', $domain)) {
            return true;
        }
        
        // Is domain extension blocked?
        $host = parse_url($url, PHP_URL_HOST); // returns www.google.com
        $ext = substr(strrchr($host, '.'), 1); 
        if ($hotaru->isBlocked('url', '.' . $ext)) { // dot added here
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
        require_once(EXTENSIONS . 'SWCMS/class.httprequest.php');
        //require_once(EXTENSIONS . 'http/class.http.php');
        
        if ($url != 'http://' && $url != ''){
            $r = new HTTPRequest($url);
            $string = $r->DownloadToString();
            //$http = new Http();
            //$http->execute($url);
            //$string = $http->result;
        } else {
            $string = '';
        }
        
        if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $string , $matches)) {
            $encoding=trim($matches[1]);
            //you need iconv to encode to utf-8
            if (function_exists("iconv"))
            {
                if (strcasecmp($encoding, 'utf-8') != 0) {
                    //convert the html code into utf-8 whatever encoding it is using
                    $string=iconv($encoding, 'UTF-8//IGNORE', $string);
                }
            }
        }
        
        if (preg_match("'<title>([^<]*?)</title>'", $string, $matches)) {
            $title = trim($matches[1]);
        } else {
            $title = '';
        }
        
        return sanitize(utf8_encode($title), 1);
    }
    
}
?>
