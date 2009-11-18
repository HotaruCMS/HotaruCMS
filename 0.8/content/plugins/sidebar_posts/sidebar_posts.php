<?php
/**
 * name: Sidebar Posts
 * description: Adds links in the sidebar to the latest posts and top stories on the site.
 * version: 0.8
 * folder: sidebar_posts
 * class: SidebarPosts
 * requires: sidebar_widgets 0.5, submit 1.4
 * hooks: install_plugin, hotaru_header, header_include
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
        // Widgets:
        require_once(PLUGINS . 'sidebar_widgets/libs/Sidebar.php');
        $sidebar = new Sidebar($this->hotaru);
        // plugin name, function name, optional arguments
        $sidebar->addWidget('sidebar_posts', 'sidebar_posts_top', 'top');
        $sidebar->addWidget('sidebar_posts', 'sidebar_posts_latest', 'new');
        $sidebar->addWidget('sidebar_posts', 'sidebar_posts_upcoming', 'upcoming');
        $sidebar->addWidget('sidebar_posts', 'sidebar_posts_day', 'top-24-hours');
        $sidebar->addWidget('sidebar_posts', 'sidebar_posts_week', 'top-7-days');
        $sidebar->addWidget('sidebar_posts', 'sidebar_posts_month', 'top-30-days');
        $sidebar->addWidget('sidebar_posts', 'sidebar_posts_year', 'top-365-days');
        $sidebar->addWidget('sidebar_posts', 'sidebar_posts_all-time', 'top-all-time');
    }
    
    
    /**
     * Display the top or latest posts in the sidebar
     *
     * @param $type either 'top' or 'new', matching the post_status in the db.
     */
    public function sidebar_widget_sidebar_posts($type = 'top')
    {
        $this->includeLanguage();
        $this->sidebarPostsDefault($type);
    }
    
    
    /**
     * Display the default sidebar box
     *
     * @param $type either 'top' or 'new', matching the post_status in the db.
     */
    public function sidebarPostsDefault($type)
    {
        $posts = $this->getSidebarPosts($type, false);
        $title = $this->getSidebarTitle($type);
        
        if (isset($posts) && !empty($posts)) {
            
            $output = "<h2 class='sidebar_widget_head sidebar_posts_title'>\n";
            $output .= "<a href='" . $this->hotaru->url(array('page'=>'rss', 'status'=>$type)) . "' title='" . $this->lang["sidebar_posts_icon_anchor_title"] . "'>\n";
            $output .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_16.png'></a>&nbsp;\n"; // RSS icon
            if ($type == 'new') {
                $link = $this->hotaru->url(array('page'=>'latest'));
            } else {
                $link = BASEURL;
            }
            $output .= "<a href='" . $link . "' title='" . $this->lang["sidebar_posts_title_anchor_title"] . "'>" . $title . "</a></h2>\n"; 
                
            $output .= "<ul class='sidebar_widget_body sidebar_posts_items'>\n";
            $output .= $this->getSidebarPostItems($posts, $type);
            $output .= "</ul>\n";
        }
        
        // Display the whole thing:
        if (isset($output) && $output != '') { echo $output; }
    }

    
    /**
     * Get sidebar title
     *
     * @param $type either 'top' or 'new', matching the post_status in the db.
     * return array $posts
     */
    public function getSidebarTitle($type)
    {
        // FILTER TO NEW POSTS OR TOP POSTS?
        if ($type == 'new' && $this->hotaru->title != 'latest') { 
            $title = $this->lang['sidebar_posts_latest_posts'];
        } elseif ($type == 'top' && $this->hotaru->title != 'top') {
            $title = $this->lang['sidebar_posts_top_posts'];
        } elseif ($type == 'upcoming' && $this->hotaru->title != 'upcoming') {
            $title = $this->lang['sidebar_posts_upcoming_posts'];
        } else {
            switch($type) {
                case 'top-24-hours':
                    $title = $this->lang['sidebar_posts_top_24_hours'];
                    break;
                case 'top-7-days':
                    $title = $this->lang['sidebar_posts_top_7_days'];
                    break;
                case 'top-30-days':
                    $title = $this->lang['sidebar_posts_top_30_days'];
                    break;
                case 'top-365-days':
                    $title = $this->lang['sidebar_posts_top_365_days'];
                    break;
                case 'top-all-time':
                    $title = $this->lang['sidebar_posts_top_all_time'];
                    break;
                default:
                    $title = "No title?";
            }
        }
        return $title;
    }
    

    /**
     * Get sidebar posts
     *
     * @param $type either 'top' or 'new', matching the post_status in the db.
     * return array $posts
     */
    public function getSidebarPosts($type, $custom = true, $limit = 10)
    {
        $this->hotaru->vars['limit'] = $limit;
        
        if (!$custom) 
        {
            // Show latest on front page, top stories on latest page, or both otherwise
            if ($type == 'new' && $this->hotaru->title != 'latest') { 
                $posts = $this->hotaru->post->prepareList('new');
            } elseif ($type == 'top' && $this->hotaru->title != 'top') {
                $posts = $this->hotaru->post->prepareList('top');
            } elseif ($type == 'upcoming' && $this->hotaru->title != 'upcoming') {
                $posts = $this->hotaru->post->prepareList('upcoming');
            }
        }
        else
        {
            // Return posts regardless of what page we're viewing
            if ($type == 'new') { 
                $posts = $this->hotaru->post->prepareList('new');    // get latest stories
            } elseif ($type == 'top') {
                $posts = $this->hotaru->post->prepareList('top');    // get top stories
            } elseif ($type == 'upcoming') {
                $posts = $this->hotaru->post->prepareList('upcoming');    // get upcoming stories
            }
        }
        
        if ($type == 'all') {
            $posts = $this->hotaru->post->prepareList('all');    // get all stories
        } elseif ($type == 'top-24-hours') {
            $posts = $this->hotaru->post->prepareList('top-24-hours');    // get top stories from last 24 hours
        } elseif ($type == 'top-48-hours') {
            $posts = $this->hotaru->post->prepareList('top-48-hours');    // get top stories from last 48 hours
        } elseif ($type == 'top-7-days') {
            $posts = $this->hotaru->post->prepareList('top-7-days');    // get top stories from last 7 days
        } elseif ($type == 'top-30-days') {
            $posts = $this->hotaru->post->prepareList('top-30-days');    // get top stories from last 30 days
        } elseif ($type == 'top-365-days') {
            $posts = $this->hotaru->post->prepareList('top-365-days');    // get top stories from last 365 days
        } elseif ($type == 'top-all-time') {
            $posts = $this->hotaru->post->prepareList('top-all-time');    // get top stories from all time
        }
        
        if ($posts) { return $posts; } else { return false; }
    }
    
    
    /**
     * Get sidebar post items
     *
     * @param array $posts 
     * return string $ouput
     */
    public function getSidebarPostItems($posts = array(), $type = 'new')
    {
        $need_cache = false;
        
        // check for a cached version and use it if no recent update:
        $output = $this->hotaru->smartCache('html', 'posts', 10, '', $type);
        if ($output) {
            return $output;
        } else {
            $need_cache = true;
        }
        
        if ($this->hotaru->post->vars['useCategories']) {
            require_once(PLUGINS . 'categories/libs/Category.php');
            $cat = new Category($this->db);
        }
        
        $vote_settings = $this->getSerializedSettings('vote_simple', 'vote_settings');
        
        if (!$posts) { return false; }
        
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
            $output .= "<li class='sidebar_posts_item'>\n";
            
            // show vote if enabled in Vote settings
            $sb_votes = $vote_settings['vote_sidebar_posts'];
            if ($sb_votes == 'checked') {
                $output .= "<div class='sidebar_posts_vote vote_color_" . $item->post_status . "'>";
                $output .= $item->post_votes_up;
                $output .= "</div>\n";
                
                $output .= "<div class='sidebar_posts_link sidebar_posts_indent'>\n";
            } else {
                $output .= "<div class='sidebar_posts_link'>\n";
            }
            $item_title = stripslashes(html_entity_decode(urldecode($item->post_title), ENT_QUOTES,'UTF-8'));
            $output .= "<a href='" . $this->hotaru->url(array('page'=>$item->post_id)) . "'>\n" . $item_title . "\n</a></div>\n";
            $output .= "</li>\n";
        }
        
        if ($need_cache) {
            $this->hotaru->smartCache('html', 'posts', 10, $output, $type); // make or rewrite the cache file
        }
        
        return $output;
    }

}
?>