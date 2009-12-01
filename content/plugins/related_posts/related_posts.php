<?php
/**
 * name: Related Posts
 * description: Show a list of related posts
 * version: 0.1
 * folder: related_posts
 * class: relatedPosts
 * requires: submit 1.4, search 0.7
 * hooks: install_plugin, header_include, submit_settings_get_values, submit_settings_form2, submit_save_settings, submit_step3_pre_buttons, submit_step3_post_buttons, submit_show_post_middle
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

class relatedPosts extends PluginFunctions
{

    /**
     * Default settings on install
     */
    public function install_plugin()
    {
        // Default settings
        if (!$this->getSetting('submit_related_posts_submit')) { $this->updateSetting('submit_related_posts_submit', 10); }
        if (!$this->getSetting('submit_related_posts_post')) { $this->updateSetting('submit_related_posts_post', 5); }
    }
    
    
    /**
     * Gets current settings from the database
     */
    public function submit_settings_get_values()
    {
        // Get settings from database if they exist... should return 'checked'
        $this->hotaru->vars['related_posts_submit'] = $this->getSetting('submit_related_posts_submit');
        $this->hotaru->vars['related_posts_post'] = $this->getSetting('submit_related_posts_post');
        
        // doesn't exist - use default:
        if (!isset($this->hotaru->vars['related_posts_submit'])) {
            $this->hotaru->vars['related_posts_submit'] = 10;
        }
        // doesn't exist - use default:
        if (!isset($this->hotaru->vars['related_posts_post'])) {
            $this->hotaru->vars['related_posts_post'] = 5;
        }
    
    }
    
    
    /**
     * Add related posts field to the submit settings form
     */
    public function submit_settings_form2()
    {
        $this->includeLanguage('related_posts');
        echo "<br /><input type='text' size=5 name='related_posts_submit' value='" . $this->hotaru->vars['related_posts_submit'] . "' /> ";
        echo $this->lang["submit_settings_related_posts_submit"] . "<br />\n";
        echo "<br /><input type='text' size=5 name='related_posts_post' value='" . $this->hotaru->vars['related_posts_post'] . "' /> ";
        echo $this->lang["submit_settings_related_posts_post"] . "<br />\n";
    }
    
    
    /**
     * Save related posts settings.
     */
    public function submit_save_settings()
    {
        // Related posts on submit page
        if ($this->cage->post->keyExists('related_posts_submit')) { 
            if (is_numeric($this->cage->post->testInt('related_posts_submit'))) {
                $this->hotaru->vars['related_posts_submit'] = $this->cage->post->testInt('related_posts_submit'); 
            }
        } 
        
        // Related posts on post page
        if ($this->cage->post->keyExists('related_posts_post')) { 
            if (is_numeric($this->cage->post->testInt('related_posts_post'))) {
                $this->hotaru->vars['related_posts_post'] = $this->cage->post->testInt('related_posts_post'); 
            }
        } 
    
        // if empty or not numeric, the existing value will be saved
            
        $this->updateSetting('submit_related_posts_submit', $this->hotaru->vars['related_posts_submit']);
        $this->updateSetting('submit_related_posts_post', $this->hotaru->vars['related_posts_post']);
    
    }
    
    
    /**
     * Show message to check related posts
     */
    public function submit_step3_pre_buttons()
    {
        $this->includeLanguage('related_posts');
        echo $this->lang["related_posts_instruct"];
    }
    
    
    /**
     * Show message to check related posts
     */
    public function submit_step3_post_buttons()
    {
        // Get settings from database if they exist... should return 'checked'
        $num_posts = $this->getSetting('submit_related_posts_submit');
        $this->prepareSearchTerms($num_posts);
    }
    
    
    /**
     * Show related posts on a post page
     */
    public function submit_show_post_middle()
    { 
        if ($this->hotaru->isPage('submit2')) { return false; }
        
        // Get settings from database if they exist... should return 'checked'
        $num_posts = $this->getSetting('submit_related_posts_post');
        $this->prepareSearchTerms($num_posts);
    }
    
    
    /**
     * prepare search terms
     *
     * NOTE: I originally wanted to include the title and category in 
     * the search terms, but found that using ONLY tags is best because 
     * too many words dilute the target topic and anything with less than 
     * 4 characters returns latest first instead of relevance first Using 
     * the title increases the chance of 3 character words. Nick.
     */
    public function prepareSearchTerms($num_posts = 10)
    {
        $this->includeLanguage('related_posts');
        
        /* when we start reading other posts, we'll lose this original one
           which we need later to show comments and whatnot. */
        $original_id = $this->hotaru->post->id;

        /* strip all words less than 4 chars from the title
           and make a space separated string: 
        $title = $this->hotaru->post->title;
        $title_array = explode(' ', $title);
        $new_title = "";
        foreach($title_array as $title_word) {
            if (strlen(trim($title_word)) >= 4) {
                $new_title .= $title_word . " ";
            }
        }*/
        
        // remove hyphens from category safe name
        /*
        if ($this->hotaru->post->vars['useCategories']) {
            require_once(PLUGINS . 'categories/libs/Category.php');
            $cat = new Category($this->db);
            $cat_safe_name = $cat->getCatSafeName($this->hotaru->post->vars['category']);
            $category = str_replace("-"," ", $cat_safe_name); 
        }*/
        
        // make the tags a space separated string
        $tags = str_replace(', ', ' ', $this->hotaru->post->vars['tags']);
        $tags = str_replace(',', ' ', $tags); // if no space after commas
        
        // search terms in a space separated string
        //$search_terms = trim($new_title) . " " . $tags . " " . $category;
        
        $search_terms = $tags;
        
        $output = $this->showRelatedPosts($search_terms, $num_posts);
        echo $output;
        
        $this->hotaru->post->readPost($original_id); // fill the object with the original post details.
    }
    
    
    /**
     * Show related posts
     *
     * @param int $num_posts - max number of posts to show
     *
     */
    public function showRelatedPosts($search_terms = '', $num_posts = 10)
    {
        $results = $this->getRelatedPosts($search_terms, $num_posts);
        if ($results) 
        {
            $output = "<h2 id='related_posts_title'>" . $this->lang['related_posts'] . "</h2>";
        
            $output .= "<ul class='related_posts'>\n";
            foreach ($results as $item) {
                $this->hotaru->post->readPost(0, $item); // needed for the url function
                $output .= "<li class='related_posts_item'>\n";
                $output .= "<div class='related_posts_vote vote_color_" . $item->post_status . "'>";
                $output .= $item->post_votes_up;
                $output .= "</div>\n";
                $output .= "<div class='related_posts_link related_posts_indent'>\n";
                $output .= "<a href='" . $this->hotaru->url(array('page'=>$item->post_id)) . "' ";
                $output .= "title='" . $this->lang['related_links_new_tab'] . "'>\n";
                $output .= stripslashes(urldecode($item->post_title)); 
                $output .= "</a>";
                $output .= "</div>";
                $output .= "</li>\n";
            }
            $output .= "</ul>\n";
        }
        else 
        {
                // Show "No other posts found with matching tags"
                $output .= "<div id='related_posts_none'>\n";
                $output .= $this->lang['related_links_no_results'];
                $output .= "</div>\n";
        }

        return $output;
    }
    
    /**
     * Get related results from the database
     *
     * @param string $search_terms - space separated string of words
     * @param int $num_posts - the max number of posts to return
     * return array|false
     */
    public function getRelatedPosts($search_terms = '', $num_posts = 10)
    {
        if (!$this->isActive('search')) { return false; }
        
        require_once(PLUGINS . 'search/search.php');
        $search = new Search('related_links', $this->hotaru);
        $this->hotaru->vars['filter']['post_archived != %s'] = 'Y';
        $this->hotaru->vars['filter']['post_id != %d'] = $this->hotaru->post->id;
        $prepared_search = $search->prepareSearchFilter($search_terms);
        $prepared_filter = $this->hotaru->post->filter(
            $this->hotaru->vars['filter'], 
            $num_posts, 
            false, 
            $this->hotaru->vars['select'], 
            $this->hotaru->vars['orderby']
        );

        $results = $this->hotaru->post->getPosts($prepared_filter);
        return $results;
    }

}
?>