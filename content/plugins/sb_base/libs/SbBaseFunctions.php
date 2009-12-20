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
    /**
     * Prepare filter and breadcrumbs for social bookmarking pages
     * Two main types: one for list pages and the other for 
     * miscelleneous stuff like Sidebar Posts
     *
     * @param string $type e.g. latest, upcoming, top-24-hours
     * @return array
     */
    public function prepareList($hotaru, $type = '')
    {
        if (!isset($hotaru->vars['filter'])) { $hotaru->vars['filter'] = array(); }
        
        if ($type) {
            // For sidebar posts or other non-pages... 
            $hotaru->vars['filter'] = array(); // flush filter
            $this->prepareListFilters($type);
            
        } else {
            // for pages, i.e. lists of stories with pagination
            
            switch ($hotaru->pageName) {
                case 'index':
                    $this->prepareListFilters($hotaru, 'top');
                    break;
                case 'latest':
                    $this->prepareListFilters($hotaru, 'new');
                    break;
                case 'upcoming':
                    $this->prepareListFilters($hotaru, 'upcoming');
                    break;
                case 'sort':
                    $sort = $hotaru->cage->get->testPage('sort');
                    $this->prepareListFilters($hotaru, $sort);
                    break;
                default:
                    $this->prepareListFilters($hotaru, 'all');
                }

            $hotaru->pluginHook('sb_base_functions_preparelist'); // formerly post_list_filter
        }
        
        // defaults
        if (!isset($hotaru->vars['select'])) { $hotaru->vars['select'] = '*'; }
        if (!isset($hotaru->vars['orderby'])) { $hotaru->vars['orderby'] = 'post_date DESC'; }
        $limit = 0; 
        $all = true;
        
        // $type is used in sidebar posts, etc so we need to specify a limit, e.g. 10.
        if ($type) { 
            if ($hotaru->vars['limit']) { $limit = $hotaru->vars['limit']; } else { $limit = 0; }
            $all = false;
        }
        
        $prepared_filter = $this->filter($hotaru->vars['filter'], $limit, $all, $hotaru->vars['select'], $hotaru->vars['orderby']);
        
        $stories = $this->getPosts($hotaru, $prepared_filter);
        
        return $stories;
    }
    
    
    /**
     * Prepare list filters
     *
     * @param string $type e.g. latest, upcoming, top-24-hours
     */
    public function prepareListFilters($hotaru, $type = '')
    {
        if ($type == 'new')
        {
            // Filters page to "new" stories only
            $hotaru->vars['filter']['post_archived = %s'] = 'N'; 
            $hotaru->vars['filter']['post_status = %s'] = 'new';
            $hotaru->vars['orderby'] = "post_date DESC";
            //$rss = "<a href='" . $hotaru->url(array('page'=>'rss', 'status'=>'new')) . "'>";
            //$rss .= " <img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png' alt='RSS' /></a>";
            //$hotaru->vars['page_title'] = $this->lang["post_breadcrumbs_latest"] . $rss;
        } 
        elseif ($type == 'upcoming') 
        {
            // Filters page to "new" stories by most votes, but only stories from the last X days!
            $vote_settings = unserialize($this->plugins->getSetting('vote_settings', 'vote_simple')); 
            $upcoming_duration = "-" . $vote_settings['vote_upcoming_duration'] . " days"; // default: -5 days
            
            $hotaru->vars['filter']['post_archived = %s'] = 'N'; 
            $hotaru->vars['filter']['post_status = %s'] = 'new'; 
            $start = date('YmdHis', strtotime("now"));
            $end = date('YmdHis', strtotime($upcoming_duration)); // should be negative
            $hotaru->vars['filter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
            $hotaru->vars['orderby'] = "post_votes_up DESC";
            //$hotaru->vars['page_title'] = $this->lang["post_breadcrumbs_upcoming"];
        } 
        elseif ($type == 'top-24-hours')
        {
            // Filters page to "top" stories from the last 24 hours only
            $hotaru->vars['filter']['post_status = %s'] = 'top'; 
            $start = date('YmdHis', strtotime("now"));
            $end = date('YmdHis', strtotime("-1 day"));
            $hotaru->vars['filter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
            $hotaru->vars['orderby'] = "post_votes_up DESC";
            //$hotaru->vars['page_title'] = $this->lang["post_breadcrumbs_top_24_hours"];
        } 
        elseif ($type == 'top-48-hours') 
        {
            // Filters page to "top" stories from the last 48 hours only
            $hotaru->vars['filter']['post_status = %s'] = 'top'; 
            $start = date('YmdHis', strtotime("now"));
            $end = date('YmdHis', strtotime("-2 days"));
            $hotaru->vars['filter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
            $hotaru->vars['orderby'] = "post_votes_up DESC";
            //$hotaru->vars['page_title'] = $this->lang["post_breadcrumbs_top_48_hours"];
        } 
        elseif ($type == 'top-7-days')
        {
            // Filters page to "top" stories from the last 7 days only
            $hotaru->vars['filter']['post_status = %s'] = 'top'; 
            $start = date('YmdHis', strtotime("now"));
            $end = date('YmdHis', strtotime("-7 days"));
            $hotaru->vars['filter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
            $hotaru->vars['orderby'] = "post_votes_up DESC";
            //$hotaru->vars['page_title'] = $this->lang["post_breadcrumbs_top_7_days"];
        } 
        elseif ($type == 'top-30-days')
        {
            // Filters page to "top" stories from the last 30 days only
            $hotaru->vars['filter']['post_status = %s'] = 'top'; 
            $start = date('YmdHis', strtotime("now"));
            $end = date('YmdHis', strtotime("-30 days"));
            $hotaru->vars['filter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
            $hotaru->vars['orderby'] = "post_votes_up DESC";
            //$hotaru->vars['page_title'] = $this->lang["post_breadcrumbs_top_30_days"];
        } 
        elseif ($type == 'top-365-days')
        {
            // Filters page to "top" stories from the last 365 days only
            $hotaru->vars['filter']['post_status = %s'] = 'top'; 
            $start = date('YmdHis', strtotime("now"));
            $end = date('YmdHis', strtotime("-365 days"));
            $hotaru->vars['filter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
            $hotaru->vars['orderby'] = "post_votes_up DESC";
            //$hotaru->vars['page_title'] = $this->lang["post_breadcrumbs_top_365_days"];
        } 
        elseif ($type == 'top-all-time')
        {
            // Filters page to "top" stories in order of votes
            $hotaru->vars['filter']['post_status = %s'] = 'top'; 
            $hotaru->vars['orderby'] = "post_votes_up DESC";
            //$hotaru->vars['page_title'] = $this->lang["post_breadcrumbs_top_all_time"];
        } 
        elseif ($type == 'top')
        {
            // Assume 'top' page and filter to 'top' stories.
            $hotaru->vars['filter']['post_archived = %s'] = 'N'; 
            $hotaru->vars['filter']['post_status = %s'] = 'top';
            $hotaru->vars['orderby'] = "post_date DESC";
            //$rss = "<a href='" . $hotaru->url(array('page'=>'rss', 'status'=>'top')) . "'>";
            //$rss .= " <img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png' alt='RSS' /></a>";
            //$hotaru->vars['page_title'] = $this->lang["post_breadcrumbs_top"] . $rss;
        }
        else
        {
            // Filters page to "all" stories
            $hotaru->vars['filter']['post_archived = %s'] = 'N'; 
            $hotaru->vars['filter']['(post_status = %s OR post_status = %s)'] = array('top', 'new');
            $hotaru->vars['orderby'] = "post_date DESC";
            //$rss = "<a href='" . $hotaru->url(array('page'=>'rss')) . "'>";
            //$rss .= " <img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png' alt='RSS' /></a>";
            //$hotaru->vars['page_title'] = $this->lang["post_breadcrumbs_all"] . $rss;
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
    public function getPosts($hotaru, $prepared_array = array())
    {
        if (!empty($prepared_array)) {
            if (empty($prepared_array[1])) {
                $posts = $hotaru->db->get_results($prepared_array[0]); // ignoring the prepare function.
            } else {
                $posts = $hotaru->db->get_results($hotaru->db->prepare($prepared_array)); 
            }
            if ($posts) { return $posts; }
        }
        
        return false;
    }
}
?>
