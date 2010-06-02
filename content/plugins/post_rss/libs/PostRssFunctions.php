<?php
/**
 * Post RSS functions
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

class PostRssFunctions
{
    protected $feed_array   = array();  // contains a prepared SQL query
    protected $feed_results = NULL;     // object containing the results of the above query
    
    
    /**
     * Access modifier to set protected properties
     */
    public function __set($var, $val)
    {
        $this->$var = $val;
    }
    
    
    /**
     * Access modifier to get protected properties
     * The & is necessary (http://bugs.php.net/bug.php?id=39449)
     */
    public function &__get($var)
    {
        return $this->$var;
    }
    
    
    /**
     * Publish content as an RSS feed
     * Uses the 3rd party RSS Writer class.
     *
     * This function checks the url for the kind of feed
     * and builds a prepared SQL query
     */    
    public function postRssFeedQuery($h)
    {
        // Feed details
        $h->vars['postRssFeed']['title'] = SITE_NAME;
        $h->vars['postRssFeed']['link'] = BASEURL;
        // description set further down...

        // Limit:
        if ($h->cage->get->keyExists('limit')) {
        	$h->vars['postRssLimit'] = $h->cage->get->getInt('limit');
        }
        
        $this->postRssStatus($h);
        
        // allow other plugins to create a feed
        $h->pluginHook('post_rss_feed');

        // set post rss filter defaults if not already set
        $this->checkPostRssDefaults($h);
        
        // get the prepared SQL query
        $this->feed_array = $h->db->select(
        	$h,
        	array($h->vars['postRssSelect']),
        	'posts',
            $h->vars['postRssFilter'],
            $h->vars['postRssOrderBy'],
            $h->vars['postRssLimit']
        );
        
        return $this->feed_array;
    }
    
    
    /**
     * check post status for the RSS feed
     */
    public function postRssStatus($h)
    {
        $h->vars['postRssStatus'] = $h->cage->get->testAlpha('status');
        
        if (!$h->vars['postRssStatus']) { return false; }
        
        $h->vars['postRssFilter']['post_status = %s'] = $h->vars['postRssStatus'];
        
        switch ($h->vars['postRssStatus']) {
            case 'new':
                $h->vars['postRssFeed']['description'] = $h->lang["post_rss_latest_from"] . " " . SITE_NAME; 
                break;
            case 'top':
                $h->vars['postRssFeed']['description'] = $h->lang["post_rss_top_stories_from"] . " " . SITE_NAME;
                break;
            case 'upcoming':
                $h->vars['postRssFeed']['description'] = $h->lang["post_rss_upcoming_stories_from"] . " " . SITE_NAME;
                // Filters page to "new" stories by most votes, but only stories from the last X days!
                
                // figure out what vote plugin is being used and get the upcoing duration from that plugin's settings
                $sql = "SELECT plugin_folder FROM " . TABLE_PLUGINS . " WHERE plugin_type = %s AND plugin_enabled = %d";
                $vote_plugin = $h->db->get_var($h->db->prepare($sql, 'vote', 1));
                $vote_settings = unserialize($h->getSetting($vote_plugin . '_settings', $vote_plugin));
                $upcoming_duration = "-" . $vote_settings['upcoming_duration'] . " days"; // default: -5 days
                
                $h->vars['postRssFilter']['post_status = %s'] = 'new'; 
                $start = date('YmdHis', strtotime("now"));
                $end = date('YmdHis', strtotime($upcoming_duration)); // should be negative
                $h->vars['postRssFilter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
                $h->vars['postRssOrderBy'] = "post_votes_up DESC, post_date DESC";
            default:
            	
        }
    }


    /**
     * Set Post RSS filter defaults if not already set
     */
    public function checkPostRssDefaults($h)
    {
        // if no select set...
        if (!isset($h->vars['postRssSelect'])) { 
            $h->vars['postRssSelect'] = '*'; 
        }
        
        // if no order by set...
        if (!isset($h->vars['postRssOrderBy'])) { 
            $h->vars['postRssOrderBy'] = 'post_date DESC'; 
        }
        
        // if no filter set...
        if (!isset($h->vars['postRssFilter'])) {
            $h->vars['postRssFilter']['(post_status = %s || post_status = %s)'] = array('top', 'new'); // default to all posts
        }
        
        // if no limit set...
        if (!isset($h->vars['postRssLimit'])) { 
            $h->vars['postRssLimit'] = 10; 
        }
        
        // if no feed title set...
        if (!isset($h->vars['postRssFeed']['title'])) { 
            $h->vars['postRssFeed']['title'] = SITE_NAME; 
        }
        
        // if no feed link set...
        if (!isset($h->vars['postRssFeed']['link'])) { 
            $h->vars['postRssFeed']['link'] = BASEURL; 
        }
        
        // if no feed description set...
        if (!isset($h->vars['postRssFeed']['description'])) { 
            $h->vars['postRssFeed']['description'] = $h->lang["post_rss_from"] . " " . SITE_NAME;
        }
    }
    
    
    /**
     * Assign values to $feed object and serve the feed
     *
     * @param object $results - post rows
     */
    public function doPostRssFeed($h, $results = NULL)
    {
        if (!$results) { return false; }

        $title    = $h->vars['postRssFeed']['title'];
        $link     = $h->vars['postRssFeed']['link'];
        $description = $h->vars['postRssFeed']['description'];

        $items = array();
        
        foreach ($results as $result) 
        {
            $h->post->url = $result->post_url; // used in Hotaru's url function
            $h->post->category = $result->post_category; // used in Hotaru's url function

            $item['title'] = $result->post_title;
            $item['link']  = $h->url(array('page'=>$result->post_id));
            $item['date'] = $result->post_date; 
            $item['description'] = $result->post_content;
            
            $h->vars['post_rss_item'] = $item;
            $h->pluginHook('post_rss_feed_items', '', array('result'=>$result));
            
            array_push($items, $h->vars['post_rss_item']);
        }
        
        // do it!
        $h->rss($title, $link, $description, $items);
    }
}
?>