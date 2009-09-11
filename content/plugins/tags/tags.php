<?php
/**
 * name: Tags
 * description: Enables tags for posts
 * version: 0.4
 * folder: tags
 * class: Tags
 * requires: submit 0.3
 * hooks: install_plugin, header_include, submit_hotaru_header_1, post_read_post_1, post_read_post_2, post_add_post, post_update_post, submit_form_2_assign, submit_form_2_fields, submit_form_2_check_for_errors, submit_form_2_process_submission, submit_show_post_extra_fields, submit_settings_get_values, submit_settings_form, submit_save_settings, post_list_filter, post_delete_post
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
        global $db, $plugins, $post;
        
        // Create a new table column called "post_tags" if it doesn't already exist
        $exists = $db->column_exists('posts', 'post_tags');
        if (!$exists) {
            $db->query("ALTER TABLE " . TABLE_POSTS . " ADD post_tags TEXT NULL AFTER post_content");
            $db->query("ALTER TABLE " . TABLE_POSTS . " ADD FULLTEXT (post_tags)"); // Make it fulltext searchable
        } 
        
        // Create a new empty table called "tags" if it doesn't already exist
        $exists = $db->table_exists('tags');
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
            $db->query($sql); 
        }
        
        $plugins->pluginSettingsUpdate('submit', 'submit_tags', 'checked');
        $plugins->pluginSettingsUpdate('submit', 'submit_max_tags', 50);
        
        // Could possibly do with some code here that extracts all existingtags from the posts table and populates the tags table with them.
        // Maybe in a later version.
    }
    
    
    /**
     * Add additional member variables when the $post object is read in the Submit plugin.
     */
    public function submit_hotaru_header_1()
    {
        global $post, $hotaru, $plugins, $cage;
        
        if (!defined('TABLE_TAGS')) { define("TABLE_TAGS", DB_PREFIX . 'tags'); }
        
        // include language file
        $plugins->includeLanguage('tags');
        
        $post->vars['tags'] = '';
        $post->vars['postMaxTags'] = 50;    // max characters for tags
        $post->vars['useTags'] = true;
        
        // Get page title
        if ($cage->get->keyExists('tag')) { $hotaru->setTitle($hotaru->pageToTitleCaps(($cage->get->noTags('tag')))); } // friendly URLs: FALSE
        if (!$hotaru->getTitle() && $cage->get->keyExists('pos2')) { $hotaru->setTitle($hotaru->pageToTitleCaps(($cage->get->noTags('pos2')))); } // friendly URLs: TRUE
        
    }
    
    
    /**
     * Read tag settings
     */
    public function post_read_post_1()
    {
        global $plugins, $post;
        
        //tags
        if (($plugins->pluginSettings('submit', 'submit_tags') == 'checked') && ($plugins->pluginActive('tags'))) { 
            $post->vars['useTags'] = true; 
        } else { 
            $post->vars['useTags'] = false; 
        }
        
        $max_tags = $plugins->pluginSettings('submit', 'submit_max_tags');
        if (!empty($max_tags)) { $post->vars['postMaxTags'] = $max_tags; }
    }
    
    
    /**
     * Read tag settings if post_id exists.
     */
    public function post_read_post_2()
    {
        global $post, $post_row;
        
        $post->vars['tags'] = urldecode($post_row->post_tags);
    }
    
    
    /**
     * Add tags to the posts and tags tables
     */
    public function post_add_post()
    {
        global $post, $db, $last_insert_id, $current_user;
        
        // Posts table
        $sql = "UPDATE " . TABLE_POSTS . " SET post_tags = %s WHERE post_id = %d";
        $db->query($db->prepare($sql, urlencode(trim($post->vars['tags'])), $last_insert_id));
            
        // Tags table
        if (!empty($post->vars['tags'])) {
            $tags_array = explode(',', $post->vars['tags']);
            if ($tags_array) {
                foreach ($tags_array as $tag) {
                    $sql = "INSERT INTO " . TABLE_TAGS . " SET tags_post_id = %d, tags_date = CURRENT_TIMESTAMP, tags_word = %s, tags_updateby = %d";
                    $db->query($db->prepare($sql, $last_insert_id, urlencode(str_replace(' ', '_', trim($tag))), $current_user->getId()));
                }
            }
        }
    }
    
    
    /**
     * Update tags in the posts and tags tables
     */
    public function post_update_post()
    {
        global $post, $db, $current_user;
        
        // Posts table
        $sql = "UPDATE " . TABLE_POSTS . " SET post_tags = %s WHERE post_id = %d";
        $db->query($db->prepare($sql, urlencode(trim($post->vars['tags'])), $post->post_id));
            
        // Delete existing tags from tags table
        $sql = "DELETE from " . TABLE_TAGS . " WHERE tags_post_id = %d";
        $db->query($db->prepare($sql, $post->post_id));
        
        // Reinsert into tags table
        if (!empty($post->vars['tags'])) {
            $tags_array = explode(',', $post->vars['tags']);
            if ($tags_array) {
                foreach ($tags_array as $tag) {
                    $sql = "INSERT INTO " . TABLE_TAGS . " SET tags_post_id = %d, tags_date = CURRENT_TIMESTAMP, tags_word = %s, tags_updateby = %d";
                    $db->query($db->prepare($sql, $post->post_id, urlencode(str_replace(' ', '_', trim($tag))), $current_user->getId()));
                }
            }
        }
    }
    
    
    /**
     * Delete tags for a deleted post
     */    
    public function delete_post()
    {
        global $db, $post;
        $sql = "DELETE FROM " . TABLE_TAGS . " WHERE tags_post_id = %d";
        $db->query($db->prepare($sql, $post->post_id));        
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
        global $cage, $hotaru, $tags_check, $post;
        
        if ($cage->post->getAlpha('submit2') == 'true') {
            // Submitted this form...
            $tags_check = $cage->post->noTags('post_tags');    
            
        } elseif ($cage->post->getAlpha('submit3') == 'edit') {
            // Come back from step 3 to make changes...
            $tags_check = $post->vars['tags'];
            
        } elseif ($hotaru->isPage('edit_post')) {
            // Editing a previously submitted post
            if ($cage->post->getAlpha('edit_post') == 'true') {
                $tags_check = $cage->post->noTags('post_tags');
            } else {
                $tags_check = $post->vars['tags'];
            }
            
        } else {
            // First time here...
            $tags_check = "";
        }
    
    }
    
    /**
     * Add a tags field to submit form 2
     */
    public function submit_form_2_fields()
    {
        global $lang, $post, $tags_check;
    
        if ($post->vars['useTags']) { 
            echo "<tr>";
                echo "<td>" . $lang["submit_form_tags"] . "&nbsp; </td>";
                echo "<td><input type='text' size=50 name='post_tags' value='" . $tags_check . "'></td>";
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
        global $hotaru, $lang, $post, $cage, $lang, $tags_check;
        
        // ******** CHECK TAGS ********
        if ($post->vars['useTags']) {
            $tags_check = $cage->post->noTags('post_tags');    
            if (!$tags_check) {
                // No tags present...
                $hotaru->messages[$lang['submit_form_tags_not_present_error']] = "red";
                $error_tags = 1;
            } elseif (strlen($tags_check) > $post->post_max_tags) {
                // total tag length is too long
                $hotaru->messages[$lang['submit_form_tags_length_error']] = "red";
                $error_tags = 1;
            } else {
                // tags are okay.
                $error_tags = 0;
            }
        }
        
        return $error_tags;
    }
    
    
    /**
     * Set $post->post_tags to submitted string of tags
     */
    public function submit_form_2_process_submission()
    {
        global $cage, $post;
        
        $post->vars['tags'] = $cage->post->noTags('post_tags');
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
        global $hotaru, $post, $cage, $filter, $lang, $page_title;
        
        if ($cage->get->keyExists('tag')) 
        {
            // friendly URLs: FALSE
            $tag = $cage->get->noTags('tag'); 
            
            // friendly URLs: TRUE
            if (!$tag) { $tag = $cage->get->noTags('pos2'); } 
            
            if ($tag) {
                $filter['post_tags LIKE %s'] = '%' . urlencode($tag) . '%'; 
                $rss = " <a href='" . url(array('page'=>'rss', 'tag'=>$tag)) . "'>";
            }
            
            $rss .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png'></a>";
            // Undo the filter that limits results to either 'top' or 'new' (See submit.php -> sub_prepare_list())
            if(isset($filter['post_status = %s'])) { unset($filter['post_status = %s']); }
            $filter['post_status != %s'] = 'processing';
            $page_title = $lang["post_breadcrumbs_tag"] . " &raquo; " . $hotaru->getTitle() . $rss;
            
            return true;    
        }
        
        return false;    
    }
    
    
    /**
     * Shows tags in each post
     */
    public function submit_show_post_extra_fields()
    { 
        global $post, $lang;
        
        if ($post->vars['useTags'] && $post->vars['tags']) { 
            $tags = explode(',', $post->vars['tags']);
            
            echo "<li>" . $lang["submit_show_tags"] . " ";
            
            echo "<div class='show_post_tags'>";
            foreach ($tags as $tag) {
                echo "<a href='" . url(array('tag' => str_replace(' ', '_', trim($tag)))) . "'>" . trim($tag) . "</a>&nbsp;";
            }
            echo "</div>";
            echo "</li>";
        }        
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
        global $plugins, $tags, $max_tags;
        
        // Get settings from database if they exist...
        $tags = $plugins->pluginSettings('submit', 'submit_tags');
        $max_tags = $plugins->pluginSettings('submit', 'submit_max_tags');
        
        // otherwise set to blank...
        if (!$tags) { $tags = ''; }
        if (!$max_tags) { $max_tags = ''; }
    }
    
    
    /**
     * Add tags field to the submit settings form
     */
    public function submit_settings_form()
    {
        global $plugins, $lang, $tags, $max_tags;
        
        echo "<input type='checkbox' name='tags' value='tags' " . $tags . ">&nbsp;&nbsp;" . $lang["submit_settings_tags"];
        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        echo $lang["submit_settings_max_tags"] . ": <input type='text' size=5 name='max_tags' value='" . $max_tags . "' /><br />\n";
    }
    
    
    /**
     * Save tag settings.
     */
    public function submit_save_settings()
    {
        global $plugins, $cage, $lang, $tags, $max_tags;
        
        // Tags
        if ($cage->post->keyExists('tags')) { 
            $tags = 'checked'; 
            $post->vars['useTags'] = true;
        } else { 
            $tags = ''; 
            $post->vars['useTags'] = false;
        }
            
        // Tags length
        if ($cage->post->keyExists('max_tags')) { 
            $max_tags = $cage->post->getInt('max_tags'); 
            if (empty($max_tags)) { $max_tags = $post->vars['postMaxTags']; }
        } else { 
            $max_tags = $post->vars['postMaxTags']; 
        } 
        
        $plugins->pluginSettingsUpdate('submit', 'submit_tags', $tags);
        $plugins->pluginSettingsUpdate('submit', 'submit_max_tags', $max_tags);
    }
    
    
    /**
     * Delete tags when post deleted
     */
    public function post_delete_post()
    {
        global $db, $post;
        
        $sql = "DELETE FROM " . TABLE_TAGS . " WHERE tags_post_id = %d";
        $db->query($db->prepare($sql, $post->post_id));
    }
    
}
?>