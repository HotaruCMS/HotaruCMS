<?php
/**
 * name: Tag Cloud
 * description: Tag cloud page and widget
 * version: 0.1
 * folder: tag_cloud
 * class: TagCloud
 * requires: submit 1.9, widgets 0.6, tags 1.4
 * hooks: install_plugin, theme_index_top, header_include, theme_index_main, admin_plugin_settings, admin_sidebar_plugin_settings
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

class TagCloud
{
     /**
     * ********************************************************************* 
     * ********************* FUNCTIONS FOR POST CLASS ********************** 
     * *********************************************************************
     * ****************************************************************** */
     
    /**
     * Add a post_tags field to posts table if it doesn't alredy exist
     */
    public function install_plugin($h)
    {
        $tag_cloud_settings = $h->getSerializedSettings();
        
        if (!isset($tag_cloud_settings['tags_num_tags_page'])) { $tag_cloud_settings['tags_num_tags_page'] = 100; }
        if (!isset($tag_cloud_settings['tags_num_tags_widget'])) { $tag_cloud_settings['tags_num_tags_widget'] = 25; }
        if (!isset($tag_cloud_settings['tags_widget_title'])) { $tag_cloud_settings['tags_widget_title'] = 'checked'; }
        
        $h->updateSetting('tag_cloud_settings', serialize($tag_cloud_settings));

        // widget
        $h->addWidget('tag_cloud', 'tag_cloud', '');  // plugin name, function name, optional arguments
    }
    
    
    /**
     * Add additional member variables when the $post object is read in the Submit plugin.
     */
    public function theme_index_top($h)
    {
        // Get page title:
        switch ($h->pageName)
        {
            case 'tag-cloud':
                $h->pageTitle = $h->lang["tag_cloud"];
                $h->pageType = 'tags';
                break;
        }
    }
    
    
    /**
     * Display the tag cloud page
     *
     * @return bool
     */
    public function theme_index_main($h)
    {
        if ($h->pageName == 'tag-cloud') 
        {
            // get the number of tags to show:
            $tag_cloud_settings = $h->getSerializedSettings();
            $tag_count = $tag_cloud_settings['tags_num_tags_page'];
            
            // build the tag cloud:
            $h->vars['tagCloud'] = $this->buildTagCloud($h, $tag_count);
            
            // display the tag cloud:
            $h->displayTemplate('tag_cloud');
            return true;
        } 
        
        return false;
    }
            
            
    /**
     * Widget Tag Cloud
     */
    public function widget_tag_cloud($h)
    {
        $tag_cloud_settings = $h->getSerializedSettings('tag_cloud');
        $tag_count = $tag_cloud_settings['tags_num_tags_widget'];
        $show_title = $tag_cloud_settings['tags_widget_title'];
        
        // build the tag cloud:
        $cloud = $this->buildTagCloud($h, $tag_count);
        if (!$cloud) { return false; }
        
        if ($show_title) {
            echo "<h2 class='widget_head widget_tag_cloud_title'>";
            echo "<a href='" . $h->url(array('page' => 'tag-cloud')) . "'>";
            echo $h->lang["tag_cloud_widget_title"] . "</a>\n";
            echo "</h2>\n";
        }
        
        echo "<div class='widget_body widget_tag_cloud'>";
        foreach ($cloud as $tag) {
            echo "<a href='" . $h->url(array('tag' => $tag['link_word'])) . "' ";
            echo "class='widget_tag_group" . $tag['class'] . "'>" . $tag["show_word"] . "</a>\n";
        }
        echo "<a href='" . $h->url(array('page' => 'tag-cloud')) . "' ";
        echo "class='widget_more_tags'>" . $h->lang["tag_cloud_widget_more"] . "</a>\n";
        echo "</div>";
    }
    
    
    /**
     * Build Tag Cloud
     *
     * @param int $count number of tags to show
     * @return array
     */
    public function buildTagCloud($h, $count)
    {
        $sql = "SELECT tags_word FROM " . TABLE_TAGS . ", " . TABLE_POSTS;
        $sql .= " WHERE tags_archived = %s AND (tags_post_id = post_id) AND";
        $sql .= " (post_status = %s || post_status = %s)";
        
        $query = $h->db->prepare($sql, 'N', 'new', 'top');
        $h->smartCache('on', 'tags', 60, $query); // start using cache
        $tags = $h->db->get_results($query);
        $h->smartCache('off'); // stop using cache
       
        if (!$tags) { return false; }
        
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

}
?>