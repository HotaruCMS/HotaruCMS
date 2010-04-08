<?php
/**
 * name: Categories
 * description: Enables categories for posts
 * version: 1.4
 * folder: categories
 * class: Categories
 * type: categories
 * requires: sb_base 0.1, submit 1.9, category_manager 0.7
 * hooks: sb_base_theme_index_top, header_include, pagehandling_getpagename, sb_base_functions_preparelist, sb_base_show_post_author_date, header_end, breadcrumbs, header_meta
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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

class Categories
{
    /**
     * Determine if we are filtering to a category
     * Categories might be numeric, e.g. category=3 or safe names, e.g. category=news_and_business
     * We also test for urls like domain.com/News/ where "News" is a category 
     */
    public function sb_base_theme_index_top($h)
    {
        // if there's a "category" key in the url...
        
        if ($h->cage->get->keyExists('category'))
        { 
            $category = $h->cage->get->noTags('category');
            if (is_numeric($category)) {
                // category is numeric
                $h->vars['category_id'] = $category;
                $h->vars['category_name'] = $h->getCatName($category);
                $h->vars['category_safe_name'] = $h->getCatSafeName($category);
                $h->pageTitle = $h->vars['category_name'];
            } else {
                // category should be a safe name
                $h->vars['category_id'] = $h->getCatId($category);
                $h->vars['category_name'] = $h->getCatName(0, $category);
                $h->vars['category_safe_name'] = $category;
                $h->pageTitle = $h->vars['category_name'];
            }
            if (!$h->pageName) { $h->pageName = 'popular'; }
            $h->subPage = 'category';
            $h->pageType = 'list';
        }
        elseif (!$h->pageType)  // only do this if we don't know the pageType yet... 
        {
            if ($h->pageName == 'all') { return false; } // when sorting to "all", we don't want to filter to the "all" category!

            /*  if $h->pageName is set, then there must be an odd number of query vars where
                the first one is the page name. Let's see if it's a category safe name... */
            $sql = "SELECT category_id, category_name FROM " . TABLE_CATEGORIES . " WHERE category_safe_name = %s LIMIT 1";
            $exists = $h->db->get_row($h->db->prepare($sql, $h->pageName));
            if ($exists) {
                $h->vars['category_id'] = $exists->category_id;
                $h->vars['category_name'] = $exists->category_name;
                $h->vars['category_safe_name'] = $h->pageName;
                $h->pageTitle = $h->vars['category_name'];
                $h->subPage = 'category';  // overwrite the current pageName which is the category name
                $h->pageType = 'list';
            }
        }
    }
    
    
    /**
     * Include CSS and JavaScript files for this plugin
     */
    public function header_include($h)
    {
        // include a files that match the name of the plugin folder:
        $h->includeJs('categories', 'suckerfish');
        $h->includeCss();
    }
    
    
    
    /**
     * Checks if url query string is /category_name/post_name/
     *
     * @return bool
     *
     * Only used for friendly urls. This is necessary because if a url 
     * is /people/top-10-longest-beards/ there's no actual mention of "category" there!
     */
    public function pagehandling_getpagename($h)
    {
        // Can't get keys from the url with Inspekt, so must get the whole query string instead.
        $query_string = $h->cage->server->sanitizeTags('QUERY_STRING');

        // no query string? exit...
        if (!$query_string) { return false; }
        
        // we actually only need the first pair, so won't bother looping.
        $query_string = preg_replace('/&amp;/', '&', $query_string);
        $pairs = explode('&', $query_string); 
        
        // no pairs or equal sign? exit...
        if (!$pairs[0] || !strpos($pairs[0], '=')) { return false; }
        
        list($key, $value) = explode('=', $pairs[0]);
        
        // no key or no value? exit...
        if (!$key || !$value) { return false; }

        $sql = "SELECT category_id FROM " . TABLE_CATEGORIES . " WHERE category_safe_name = %s LIMIT 1";
        $exists = $h->db->get_var($h->db->prepare($sql, $key));
        
        // no category? exit...
        if (!$exists) { return false; }
        
        // Now we know that $key is a category so $value must be the post name. Go get the post_id...
        $h->post->id = $h->post->isPostUrl($h, $value);
        
        // no post? exit...
        if (!$h->post->id) { return false; }
        
        $h->post->readPost($h, $h->post->id);
        $h->pageName = $h->post->url; // slug for page title
        $h->pageTitle = $h->post->title;
        $h->pageType = 'post';
        return true;
    }
    
    
    /**
     * Also changes meta when browsing a category page
     */
    public function header_meta($h)
    {    
        if ($h->subPage == 'category')
        { 
            $cat_meta = $h->getCatMeta($h->vars['category_id']);
            
            if (isset($cat_meta->category_desc)) {
                echo '<meta name="description" content="' . urldecode($cat_meta->category_desc) . '" />' . "\n";
            } else {
                echo '<meta name="description" content="' . $h->lang['header_meta_description'] . '" />' . "\n";  // default meta tags
            }
            
            if (isset($cat_meta->category_keywords)) {
                echo '<meta name="keywords" content="' . urldecode($cat_meta->category_keywords) . '" />' . "\n";
            } else {
                echo '<meta name="description" content="' . $h->lang['header_meta_keywords'] . '" />' . "\n";  // default meta tags
            }

            return true;
        }
    }
    
    
    /**
     * Read category settings
     */
    public function post_read_post_1()
    {
        //categories
        if (($this->getSetting('submit_categories') == 'checked') 
            && ($this->isActive())) { 
            $h->post->vars['useCategories'] = true; 
        } else { 
            $h->post->vars['useCategories'] = false; 
        }
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
    public function sb_base_functions_preparelist($h)
    {
        if ($h->subPage == 'category') 
        {
            // When a user clicks a parent category, we need to show posts from all child categories, too.
            // This only works for one level of sub-categories.
            $filter_string = '(post_category = %d';
            $values = array($h->vars['category_id']);
            $parent = $h->getCatParent($h->vars['category_id']);
            if ($parent == 1) {
                $children = $h->getCatChildren($h->vars['category_id']);
                if ($children) {
                    foreach ($children as $child_id) {
                        $filter_string .= ' || post_category = %d';
                        array_push($values, $child_id->category_id); 
                    }
                }
            }
            $filter_string .= ')';
            $h->vars['filter'][$filter_string] = $values; 
            $h->vars['filter']['post_archived = %s'] = 'N'; // don't include archived posts
        }
    }
    
    
    /**
     * Shows categories before post title in breadcrumbs
     */
    public function breadcrumbs($h)
    { 
        $crumbs = '';
                
        if ($h->subPage == 'category') // the pageType is "list"
        {
            $parent_id = $h->getCatParent($h->vars['category_id']);
            if ($parent_id > 1) {
                $parent_name = $h->getCatName($parent_id);
                $parent_name = stripslashes(htmlentities($parent_name, ENT_QUOTES, 'UTF-8'));
                $crumbs .= "<a href='" . $h->url(array('category'=>$parent_id)) . "'>";
                $crumbs .= $parent_name . "</a> &raquo; \n";
            }
    
            $crumbs .= "<a href='" . $h->url(array('category'=>$h->vars['category_id'])) . "'>\n";
            $crumbs .= $h->vars['category_name'] . "</a>\n ";

            $crumbs .= $h->rssBreadcrumbsLink('', array('category'=>$h->vars['category_id']));
        }
        elseif ($h->pageType == 'post') // the pageName is the post slug (post_url)
        {
            $parent_id = $h->getCatParent($h->post->category);
            if ($parent_id > 1) {
                $parent_name = $h->getCatName($parent_id);
                $parent_name = stripslashes(htmlentities($parent_name, ENT_QUOTES, 'UTF-8'));
                $crumbs .= "<a href='" . $h->url(array('category'=>$parent_id)) . "'>";
                $crumbs .= $parent_name . "</a> &raquo; \n";
            }
    
            $crumbs .= "<a href='" . $h->url(array('category'=>$h->post->category)) . "'>\n";
            $crumbs .= $h->getCatName($h->post->category) . "</a> &raquo; \n";
            $crumbs .= "<a href='" . $h->url(array('page'=>$h->post->id)) . "'>" . $h->post->title . "</a>\n";
        }
        
        if ($crumbs) { return $crumbs; } else { return false; }
    }
    
    
    /**
     * Shows category in each post
     */
    public function sb_base_show_post_author_date($h)
    { 
        if ($h->post->category != 1) { 

            $cat_name = $h->getCatName($h->post->category);
            
            echo " " . $h->lang["sb_base_post_in"] . " ";
            echo "<a href='" . $h->url(array('category'=>$h->post->category)) . "'>" . $cat_name . "</a>\n";
        }        
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
    public function header_end($h)
    {
        $output = '';
        
        // get all top-level categories
        $sql    = "SELECT * FROM " . TABLE_CATEGORIES . " WHERE category_id != %d AND category_parent = %d ORDER BY category_order ASC";
        $query = $h->db->prepare($sql, 1, 1);
        $h->smartCache('on', 'categories', 60, $query); // start using cache
        $categories = $h->db->get_results($query);
       
        if($categories)
        {
            foreach ($categories as $category)
            {
                $parent = $category->category_id;
                
                // Check for children 
                $sql = "SELECT count(*) FROM " . TABLE_CATEGORIES . " WHERE category_parent = %d"; 
                $countchildren = $h->db->get_var($h->db->prepare($sql, $parent)); 
                   
                // If children, go to a recursive function to build links for all children of this top-level category 
                if ($countchildren) { 
                    $depth = 1;
                    $output = $this->buildMenuBar($h, $category, $output, $parent, $depth);
                    $output .= "</ul>";
                } else {  
                    $output = $this->categoryLink($h, $category, $output); 
                }
                
                $output .= "</li>\n";
            }
            
            // Output the category bar
            $h->vars['output'] = $output;   
            $h->displayTemplate('category_bar');
        }
        
        $h->smartCache('off'); // stop using cache
    }
    

    /** 
     * Build Category Menu Bar - recursive function 
     * 
     * @param array $category  
     * @param string $output  
     * @param int $parent 
     * @return string $output 
     */ 
    public function buildMenuBar($h, $category, $output, $parent, $depth) 
    { 
        $output = $this->categoryLink($h, $category, $output); 

        $sql = "SELECT count(*) FROM " . TABLE_CATEGORIES . " WHERE category_parent = %d"; 
        $countchildren = $h->db->get_var($h->db->prepare($sql, $category->category_id)); 

        if ($countchildren) { 
            $output .=  "<ul class='children'>\n"; 
            
            $sql = "SELECT * FROM " . TABLE_CATEGORIES . " WHERE category_parent = %d ORDER BY category_order ASC"; 
            $children = $h->db->get_results($h->db->prepare($sql, $category->category_id)); 
            
            $depth++;
            foreach ($children as $child) { 
                if ($depth < 3) { 
                    $output = $this->buildMenuBar($h, $child, $output, $child->category_id, $depth);
                }
            } 
            $output .= "";
            return $output; 
        }  
        $output .= "</li>";
        return $output; 
    }


    /** 
     * HTML link for each category 
     * 
     * @param array $category  
     * @param string $output  
     * @return string $output 
     */ 
    public function categoryLink($h, $category, $output) 
    { 
        if (FRIENDLY_URLS == "true") {  
            $link = $category->category_safe_name;  
        } else { 
            $link = $category->category_id; 
        }
        
        $active = '';
        if (isset($h->vars['category_id']) && ($h->vars['category_id'] == $category->category_id)) {
            $active = " class='active_cat'";
        }
        
        $category = stripslashes(html_entity_decode(urldecode($category->category_name), ENT_QUOTES,'UTF-8'));
        $output .= '<li' . $active . '><a href="' . $h->url(array('category'=>$link)) .'">' . $category . "</a>\n";
        
        return $output; 
    } 

}

?>
