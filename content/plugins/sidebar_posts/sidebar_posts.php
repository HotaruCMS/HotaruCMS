<?php
/**
 * name: Sidebar Posts
 * description: Adds links in the sidebar to the latest posts and top stories on the site.
 * version: 0.5
 * folder: sidebar_posts
 * class: SidebarPosts
 * requires: sidebar_widgets 0.4, submit 0.7
 * hooks: install_plugin, hotaru_header
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
 
return false; die(); // We don't want to just drop into the file.

class SidebarPosts extends PluginFunctions
{
    /**
     *  Add default settings for Sidebar Posts plugin on installation
     */
    public function install_plugin()
    {
        // Default settings
        require_once(PLUGINS . 'sidebar_widgets/libs/Sidebar.php');
        $sidebar = new Sidebar($this->hotaru);
        // plugin name, function name, optional arguments
        $sidebar->addWidget('sidebar_posts', 'sidebar_posts_top', 'top');
        $sidebar->addWidget('sidebar_posts', 'sidebar_posts_latest', 'new');
    }
    
    
    /**
     * Display the top or latest posts in the sidebar
     *
     * @param $type either 'top' or 'new', matching the post_status in the db.
     */
    function sidebar_widget_sidebar_posts($type = 'top')
    {
        $this->includeLanguage();
        
        // FILTER TO NEW POSTS OR TOP POSTS?
        if ($type == 'new' && $this->hotaru->title != 'latest') { 
            $posts = $this->hotaru->post->getPosts($this->hotaru->post->filter(array('post_status = %s' => 'new'), 10));    // get latest stories
            $title = $this->lang['sidebar_posts_latest_posts'];
        } elseif ($type == 'top' && $this->hotaru->title != 'top') {
            $posts = $this->hotaru->post->getPosts($this->hotaru->post->filter(array('post_status = %s' => 'top'), 10));    // get top stories
            $title = $this->lang['sidebar_posts_top_posts'];
        }
        
        if (isset($posts) && !empty($posts)) {
            
            $output = "<h2 class='sidebar_widget_head sidebar_posts_title'>";
            $output .= "<a href='" . $this->hotaru->url(array('page'=>'rss', 'status'=>$type)) . "' title='" . $this->lang["sidebar_posts_icon_anchor_title"] . "'><img src='" . BASEURL . "content/themes/" . THEME . "images/rss_16.png'></a>&nbsp;"; // RSS icon
            $link = BASEURL;
            $output .= "<a href='" . $link . "' title='" . $this->lang["sidebar_posts_title_anchor_title"] . "'>" . $title . "</a></h2>"; 
                
            $output .= "<ul class='sidebar_widget_body sidebar_posts_items'>";
            
            if ($this->hotaru->post->vars['useCategories']) {
                require_once(PLUGINS . 'categories/libs/Category.php');
                $cat = new Category($this->db);
            }
                    
            foreach ($posts as $item) {
                
                $this->hotaru->post->url = $item->post_url; // used in Hotaru's url function
                
                //reset defaults:
                $this->hotaru->post->vars['category'] = 1;
                $this->hotaru->post->vars['catSafeName'] = '';
                
                if ($this->hotaru->post->vars['useCategories'] && ($item->post_category != 1)) {
                    $this->hotaru->post->vars['category'] = $item->post_category;
                    $this->hotaru->post->vars['catSafeName'] =  $cat->getCatSafeName($item->post_category);
                }

                // POST TITLE
                $output .= "<li class='sidebar_posts_item'>";
                $output .= "<span class='sidebar_posts_title'>";
                $output .= "<a href='" . $this->hotaru->url(array('page'=>$item->post_id)) . "'>" . urldecode($item->post_title) . "</a></span>";
                $output .= '</li>';
            }
        }
        
        // Display the whole thing:
        if (isset($output)) { echo $output . "</ul>"; }
    }

}
?>