<?php
/**
 * name: Tags
 * description: Enables tags for posts
 * version: 0.2
 * folder: tags
 * prefix: tg
 * requires: submit 0.1
 * hooks: install_plugin, header_include, submit_hotaru_header_1, submit_class_post_read_post_1, submit_class_post_read_post_2, submit_class_post_add_post, submit_class_post_update_post, submit_form_2_assign, submit_form_2_fields, submit_form_2_check_for_errors, submit_form_2_process_submission, submit_show_post_extra_fields, submit_settings_get_values, submit_settings_form, submit_save_settings, submit_list_filter, submit_class_post_delete_post
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


 /**
 * ********************************************************************* 
 * ********************* FUNCTIONS FOR POST CLASS ********************** 
 * *********************************************************************
 * ****************************************************************** */
 
 
/**
 * Add a post_tags field to posts table if it doesn't alredy exist
 */
function tg_install_plugin()
{
    global $db, $plugin, $post;
    
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
    
    // Default settings (Note: we can't use $post->post_vars because it hasn't been filled yet.)
    $plugin->plugin_settings_update('submit', 'submit_tags', 'checked');
    $plugin->plugin_settings_update('submit', 'submit_max_tags', 50);
    
    // Could possibly do with some code here that extracts all existingtags from the posts table and populates the tags table with them.
    // Maybe in a later version.
}


/**
 * Add additional member variables when the $post object is read in the Submit plugin.
 */
function tg_submit_hotaru_header_1()
{
    global $post, $hotaru, $plugin, $cage;
    
    if (!defined('TABLE_TAGS')) { define("TABLE_TAGS", DB_PREFIX . 'tags'); }
    
    // include language file
    $plugin->include_language('tags');
    
    $post->post_vars['post_tags'] = '';
    $post->post_vars['post_max_tags'] = 50;    // max characters for tags
    $post->post_vars['use_tags'] = true;
    
    // Get page title
    if ($cage->get->keyExists('tag')) { $hotaru->title = $hotaru->page_to_title_caps(($cage->get->noTags('tag'))); } // friendly URLs: FALSE
    if (!$hotaru->title && $cage->get->keyExists('pos2')) { $hotaru->title = $hotaru->page_to_title_caps(($cage->get->noTags('pos2'))); } // friendly URLs: TRUE
    
}


/**
 * Include css
 */
function tg_header_include()
{
    global $plugin;
    $plugin->include_css('tags');
}

/**
 * Read tag settings
 */
function tg_submit_class_post_read_post_1()
{
    global $plugin, $post;
    
    //tags
    if (($plugin->plugin_settings('submit', 'submit_tags') == 'checked') && ($plugin->plugin_active('tags'))) { 
        $post->post_vars['use_tags'] = true; 
    } else { 
        $post->post_vars['use_tags'] = false; 
    }
    
    $max_tags = $plugin->plugin_settings('submit', 'submit_max_tags');
    if (!empty($max_tags)) { $post->post_vars['post_max_tags'] = $max_tags; }
}


/**
 * Read tag settings if post_id exists.
 */
function tg_submit_class_post_read_post_2()
{
    global $post, $post_row;
    
    $post->post_vars['post_tags'] = urldecode($post_row->post_tags);
}


/**
 * Add tags to the posts and tags tables
 */
function tg_submit_class_post_add_post()
{
    global $post, $db, $last_insert_id, $current_user;
    
    // Posts table
    $sql = "UPDATE " . TABLE_POSTS . " SET post_tags = %s WHERE post_id = %d";
    $db->query($db->prepare($sql, urlencode(trim($post->post_vars['post_tags'])), $last_insert_id));
        
    // Tags table
    if (!empty($post->post_vars['post_tags'])) {
        $tags_array = explode(',', $post->post_vars['post_tags']);
        if ($tags_array) {
            foreach ($tags_array as $tag) {
                $sql = "INSERT INTO " . TABLE_TAGS . " SET tags_post_id = %d, tags_date = CURRENT_TIMESTAMP, tags_word = %s, tags_updateby = %d";
                $db->query($db->prepare($sql, $last_insert_id, urlencode(str_replace(' ', '_', trim($tag))), $current_user->id));
            }
        }
    }
}


/**
 * Update tags in the posts and tags tables
 */
function tg_submit_class_post_update_post()
{
    global $post, $db, $current_user;
    
    // Posts table
    $sql = "UPDATE " . TABLE_POSTS . " SET post_tags = %s WHERE post_id = %d";
    $db->query($db->prepare($sql, urlencode(trim($post->post_vars['post_tags'])), $post->post_id));
        
    // Delete existing tags from tags table
    $sql = "DELETE from " . TABLE_TAGS . " WHERE tags_post_id = %d";
    $db->query($db->prepare($sql, $post->post_id));
    
    // Reinsert into tags table
    if (!empty($post->post_vars['post_tags'])) {
        $tags_array = explode(',', $post->post_vars['post_tags']);
        if ($tags_array) {
            foreach ($tags_array as $tag) {
                $sql = "INSERT INTO " . TABLE_TAGS . " SET tags_post_id = %d, tags_date = CURRENT_TIMESTAMP, tags_word = %s, tags_updateby = %d";
                $db->query($db->prepare($sql, $post->post_id, urlencode(str_replace(' ', '_', trim($tag))), $current_user->id));
            }
        }
    }
}


