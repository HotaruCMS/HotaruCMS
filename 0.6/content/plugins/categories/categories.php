<?php
/**
 * name: Categories
 * description: Enables categories for posts
 * version: 0.3
 * folder: categories
 * class: Categories
 * requires: submit 0.3, category_manager 0.2
 * hooks: install_plugin, hotaru_header, header_include, submit_hotaru_header_1, submit_hotaru_header_2, post_read_post_1, post_read_post_2, post_add_post, post_update_post, submit_form_2_assign, submit_form_2_fields, submit_form_2_check_for_errors, submit_form_2_process_submission, submit_settings_get_values, submit_settings_form, submit_save_settings, post_list_filter, submit_show_post_author_date, submit_is_page_main, navigation_last, admin_sidebar_plugin_settings, admin_plugin_settings
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

class Categories extends PluginFunctions
{

     /* ******************************************************************** 
     * ********************************************************************* 
     * ********************* FUNCTIONS FOR POST CLASS ********************** 
     * *********************************************************************
     * ****************************************************************** */
    
    /**
     * Adds default settings for Submit plugin
     */
    public function install_plugin()
    {
        global $db, $plugins, $post;
        
        // Default settings (Note: we can't use $post->vars because it hasn't been filled yet.)
        $plugins->pluginSettingsUpdate('submit', 'submit_categories', 'checked');
        $plugins->pluginSettingsUpdate('sidebar_widgets', 'categories', '');
        $plugins->pluginSettingsUpdate('categories', 'categories_bar', 'menu');
    }
    
    
    /**
     * Defines db table and includes language file
     */
    public function hotaru_header()
    {
        global $post, $hotaru, $cage, $plugins;
        
        // The categories table is defined 
        if (!defined('TABLE_CATEGORIES')) { define("TABLE_CATEGORIES", DB_PREFIX . "categories"); }
        
        // include language file
        $plugins->includeLanguage('categories');
        
        // Get page title    
        if ($cage->get->keyExists('category'))
        {
            $cat = new Category();
            
            if (is_numeric($cage->get->notags('category'))) 
            { 
                $hotaru->setTitle($cat->getCatName($cage->get->getInt('category'))); // friendly URLs: FALSE
            } 
            else 
            {
                $hotaru->setTitle($hotaru->pageToTitleCaps(($cage->get->notags('category')))); // friendly URLs: TRUE
            } 
        }
    }
    
    
    /**
     * Adds additional member variables when the $post object is read in the Submit plugin.
     */
    public function submit_hotaru_header_1()
    {
        global $post, $plugins;
        
        // The categories table is defined 
        if (!defined('TABLE_CATEGORIES')) { define("TABLE_CATEGORIES", DB_PREFIX . "categories"); }
        
        // include language file
        $plugins->includeLanguage('categories');
        
        $post->vars['category'] = 1;    // default category ('all').
        $post->vars['catName'] = '';
        $post->vars['catSafeName'] = '';
        $post->vars['useCategories'] = true;
        
    }
    
    
    /**
     * Checks if url query string is /category_name/post_name/
     *
     * @return bool
     *
     * Only used for friendly urls. This is necessary because if a url is 
     * /people/top-10-longest-beards/ there's no actual mention of "category" there!
     */
    public function submit_hotaru_header_2()
    {
        global $db, $hotaru, $post, $plugins, $cage;
            
        if (FRIENDLY_URLS == "true" && $post->id == 0) {
            // No post stored in post object, nothing was succesfully read by the Submit plugin        
                    
            // Can't get keys from the url with Inspekt, so must get the whole query string instead.
            $query_string = $cage->server->getMixedString2('QUERY_STRING');
            
            if ($query_string) {
                // we actually only need the first pair, so won't bother looping.
                $query_string = preg_replace('/&amp;/', '&', $query_string);
                $pairs = explode('&', $query_string); 
                if ($pairs[0]) {
                    list($key, $value) = explode('=', $pairs[0]);
                    if ($key) {
                        // Using db_prefix because table_categories might not be defined yet (depends on plugin install order)
                        $sql = "SELECT category_id FROM " . DB_PREFIX . "categories WHERE category_safe_name = %s LIMIT 1";
                        $exists = $db->get_var($db->prepare($sql, $key));        
                        if ($exists && $value) {
                            // Now we know that $key is a category so $value must be the post name. Go get the post_id...
                            $post->id = $post->isPostUrl($value);
                            $post->readPost($post->id);
                            $post->vars['isCategoryPost'] = true; 
                            return true;
                        } 
                    }
                }
            }
        }
            
        $post->vars['isCategoryPost'] = false;
        return false;
    }
    
    
    /**
     * Read category settings
     */
    public function post_read_post_1()
    {
        global $plugins, $post;
        
        //categories
        if (($plugins->pluginSettings('submit', 'submit_categories') == 'checked') 
            && ($plugins->pluginActive('categories'))) { 
            $post->vars['useCategories'] = true; 
        } else { 
            $post->vars['useCategories'] = false; 
        }
    }
    
    
    /**
     * Read category from the post in the database.
     */
    public function post_read_post_2()
    {
        global $db, $post, $post_row;
        
        $post->vars['category'] = $post_row->post_category;
        
        $sql = "SELECT category_name, category_safe_name FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
        $cat = $db->get_row($db->prepare($sql, $post->vars['category']));
        $post->vars['catName'] = urldecode($cat->category_name);
        $post->vars['catSafeName'] = urldecode($cat->category_safe_name);
    }
    
    
    /**
     * Adds category to the posts table
     */
    public function post_add_post()
    {
        global $post, $db, $last_insert_id;
        
        $sql = "UPDATE " . TABLE_POSTS . " SET post_category = %d WHERE post_id = %d";
        $db->query($db->prepare($sql, $post->vars['category'], $last_insert_id));
    }
    
    
    /**
     * Updates category in the posts table
     */
    public function post_update_post()
    {
        global $post, $db;
        
        $sql = "UPDATE " . TABLE_POSTS . " SET post_category = %d WHERE post_id = %d";
        $db->query($db->prepare($sql, $post->vars['category'], $post->id));
    }
    
    
     /* ******************************************************************** 
     * ********************************************************************* 
     * ********************* FUNCTIONS FOR SUBMIT FORM ********************* 
     * *********************************************************************
     * ****************************************************************** */
     
    
    /**
     * Sets $category_check to the value of the chosen category
     */
    public function submit_form_2_assign()
    {
        global $cage, $hotaru, $category_check, $post;
        
        if ($cage->post->getAlpha('submit2') == 'true') {
            // Submitted this form...
            $category_check = $cage->post->getInt('post_category');
            
        } elseif ($cage->post->getAlpha('submit3') == 'edit') {
            // Come back from step 3 to make changes...
            $category_check = $post->vars['category'];
            
        } elseif ($hotaru->is_page('edit_post')) {
            // Editing a previously submitted post
            if ($cage->post->getAlpha('edit_post') == 'true') {
                $category_check = $cage->post->getInt('post_category');
            } else {
                $category_check = $post->vars['category'];
            }
        
        } else {
            // First time here...
            $category_check = 1;
        }
    
    }
    
    
    /**
     * Adds a category drop-down box to submit form 2
     */
    public function submit_form_2_fields()
    {
        global $db, $lang, $post, $category_check;
    
        if ($post->vars['useCategories']) { 
            echo "<tr>\n";
                echo "<td>" . $lang["submit_form_category"] . ":&nbsp; </td>\n";
                echo "<td><select name='post_category'>\n";
                $sql = "SELECT category_name FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
                $category_name = $db->get_var($db->prepare($sql, $category_check));
                if ($category_name == 'all') { $category_name = $lang['submit_form_category_select']; }
                echo "<option value=" . $category_check . ">" . urldecode($category_name) . "</option>\n";
                $sql = "SELECT category_id, category_name FROM " . TABLE_CATEGORIES . " ORDER BY category_order ASC";
                $cats = $db->get_results($db->prepare($sql));
                if ($cats) {
                    foreach ($cats as $cat) {
                        if ($cat->category_id != 1) { 
                            echo "<option value=" . $cat->category_id . ">" . urldecode($cat->category_name) . "</option>\n";
                        }
                    }
                }
                echo "</select></td>\n";
                echo "<td>&nbsp;</td>\n";
            echo "</tr>";
        }
    }
    
    
    /**
     * Checks for category error from submit form 2
     *
     * @return int
     */
    public function submit_form_2_check_for_errors()
    {
        global $hotaru, $lang, $post, $cage, $category_check;
        
        // ******** CHECK CATEGORY ********
        if ($post->vars['useCategories']) {
            $category_check = $cage->post->getInt('post_category');    
            if (!$category_check) {
                // No category present...
                $hotaru->messages[$lang['submit_form_category_error']] = "red";
                $error_category = 1;
            } else {
                // category is okay.
                $error_category = 0;
            }
        }
        
        return $error_category;
    }
    
    
    /**
     * Sets $post->post_category to submitted category id
     */
    public function submit_form_2_process_submission()
    {
        global $db, $cage, $post;
        
        $post->vars['category'] = $cage->post->getInt('post_category');
        
        $sql = "SELECT category_name, category_safe_name FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
        $cat = $db->get_row($db->prepare($sql, $post->vars['category']));
        $post->vars['catName'] = urldecode($cat->category_name);
        $post->vars['catSafeName'] = urldecode($cat->category_safe_name);
    }
    
    
     /* ******************************************************************** 
     * ********************************************************************* 
     * ******************* FUNCTIONS FOR SHOWING POSTS ********************* 
     * *********************************************************************
     * ****************************************************************** */
     
    
    /**
     * Checks is the url is a category->post name pair and displays the post
     *
     * @return bool
     */
    public function submit_is_page_main()
    {
        global $db, $post, $plugins, $cage, $hotaru;
        
        if ($post->vars['isCategoryPost']) {
            $hotaru->displayTemplate('post', 'submit');
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Gets a category from the url and sets the filter for get_posts
     *
     * @return bool
     */
    public function post_list_filter()
    {
        global $hotaru, $post, $cage, $filter, $lang, $page_title;
        
        if ($cage->get->keyExists('category')) 
        {
            $cat = new Category();
            
            if (FRIENDLY_URLS == "true") 
            {
                $category = $cage->get->noTags('category'); 
                if ($category) { 
                    $filter['post_category = %d'] = $cat->getCatId($category); 
                    $rss = " <a href='" . url(array('page'=>'rss', 'category'=>$cat->getCatId($category))) . "'>";
                } 
            } 
            else 
            {
                $category = $cage->get->getInt('category'); 
                if ($category) {
                    $filter['post_category = %d'] = $category; 
                    $rss = " <a href='" . url(array('page'=>'rss', 'category'=>$category)) . "'>";
                }
            }
            
            $rss .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png'></a>";
            // Undo the filter that limits results to either 'top' or 'new' (See submit.php -> sub_prepare_list())
            if(isset($filter['post_status = %s'])) { unset($filter['post_status = %s']); }
            $filter['post_status != %s'] = 'processing';
            $page_title = $lang["post_breadcrumbs_category"] . " &raquo; " . $hotaru->getTitle() . $rss;
            
            return true;
        } 
    
        return false;
    }
    
    
    /**
     * Shows tags in each post
     */
    public function submit_show_post_author_date()
    { 
        global $post, $lang;
        
        if ($post->vars['useCategories'] && $post->vars['category']) { 
        
            $category =  $post->vars['category'];
            $cat_name = $post->vars['catName'];
            
            echo " " . $lang["submit_show_post_in_category"] . " ";
            
            echo "<a href='" . url(array('category'=>$category)) . "'>" . $cat_name . "</a></li>\n";
        }        
    }
    
    
    /**
     * Displays categories as a tree
     *
     * @param mixed $args
     *
     * This isn't a plugin hook, but a public function call created in the Sidebar plugin.
     */
    public function sidebar_widget_categories($args)
    {
        global $db, $the_cats, $cat_level, $lang, $hotaru, $plugins, $sidebar;
        
        $catObj = new Category();
        
        // Get settings from database if they exist...
        $bar = $plugins->pluginSettings('categories', 'categories_bar');
        
        // Only show if the sidebar is enabled
        if ($bar == 'side') {
        
            $sql = "SELECT * FROM " . TABLE_CATEGORIES . " ORDER BY category_order ASC";
            $the_cats = $db->get_results($db->prepare($sql));
            
            echo "<h2>" . $lang["sidebar_categories"] . "</h2>";
            echo "<ul class='sidebar_categories'>\n";
            foreach ($the_cats as $cat) {
                $cat_level = 1;    // top level category.
                if ($cat->category_name != "all") {
                    echo "<li>";
                    if ($cat->category_parent > 1) {
                        $depth = $catObj->getCatLevel($cat->category_id);
                        for($i=1; $i<$depth; $i++) {
                            echo "--- ";
                        }
                    } 
                    echo "<a href='" . url(array('category'=>$cat->category_id)) . "'>";
                    echo urldecode($cat->category_name) . "</a></li>\n";
                }
            }
            echo "</ul>\n";
        
        }
    }
    
    
     /* ******************************************************************** 
     * ********************************************************************* 
     * ****************** FUNCTIONS FOR SUBMIT SETTINGS ******************** 
     * *********************************************************************
     * ****************************************************************** */
     
    
    /**
     * Gets current tag settings from the database
     */
    public function submit_settings_get_values()
    {
        global $plugins, $categories;
        
        // Get settings from database if they exist... should return 'checked'
        $categories = $plugins->pluginSettings('submit', 'submit_categories');
        
        // otherwise set to blank...
        if (!$categories) { $categories = ''; }
    
    }
    
    
    /**
     * Add tags field to the submit settings form
     */
    public function submit_settings_form()
    {
        global $plugins, $lang, $categories;
        
        echo "<input type='checkbox' name='categories' value='categories' " . $categories . ">&nbsp;&nbsp;" . $lang["submit_settings_categories"] . "<br />";
    
    }
    
    
    /**
     * Save tag settings.
     */
    public function submit_save_settings()
    {
        global $plugins, $cage, $lang, $categories;
        
        // Categories
        if ($cage->post->keyExists('categories')) { 
            $categories = 'checked'; 
            $post->vars['useCategories'] = true;
        } else { 
            $categories = ''; 
            $post->vars['useCategories'] = false;
        }
            
        $plugins->pluginSettingsUpdate('submit', 'submit_categories', $categories);
    
    }
    
    
     /* ******************************************************************** 
     * ********************************************************************* 
     * ************************* ADMIN SETTINGS **************************** 
     * *********************************************************************
     * ****************************************************************** */
    
    /**
     * Admin sidebar link to settings page
     */
    public function admin_sidebar_plugin_settings() {
        echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>'categories'), 'admin') . "'>Categories</a></li>";
    }
    
    
    /**
     * Call the settings function
     */
    public function admin_plugin_settings() {
        require_once(PLUGINS . 'categories/categories_settings.php');
        $catSettings = new CategoriesSettings();
        $catSettings->settings();
        return true;
    }
    
     /* ******************************************************************** 
     * ********************************************************************* 
     * ************************* EXTRA FUNCTIONS *************************** 
     * *********************************************************************
     * ****************************************************************** */
     
    
    /**
     * Category Bar - shows categories as a drop-down suckerfish menu
     *
     * @link http://www.cssnewbie.com/easy-css-dropdown-menus/
     */
    public function navigation_last()
    {
        global $db, $plugins;
    
        // Get settings from database if they exist...
        $bar = $plugins->pluginSettings('categories', 'categories_bar');
        
        // Only show if the menu bar is enabled
        if ($bar == 'menu') {
        
            $output = '';
        
            $sql    = "SELECT * FROM " . TABLE_CATEGORIES . " WHERE category_parent = %d AND category_id != %d ORDER BY category_order ASC";
            $categories = $db->get_results($db->prepare($sql, 1, 1));
            
            if($categories)
            {
                foreach ($categories as $category) {
            
                    if (FRIENDLY_URLS == "true") { 
                        $link = $category->category_safe_name; 
                    } else {
                        $link = $category->category_id;
                    }
                    
                    $output .= '<li><a href="' . url(array('category'=>$link)) . '">' . urldecode($category->category_name) . "</a>\n";
                    $parent = $category->category_id;
                    
                    if ($parent > 1) 
                    {
                        $sql = "SELECT * FROM " . TABLE_CATEGORIES . " WHERE category_parent = %d ORDER BY category_order ASC";
                        $children = $db->get_results($db->prepare($sql, $parent));
                        
                        $sql = "SELECT count(*) FROM " . TABLE_CATEGORIES . " WHERE category_parent = %d";
                        $countchildren = $db->get_var($db->prepare($sql, $parent));
                        
                        if ($countchildren) 
                        {
                            $output .= "<ul>\n";
                                foreach ($children as $child) 
                                {
                                    if (FRIENDLY_URLS == "true") { 
                                        $link = $child->category_safe_name; 
                                    } else {
                                        $link = $child->category_id;
                                    }
                                    $output .= '<li><a href="' . url(array('category'=>$link)) .'">' . urldecode($child->category_name) . "</a>\n";
                                }
                            $output .= "</ul>\n";
                            $output .= "</li>\n";
                        }
                    }
                }
                        
                // Output the category bar
                echo "<ul id='category_bar'>\n";
                echo $output;
                echo "</ul>\n";
            
            }
    
        }
    }

}

?>