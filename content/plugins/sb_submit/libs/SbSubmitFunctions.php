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
     * Check if a post has been submitted
     *
     * @param $step - submit step
     * @return bool
     */
    public function checkSubmitted($hotaru, $step = 'submit1')
    {
        switch ($step) {
            case 'submit1':
                // set defaults:
                $hotaru->vars['submitted_data']['submit_orig_url'] = '';
                // Uses getAlpha for Submit page, keyExists for EVB & Bookmarklet
                // Also checks if the "Submit without a URL" box is checked instead
                if ($hotaru->cage->post->keyExists('no_link')) { return true; }
                if ($hotaru->cage->post->getAlpha('submit1') == 'true') { return true; }
                if ($hotaru->cage->get->keyExists('url')) { return true; }
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
    public function saveSubmitted($hotaru, $step = 'submit1')
    {
        switch ($step) {
            case 'submit1':
                // defaults:
                $hotaru->vars['submitted_data'] = array();
                $hotaru->vars['submitted_data']['submit_orig_url'] = '';
                $hotaru->vars['submitted_data']['submit_use_link'] = true;
                
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
                    
                // is a url submitted via the url? (i.e. EVB or Bookmarklet)
                } elseif ($hotaru->cage->get->keyExists('url')) { 
                    $url = $hotaru->cage->get->testUri('url');
                    if (!$url) { break; }
                    $hotaru->vars['submitted_data']['submit_orig_url'] = urlencode($url);
                    $hotaru->vars['submitted_data']['submit_use_link'] = true;
                }
                
                // save submitted data...
                $key = $this->saveSubmitStep($hotaru);
                return $key;
                break;
            default:
                return false;
        }
    }
    
    
     /**
     * Save or retrieve submission step data
     *
     * @param $key - empty when setting
     * @return bool
     */
    public function saveSubmitStep($hotaru, $key = '')
    {
        // delete everything in this table older than 30 minutes:
        $exp = date('YmdHis', strtotime("-30 mins"));
        $sql = "DELETE FROM " . TABLE_TEMPDATA . " WHERE tempdata_updatedts < %s";
        $hotaru->db->query($hotaru->db->prepare($sql, $exp));
        
        if (!$key) {
            $sid = preg_replace('/[^a-z0-9]+/i', '', session_id());
            $key = md5(microtime() . $sid . rand());
            $sql = "INSERT INTO " . TABLE_TEMPDATA . " (tempdata_key, tempdata_value, tempdata_updateby) VALUES (%s,%s, %d)";
            $hotaru->db->query($hotaru->db->prepare($sql, $key, serialize($hotaru->vars['submitted_data']), $hotaru->currentUser->id));
            return $key;
        }
        
        if ($key) {
            $cleanKey = preg_replace('/[^a-z0-9]+/','',$key);
            if (strcmp($key,$cleanKey) != 0) {
                return false;
            } else {
                $sql = "SELECT tempdata_value FROM " . TABLE_TEMPDATA . " WHERE tempdata_key = %s ORDER BY tempdata_updatedts DESC LIMIT 1";
                $submitted_data = $hotaru->db->get_var($hotaru->db->prepare($sql, $key));
                if ($submitted_data) { return unserialize($submitted_data); } else { return false; } 
            }
        }
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
        
        // ******** CHECK URL ********
        
        // get the settings we need:
        $submit_settings = $hotaru->getSerializedSettings('sb_submit');
        $daily_limit = $submit_settings['daily_limit'];
        $freq_limit = $submit_settings['freq_limit'];
        
        // get the last submitted data by this user:
        $submitted_data = $this->saveSubmitStep($hotaru, $key);
        print_r($submitted_data);
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
            $error = 1;
        } elseif (!$url) {
            // No url present...
            $hotaru->message = $hotaru->lang['submit_url_not_present_error'];
            $hotaru->messageType = 'red';
            $error = 1;
        } elseif ($hotaru->urlExists($url)) {
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
    
}
?>
