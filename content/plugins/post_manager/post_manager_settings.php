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
    
class PostManagerSettings extends PostManager
{
    /**
     * Main function that calls others
     *
     * @return bool
     */
    public function settings()
    {    
        // clear variables:
        $this->hotaru->vars['search_term'] = '';
        $this->hotaru->vars['post_status_filter'] = 'all';
        
        // Get unique statuses for Filter form:
        $this->hotaru->vars['statuses'] = $this->hotaru->post->getUniqueStatuses(); 
        
        $p = new Post($this->hotaru);
        
        // if checkboxes (delete)
        if (($this->cage->get->getAlpha('type') == 'checkboxes') && ($this->cage->get->keyExists('post'))) {
            foreach ($this->cage->get->keyExists('post') as $id => $checked) {
                $p->id = $id;
                $p->changeStatus('buried');
            }

            $this->hotaru->message = $this->lang["post_man_checkboxes_executed"];
            $this->hotaru->messageType = 'green';
        }
        
        
        // if search
        if ($this->cage->get->getAlpha('type') == 'search') {
            $search_term = $this->cage->get->getMixedString2('search_value');
            $this->hotaru->vars['search_term'] = $search_term; // used to refill the search box after a search
            if ($this->isActive('search')) {
                $s = new Search('post_manager', $this->hotaru);
                $s->prepareSearchFilter(stripslashes(trim($this->db->escape($search_term))));
                $filtered_search = $p->filter($this->hotaru->vars['filter'], 0, true, $this->hotaru->vars['select'], $this->hotaru->vars['orderby']);
                $posts = $p->getPosts($filtered_search);
            } else {
                $this->hotaru->message = $this->lang["post_man_need_search"];
                $this->hotaru->messageType = 'red';
            }
        }
        
        
        // if filter
        if ($this->cage->get->getAlpha('type') == 'filter') {
            $filter = $this->cage->get->testAlnumLines('post_status_filter');
            $this->hotaru->vars['post_status_filter'] = $filter;  // used to refill the filter box after use
            switch ($filter) {
                case 'all': 
                    $sort_clause = ' ORDER BY post_date DESC'; // ordered newest first for convenience
                    $sql = "SELECT * FROM " . TABLE_POSTS . $sort_clause;
                    $filtered_results = $this->db->get_results($this->db->prepare($sql)); 
                    break;
                case 'not_buried': 
                    $where_clause = " WHERE post_status != %s"; 
                    $sort_clause = ' ORDER BY post_date DESC'; // ordered newest first for convenience
                    $sql = "SELECT * FROM " . TABLE_POSTS . $where_clause . $sort_clause;
                    $filtered_results = $this->db->get_results($this->db->prepare($sql, 'buried')); 
                    break;
                case 'newest':
                    $sort_clause = ' ORDER BY post_date DESC';  // same as "all"
                    $sql = "SELECT * FROM " . TABLE_POSTS . $sort_clause;
                    $filtered_results = $this->db->get_results($this->db->prepare($sql)); 
                    break;
                case 'oldest':
                    $sort_clause = ' ORDER BY post_date ASC';
                    $sql = "SELECT * FROM " . TABLE_POSTS . $sort_clause;
                    $filtered_results = $this->db->get_results($this->db->prepare($sql)); 
                    break;
                default:
                    $where_clause = " WHERE post_status = %s"; $sort_clause = ' ORDER BY post_date DESC'; // ordered newest first for convenience
                    $sql = "SELECT * FROM " . TABLE_POSTS . $where_clause . $sort_clause;
                    $filtered_results = $this->db->get_results($this->db->prepare($sql, $filter)); // filter = new, top, or other post status
                    break;
            }

            $posts = $filtered_results;
        }

        if(!isset($posts)) {
            // default list
            $sort_clause = ' ORDER BY post_date DESC'; // ordered newest first for convenience
            $sql = "SELECT * FROM " . TABLE_POSTS . $sort_clause;
            $posts = $this->db->get_results($this->db->prepare($sql)); 
        }
        
        if ($posts) { 
            $this->hotaru->vars['post_man_rows'] = $this->drawRows($p, $posts, $filter, $search_term);
        }
        
        // Show template:
        $this->hotaru->displayTemplate('post_man_main', 'post_manager');
    }
    
    
    public function drawRows($p, $posts, $filter = '', $search_term = '')
    {
        // prepare for showing posts, 20 per page
        $pg = $this->cage->get->getInt('pg');
        $items = 20;
        
        require_once(EXTENSIONS . 'Paginated/Paginated.php');
        require_once(EXTENSIONS . 'Paginated/DoubleBarLayout.php');
        $pagedResults = new Paginated($posts, $items, $pg);
        
        $output = "";
        $alt = 0;
        while($post = $pagedResults->fetchPagedRow()) {    //when $story is false loop terminates    
            $alt++;
            
            // We need user for the post author's name:
            $user = new UserBase($this->hotaru);
            $user->getUserBasic($post->post_author);
            
            // If the Category class is available we can show the post's category name instead of just the id
            if (file_exists(PLUGINS . 'categories/libs/Category.php')) {
                include_once(PLUGINS . 'categories/libs/Category.php');
                $cat = new Category($this->db);
                $category = stripslashes($cat->getCatName($post->post_category)); // shows cat name
            } else {
                $category = $post->post_category;   // shows cat id
            }

            // need to read the post into the Post object and store it in Hotaru (the url function needs it for friendly urls).
            $p->readPost($post->post_id);
            $this->hotaru->post = $p;

            $edit_link = BASEURL . "index.php?from=post_man&amp;page=edit_post&amp;post_id=" . $post->post_id; 
            if ($filter) { $edit_link .= "&amp;post_status_filter=" . $filter; }
            if ($search_term) { $edit_link .= "&amp;search_value=" . $search_term; }
            if ($pg) { $edit_link .= "&amp;pg=" . $pg; }
            
            $output .= "<tr class='table_row_" . $alt % 2 . "'>\n";
            $output .= "<td class='pm_id'>" . $post->post_id . "</td>\n";
            $output .= "<td class='pm_status'>" . $post->post_status . "</td>\n";
            $output .= "<td class='pm_date'>" . date('d M y', strtotime($post->post_date)) . "</a></td>\n";
            $output .= "<td class='pm_title'><a class='table_drop_down' href='#' title='" . $this->lang["post_man_show_content"] . "'>";
            $output .= stripslashes(urldecode($post->post_title)) . "</a></td>\n";
            $output .= "<td class='pm_edit'>" . "<a href='" . $edit_link . "'>\n";
            $output .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/edit.png'>" . "</a></td>\n";
            $output .= "<td class='pm_check'><input type='checkbox' name='post[" . $post->post_id . "] value='" . $post->post_id . "'></td>\n";
            $output .= "</tr>\n";
            
            $output .= "<tr class='table_tr_details' style='display:none;'>\n";
            $output .= "<td colspan=6 class='table_description pm_description'>\n";
            $output .= "<a class='table_hide_details' style='float: right;' href='#'>[" . $this->lang["admin_theme_plugins_close"] . "]</a>";
            $output .= "<b>" . stripslashes(urldecode($post->post_title)) . "</b><br />\n";
            $output .= "<i>" . $this->hotaru->lang["post_man_posted"] ."</i> " .  date('d M Y H:i:s', strtotime($post->post_date)) . "<br />\n";
            $output .= "<i>" . $this->hotaru->lang["post_man_author"] ."</i> " . $user->name . " (id:" .  $post->post_author . ")<br />\n";
            $output .= "<p><i>" . $this->hotaru->lang["post_man_content"] ."</i> " . stripslashes(urldecode($post->post_content)) . "</p> \n";
            $output .= "<i>" . $this->hotaru->lang["post_man_category"] ."</i> " . $category . "<br /> \n";   // we got $category above
            $output .= "<i>" . $this->hotaru->lang["post_man_tags"] ."</i> " . (urldecode($post->post_tags)) . "<br /> \n";
            $output .= "<i>" . $this->hotaru->lang["post_man_urls"] ."</i> <a href='" . $this->hotaru->url(array('page'=>$post->post_id)) . "'>" . SITE_NAME . " Post</a> | \n";
            $output .= "<a href='" . urldecode($post->post_orig_url) . "'>Original Post</a><br />\n";
            $output .= "</td></tr>";
        }
        
        if ($pagedResults) {
            $pagedResults->setLayout(new DoubleBarLayout());
            $this->hotaru->vars['post_man_navi'] = $pagedResults->fetchPagedNavigation('', $this->hotaru);
        }
        
        return $output;
    }

}
?>