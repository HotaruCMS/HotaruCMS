<?php
/**
 * name: Tags
 * description: Enables tags for posts
 * version: 0.7
 * folder: tags
 * class: Tags
 * requires: submit 0.7
 * hooks: install_plugin, header_include_raw, submit_hotaru_header_1, header_meta, post_read_post_1, post_read_post_2, post_add_post, post_update_post, submit_form_2_assign, submit_form_2_fields, submit_form_2_check_for_errors, submit_form_2_process_submission, submit_show_post_extra_fields, submit_show_post_extras, submit_settings_get_values, submit_settings_form, submit_save_settings, post_list_filter, post_delete_post
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
              `tags_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
              `tags_date` timestamp NOT NULL,
              `tags_word` varchar(64) NOT NULL DEFAULT '',
              `tags_updateby` int(20) NOT NULL DEFAULT 0, 
              UNIQUE KEY `tags_post_id` (`tags_post_id`,`tags_word`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Post Tags';";
            $this->db->query($sql); 
        }
        
        $this->updateSetting('submit_tags', 'checked', 'submit');
        $this->updateSetting('submit_max_tags', 50, 'submit');
        
        // Could possibly do with some code here that extracts all existingtags from the posts table and populates the tags table with them.
        // Maybe in a later version.
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
            $this->hotaru->title = $this->hotaru->pageToTitleCaps(($this->cage->get->noTags('tag'))); 
        } 
        
        // friendly URLs: TRUE
        if (!$this->hotaru->title && $this->cage->get->keyExists('pos2')) { 
            $this->hotaru->title = $this->hotaru->pageToTitleCaps(($this->cage->get->noTags('pos2'))); 
        }
        
    }
    
    
    /**
     * Match meta tag to a post's keywords (description is done in the Submit plugin)
     */
    public function header_meta()
    {    
        if ($this->hotaru->pageType == 'post') {
            echo '<meta name="keywords" content="' . $this->hotaru->post->vars['tags'] . '">' . "\n";
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
                echo "});\n";
            echo "});\n";
            echo "</script>\n";
    }
    
    
    /**
     * Read tag settings
     */
    public function post_read_post_1()
    {
        //tags
        if (($this->getSetting('submit_tags', 'submit') == 'checked') && ($this->isActive())) { 
            $this->hotaru->post->vars['useTags'] = true; 
        } else { 
            $this->hotaru->post->vars['useTags'] = false; 
        }
        
        $max_tags = $this->getSetting('submit_max_tags', 'submit');
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
                    $this->db->query($this->db->prepare($sql, $this->hotaru->post->post_id, urlencode(str_replace(' ', '_', trim($tag))), $this->current_user->id));
                }
            }
        }
    }
    
    
    /**
     * Delete tags for a deleted post
     */    
    public function delete_post()
    {
        $sql = "DELETE FROM " . TABLE_TAGS . " WHERE tags_post_id = %d";
        $this->db->query($this->db->prepare($sql, $this->hotaru->post->post_id));        
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
            $this->hotaru->post->vars['tags_check'] = $this->cage->post->noTags('post_tags');    
            
        } elseif ($this->cage->post->getAlpha('submit3') == 'edit') {
            // Come back from step 3 to make changes...
            $this->hotaru->post->vars['tags_check'] = $this->hotaru->post->vars['tags'];
            
        } elseif ($this->hotaru->isPage('edit_post')) {
            // Editing a previously submitted post
            if ($this->cage->post->getAlpha('edit_post') == 'true') {
                $this->hotaru->post->vars['tags_check'] = $this->cage->post->noTags('post_tags');
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
            $this->hotaru->post->vars['tags_check'] = $this->cage->post->noTags('post_tags');
            
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
        $this->hotaru->post->vars['tags'] = $this->cage->post->noTags('post_tags');
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
            $tag = $this->cage->get->noTags('tag'); 
            
            // friendly URLs: TRUE
            if (!$tag) { $tag = $this->cage->get->noTags('pos2'); } 
            
            if ($tag) {
                $this->hotaru->vars['filter']['post_tags LIKE %s'] = '%' . urlencode($tag) . '%'; 
                $rss = " <a href='" . $this->hotaru->url(array('page'=>'rss', 'tag'=>$tag)) . "'>";
            }
            
            $rss .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png'></a>";
            // Undo the filter that limits results to either 'top' or 'new' (See submit.php -> sub_prepare_list())
            if(isset($this->hotaru->vars['filter']['post_status = %s'])) { unset($this->hotaru->vars['filter']['post_status = %s']); }
            $this->hotaru->vars['filter']['post_status != %s'] = 'processing';
            $this->hotaru->vars['page_title'] = $this->lang["post_breadcrumbs_tag"] . " &raquo; " . $this->hotaru->title . $rss;
            
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
            echo '<li><a class="tags_link" href="#">' . $this->lang["submit_show_tags"]  . '</a></li>';
        }
    }
    
    
     /**
     * List of tags
     */
    public function submit_show_post_extras()
    {
        $tags = explode(',', $this->hotaru->post->vars['tags']);
    
        echo "<div class='show_tags' style='display: none;'>";
            echo "<ul>" . $this->lang["submit_show_tags"] . ": ";
                foreach ($tags as $tag) {
                    echo "<a href='" . $this->hotaru->url(array('tag' => str_replace(' ', '_', trim($tag)))) . "'>" . trim($tag) . "</a>&nbsp;";
                }
            echo "</ul>";
        echo "</div>";
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
        $tags = $this->getSetting('submit_tags', 'submit');
        $max_tags = $this->getSetting('submit_max_tags', 'submit');
        
        // otherwise set to blank...
        if (!$tags) { $tags = ''; }
        if (!$max_tags) { $max_tags = ''; }
        
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
        
        $this->updateSetting('submit_tags', $tags, 'submit');
        $this->updateSetting('submit_max_tags', $max_tags, 'submit');
    }
    
    
    /**
     * Delete tags when post deleted
     */
    public function post_delete_post()
    {
        global $post;
        
        $sql = "DELETE FROM " . TABLE_TAGS . " WHERE tags_post_id = %d";
        $this->db->query($this->db->prepare($sql, $this->hotaru->post->id));
    }
    
}
?>