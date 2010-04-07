<?php
/**
 * SB Base functions
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

class SbBaseFunctions
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
     * Prepare filter and breadcrumbs for social bookmarking pages
     * Two main types: one for list pages and the other for 
     * miscelleneous stuff like Sidebar Posts
     *
     * @param string $type e.g. latest, upcoming, top-24-hours
     * @param string $return - 'posts', 'count' or 'query'
     * @return array
     */
    public function prepareList($h, $type = '', $return = 'posts')
    {
        if (!isset($h->vars['filter'])) { $h->vars['filter'] = array(); }
        
        if ($type) {
            // For the posts widget or other non-pages... 
            $h->vars['filter'] = array(); // flush filter
            $this->prepareListFilters($h, $type);
            
        } else {
            // for pages, i.e. lists of stories with pagination
            switch ($h->pageName) {
                case 'index':
                    $this->prepareListFilters($h, 'top');
                    break;
                case 'latest':
                    $this->prepareListFilters($h, 'new');
                    break;
                case 'upcoming':
                    $this->prepareListFilters($h, 'upcoming');
                    break;
                case 'sort':
                    $sort = $h->cage->get->testPage('sort');
                    $this->prepareListFilters($h, $sort);
                    break;
                default:
                    $this->prepareListFilters($h, 'all');
                }

            $h->pluginHook('sb_base_functions_preparelist', '', array('return' => $return)); // formerly post_list_filter
        }
        
        // defaults
        if (!isset($h->vars['select'])) { $h->vars['select'] = '*'; }
        if (!isset($h->vars['orderby'])) { $h->vars['orderby'] = 'post_date DESC'; }
        $limit = 0; 
        $all = true;
        
        // $type is used in sidebar posts, etc so we need to specify a limit, e.g. 10.
        if ($type) { 
            if ($h->vars['limit']) { $limit = $h->vars['limit']; } else { $limit = 0; }
            $all = false;
        }
        
        // if we want to count the totals, we need to replace the select clause with COUNT, but some queries that use MATCH and relevance are a bit complicated, 
        // so we'll let those plugins (e.g. search) add COUNT to their queries themselves and skip them here (which we can do by checking for MATCH).
        if ($return == 'count' && (strpos($h->vars['select'], "MATCH") === false)) { $h->vars['select'] = "count(post_id) AS number"; }
        if ($return == 'query') { $all = true; }    // this removes the "LIMIT" parameter so we can add it later when paginating.
        
        $prepared_filter = $this->filter($h->vars['filter'], $limit, $all, $h->vars['select'], $h->vars['orderby']);
        
        if ($return == 'query') { 
            if (isset($prepared_filter[1])) {
                return $h->db->prepare($prepared_filter);
            } else {
                return $prepared_filter[0];    // returns the prepared query array
            }
        } elseif($return == 'count') {
            unset($h->vars['select']);  // so it doesn't get used again unintentionally
            $count_array = $this->getPosts($h, $prepared_filter);
            return $count_array[0]->number; // returns the number of posts
        } else {
            return $this->getPosts($h, $prepared_filter);   // returns the posts OR post count depending on the query
        }
    }
    
    
    /**
     * Prepare list filters
     *
     * @param string $type e.g. latest, upcoming, top-24-hours
     */
    public function prepareListFilters($h, $type = '')
    {
        if ($type == 'new')
        {
            // Filters page to "new" stories only
            $h->vars['filter']['post_archived = %s'] = 'N'; 
            $h->vars['filter']['post_status = %s'] = 'new';
            $h->vars['orderby'] = "post_date DESC";
        } 
        elseif ($type == 'upcoming') 
        {
            // Filters page to "new" stories by most votes, but only stories from the last X days!
            $vote_settings = unserialize($h->getSetting('vote_settings', 'vote')); 
            $upcoming_duration = "-" . $vote_settings['upcoming_duration'] . " days"; // default: -5 days
            
            $h->vars['filter']['post_archived = %s'] = 'N'; 
            $h->vars['filter']['post_status = %s'] = 'new'; 
            $start = date('YmdHis', strtotime("now"));
            $end = date('YmdHis', strtotime($upcoming_duration)); // should be negative
            $h->vars['filter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
            $h->vars['orderby'] = "post_votes_up DESC, post_date DESC";
        } 
        elseif ($type == 'top-24-hours')
        {
            // Filters page to "top" stories from the last 24 hours only
            $h->vars['filter']['post_status = %s'] = 'top'; 
            $start = date('YmdHis', strtotime("now"));
            $end = date('YmdHis', strtotime("-1 day"));
            $h->vars['filter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
            $h->vars['orderby'] = "post_votes_up DESC, post_date DESC";
        } 
        elseif ($type == 'top-48-hours') 
        {
            // Filters page to "top" stories from the last 48 hours only
            $h->vars['filter']['post_status = %s'] = 'top'; 
            $start = date('YmdHis', strtotime("now"));
            $end = date('YmdHis', strtotime("-2 days"));
            $h->vars['filter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
            $h->vars['orderby'] = "post_votes_up DESC, post_date DESC";
        } 
        elseif ($type == 'top-7-days')
        {
            // Filters page to "top" stories from the last 7 days only
            $h->vars['filter']['post_status = %s'] = 'top'; 
            $start = date('YmdHis', strtotime("now"));
            $end = date('YmdHis', strtotime("-7 days"));
            $h->vars['filter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
            $h->vars['orderby'] = "post_votes_up DESC, post_date DESC";
        } 
        elseif ($type == 'top-30-days')
        {
            // Filters page to "top" stories from the last 30 days only
            $h->vars['filter']['post_status = %s'] = 'top'; 
            $start = date('YmdHis', strtotime("now"));
            $end = date('YmdHis', strtotime("-30 days"));
            $h->vars['filter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
            $h->vars['orderby'] = "post_votes_up DESC, post_date DESC";
        } 
        elseif ($type == 'top-365-days')
        {
            // Filters page to "top" stories from the last 365 days only
            $h->vars['filter']['post_status = %s'] = 'top'; 
            $start = date('YmdHis', strtotime("now"));
            $end = date('YmdHis', strtotime("-365 days"));
            $h->vars['filter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
            $h->vars['orderby'] = "post_votes_up DESC, post_date DESC";
        } 
        elseif ($type == 'top-all-time')
        {
            // Filters page to "top" stories in order of votes
            $h->vars['filter']['post_status = %s'] = 'top'; 
            $h->vars['orderby'] = "post_votes_up DESC, post_date DESC";
        } 
        elseif ($type == 'top')
        {
            // Assume 'top' page and filter to 'top' stories.
            $h->vars['filter']['post_archived = %s'] = 'N'; 
            $h->vars['filter']['post_status = %s'] = 'top';
            $h->vars['orderby'] = "post_date DESC";
        }
        else
        {
            // Filters page to "all" stories
            $h->vars['filter']['post_archived = %s'] = 'N'; 
            $h->vars['filter']['(post_status = %s OR post_status = %s)'] = array('top', 'new');
            $h->vars['orderby'] = "post_date DESC";
        }
    }
    
    
    /**
     * Gets all the posts from the database
     *
     * @param array $vars - search parameters
     * @param int $limit - no. of rows to retrieve
     * @param bool $all - true to retrieve ALL rows, else default 20
     * @param string $select - the select clause
     * @param string $orderby - the order by clause
     * @return array|false $prepare_array is the prepared SQL statement
     *
     * Example usage: $post->filter(array('post_tags LIKE %s' => '%tokyo%'), 10);
     */    
    public function filter($vars = array(), $limit = 0, $all = false, $select = '*', $orderby = 'post_date DESC')
    {
        if(!isset($filter)) { $filter = ''; }
        $prepare_array = array();
        $prepare_array[0] = "temp";    // placeholder to be later filled with the SQL query.
        
        // default to posts of type "news" if not otherwise set
        if (!isset($vars['post_type'])) { $vars['post_type = %s'] = 'news'; } 
        
        if (!empty($vars)) {
            $filter = " WHERE ";
            foreach ($vars as $key => $value) {
                $filter .= $key . " AND ";    // e.g. " post_tags LIKE %s "
                
                // Push the values of %s and %d into the prepare_array
                
                // sometimes the filter might contain multiple values, eg.
                // WHERE post_status = %s OR post_status = %s. In that case,
                // the values are stored in an array, e.g. array('top', 'new').
                if (is_array($value)) {
                    foreach ($value as $v) {
                        array_push($prepare_array, $v);
                    }
                } else {
                    // otherwise, push the single value into $prepared_array:
                    array_push($prepare_array, $value);
                }
                
            }
            $filter = rstrtrim($filter, " AND ");
        }
        
        if ($all == true) {
            $limit = '';
        } elseif ($limit == 0) { 
            $limit = " LIMIT 20"; 
        } else { 
            $limit = " LIMIT " . $limit; 
        }
        
        if ($orderby) { $orderby = "ORDER BY " . $orderby; }
        
        $sql = "SELECT " . $select . " FROM " . TABLE_POSTS . $filter . " " . $orderby . $limit;
        
        $prepare_array[0] = $sql;
        
        // $prepare_array needs to be passed to $this->db->prepare, i.e. $this->db->get_results($this->db->prepare($prepare_array));
                
        if ($prepare_array) { return $prepare_array; } else { return false; }
    }
    
    
    /**
     * Gets all the posts from the database
     *
     * @param array $prepared array - prepared SQL statement from filter()
     * @return array|false - array of posts
     */    
    public function getPosts($h, $prepared_array = array())
    {
        if (!$prepared_array) { return false; }
        
        if (empty($prepared_array[1])) {
            $h->smartCache('on', 'posts', 60, $prepared_array[0]); // start using cache
            $posts = $h->db->get_results($prepared_array[0]); // ignoring the prepare function.
        } else {
            $query = $h->db->prepare($prepared_array);
            $h->smartCache('on', 'posts', 60, $query); // start using cache
            $posts = $h->db->get_results($query);
        }
        
        $h->smartCache('off'); // stop using cache
        
        if ($posts) { return $posts; } else { return false; }
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
        $h->vars['postRssFeed']['description'] = '';

        // Limit:
        $h->vars['postRssLimit'] = $h->cage->get->getInt('limit');
        
        $this->postRssStatus($h); // Status:
        $this->postRssUser($h); // User
        $this->postRssTag($h); // Tag
        $this->postRssMediaType($h); // Media type
        $this->postRssCategory($h); // Category
        $this->postRssSearch($h); // Search
        
        // allow other plugins to create a feed
        $h->pluginHook('post_rss_feed');

        // set post rss filter defaults if not already set
        $this->checkPostRssDefaults($h);
        
        // get the prepared SQL query
        $this->feed_array = $this->filter(
            $h->vars['postRssFilter'], 
            $h->vars['postRssLimit'], 
            false, 
            $h->vars['postRssSelect'], 
            $h->vars['postRssOrderBy']
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
                $h->vars['postRssFeed']['description'] = $h->lang["sb_base_rss_latest_from"] . " " . SITE_NAME; 
                break;
            case 'top':
                $h->vars['postRssFeed']['description'] = $h->lang["sb_base_rss_top_stories_from"] . " " . SITE_NAME;
                break;
            case 'upcoming':
                $h->vars['postRssFeed']['description'] = $h->lang["sb_base_rss_upcoming_stories_from"] . " " . SITE_NAME;
                // Filters page to "new" stories by most votes, but only stories from the last X days!
                $vote_settings = unserialize($h->getSetting('vote_settings', 'vote')); 
                $upcoming_duration = "-" . $vote_settings['upcoming_duration'] . " days"; // default: -5 days
                $h->vars['postRssFilter']['post_status = %s'] = 'new'; 
                $start = date('YmdHis', strtotime("now"));
                $end = date('YmdHis', strtotime($upcoming_duration)); // should be negative
                $h->vars['postRssFilter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
                $h->vars['postRssOrderBy'] = "post_votes_up DESC, post_date DESC";
        }
    }
    
    
    /**
     * If a user feed, set it up
     */
    public function postRssUser($h)
    {
        $user = $h->cage->get->testUsername('user');
        if (!$user) { return false; }
        
        $user_id = $h->getUserIdFromName($user);
        if ($user_id) { $h->vars['postRssFilter']['post_author = %d'] = $user_id; }
        $h->vars['postRssFeed']['description'] = $h->lang["sb_base_rss_stories_from_user"] . " " . $user; 
    }
    
    
    /**
     * If a tag feed, set it up
     */
    public function postRssTag($h)
    {
        $tag = $h->cage->get->noTags('tag');
        if (!$tag) { return false; }
        
        $h->vars['postRssFilter']['post_tags LIKE %s'] = '%' . urlencode(stripslashes($tag)) . '%'; 
        $tag = str_replace('_', ' ', stripslashes(html_entity_decode($tag, ENT_QUOTES,'UTF-8'))); 
        $h->vars['postRssFeed']['description'] = $h->lang["sb_base_rss_stories_tagged"] . " " . $tag;
    }
    
    
    /**
     * If a media type feed, set it up
     */
    public function postRssMediaType($h)
    {
        $media = $h->cage->get->testAlnumLines('media');
        if (!$media) { return false; }

        $h->vars['postRssFilter']['post_media = %s'] = $media;
        $h->includeLanguage('media_select', 'media_select');
        if (isset($h->vars['postRssStatus']) && ($h->vars['postRssStatus'] != '')) { 
            $h->vars['postRssStatus'] .= "_"; 
        } else { 
            $h->vars['postRssStatus'] = ""; 
        }
        $media_word = "sb_base_rss_stories_media_" . $h->vars['postRssStatus'] . $media;
        $h->vars['postRssFeed']['description'] = $h->lang[$media_word];
    }
    
    
    /**
     * If a category feed, set it up
     */
    public function postRssCategory($h)
    {
        $category = $h->cage->get->noTags('category');
        
        if (!$category) { return false; }
        
        if (FRIENDLY_URLS == "true") { $cat_id = $h->getCatId($category); }
        if (FRIENDLY_URLS == "false") { $cat_id = $category; }
        
        if (!$cat_id) { return false; }

        // When a user clicks a parent category, we need to show posts from all child categories, too.
        // This only works for one level of sub-categories.

        $filter_string = '(post_category = %d';
        $values = array($cat_id);
        $parent = $h->getCatParent($cat_id);
        if ($parent == 1) {
            $children = $h->getCatChildren($cat_id);
            if ($children) {
                foreach ($children as $child_id) {
                    $filter_string .= ' || post_category = %d';
                    array_push($values, $child_id->category_id); 
                }
            }
        }
        $filter_string .= ')';
        $h->vars['postRssFilter'][$filter_string] = $values; 

        $category = str_replace('_', ' ', stripslashes(html_entity_decode($cat_id, ENT_QUOTES,'UTF-8'))); 
        $h->vars['postRssFeed']['description'] = $h->lang["sb_base_rss_stories_in_category"] . " " . $h->getCatName($cat_id); 
    }
    
    
    /**
     * If a search feed, set it up
     */
    public function postRssSearch($h)
    {
        $search = $h->cage->get->sanitizeTags('search');
        if (!$search || !$h->isActive('search')) { return false; }
        
        require_once(PLUGINS . 'search/search.php');
        $search_plugin = new Search();
        $prepared_search = $search_plugin->prepareSearchFilter($h, $search); 
        extract($prepared_search);
        // override "relevance DESC" so the RSS feed updates with the latest related terms.
        $h->vars['postRssOrderBy'] = "post_date DESC";
        $h->vars['postRssFeed']['description'] = $h->lang["sb_base_rss_stories_search"] . " " . stripslashes($search);
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
            $h->vars['postRssFilter']['post_status = %s || post_status = %s'] = array('top', 'new'); // default to all posts
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
            $h->vars['postRssFeed']['description'] = ''; 
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
        
        require_once(EXTENSIONS . 'RSSWriterClass/rsswriter.php');
        
        $feed           = new RSS();
        $feed->title    = $h->vars['postRssFeed']['title'];
        $feed->link     = $h->vars['postRssFeed']['link'];
        $feed->description = $h->vars['postRssFeed']['description'] = $h->vars['postRssFeed']['description'];

        // get sb base settings
        $sb_base_settings = $h->getSerializedSettings('sb_base');
            
        foreach ($results as $result) 
        {
            $h->post->url = $result->post_url; // used in Hotaru's url function
            $h->post->category = $result->post_category; // used in Hotaru's url function
            
            $item = new RSSItem();
            $title = html_entity_decode(urldecode($result->post_title), ENT_QUOTES,'UTF-8');
            $item->title = stripslashes($title);
            
            // if RSS redirecting is enabled, append forward=1 to the url
            if (isset($sb_base_settings['rss_redirect']) && !empty($sb_base_settings['rss_redirect'])) {
                $item->link  = html_entity_decode($h->url(array('page'=>$result->post_id, 'forward'=>$result->post_id)), ENT_QUOTES,'UTF-8');
            } else {
                $item->link  = $h->url(array('page'=>$result->post_id));
            }
            $item->setPubDate($result->post_date); 
            $item->description = "<![CDATA[ " . stripslashes(urldecode($result->post_content)) . " ]]>";
            $feed->addItem($item);
        }
        
        // do it!
        echo $feed->serve();
    }
}
?>