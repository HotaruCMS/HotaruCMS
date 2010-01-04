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
    public function prepareList($h, $type = '')
    {
        if (!isset($h->vars['filter'])) { $h->vars['filter'] = array(); }
        
        if ($type) {
            // For sidebar posts or other non-pages... 
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

            $h->pluginHook('sb_base_functions_preparelist'); // formerly post_list_filter
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
        
        $prepared_filter = $this->filter($h->vars['filter'], $limit, $all, $h->vars['select'], $h->vars['orderby']);
        
        $stories = $this->getPosts($h, $prepared_filter);
        
        return $stories;
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
            $h->vars['orderby'] = "post_votes_up DESC";
        } 
        elseif ($type == 'top-24-hours')
        {
            // Filters page to "top" stories from the last 24 hours only
            $h->vars['filter']['post_status = %s'] = 'top'; 
            $start = date('YmdHis', strtotime("now"));
            $end = date('YmdHis', strtotime("-1 day"));
            $h->vars['filter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
            $h->vars['orderby'] = "post_votes_up DESC";
        } 
        elseif ($type == 'top-48-hours') 
        {
            // Filters page to "top" stories from the last 48 hours only
            $h->vars['filter']['post_status = %s'] = 'top'; 
            $start = date('YmdHis', strtotime("now"));
            $end = date('YmdHis', strtotime("-2 days"));
            $h->vars['filter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
            $h->vars['orderby'] = "post_votes_up DESC";
        } 
        elseif ($type == 'top-7-days')
        {
            // Filters page to "top" stories from the last 7 days only
            $h->vars['filter']['post_status = %s'] = 'top'; 
            $start = date('YmdHis', strtotime("now"));
            $end = date('YmdHis', strtotime("-7 days"));
            $h->vars['filter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
            $h->vars['orderby'] = "post_votes_up DESC";
        } 
        elseif ($type == 'top-30-days')
        {
            // Filters page to "top" stories from the last 30 days only
            $h->vars['filter']['post_status = %s'] = 'top'; 
            $start = date('YmdHis', strtotime("now"));
            $end = date('YmdHis', strtotime("-30 days"));
            $h->vars['filter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
            $h->vars['orderby'] = "post_votes_up DESC";
        } 
        elseif ($type == 'top-365-days')
        {
            // Filters page to "top" stories from the last 365 days only
            $h->vars['filter']['post_status = %s'] = 'top'; 
            $start = date('YmdHis', strtotime("now"));
            $end = date('YmdHis', strtotime("-365 days"));
            $h->vars['filter']['(post_date >= %s AND post_date <= %s)'] = array($end, $start); 
            $h->vars['orderby'] = "post_votes_up DESC";
        } 
        elseif ($type == 'top-all-time')
        {
            // Filters page to "top" stories in order of votes
            $h->vars['filter']['post_status = %s'] = 'top'; 
            $h->vars['orderby'] = "post_votes_up DESC";
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
        if (!empty($prepared_array)) {
            if (empty($prepared_array[1])) {
                $posts = $h->db->get_results($prepared_array[0]); // ignoring the prepare function.
            } else {
                $posts = $h->db->get_results($h->db->prepare($prepared_array));
            }
            if ($posts) { return $posts; }
        }
        
        return false;
    }
}
?>
