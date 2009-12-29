<?php
/**
 * name: Tags
 * description: Enables tags for posts
 * version: 1.3
 * folder: tags
 * class: Tags
 * requires: submit 1.4, sidebar_widgets 0.5
 * hooks: install_plugin, theme_index_top, header_include, header_include_raw, header_meta, theme_index_main, submit_form_2_assign, submit_form_2_fields, submit_form_2_check_for_errors, submit_form_2_process_submission, submit_show_post_extra_fields, submit_show_post_extras, submit_settings_get_values, submit_settings_form, submit_save_settings, post_list_filter, post_delete_post, admin_plugin_settings, admin_sidebar_plugin_settings
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

class Tags extends PluginFunctions
{
     /**
     * ********************************************************************* 
     * ********************* FUNCTIONS FOR POST CLASS ********************** 
     * *********************************************************************
     * ****************************************************************** */
     
    /**
     * Add a post_tags field to posts table if it doesn't alredy exist
     */
    public function install_plugin()
    {
        $tags_settings = $this->getSerializedSettings();
        
        if (!isset($tags_settings['tags_num_tags_page'])) { $tags_settings['tags_num_tags_page'] = 100; }
        if (!isset($tags_settings['tags_num_tags_widget'])) { $tags_settings['tags_num_tags_widget'] = 25; }
        if (!isset($tags_settings['tags_widget_title'])) { $tags_settings['tags_widget_title'] = 'checked'; }
        
        $this->updateSetting('tags_settings', serialize($tags_settings));
        
        /*
        require_once(PLUGINS . 'sidebar_widgets/libs/Sidebar.php');
        $sidebar = new Sidebar($this->hotaru);
        $sidebar->addWidget('tags', 'tag_cloud', '');  // plugin name, function name, optional arguments
        */
    }
    
    
    /**
     * Add additional member variables when the $post object is read in the Submit plugin.
     */
    public function theme_index_top()
    {
        // Get page title:
        switch ($h->pageName)
        {
            case 'tag-cloud':
                $h->pageTitle = $h->lang["tags_tag_cloud"];
                $h->pageType = 'tags';
                break;
        }
        
        // friendly URLs: FALSE
        if ($h->cage->get->keyExists('tag')) { 
            $h->pageTitle = stripslashes(make_name($h->cage->get->noTags('tag')));
            $h->pageType = 'tags';
        } 
        
        // friendly URLs: TRUE
        if (!$h->pageTitle && $h->cage->get->keyExists('pos2')) { 
            $h->pageTitle = stripslashes(make_name($h->cage->get->noTags('pos2'))); 
            $h->pageType = 'tags';
        }
        
    }
    
    
    /**
     * Match meta tag to a post's keywords (description is done in the Submit plugin)
     */
    public function header_meta()
    {    
        if ($h->pageType == 'post') {
            echo '<meta name="keywords" content="' . stripslashes($h->post->tags) . '">' . "\n";
            return true;
        }
    }
    
    
    /**
     * JavaScript for dropdown tags list
     */
    public function header_include_raw()
    {    
        echo "<script type='text/javascript'>\n";
        echo "$(document).ready(function(){\n";
            echo "$('.tags_link').click(function () {\n";
            echo "var target = $(this).parents('div').next('div').children('div.show_tags');\n";
            echo "target.fadeToggle();\n";
            echo "return false;\n";
            echo "});\n";
        echo "});\n";
        echo "</script>\n";
    }
    
    
    /**
     * Display the tag cloud page
     *
     * @return bool
     */
    public function theme_index_main()
    {
        if ($h->pageName == 'tag-cloud') 
        {
            // get the number of tags to show:
            $tags_settings = $h->getSerializedSettings();
            $tag_count = $tags_settings['tags_num_tags_page'];
            
            // build the tag cloud:
            $h->vars['tagCloud'] = $this->buildTagCloud($tag_count);
            
            // display the tag cloud:
            $h->displayTemplate('tag_cloud');
            return true;
        } 
        
        return false;
    }
            
            
    /**
     * Sidebar Widget Tag Cloud
     */
    public function sidebar_widget_tag_cloud()
    {
        $tags_settings = $this->getSerializedSettings('tags');
        $tag_count = $tags_settings['tags_num_tags_widget'];
        $show_title = $tags_settings['tags_widget_title'];
        
        // build the tag cloud:
        $cloud = $this->buildTagCloud($tag_count);
        if (!$cloud) { return false; }
        
        if ($show_title) {
            echo "<h2 class='sidebar_widget_head widget_tag_cloud_title'>";
            echo "<a href='" . $h->url(array('page' => 'tag-cloud')) . "'>";
            echo $h->lang["tags_tag_cloud_widget_title"] . "</a>\n";
            echo "</h2>\n";
        }
        
        echo "<div class='sidebar_widget_body widget_tag_cloud'>";
        foreach ($cloud as $tag) {
            echo "<a href='" . $h->url(array('tag' => $tag['link_word'])) . "' ";
            echo "class='widget_tag_group" . $tag['class'] . "'>" . $tag["show_word"] . "</a>\n";
        }
        echo "<a href='" . $h->url(array('page' => 'tag-cloud')) . "' ";
        echo "class='widget_more_tags'>" . $h->lang["tags_tag_cloud_widget_more"] . "</a>\n";
        echo "</div>";
    }
    
    
    /**
     * Build Tag Cloud
     *
     * @param int $count number of tags to show
     * @return array
     */
    public function buildTagCloud($count)
    {
        // get tags from the database:
        //$sql = "SELECT tags_word FROM " . TABLE_TAGS . " WHERE tags_archived = %s";
        //$tags = $h->db->get_results($h->db->prepare($sql, 'N'));
        
        $h->smartCache('on', 'tags', 10); // start using cache (lasts 10 mins if no update to tags table)
        
        $sql = "SELECT tags_word FROM " . TABLE_TAGS . ", " . TABLE_POSTS;
        $sql .= " WHERE tags_archived = %s AND (tags_post_id = post_id) AND";
        $sql .= " (post_status = %s || post_status = %s)";
        $tags = $h->db->get_results($h->db->prepare($sql, 'N', 'new', 'top'));
        
        if (!$tags) { return false; }
        
        $h->smartCache('off'); // stop using cache
        
        // Put the tags in an array:
        $tags_array = array();
        if ($tags) {
            foreach ($tags as $tag) {
                array_push($tags_array, $tag->tags_word);
            }
        }
        
        // Find the most popular X tags from withing $tags_array:
        $sorted_tags = array_count_values($tags_array);
        arsort($sorted_tags);
        $popular_tags = array_chunk($sorted_tags, $count, TRUE);
        
        // convert first chunk from associative to ordinary array:
        $popular_tags = array_keys($popular_tags[0]); 
        
        // Divide into 10 groups and assign a class number (0 ~ 9) to each group:
        $grouped_tags = array_chunk($popular_tags, ($count/10), TRUE);
        foreach ($grouped_tags as $groupid => $group) {
            foreach ($group as $rank => $tag) {
                $tag = trim(urldecode($tag));
                $classed_tags[$rank]['link_word'] = $tag;
                $classed_tags[$rank]['show_word'] = $tag = stripslashes(str_replace('_', ' ', $tag));
                $classed_tags[$rank]['class'] = $groupid;
            }
        }
        
        // Shuffle the order of the classed tags:
        shuffle($classed_tags);
        
        return $classed_tags;
    }
    
    
    /**
     * Add tags to the tags table
     */
    public function post_add_post()
    {
        // Tags table
        if (!empty($h->post->vars['tags'])) {
            $tags_array = explode(',', $h->post->vars['tags']);
            if ($tags_array) {
                foreach ($tags_array as $tag) {
                    $sql = "INSERT INTO " . TABLE_TAGS . " SET tags_post_id = %d, tags_date = CURRENT_TIMESTAMP, tags_word = %s, tags_updateby = %d";
                    $h->db->query($h->db->prepare($sql, $h->post->vars['last_insert_id'], urlencode(str_replace(' ', '_', trim($tag))), $this->current_user->id));
                }
            }
        }
    }
    
    
    /**
     * Update tags in the tags table
     */
    public function post_update_post()
    {
        // Delete existing tags from tags table
        $sql = "DELETE from " . TABLE_TAGS . " WHERE tags_post_id = %d";
        $h->db->query($h->db->prepare($sql, $h->post->id));
        
        // Reinsert into tags table
        if (!empty($h->post->vars['tags'])) {
            $tags_array = explode(',', $h->post->vars['tags']);
            if ($tags_array) {
                foreach ($tags_array as $tag) {
                    $sql = "INSERT INTO " . TABLE_TAGS . " SET tags_post_id = %d, tags_date = CURRENT_TIMESTAMP, tags_word = %s, tags_updateby = %d";
                    $h->db->query($h->db->prepare($sql, $h->post->id, urlencode(str_replace(' ', '_', trim($tag))), $this->current_user->id));
                }
            }
        }
    }
    
    
     /**
     * ********************************************************************* 
     * ******************* FUNCTIONS FOR SHOWING POSTS ********************* 
     * *********************************************************************
     * ****************************************************************** */
     
    
    /**
     * Gets a tag from the url and sets the filter for get_posts
     */
    public function post_list_filter()
    {
        if ($h->cage->get->keyExists('tag')) 
        {
            // friendly URLs: FALSE
            $tag = stripslashes($h->cage->get->noTags('tag')); 
            
            // friendly URLs: TRUE
            if (!$tag) { $tag = $h->cage->get->noTags('pos2'); } 
            
            if ($tag) {
                $h->vars['filter']['post_tags LIKE %s'] = '%' . urlencode($tag) . '%'; 
                $h->vars['filter']['post_archived = %s'] = 'N'; // don't include archived posts
                $rss = " <a href='" . $h->url(array('page'=>'rss', 'tag'=>$tag)) . "'>";
            }
            
            $rss .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png'></a>";       
            $h->vars['page_title'] = $h->lang["post_breadcrumbs_tag"] . " &raquo; " . stripslashes($h->title) . $rss;
            
            return true;    
        }
        
        return false;    
    }
    
    
    /**
     * Shows tags in each post
     */
    public function submit_show_post_extra_fields()
    { 
        if ($h->post->vars['useTags'] && $h->post->vars['tags'])
        { 
            echo "<li><a class='tags_link' href='#'>" . $h->lang["submit_show_tags"]  . "</a></li>\n";
        }
    }
    
    
     /**
     * List of tags
     */
    public function submit_show_post_extras($vars = array())
    {
        if (!$h->post->vars['tags']) { return false; }
        
        $tags = explode(',', $h->post->vars['tags']);
        
        // lots of nice issets for php 5.3 compatibility
        if (isset($vars[0]) && isset($vars[1]) && ($vars[0] == "tags") && ($vars[1] == "raw")) {
            $raw = true;
        } else {
            $raw = false;
        }
        
        if (!$raw) {
            echo "<div class='show_tags' style='display: none;'>\n";
            echo "<ul>" . $h->lang["submit_show_tags"] . ": \n";
        }
        
        foreach ($tags as $tag) {
            echo "<a href='" . $h->url(array('tag' => str_replace(' ', '_', trim($tag)))) . "'>" . trim($tag) . "</a>&nbsp;\n";
        }
        
        if (!$raw) {
            echo "</ul>\n";
            echo "</div>\n";
        }
    }

    
    /**
     * Delete tags when post deleted
     */
    public function post_delete_post()
    {
        $sql = "DELETE FROM " . TABLE_TAGS . " WHERE tags_post_id = %d";
        $h->db->query($h->db->prepare($sql, $h->post->id));
    }
    
    
    /**
     * Show link in the Admin sidebar
     */
    public function admin_sidebar_plugin_settings()
    {
        $vars['plugin'] = $this->folder;
        $vars['name'] = $h->lang["tags_tag_cloud"];
        return $vars;
    }
        
}
?>