<?php
/**
 * name: Categories
 * description: Enables categories for posts
 * version: 1.1
 * folder: categories
 * class: Categories
 * requires: submit 1.4, category_manager 0.6
 * hooks: install_plugin, hotaru_header, header_include, submit_hotaru_header_1, submit_hotaru_header_2, post_read_post_1, post_read_post_2, post_add_post, post_update_post, submit_form_2_assign, submit_form_2_fields, submit_form_2_check_for_errors, submit_form_2_process_submission, submit_settings_get_values, submit_settings_form, submit_save_settings, post_list_filter, submit_show_post_author_date, post_header, admin_sidebar_plugin_settings, admin_plugin_settings, breadcrumbs
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
        // Default settings
        if (!$this->getSetting('submit_categories')) { $this->updateSetting('submit_categories', 'checked'); }
        if (!$this->getSetting('categories_bar')) { $this->updateSetting('categories_bar', 'menu'); }
        
        if ($this->isActive('sidebar_widgets')) {
            require_once(PLUGINS . 'sidebar_widgets/libs/Sidebar.php');
            $sidebar = new Sidebar($this->hotaru);
            $sidebar->addWidget('categories', 'categories', ''); // plugin name, function name, optional arguments
        }
    }
    
    
    /**
     * Defines db table and includes language file
     */
    public function hotaru_header()
    {
        // The categories table is defined 
        if (!defined('TABLE_CATEGORIES')) { define("TABLE_CATEGORIES", DB_PREFIX . "categories"); }
        
        $this->hotaru->vars['categories_smart_cache'] = 10; // 10 minutes
        
        // include language file
        $this->includeLanguage();
        
        // Get page title    
        if ($this->cage->get->keyExists('category'))
        {
            require_once(PLUGINS . 'categories/libs/Category.php');
            $cat = new Category($this->db);
            
            if (is_numeric($this->cage->get->notags('category'))) 
            { 
                $this->hotaru->title = $cat->getCatName($this->cage->get->getInt('category')); // friendly URLs: FALSE
            } 
            else 
            {
                $this->hotaru->title = $cat->getCatName(0, $this->cage->get->notags('category')); // friendly URLs: TRUE
            } 
            $this->hotaru->title = stripslashes(htmlentities($this->hotaru->title, ENT_QUOTES,'UTF-8'));
        }
    }
    
    
    /**
     * Include All CSS and JavaScript files for this plugin
     */
    public function header_include()
    {
        // include a files that match the name of the plugin folder:
        $this->hotaru->includeJs('suckerfish', $this->folder); // filename, folder name
        $this->hotaru->includeCss('', $this->folder);
    }
    
    
    /**
     * Adds additional member variables when the $post object is read in the Submit plugin.
     */
    public function submit_hotaru_header_1()
    {
        // The categories table is defined 
        if (!defined('TABLE_CATEGORIES')) { define("TABLE_CATEGORIES", DB_PREFIX . "categories"); }
        
        // include language file
        $this->includeLanguage('categories');
        
        $this->hotaru->post->vars['category'] = 1;    // default category ('all').
        $this->hotaru->post->vars['catName'] = '';
        $this->hotaru->post->vars['catSafeName'] = '';
        $this->hotaru->post->vars['useCategories'] = true;
        
    }
    
    
    /**
     * Checks if url query string is /category_name/post_name/
     *
     * @return bool
     *
     * Only used for friendly urls. This is necessary because if a url 
     * is /people/top-10-longest-beards/ there's no actual mention of "category" there!
     */
    public function submit_hotaru_header_2()
    {
        if (FRIENDLY_URLS == "true" && $this->hotaru->post->id == 0) {
            // No post stored in post object, nothing was succesfully read by the Submit plugin        
                    
            // Can't get keys from the url with Inspekt, so must get the whole query string instead.
            $query_string = $this->cage->server->getMixedString2('QUERY_STRING');
            
            if ($query_string) {
                // we actually only need the first pair, so won't bother looping.
                $query_string = preg_replace('/&amp;/', '&', $query_string);
                $pairs = explode('&', $query_string); 
                if ($pairs[0] && strpos($pairs[0], '=')) {
                    list($key, $value) = explode('=', $pairs[0]);
                    if ($key) {
                        // Using db_prefix because table_categories might not be defined yet (depends on plugin install order)
                        
                        $sql = "SELECT category_id FROM " . DB_PREFIX . "categories WHERE category_safe_name = %s LIMIT 1";
                        $exists = $this->db->get_var($this->db->prepare($sql, $key));
                        
                        if ($exists && $value) {
                            // Now we know that $key is a category so $value must be the post name. Go get the post_id...
                            $this->hotaru->post->id = $this->hotaru->post->isPostUrl($value);
                            if ($this->hotaru->post->id) {
                                $this->hotaru->post->readPost($this->hotaru->post->id);
                                $this->hotaru->post->vars['isCategoryPost'] = true; 
                                $this->hotaru->pageType = 'post';
                                $this->hotaru->title = $this->hotaru->post->title;
                                return true;
                            } else {
                                $this->hotaru->post->vars['isCategoryPost'] = 'error';
                            }
                        } 
                    }
                }
            }
        }
        $this->hotaru->post->vars['isCategoryPost'] = false;
        return false;
    }
    
    
    /**
     * Read category settings
     */
    public function post_read_post_1()
    {
        //categories
        if (($this->getSetting('submit_categories') == 'checked') 
            && ($this->isActive())) { 
            $this->hotaru->post->vars['useCategories'] = true; 
        } else { 
            $this->hotaru->post->vars['useCategories'] = false; 
        }
    }
    
    
    /**
     * Read category from the post in the database.
     */
    public function post_read_post_2()
    {
        if (!isset($this->hotaru->post->vars['post_row']->post_category)) { return false; }
        
        $this->hotaru->post->vars['category'] = $this->hotaru->post->vars['post_row']->post_category;
        
        // Build SQL
        $query = "SELECT category_name, category_safe_name FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
        $sql = $this->db->prepare($query, $this->hotaru->post->vars['category']);
        
        // Create temp cache array
        if (!isset($this->hotaru->vars['tempCategoryCache'])) { $this->hotaru->vars['tempCategoryCache'] = array(); }

        // If this query has already been read once this page load, we should have it in memory...
        if (array_key_exists($sql, $this->hotaru->vars['tempCategoryCache'])) {
            // Fetch from memory
            $cat = $this->hotaru->vars['tempCategoryCache'][$sql];
        } else {
            // Fetch from database
            $cat = $this->db->get_row($sql);
            $this->hotaru->vars['tempCategoryCache'][$sql] = $cat;
        }
        
        $this->hotaru->post->vars['catName'] = urldecode($cat->category_name);
        $this->hotaru->post->vars['catSafeName'] = urldecode($cat->category_safe_name);
    }
    
    
    /**
     * Adds category to the posts table
     */
    public function post_add_post()
    {
        $sql = "UPDATE " . TABLE_POSTS . " SET post_category = %d WHERE post_id = %d";
        $this->db->query($this->db->prepare($sql, $this->hotaru->post->vars['category'], $this->hotaru->post->vars['last_insert_id']));
    }
    
    
    /**
     * Updates category in the posts table
     */
    public function post_update_post()
    {
        $sql = "UPDATE " . TABLE_POSTS . " SET post_category = %d WHERE post_id = %d";
        $this->db->query($this->db->prepare($sql, $this->hotaru->post->vars['category'], $this->hotaru->post->id));
    }
    
    
     /* ******************************************************************** 
     * ********************************************************************* 
     * ********************* FUNCTIONS FOR SUBMIT FORM ********************* 
     * *********************************************************************
     * ****************************************************************** */
     
    
    /**
     * Sets $this->hotaru->post->vars['category_check'] to the value of the chosen category
     */
    public function submit_form_2_assign()
    {
        if ($this->cage->post->getAlpha('submit2') == 'true') {
            // Submitted this form...
            $this->hotaru->post->vars['category_check'] = $this->cage->post->getInt('post_category');
            
        } elseif ($this->cage->post->getAlpha('submit3') == 'edit') {
            // Come back from step 3 to make changes...
            $this->hotaru->post->vars['category_check'] = $this->hotaru->post->vars['category'];
            
        } elseif ($this->hotaru->isPage('edit_post')) {
            // Editing a previously submitted post
            if ($this->cage->post->getAlpha('edit_post') == 'true') {
                $this->hotaru->post->vars['category_check'] = $this->cage->post->getInt('post_category');
            } else {
                $this->hotaru->post->vars['category_check'] = $this->hotaru->post->vars['category'];
            }
        
        } else {
            // First time here...
            $this->hotaru->post->vars['category_check'] = 1;
        }
    
    }
    
    
    /**
     * Adds a category drop-down box to submit form 2
     */
    public function submit_form_2_fields()
    {
        if ($this->hotaru->post->vars['useCategories']) { 
            echo "<tr>\n";
                echo "<td>" . $this->lang["submit_form_category"] . ":&nbsp; </td>\n";
                echo "<td><select name='post_category'>\n";
                
                $sql = "SELECT category_name FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
                $category_name = $this->db->get_var($this->db->prepare($sql, $this->hotaru->post->vars['category_check']));
                
                $category_name = stripslashes(htmlentities(urldecode($category_name), ENT_QUOTES,'UTF-8'));
                
                if ($category_name == 'all') { 
                    $category_name = $this->lang['submit_form_category_select']; 
                }
                
                echo "<option value=" . $this->hotaru->post->vars['category_check'] . ">" . $category_name . "</option>\n";
                
                $sql = "SELECT category_id, category_name FROM " . TABLE_CATEGORIES . " ORDER BY category_order ASC";
                $cats = $this->db->get_results($this->db->prepare($sql));
                
                if ($cats) {
                    foreach ($cats as $cat) {
                        if ($cat->category_id != 1) { 
                            $cat_name = stripslashes(htmlentities(urldecode($cat->category_name), ENT_QUOTES,'UTF-8'));
                            echo "<option value=" . $cat->category_id . ">" . $cat_name . "</option>\n";
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
        // ******** CHECK CATEGORY ********
        if ($this->hotaru->post->vars['useCategories']) {
            $this->hotaru->post->vars['category_check'] = $this->cage->post->getInt('post_category');    
            if ($this->hotaru->post->vars['category_check'] > 1) {
                // category is okay.
                $error_category = 0;
            } else {
                // No category present...
                $this->hotaru->messages[$this->lang['submit_form_category_error']] = "red";
                $error_category = 1;
            }
        }
        return $error_category;
    }
    
    
    /**
     * Sets $this->hotaru->post->post_category to submitted category id
     */
    public function submit_form_2_process_submission()
    {
        $this->hotaru->post->vars['category'] = $this->cage->post->getInt('post_category');
        
        $sql = "SELECT category_name, category_safe_name FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
        $cat = $this->db->get_row($this->db->prepare($sql, $this->hotaru->post->vars['category']));
        $this->hotaru->post->vars['catName'] = urldecode($cat->category_name);
        $this->hotaru->post->vars['catSafeName'] = urldecode($cat->category_safe_name);
    }
    
    
     /* ******************************************************************** 
     * ********************************************************************* 
     * ******************* FUNCTIONS FOR SHOWING POSTS ********************* 
     * *********************************************************************
     * ****************************************************************** */
    
    /**
     * Gets a category from the url and sets the filter for get_posts
     *
     * @return bool
     */
    public function post_list_filter()
    {
        if (!$this->cage->get->keyExists('category')) { return false; }

        require_once(PLUGINS . 'categories/libs/Category.php');
        $cat = new Category($this->db);
        
        if (FRIENDLY_URLS == "true") {
            $cat_id = $cat->getCatId($this->cage->get->noTags('category')); 
        } else {
            $cat_id = $this->cage->get->getInt('category'); 
        }
        
        // When a user clicks a parent category, we need to show posts from all child categories, too.
        // This only works for onle level of sub-categories.
        $filter_string = '(post_category = %d';
        $values = array($cat_id);
        $parent = $cat->getCatParent($cat_id);
        if ($parent == 1) {
            $children = $cat->getCatChildren($cat_id);
            if ($children) {
                foreach ($children as $child_id) {
                    $filter_string .= ' || post_category = %d';
                    array_push($values, $child_id->category_id); 
                }
            }
        }
        $filter_string .= ')';
        $this->hotaru->vars['filter'][$filter_string] = $values; 
        $this->hotaru->vars['filter']['post_archived = %s'] = 'N'; // don't include archived posts
        $rss = " <a href='" . $this->hotaru->url(array('page'=>'rss', 'category'=>$cat_id)) . "'>";
        $rss .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png'></a>";
        
        /* check if we're looking at a category to determine correct breadcrumbs. 
        This is necessary because when sorting, we have to override whatever is already there. */
        
        if ($this->hotaru->cage->get->keyExists('category')) { 
            $category = $this->hotaru->cage->get->noTags('category');
            require_once(PLUGINS . 'categories/libs/Category.php');
            $cat = new Category($this->db);
            if (is_numeric($category)) { 
                $parent_id = $cat->getCatParent($category);
                $category_id = $category;
                $category = $cat->getCatName($category_id);
            } else {
                $category_id = $cat->getCatId($category);
                $parent_id = $cat->getCatParent($category_id);
                $category = $cat->getCatName('', $category);
            }
            
            if ($parent_id) { 
                $parent = $cat->getCatName($parent_id);
                $parent = stripslashes(htmlentities($parent, ENT_QUOTES,'UTF-8'));
            }

            $category = stripslashes(htmlentities($category, ENT_QUOTES,'UTF-8'));

            $this->hotaru->title = $category; // used in title tags
        }
        
        if ($parent_id > 1) {
            $this->hotaru->vars['page_title'] = "<a href='" . $this->hotaru->url(array('category'=>$parent_id)) . "'>" . $parent . "</a> &raquo; " . $this->hotaru->title . $rss;
        } else {
            $this->hotaru->vars['page_title'] = $this->hotaru->title . $rss;
        }
        
        // use these in post page breadcrumbs:
        $this->hotaru->vars['cat_id'] = $category_id;
        $this->hotaru->vars['cat_name'] = $category;
        $this->hotaru->vars['parent_cat_id'] = $parent_id;
        $this->hotaru->vars['parent_cat_name'] = $parent;
        
        return true;

    }
    
    
    /**
     * Shows categories before post title in breadcrumbs
     */
    public function breadcrumbs()
    { 
        if ($this->hotaru->pageType != 'post') { return false; }
        if (!$this->hotaru->post->vars['useCategories']) { return false; }
        
        require_once(PLUGINS . 'categories/libs/Category.php');
        $cat = new Category($this->db);
        $cat_id = $cat->getCatId($this->hotaru->post->vars['catSafeName']);
        $cat_name = $cat->getCatName($cat_id);
        $parent_id = $cat->getCatParent($cat_id);

        
        if ($parent_id > 1) {
            $parent_name = $cat->getCatName($parent_id);
            $parent_name = stripslashes(htmlentities($parent_name, ENT_QUOTES, 'UTF-8'));
            echo "<a href='" . $this->hotaru->url(array('category'=>$parent_id)) . "'>";
            echo $parent_name . "</a> &raquo; ";
        }
        
        if ($cat_id) {
            $cat_name = stripslashes(htmlentities($cat_name, ENT_QUOTES, 'UTF-8'));
            echo "<a href='" . $this->hotaru->url(array('category'=>$cat_id)) . "'>";
            echo $cat_name . "</a> &raquo; ";
        }
    }
    
    
    /**
     * Shows tags in each post
     */
    public function submit_show_post_author_date()
    { 
        if ($this->hotaru->post->vars['useCategories'] && $this->hotaru->post->vars['category']) { 
        
            $category =  $this->hotaru->post->vars['category'];
            $cat_name = $this->hotaru->post->vars['catName'];
            
            echo " " . $this->lang["submit_show_post_in_category"] . " ";
            
            $cat_name = stripslashes(html_entity_decode($cat_name, ENT_QUOTES,'UTF-8'));
            echo "<a href='" . $this->hotaru->url(array('category'=>$category)) . "'>" . $cat_name . "</a></li>\n";
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
        if (!$this->isActive('sidebar_widgets')) { return false; }
        
        require_once(PLUGINS . 'categories/libs/Category.php');
        $catObj = new Category($this->db);
        
        // Get settings from database if they exist...
        //setting name. plugin name.
        $bar = $this->getSetting('categories_bar', 'categories');
                
        // Only show if the sidebar is enabled
        if ($bar == 'side') {
        
            $sql = "SELECT * FROM " . TABLE_CATEGORIES . " ORDER BY category_order ASC";
            $the_cats = $this->db->get_results($this->db->prepare($sql));
            
            echo "<h2 class='sidebar_widget_head'>" . $this->lang["sidebar_categories"] . "</h2>";
            echo "<div class='sidebar_widget_body'>\n";
            echo "<ul class='sidebar_categories'>\n";
            foreach ($the_cats as $cat) {
                $cat_level = 1;    // top level category.
                if ($cat->category_name != "all") {
                    echo "<li>";
                    if ($cat->category_parent > 1) {
                        $depth = $catObj->getCatLevel($cat->category_id, $cat_level, $the_cats);
                        for($i=1; $i<$depth; $i++) {
                            echo "--- ";
                        }
                    } 
                    $category = stripslashes(html_entity_decode(urldecode($cat->category_name), ENT_QUOTES,'UTF-8'));
                    echo "<a href='" . $this->hotaru->url(array('category'=>$cat->category_id)) . "'>";
                    echo $category . "</a></li>\n";
                }
            }
            echo "</ul></div>\n";
        
        }
    }
    
    
     /* ******************************************************************** 
     * ********************************************************************* 
     * ****************** FUNCTIONS FOR SUBMIT SETTINGS ******************** 
     * *********************************************************************
     * ****************************************************************** */
     
    
    /**
     * Gets current category settings from the database
     */
    public function submit_settings_get_values()
    {
        // Get settings from database if they exist... should return 'checked'
        $this->hotaru->vars['categories'] = $this->getSetting('submit_categories');
        
        // doesn't exist - use default:
        if (!isset($this->hotaru->vars['categories'])) {
            $this->hotaru->vars['categories'] = 'checked';
        }
    
    }
    
    
    /**
     * Add categories field to the submit settings form
     */
    public function submit_settings_form()
    {
        echo "<input type='checkbox' name='categories' value='categories' " . $this->hotaru->vars['categories'] . ">&nbsp;&nbsp;" . $this->lang["submit_settings_categories"] . "<br />";
    }
    
    
    /**
     * Save category settings.
     */
    public function submit_save_settings()
    {
        // Categories
        if ($this->cage->post->keyExists('categories')) { 
            $this->hotaru->vars['categories'] = 'checked'; 
            $this->hotaru->post->vars['useCategories'] = true;
        } else { 
            $this->hotaru->vars['categories'] = ''; 
            $this->hotaru->post->vars['useCategories'] = false;
        }
            
        $this->updateSetting('submit_categories', $this->hotaru->vars['categories']);
    
    }
    

     /* ******************************************************************** 
     * ********************************************************************* 
     * ************************* EXTRA FUNCTIONS *************************** 
     * *********************************************************************
     * ****************************************************************** */


    /**
     * Category Bar - shows categories as a drop-down menu
     *
     * Adapted from:
     * @link http://www.cssnewbie.com/easy-css-dropdown-menus/
     */
    public function post_header()
    {
        // Get settings from database if they exist...
        $bar = $this->getSetting('categories_bar');
        
        // Only show if the menu bar is enabled
        if ($bar == 'menu') {
        
            $output = '';
            
            // get all top-level categories
            $this->hotaru->smartCache('on', 'categories', $this->hotaru->vars['categories_smart_cache']); // start using cache
            $sql    = "SELECT * FROM " . TABLE_CATEGORIES . " WHERE category_id != %d AND category_parent = %d ORDER BY category_order ASC";
            $categories = $this->db->get_results($this->db->prepare($sql, 1, 1));
           
            if($categories)
            {
                foreach ($categories as $category)
                {
                    $parent = $category->category_id;
                    
                    // Check for children 
                    $sql = "SELECT count(*) FROM " . TABLE_CATEGORIES . " WHERE category_parent = %d"; 
                    $countchildren = $this->db->get_var($this->db->prepare($sql, $parent)); 
                       
                    // If children, go to a recursive function to build links for all children of this top-level category 
                    if ($countchildren) { 
                        $depth = 1;
                        $output = $this->buildMenuBar($category, $output, $parent, $depth); 
                    } else {  
                        $output = $this->categoryLink($category, $output); 
                    }
                    
                    $output .= "</li>\n";
                }
                
                // Output the category bar
                $this->hotaru->vars['output'] = $output;   
                $this->hotaru->displayTemplate('category_bar', 'categories');
            }
            
            $this->hotaru->smartCache('off'); // stop using cache
        }
    }
    

    /** 
     * Build Category Menu Bar - recursive function 
     * 
     * @param array $category  
     * @param string $output  
     * @param int $parent 
     * @return string $output 
     */ 
    public function buildMenuBar($category, $output, $parent, $depth) 
    { 
        $output = $this->categoryLink($category, $output); 

        $sql = "SELECT count(*) FROM " . TABLE_CATEGORIES . " WHERE category_parent = %d"; 
        $countchildren = $this->db->get_var($this->db->prepare($sql, $category->category_id)); 

        if ($countchildren) { 
            $output .=  "<ul class='children'>\n"; 
            
            $sql = "SELECT * FROM " . TABLE_CATEGORIES . " WHERE category_parent = %d ORDER BY category_order ASC"; 
            $children = $this->db->get_results($this->db->prepare($sql, $category->category_id)); 
            
            $depth++;
            foreach ($children as $child) { 
                if ($depth < 3) { 
                    $output = $this->buildMenuBar($child, $output, $child->category_id, $depth);
                }
            } 
            $output .= "</li></ul>"; 
            return $output; 
        }  

        return $output; 
    }


    /** 
     * HTML link for each category 
     * 
     * @param array $category  
     * @param string $output  
     * @return string $output 
     */ 
    public function categoryLink($category, $output) 
    { 
        if (FRIENDLY_URLS == "true") {  
            $link = $category->category_safe_name;  
        } else { 
            $link = $category->category_id; 
        } 
    
        $category = stripslashes(html_entity_decode(urldecode($category->category_name), ENT_QUOTES,'UTF-8'));
        $output .= '<li><a href="' . $this->hotaru->url(array('category'=>$link)) .'">' . $category . "</a>\n"; 
        
        return $output; 
    } 

}

?>