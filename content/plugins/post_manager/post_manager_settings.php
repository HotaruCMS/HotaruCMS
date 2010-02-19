<?php
/**
 * File: plugins/post_manager/post_manager_settings.php
 * Purpose: The functions that do the hard work such as adding, deleting and sorting categories.
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
    
class PostManagerSettings
{
    /**
     * Main function that calls others
     *
     * @return bool
     */
    public function settings($h)
    {    
        // grab the number of pending posts:
        $sql = "SELECT COUNT(post_id) FROM " . TABLE_POSTS . " WHERE post_status = %s";
        $num_pending = $h->db->get_var($h->db->prepare($sql, 'pending'));
        if (!$num_pending) { $num_pending = "0"; } 
        $h->vars['num_pending'] = $num_pending; 
        
        // clear variables:
        $h->vars['search_term'] = '';
        
        // Get settings 
        $submit_settings = $h->getSerializedSettings('submit');
        $set_pending = $submit_settings['set_pending'];

        if (($set_pending == 'some_pending') || ($set_pending == 'all_pending')) {
            $h->vars['post_status_filter'] = 'pending';
        } else {
            $h->vars['post_status_filter'] = 'all';
        }
        
        // Get unique statuses for Filter form:
        $h->vars['statuses'] = $h->getUniqueStatuses(); 
       
        // if checkboxes
        if (($h->cage->get->getAlpha('type') == 'checkboxes') && ($h->cage->get->keyExists('post'))) 
        {
            foreach ($h->cage->get->keyExists('post') as $id => $checked) {
                $h->readPost($id);
                $h->message = $h->lang["post_man_checkboxes_status_changed"]; // default "Changed status" message
                switch ($h->cage->get->testAlnumLines('checkbox_action')) {
                    case 'new_selected':
                        $h->changePostStatus('new');
                        $h->pluginHook('post_man_status_new');
                        break;
                    case 'top_selected':
                        $h->changePostStatus('top');
                        $h->pluginHook('post_man_status_top');
                        break;
                    case 'pending_selected':
                        $h->changePostStatus('pending');
                        $h->pluginHook('post_man_status_pending');
                        break;
                    case 'bury_selected':
                        $h->changePostStatus('buried');
                        $h->pluginHook('post_man_status_buried');
                        break;
                    case 'delete_selected':
                        $h->deletePost(); 
                        $h->pluginHook('post_man_delete');
                        $h->message = $h->lang["post_man_checkboxes_post_deleted"];
                        break;
                    default:
                        // do nothing
                        $h->message = $h->lang["post_man_checkboxes_no_action"];
                        $h->messageType = 'red';
                        break;
                }
                
            }
            
            // Need to clear both these caches to be sure related items are updated in widgets, etc.:
            $h->clearCache('html_cache', false); 
            $h->clearCache('db_cache', false); 
        }
        
        $p = new Post();
        
        // if search
        $search_term = '';
        if ($h->cage->get->getAlpha('type') == 'search') {
            $search_term = $h->cage->get->sanitizeTags('search_value');
            $h->vars['search_term'] = $search_term; // used to refill the search box after a search
            if ($h->isActive('search')) {
                if (strlen($search_term) < 3) {
                    $h->message = $h->lang["user_man_search_too_short"];
                    $h->messageType = 'red';
                } else {
                    $s = new Search();
                    require_once(PLUGINS . 'sb_base/libs/SbBaseFunctions.php');
                    $sbFuncs = new SbBaseFunctions();
                    
                    // get count
                    $s->prepareSearchFilter($h, stripslashes(trim($h->db->escape($search_term))), 'count');
                    $filtered_search = $sbFuncs->filter($h->vars['filter'], 0, true, $h->vars['select'], $h->vars['orderby']);
                    $posts_count = $sbFuncs->getPosts($h, $filtered_search);
                    $count = $posts_count[0]->number;
                    
                    // get query
                    $s->prepareSearchFilter($h, stripslashes(trim($h->db->escape($search_term))), 'query');
                    $prepared_filter = $sbFuncs->filter($h->vars['filter'], 0, true, $h->vars['select'], $h->vars['orderby']);
                    if (isset($prepared_filter[1])) {
                        $query = $h->db->prepare($prepared_filter);
                    } else {
                        $query = $prepared_filter[0];    // returns the prepared query array
                    }
                }
            } else {
                $h->message = $h->lang["post_man_need_search"];
                $h->messageType = 'red';
            }
        }
        
        
        // if filter
        $filter = '';
        if ($h->cage->get->getAlpha('type') == 'filter') {
            $filter = $h->cage->get->testAlnumLines('post_status_filter');
            $h->vars['post_status_filter'] = $filter;  // used to refill the filter box after use
            switch ($filter) {
                case 'all': 
                    $sort_clause = ' ORDER BY post_date DESC'; // ordered newest first for convenience
                    $count_sql = "SELECT count(*) AS number FROM " . TABLE_POSTS;
                    $count = $h->db->get_var($h->db->prepare($count_sql));
                    $sql = "SELECT * FROM " . TABLE_POSTS . $sort_clause;
                    $query = $h->db->prepare($sql); 
                    break;
                case 'not_buried': 
                    $where_clause = " WHERE post_status != %s"; 
                    $sort_clause = ' ORDER BY post_date DESC'; // ordered newest first for convenience
                    $count_sql = "SELECT count(*) AS number FROM " . TABLE_POSTS . $where_clause;
                    $count = $h->db->get_var($h->db->prepare($count_sql, 'buried'));
                    $sql = "SELECT * FROM " . TABLE_POSTS . $where_clause . $sort_clause;
                    $query = $h->db->prepare($sql, 'buried'); 
                    break;
                case 'newest':
                    $sort_clause = ' ORDER BY post_date DESC';  // same as "all"
                    $count_sql = "SELECT count(*) AS number FROM " . TABLE_POSTS;
                    $count = $h->db->get_var($h->db->prepare($count_sql));
                    $sql = "SELECT * FROM " . TABLE_POSTS . $sort_clause;
                    $query = $h->db->prepare($sql); 
                    break;
                case 'oldest':
                    $sort_clause = ' ORDER BY post_date ASC';
                    $count_sql = "SELECT count(*) AS number FROM " . TABLE_POSTS;
                    $count = $h->db->get_var($h->db->prepare($count_sql));
                    $sql = "SELECT * FROM " . TABLE_POSTS . $sort_clause;
                    $query = $h->db->prepare($sql); 
                    break;
                default:
                    $where_clause = " WHERE post_status = %s"; $sort_clause = ' ORDER BY post_date DESC'; // ordered newest first for convenience
                    $count_sql = "SELECT count(*) AS number FROM " . TABLE_POSTS . $where_clause;
                    $count = $h->db->get_var($h->db->prepare($count_sql, $filter));
                    $sql = "SELECT * FROM " . TABLE_POSTS . $where_clause . $sort_clause;
                    $query = $h->db->prepare($sql, $filter); // filter = new, top, or other post status
                    break;
            }
        }

        if(!isset($query)) {
            // default list
            if ($h->vars['post_status_filter'] == 'pending') {
                $where_clause = " WHERE post_status = %s";
                $sort_clause = ' ORDER BY post_date DESC'; // ordered newest first for convenience
                $count_sql = "SELECT count(*) AS number FROM " . TABLE_POSTS . $where_clause;
                $count = $h->db->get_var($h->db->prepare($count_sql, 'pending'));
                $sql = "SELECT * FROM " . TABLE_POSTS . $where_clause . $sort_clause;
                $query = $h->db->prepare($sql, 'pending'); 
            } else {
                $sort_clause = ' ORDER BY post_date DESC'; // ordered newest first for convenience
                $count_sql = "SELECT count(*) AS number FROM " . TABLE_POSTS;
                $count = $h->db->get_var($h->db->prepare($count_sql));
                $sql = "SELECT * FROM " . TABLE_POSTS . $sort_clause;
                $query = $h->db->prepare($sql); 
            }
        }
        
        $pagedResults = $h->pagination($query, $count, 20, 'posts');
        
        if ($pagedResults) { 
            $h->vars['post_man_rows'] = $this->drawRows($h, $p, $pagedResults, $filter, $search_term);
        } elseif ($h->vars['post_status_filter'] == 'pending') {
            $h->message = $h->lang['post_man_no_pending_posts'];
            $h->messageType = 'green';
        }
        
        // Show template:
        $h->displayTemplate('post_man_main', 'post_manager');
    }
    
    
    public function drawRows($h, $p, $pagedResults, $filter = '', $search_term = '')
    {
        $output = "";
        $alt = 0;
        $pg = $h->cage->get->getInt('pg');
        
        if (!$pagedResults->items) { return ""; }
        
        foreach ($pagedResults->items as $post) 
        {
            $alt++;
            
            $username = $h->getUserNameFromId($post->post_author);
            $category = $h->getCatName($post->post_category); // shows cat name

            // need to read the post into the Post object and store it in Hotaru (the url function needs it for friendly urls).
            $p->readPost($h, 0, $post);
            $h->post = $p;

            $edit_link = BASEURL . "index.php?from=post_man&amp;page=edit_post&amp;post_id=" . $post->post_id; 
            if ($filter) { $edit_link .= "&amp;post_status_filter=" . $filter; }
            if ($search_term) { $edit_link .= "&amp;search_value=" . $search_term; }
            if ($pg) { $edit_link .= "&amp;pg=" . $pg; }
            
            // put icons next to the username with links to User Manager
            $h->vars['user_manager_name_icons'] = array($username, ''); // second param is "output"
            $h->pluginHook('post_manager_user_name');
            $icons = $h->vars['user_manager_name_icons'][1]; // 1 is the second param: output
            
            $output .= "<tr class='table_row_" . $alt % 2 . "'>\n";
            $output .= "<td class='pm_id'>" . $post->post_id . "</td>\n";
            $output .= "<td class='pm_status'>" . $post->post_status . "</td>\n";
            $output .= "<td class='pm_date'>" . date('d M y', strtotime($post->post_date)) . "</a></td>\n";
            $output .= "<td class='pm_author'><a href='" . $h->url(array('user'=>$username)) . "' title='User Profile'>" . $username . "</a>" . $icons . "</td>\n";
            $output .= "<td class='pm_title'><a class='table_drop_down' href='#' title='" . $h->lang["post_man_show_content"] . "'>";
            $output .= stripslashes(urldecode($post->post_title)) . "</a></td>\n";
            $output .= "<td class='pm_edit'>" . "<a href='" . $edit_link . "'>\n";
            $output .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/edit.png'>" . "</a></td>\n";
            $output .= "<td class='pm_check'><input type='checkbox' name='post[" . $post->post_id . "]' value='" . $post->post_id . "'></td>\n";
            $output .= "</tr>\n";
            
            $output .= "<tr class='table_tr_details' style='display:none;'>\n";
            $output .= "<td colspan=7 class='table_description pm_description'>\n";
            $output .= "<a class='table_hide_details' style='float: right;' href='#'>[" . $h->lang["admin_theme_plugins_close"] . "]</a>";
            $output .= "<b>" . stripslashes(urldecode($post->post_title)) . "</b><br />\n";
            $output .= "<i>" . $h->lang["post_man_posted"] ."</i> " .  date('d M Y H:i:s', strtotime($post->post_date)) . "<br />\n";
            $output .= "<i>" . $h->lang["post_man_author"] ."</i> <a href='" . $h->url(array('user'=>$username)) . "' title='User Profile'>" . $username . "</a> (id:" .  $post->post_author . ")" . "<br />\n";
            $output .= "<p><i>" . $h->lang["post_man_content"] ."</i> " . stripslashes(urldecode($post->post_content)) . "</p> \n";
            $output .= "<i>" . $h->lang["post_man_category"] ."</i> " . $category . "<br /> \n";   // we got $category above
            $output .= "<i>" . $h->lang["post_man_tags"] ."</i> " . (urldecode($post->post_tags)) . "<br /> \n";
            $output .= "<i>" . $h->lang["post_man_urls"] ."</i> <a href='" . $h->url(array('page'=>$post->post_id)) . "'>" . SITE_NAME . " " . $h->lang["post_man_post"] ."</a> | \n";
            $output .= "<a href='" . urldecode($post->post_orig_url) . "'>" . $h->lang["post_man_original_post"] ."</a><br />\n";
            $output .= "</td></tr>";
        }
        
        if ($pagedResults) {
            $h->vars['post_man_navi'] = $h->pageBar($pagedResults);
        }
        
        return $output;
    }
}
?>
