<?php
/**
 * name: Sidebar Posts
 * description: Adds links in the sidebar to the latest posts and top stories on the site.
 * version: 0.2
 * folder: sidebar_posts
 * class: SidebarPosts
 * requires: sidebar_widgets 0.2, submit 0.3
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
        $this->updateSetting('sidebar_posts_top', 'top', 'sidebar_widgets');
        $this->updateSetting('sidebar_posts_latest', 'new', 'sidebar_widgets');
    }
    
    
    /**
     * Display the top or latest posts in the sidebar
     *
     * @param $type either 'top' or 'new', matching the post_status in the db.
     */
    function sidebar_widget_sidebar_posts($type = 'top')
    {
        global $hotaru, $post, $lang;
        
        echo "IN SIDEBAR POSTS";
        
        $this->includeLanguage();
        
        // FILTER TO NEW POSTS OR TOP POSTS?
        if ($type == 'new' && $hotaru->getTitle() != 'latest') { 
            $posts = $post->getPosts($post->filter(array('post_status = %s' => 'new'), 10));    // get latest stories
            $title = $lang['sidebar_posts_latest_posts'];
        } elseif ($type == 'top' && $hotaru->getTitle() != 'top') {
            $posts = $post->getPosts($post->filter(array('post_status = %s' => 'top'), 10));    // get top stories
            $title = $lang['sidebar_posts_top_posts'];
        }
        
        if (isset($posts) && !empty($posts)) {
            
            $output = "<h2 class='sidebar_posts_title'>";
            $output .= "<a href='" . url(array('page'=>'rss', 'status'=>$type)) . "' title='" . $lang["sidebar_posts_icon_anchor_title"] . "'><img src='" . BASEURL . "content/themes/" . THEME . "images/rss_16.png'></a>&nbsp;"; // RSS icon
            $link = BASEURL;
            $output .= "<a href='" . $link . "' title='" . $lang["sidebar_posts_title_anchor_title"] . "'>" . $title . "</a></h2>"; 
                
            $output .= "<ul class='sidebar_posts_items'>";
        
            foreach ($posts as $item) {
                    
                // POST TITLE
                $output .= "<li class='sidebar_posts_item'>";
                $output .= "<span class='sidebar_posts_title'>";
                $output .= "<a href='" . url(array('page'=>$item->post_id)) . "'>" . urldecode($item->post_title) . "</a></span>";
                $output .= '</li>';
            }
        }
        
        // Display the whole thing:
        if (isset($output)) { echo $output . "</ul>"; }
    }

}
?>