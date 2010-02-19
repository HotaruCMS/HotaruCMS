<?php
/**
 * Cache functions
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
class Caching
{
    /**
     * Hotaru CMS Smart Caching
     *
     * This function does one query on the database to get the last updated time for a 
     * specified table. If that time is more recent than the $timeout length (e.g. 10 minutes),
     * the database will be used. If there hasn't been an update, any cached results from the 
     * last 10 minutes will be used.
     *
     * @param string $switch either "on", "off" or "html"
     * @param string $table DB table name
     * @param int $timeout time before DB cache expires
     * @param string $html_sql output as HTML, or an SQL query
     * @param string $label optional label to append to filename
     * @return bool
     */
    public function smartCache($h, $switch = 'off', $table = '', $timeout = 0, $html_sql = '', $label = '')
    {
        if ($switch == 'html') { 
            $html = $html_sql;
            $result = $this->smartCacheHTML($h, $table, $timeout, $html, $label); 
        } else {
            $sql = $html_sql;
            $result = $this->smartCacheDB($h, $switch, $table, $sql, $timeout);
        }
        
        return $result;
    }
    
    
    /**
     * Hotaru CMS Smart Caching HTML output
     *
     * This function caches blocks of HTML code
     *
     * @param string $table DB table name
     * @param int $timeout timeout in minutes before cache file is deleted
     * @return bool
     */
    public function smartCacheHTML($h, $table = '', $timeout = 0, $html = '', $label = '')
    {
        if (!$table || !$timeout || (HTML_CACHE_ON != 'true')) { return false; }
        
        if(isset($h->vars['last_updates'][$table])) {
            $last_update = $h->vars['last_updates'][$table]; // cached
        } else {
            $last_update = $this->smartCacheSQL($h, $table);
            $last_update = $h->vars['last_updates'][$table] = $last_update;
        }
        
        $cache_length = $timeout*60;   // seconds
        $cache = CACHE . 'html_cache/';
        if ($label) { $label = '_' . $label; } 
        $file = $cache . $table . $label . ".cache";
        
        //echo "time now: " . time() . "<br />";
        //echo "time minus timeout: " . (time() - $timeout*60) . "<br />";
        //echo "last update: " . $last_update . "<br />";
        
        if (!$html) {
            // we only want to read the cache if it exists, hence no $html passed to this function
            if (file_exists($file)) {
                $content = file_get_contents($file);
                $last_modified = filemtime($file);
                //echo "last modified: " . $last_modified . "<br />";
                if ($last_modified <= (time() - $cache_length)) { 
                    // delete cache
                    unlink($file);
                    return false;
                } else {
                    if ($last_update >= $last_modified) { return false; } // there's been a recent update so don't use the cache.
                    return $content;    // return the HTML to display
                }
            } else {
                return false;
            }
        }

        // if we're here, we need to make or rewrite the cache
        
        $fp = fopen($file, "w");

        if (flock($fp, LOCK_EX)) { // do an exclusive lock
            ftruncate($fp, 0);  // truncate file
            fwrite($fp, $html); // write HTML
            flock($fp, LOCK_UN); // release the lock
        } else {
            echo "Couldn't get the lock for the HTML cache!";
        }
        
        fclose($fp);
        return true; // the calling function already has the HTML to output
    }
    
    
    /**
     * Hotaru CMS Smart Caching Database Queries
     *
     * This function uses the ezSQL database cache
     *
     * @param string $switch either "on" or "off"
     * @param string $table DB table name
     * @param int $timeout timeout in minutes
     * @return bool
     */
    public function smartCacheDB($h, $switch = 'off', $table = '', $sql = '', $timeout = 0)
    {
        // Stop caching?
        if ($switch != 'on') {
            $h->db->cache_queries = false;               // stop using cache
            $h->db->cache_timeout = DB_CACHE_DURATION;   // return to our default cache duration
            return false;
        }
        
        if (!$sql) { return false; }
        
        if (!$timeout) { $timeout = (DB_CACHE_DURATION * 60); } // hours * 60 = total minutes
        
        // ezSQL uses hours for its timeout. We'll use minutes and divide by 60 to get the hours.
        if ($timeout) { 
            $h->db->cache_timeout = $timeout/60; // mins/60 = hours
        } else {
            $h->db->cache_timeout = DB_CACHE_DURATION;
        }
        
        // determine time of last DB table update:
        if(isset($h->vars['last_updates'][$table])) {
            $last_update = $h->vars['last_updates'][$table]; // cached
        } else {
            $last_update = $this->smartCacheSQL($h, $table);
            $last_update = $h->vars['last_updates'][$table] = $last_update;
        }
        
        // use caching?
        if (DB_CACHE_ON == 'true') {
            $h->db->cache_queries = true;    // start using cache
        } else {
            return false;   // don't use caching
        }
        
        // check existence of a cache file for this query:
        $cache_file = CACHE . 'db_cache/' . md5($sql);
        if (!file_exists($cache_file)) {
            // no cache file so return and pull data direct from DB, caching the query at the same time.
            return true; 
        }
        
        // check if the cache file is older than our timeout:
        $file_modified = filemtime($cache_file);
        if ($file_modified < (time() - $timeout*60)) { 
            unlink($cache_file); // delete old cache file so we can make a new one with fresh data
            return true; 
        }
        
        // check if the $last_update is more recent than the cache file:
        if ($file_modified < $last_update) { 
            unlink($cache_file); // delete old cache file so we can make a new one with fresh data
            return true; 
        }

        return true;
    }
    
    

    /**
     * Picks the right SQL and gets the last_update time in seconds
     *
     * @param string $table DB table name
     * @return int $last_update
     */
    public function smartCacheSQL($h, $table = '')
    {
        /* Get the last time the table was updated */
        switch ($table) {
            case 'categories':
                $sql = "SELECT category_updatedts FROM " . DB_PREFIX . "categories ORDER BY category_updatedts DESC";
                break;
            case 'tags':
                $sql = "SELECT tags_updatedts FROM " . DB_PREFIX . "tags ORDER BY tags_updatedts DESC";
                break;
            case 'posts':
                $sql = "SELECT post_updatedts FROM " . DB_PREFIX . "posts ORDER BY post_updatedts DESC";
                break;
            case 'postvotes':
                $sql = "SELECT vote_updatedts FROM " . DB_PREFIX . "postvotes ORDER BY vote_updatedts DESC";
                break;
            case 'comments':
                $sql = "SELECT comment_updatedts FROM " . DB_PREFIX . "comments ORDER BY comment_updatedts DESC";
                break;
            case 'commentvotes':
                $sql = "SELECT cvote_updatedts FROM " . DB_PREFIX . "commentvotes ORDER BY cvote_updatedts DESC";
                break;
            case 'users':
                $sql = "SELECT user_updatedts FROM " . DB_PREFIX . "users ORDER BY user_updatedts DESC";
                break;
            case 'useractivity':
                $sql = "SELECT useract_updatedts FROM " . DB_PREFIX . "useractivity ORDER BY useract_updatedts DESC";
                break;
            case 'usermeta':
                $sql = "SELECT usermeta_updatedts FROM " . DB_PREFIX . "usermeta ORDER BY usermeta_updatedts DESC";
                break;
            case 'miscdata':
                $sql = "SELECT miscdata_updatedts FROM " . DB_PREFIX . "miscdata ORDER BY miscdata_updatedts DESC";
                break;
            case 'blocked':
                $sql = "SELECT blocked_updatedts FROM " . DB_PREFIX . "blocked ORDER BY blocked_updatedts DESC";
                break;
            default:
                return false;
        }
        
        // run DB query:
        $last_update = unixtimestamp($h->db->get_var($sql));
        
        return $last_update;
    }
}
?>