/**
 * Delete tags for a deleted post
 */    
function tg_delete_post()
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
function tg_submit_form_2_assign()
{
    global $cage, $hotaru, $tags_check, $post;
    
    if ($cage->post->getAlpha('submit2') == 'true') {
        // Submitted this form...
        $tags_check = $cage->post->noTags('post_tags');    
        
    } elseif ($cage->post->getAlpha('submit3') == 'edit') {
        // Come back from step 3 to make changes...
        $tags_check = $post->post_vars['post_tags'];
        
    } elseif ($hotaru->is_page('edit_post')) {
        // Editing a previously submitted post
        if ($cage->post->getAlpha('edit_post') == 'true') {
            $tags_check = $cage->post->noTags('post_tags');
        } else {
            $tags_check = $post->post_vars['post_tags'];
        }
        
    } else {
        // First time here...
        $tags_check = "";
    }

}

/**
 * Add a tags field to submit form 2
 */
function tg_submit_form_2_fields()
{
    global $lang, $post, $tags_check;

    if ($post->post_vars['use_tags']) { 
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
function tg_submit_form_2_check_for_errors()
{
    global $hotaru, $lang, $post, $cage, $lang, $tags_check;
    
    // ******** CHECK TAGS ********
    if ($post->post_vars['use_tags']) {
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
function tg_submit_form_2_process_submission()
{
    global $cage, $post;
    
    $post->post_vars['post_tags'] = $cage->post->noTags('post_tags');
}


 /**
 * ********************************************************************* 
 * ******************* FUNCTIONS FOR SHOWING POSTS ********************* 
 * *********************************************************************
 * ****************************************************************** */
 

/**
 * Gets a tag from the url and sets the filter for get_posts
 */
function tg_submit_list_filter()
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
        $filter['post_status != %s'] = 'processing';
        $page_title = $lang["submit_page_breadcrumbs_tag"] . " &raquo; " . $hotaru->title . $rss;
        
        return true;    
    }
    
    return false;    
}


/**
 * Shows tags in each post
 */
function tg_submit_show_post_extra_fields()
{ 
    global $post, $lang;
    
    if ($post->post_vars['use_tags'] && $post->post_vars['post_tags']) { 
        $tags = explode(',', $post->post_vars['post_tags']);
        
        echo "<li>" . $lang["submit_show_tags"] . " ";
        
        echo "<div class='show_post_tags'>";
        foreach ($tags as $tag) {
            echo "<a href='" . url(array('tag' => str_replace(' ', '_', trim($tag)))) . "'>" . trim($tag) . "</a>&nbsp;";
        }
        echo "</div>";
        echo "<li>";
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
function tg_submit_settings_get_values()
{
    global $plugin, $tags, $max_tags;
    
    // Get settings from database if they exist...
    $tags = $plugin->plugin_settings('submit', 'submit_tags');
    $max_tags = $plugin->plugin_settings('submit', 'submit_max_tags');
    
    // otherwise set to blank...
    if (!$tags) { $tags = ''; }
    if (!$max_tags) { $max_tags = ''; }
}


/**
 * Add tags field to the submit settings form
 */
function tg_submit_settings_form()
{
    global $plugin, $lang, $tags, $max_tags;
    
    echo "<input type='checkbox' name='tags' value='tags' " . $tags . ">&nbsp;&nbsp;" . $lang["submit_settings_tags"];
    echo "&nbsp;&nbsp;&nbsp;&nbsp;";
    echo $lang["submit_settings_max_tags"] . ": <input type='text' size=5 name='max_tags' value='" . $max_tags . "' /><br />\n";
}


/**
 * Save tag settings.
 */
function tg_submit_save_settings()
{
    global $plugin, $cage, $lang, $tags, $max_tags;
    
    // Tags
    if ($cage->post->keyExists('tags')) { 
        $tags = 'checked'; 
        $post->post_vars['use_tags'] = true;
    } else { 
        $tags = ''; 
        $post->post_vars['use_tags'] = false;
    }
        
    // Tags length
    if ($cage->post->keyExists('max_tags')) { 
        $max_tags = $cage->post->getInt('max_tags'); 
        if (empty($max_tags)) { $max_tags = $post->post_vars['post_max_tags']; }
    } else { 
        $max_tags = $post->post_vars['post_max_tags']; 
    } 
    
    $plugin->plugin_settings_update('submit', 'submit_tags', $tags);
    $plugin->plugin_settings_update('submit', 'submit_max_tags', $max_tags);
}

?>