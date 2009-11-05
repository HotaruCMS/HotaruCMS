<?php
/**
 * name: Tags
 * description: Enables tags for posts
 * version: 1.1
 * folder: tags
 * class: Tags
 * requires: submit 1.4, sidebar_widgets 0.5
 * hooks: install_plugin, header_include, header_include_raw, submit_hotaru_header_1, header_meta, theme_index_main, post_read_post_1, post_read_post_2, post_add_post, post_update_post, submit_form_2_assign, submit_form_2_fields, submit_form_2_check_for_errors, submit_form_2_process_submission, submit_show_post_extra_fields, submit_show_post_extras, submit_settings_get_values, submit_settings_form, submit_save_settings, post_list_filter, post_delete_post, admin_plugin_settings, admin_sidebar_plugin_settings
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
        // Create a new table column called "post_tags" if it doesn't already exist
        $exists = $this->db->column_exists('posts', 'post_tags');
        if (!$exists) {
            $this->db->query("ALTER TABLE " . TABLE_POSTS . " ADD post_tags TEXT NULL AFTER post_content");
            $this->db->query("ALTER TABLE " . TABLE_POSTS . " ADD FULLTEXT (post_tags)"); // Make it fulltext searchable
        } 
        
        // Create a new empty table called "tags" if it doesn't already exist
        $exists = $this->db->table_exists('tags');
        if (!$exists) {
            //echo "table doesn't exist. Stopping before creation."; exit;
            $sql = "CREATE TABLE `" . DB_PREFIX . "tags` (
              `tags_post_id` int(11) NOT NULL DEFAULT '0',
              `tags_archived` enum('Y','N') NOT NULL DEFAULT 'N',
              `tags_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
              `tags_date` timestamp NOT NULL,
              `tags_word` varchar(64) NOT NULL DEFAULT '',
              `tags_updateby` int(20) NOT NULL DEFAULT 0, 
              UNIQUE KEY `tags_post_id` (`tags_post_id`,`tags_word`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Post Tags';";
            $this->db->query($sql); 
        }
        
        if (!$this->db->column_exists('tags', 'tags_archived')) {
            // add new tags_archived field
            $sql = "ALTER TABLE " . DB_PREFIX . "tags ADD tags_archived ENUM(%s, %s) NOT NULL DEFAULT %s AFTER tags_post_id";
            $this->db->query($this->db->prepare($sql, 'Y', 'N', 'N'));
        }
        
        $tags_settings = $this->getSerializedSettings();
        
        if (!isset($tags_settings['submit_tags'])) { $tags_settings['submit_tags'] = 'checked'; }
        if (!isset($tags_settings['submit_max_tags'])) { $tags_settings['submit_max_tags'] = 100; }
        if (!isset($tags_settings['tags_num_tags_page'])) { $tags_settings['tags_num_tags_page'] = 100; }
        if (!isset($tags_settings['tags_num_tags_widget'])) { $tags_settings['tags_num_tags_widget'] = 25; }
        if (!isset($tags_settings['tags_widget_title'])) { $tags_settings['tags_widget_title'] = 'checked'; }
        
        $this->updateSetting('tags_settings', serialize($tags_settings));
        
        require_once(PLUGINS . 'sidebar_widgets/libs/Sidebar.php');
        $sidebar = new Sidebar($this->hotaru);
        $sidebar->addWidget('tags', 'tag_cloud', '');  // plugin name, function name, optional arguments

    }
    
    
    /**
     * Add additional member variables when the $post object is read in the Submit plugin.
     */
    public function submit_hotaru_header_1()
    {
        if (!defined('TABLE_TAGS')) { define("TABLE_TAGS", DB_PREFIX . 'tags'); }
        
        // include language file
        $this->includeLanguage();
        
        $this->hotaru->post->vars['tags'] = '';
        $this->hotaru->post->vars['maxTags'] = 50;    // max characters for tags
        $this->hotaru->post->vars['useTags'] = true;
        
        // Get page title:
        
        // friendly URLs: FALSE
        if ($this->cage->get->keyExists('tag')) { 
            $this->hotaru->title = stripslashes($this->hotaru->pageToTitleCaps(($this->cage->get->noTags('tag')))); 
        } 
        
        // friendly URLs: TRUE
        if (!$this->hotaru->title && $this->cage->get->keyExists('pos2')) { 
            $this->hotaru->title = stripslashes($this->hotaru->pageToTitleCaps(($this->cage->get->noTags('pos2')))); 
        }
        
    }
    
    
    /**
     * Match meta tag to a post's keywords (description is done in the Submit plugin)
     */
    public function header_meta()
    {    
        if ($this->hotaru->pageType == 'post') {
            echo '<meta name="keywords" content="' . stripslashes($this->hotaru->post->vars['tags']) . '">' . "\n";
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
     * Display various forms within the body of the page.
     *
     * @return bool
     */
    public function theme_index_main()
    {
        if ($this->hotaru->isPage('tag-cloud')) 
        {
            // get the number of tags to show:
            $tags_settings = $this->getSerializedSettings();
            $tag_count = $tags_settings['tags_num_tags_page'];
            
            // build the tag cloud:
            $this->hotaru->vars['tagCloud'] = $this->buildTagCloud($tag_count);
            
            // display the tag cloud:
            $this->hotaru->displayTemplate('tag_cloud', 'tags');
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
        
        if ($show_title) {
            echo "<h2 class='sidebar_widget_head widget_tag_cloud_title'>";
            echo "<a href='" . $this->hotaru->url(array('page' => 'tag-cloud')) . "'>";
            echo $this->hotaru->lang["tags_tag_cloud_widget_title"] . "</a>\n";
            echo "</h2>\n";
        }
        
        echo "<div class='sidebar_widget_body widget_tag_cloud'>";
        foreach ($cloud as $tag) {
            echo "<a href='" . $this->hotaru->url(array('tag' => $tag['link_word'])) . "' ";
            echo "class='widget_tag_group" . $tag['class'] . "'>" . $tag["show_word"] . "</a>\n";
        }
        echo "<a href='" . $this->hotaru->url(array('page' => 'tag-cloud')) . "' ";
        echo "class='widget_more_tags'>" . $this->hotaru->lang["tags_tag_cloud_widget_more"] . "</a>\n";
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
        $sql = "SELECT tags_word FROM " . TABLE_TAGS . " WHERE tags_archived = %s";
        $tags = $this->db->get_results($this->db->prepare($sql, 'N'));
        
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
     * Read tag settings
     */
    public function post_read_post_1()
    {
        $tags_settings = $this->getSerializedSettings();
        
        if (($tags_settings['submit_tags'] == 'checked') && ($this->isActive())) { 
            $this->hotaru->post->vars['useTags'] = true; 
        } else { 
            $this->hotaru->post->vars['useTags'] = false; 
        }
        
        $max_tags = $tags_settings['submit_max_tags'];
        if (!empty($max_tags)) { $this->hotaru->post->vars['maxTags'] = $max_tags; }
    }
    
    
    /**
     * Read tag settings if post_id exists.
     */
    public function post_read_post_2()
    {
        $this->hotaru->post->vars['tags'] = urldecode($this->hotaru->post->vars['post_row']->post_tags);
    }
    
    
    /**
     * Add tags to the posts and tags tables
     */
    public function post_add_post()
    {
        // Posts table
        $sql = "UPDATE " . TABLE_POSTS . " SET post_tags = %s WHERE post_id = %d";
        $this->db->query($this->db->prepare($sql, urlencode(trim($this->hotaru->post->vars['tags'])), $this->hotaru->post->vars['last_insert_id']));
            
        // Tags table
        if (!empty($this->hotaru->post->vars['tags'])) {
            $tags_array = explode(',', $this->hotaru->post->vars['tags']);
            if ($tags_array) {
                foreach ($tags_array as $tag) {
                    $sql = "INSERT INTO " . TABLE_TAGS . " SET tags_post_id = %d, tags_date = CURRENT_TIMESTAMP, tags_word = %s, tags_updateby = %d";
                    $this->db->query($this->db->prepare($sql, $this->hotaru->post->vars['last_insert_id'], urlencode(str_replace(' ', '_', trim($tag))), $this->current_user->id));
                }
            }
        }
    }
    
    
    /**
     * Update tags in the posts and tags tables
     */
    public function post_update_post()
    {
        // Posts table
        $sql = "UPDATE " . TABLE_POSTS . " SET post_tags = %s WHERE post_id = %d";
        
        $this->db->query($this->db->prepare($sql, urlencode(trim($this->hotaru->post->vars['tags'])), $this->hotaru->post->id));
            
        // Delete existing tags from tags table
        $sql = "DELETE from " . TABLE_TAGS . " WHERE tags_post_id = %d";
        $this->db->query($this->db->prepare($sql, $this->hotaru->post->id));
        
        // Reinsert into tags table
        if (!empty($this->hotaru->post->vars['tags'])) {
            $tags_array = explode(',', $this->hotaru->post->vars['tags']);
            if ($tags_array) {
                foreach ($tags_array as $tag) {
                    $sql = "INSERT INTO " . TABLE_TAGS . " SET tags_post_id = %d, tags_date = CURRENT_TIMESTAMP, tags_word = %s, tags_updateby = %d";
                    $this->db->query($this->db->prepare($sql, $this->hotaru->post->id, urlencode(str_replace(' ', '_', trim($tag))), $this->current_user->id));
                }
            }
        }
    }
    
    
     /**
     * ********************************************************************* 
     * ********************* FUNCTIONS FOR SUBMIT FORM ********************* 
     * *********************************************************************
     * ****************************************************************** */
     
    
    /**
     * Set $tags_check to the value submitted through the form
     */
    public function submit_form_2_assign()
    {
        if ($this->cage->post->getAlpha('submit2') == 'true') {
            // Submitted this form...
            $this->hotaru->post->vars['tags_check'] = stripslashes(sanitize($this->cage->post->noTags('post_tags'), 2));
            
        } elseif ($this->cage->post->getAlpha('submit3') == 'edit') {
            // Come back from step 3 to make changes...
            $this->hotaru->post->vars['tags_check'] = $this->hotaru->post->vars['tags'];
            
        } elseif ($this->hotaru->isPage('edit_post')) {
            // Editing a previously submitted post
            if ($this->cage->post->getAlpha('edit_post') == 'true') {
                $this->hotaru->post->vars['tags_check'] = stripslashes(sanitize($this->cage->post->noTags('post_tags'), 2));
            } else {
                $this->hotaru->post->vars['tags_check'] = $this->hotaru->post->vars['tags'];
            }
            
        } else {
            // First time here...
            $this->hotaru->post->vars['tags_check'] = "";
        }
    
    }
    
    /**
     * Add a tags field to submit form 2
     */
    public function submit_form_2_fields()
    {
        if ($this->hotaru->post->vars['useTags']) { 
            echo "<tr>";
                echo "<td>" . $this->lang["submit_form_tags"] . "&nbsp; </td>";
                echo "<td><input type='text' size=50 name='post_tags' value='" . $this->hotaru->post->vars['tags_check'] . "'></td>";
                echo "<td>&nbsp;</td>";
            echo "</tr>";
        }
    }
    
    
    /**
     * Add s a tags field to submit form 2
     *
     * @return int $error_tags
     */
    public function submit_form_2_check_for_errors()
    {
        // ******** CHECK TAGS ********
        if ($this->hotaru->post->vars['useTags']) 
        {
            $this->hotaru->post->vars['tags_check'] = stripslashes(sanitize($this->cage->post->noTags('post_tags'), 2));
            
            if (!$this->hotaru->post->vars['tags_check']) {
                // No tags present...
                $this->hotaru->messages[$this->lang['submit_form_tags_not_present_error']] = "red";
                $error_tags = 1;
            } elseif (strlen($this->hotaru->post->vars['tags_check']) > $this->hotaru->post->vars['maxTags']) {
                // total tag length is too long
                $this->hotaru->messages[$this->lang['submit_form_tags_length_error']] = "red";
                $error_tags = 1;
            } else {
                // tags are okay.
                $error_tags = 0;
            }
        }
        return $error_tags;
    }
    
    
    /**
     * Set $this->hotaru->post->post_tags to submitted string of tags
     */
    public function submit_form_2_process_submission()
    {
        $this->hotaru->post->vars['tags'] = stripslashes(sanitize($this->cage->post->noTags('post_tags'), 2));
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
        if ($this->cage->get->keyExists('tag')) 
        {
            // friendly URLs: FALSE
            $tag = stripslashes($this->cage->get->noTags('tag')); 
            
            // friendly URLs: TRUE
            if (!$tag) { $tag = $this->cage->get->noTags('pos2'); } 
            
            if ($tag) {
                $this->hotaru->vars['filter']['post_tags LIKE %s'] = '%' . urlencode($tag) . '%'; 
                $this->hotaru->vars['filter']['post_archived = %s'] = 'N'; // don't include archived posts
                $rss = " <a href='" . $this->hotaru->url(array('page'=>'rss', 'tag'=>$tag)) . "'>";
            }
            
            $rss .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png'></a>";       
            $this->hotaru->vars['page_title'] = $this->lang["post_breadcrumbs_tag"] . " &raquo; " . stripslashes($this->hotaru->title) . $rss;
            
            return true;    
        }
        
        return false;    
    }
    
    
    /**
     * Shows tags in each post
     */
    public function submit_show_post_extra_fields()
    { 
        if ($this->hotaru->post->vars['useTags'] && $this->hotaru->post->vars['tags'])
        { 
            echo "<li><a class='tags_link' href='#'>" . $this->lang["submit_show_tags"]  . "</a></li>\n";
        }
    }
    
    
     /**
     * List of tags
     */
    public function submit_show_post_extras()
    {
        $tags = explode(',', $this->hotaru->post->vars['tags']);
    
        echo "<div class='show_tags' style='display: none;'>\n";
            echo "<ul>" . $this->lang["submit_show_tags"] . ": \n";
                foreach ($tags as $tag) {
                    echo "<a href='" . $this->hotaru->url(array('tag' => str_replace(' ', '_', trim($tag)))) . "'>" . trim($tag) . "</a>&nbsp;\n";
                }
            echo "</ul>\n";
        echo "</div>\n";
    }
    
    
     /**
     * ********************************************************************* 
     * ****************** FUNCTIONS FOR SUBMIT SETTINGS ******************** 
     * *********************************************************************
     * ****************************************************************** */
     
    
    /**
     * Gets current tag settings from the database
     */
    public function submit_settings_get_values()
    {
        // Get settings from database if they exist...
        $tags_settings = $this->getSerializedSettings();
        $tags = $tags_settings['submit_tags'];
        $max_tags = $tags_settings['submit_max_tags'];
        
        // otherwise set to defaults...
        if (!isset($tags)) { $tags = 'checked'; }
        if (!isset($max_tags)) { $max_tags = '100'; }
        
        $this->hotaru->post->vars['tags'] = $tags;
        $this->hotaru->post->vars['max_tags'] = $max_tags;
    }
    
    
    /**
     * Add tags field to the submit settings form
     */
    public function submit_settings_form()
    {
        echo "<input type='checkbox' name='tags' value='tags' " . $this->hotaru->post->vars['tags'] . ">&nbsp;&nbsp;" . $this->lang["submit_settings_tags"];
        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        echo $this->lang["submit_settings_max_tags"] . ": <input type='text' size=5 name='max_tags' value='" . $this->hotaru->post->vars['max_tags'] . "' /><br />\n";
    }
    
    
    /**
     * Save tag settings.
     */
    public function submit_save_settings()
    {
        // Tags
        if ($this->cage->post->keyExists('tags')) { 
            $tags = 'checked'; 
            $this->hotaru->post->vars['useTags'] = true;
        } else { 
            $tags = ''; 
            $this->hotaru->post->vars['useTags'] = false;
        }
            
        // Tags length
        if ($this->cage->post->keyExists('max_tags')) { 
            $max_tags = $this->cage->post->getInt('max_tags'); 
            if (empty($max_tags)) { $max_tags = $this->hotaru->post->vars['maxTags']; }
        } else { 
            $max_tags = $this->hotaru->post->vars['maxTags']; 
        } 
        
        $tags_settings = $this->getSerializedSettings();
        $tags_settings['submit_tags'] = $tags;
        $tags_settings['submit_max_tags'] = $max_tags;
        $this->updateSetting('tags_settings', serialize($tags_settings));
    }
    
    
    /**
     * Delete tags when post deleted
     */
    public function post_delete_post()
    {
        $sql = "DELETE FROM " . TABLE_TAGS . " WHERE tags_post_id = %d";
        $this->db->query($this->db->prepare($sql, $this->hotaru->post->id));
    }
    
    
    /**
     * Show link in the Admin sidebar
     */
    public function admin_sidebar_plugin_settings()
    {
        echo "<li><a href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=tags'>" . $this->hotaru->lang["tags_tag_cloud"] . "</a></li>";
    }
        
}
?>