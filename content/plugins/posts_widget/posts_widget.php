<?php
/**
 * name: Posts Widget
 * description: Adds links in widgets to the latest posts and top stories on the site.
 * version: 1.1
 * folder: posts_widget
 * class: PostsWidget
 * requires: widgets 0.6, sb_base 0.1
 * hooks: install_plugin, hotaru_header, header_include
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
 
class PostsWidget
{
    /**
     *  Add default settings for Posts Widget plugin on installation
     */
    public function install_plugin($h)
    {
        // Widgets:
        // plugin name, function name, optional arguments
        $h->addWidget('posts_widget', 'posts_widget_top', 'top');
        $h->addWidget('posts_widget', 'posts_widget_latest', 'new');
        $h->addWidget('posts_widget', 'posts_widget_upcoming', 'upcoming');
        $h->addWidget('posts_widget', 'posts_widget_day', 'top-24-hours');
        $h->addWidget('posts_widget', 'posts_widget_week', 'top-7-days');
        $h->addWidget('posts_widget', 'posts_widget_month', 'top-30-days');
        $h->addWidget('posts_widget', 'posts_widget_year', 'top-365-days');
        $h->addWidget('posts_widget', 'posts_widget_all-time', 'top-all-time');
    }
    
    
    /**
     * Display the top or latest posts in the sidebar
     *
     * @param $type either 'top' or 'new', matching the post_status in the db.
     */
    public function widget_posts_widget($h, $type = 'top')
    {
        $this->postsWidgetDefault($h, $type);
    }
    
    
    /**
     * Display the default sidebar box
     *
     * @param $type either 'top' or 'new', matching the post_status in the db.
     */
    public function postsWidgetDefault($h, $type)
    {
        $posts = $this->getPostsWidget($h, $type, false);
        $title = $this->getWidgetTitle($h, $type);
        
        if (isset($posts) && !empty($posts)) {
            
            $output = "<h2 class='widget_head posts_widget_title'>\n";

            switch ($type) {
                case 'new':
                    $link = $h->url(array('page'=>'latest'));
                    break;
                case 'upcoming':
                    $link = $h->url(array('page'=>'upcoming'));
                    break;
                case 'top-24-hours':
                    $link = $h->url(array('sort'=>'top-24-hours'));
                    break;
                case 'top-7-days':
                    $link = $h->url(array('sort'=>'top-7-days'));
                    break;
                case 'top-30-days':
                    $link = $h->url(array('sort'=>'top-30-days'));
                    break;
                case 'top-365-days':
                    $link = $h->url(array('sort'=>'top-365-days'));
                    break;
                case 'top-all-time':
                    $link = $h->url(array('sort'=>'top-all-time'));
                    break;
                default:
                    $link = BASEURL;
            }
            $output .= "<a href='" . $link . "' title='" . $h->lang["posts_widget_title_anchor_title"] . "'>" . $title . "</a>\n";
            
            if ($type == 'top' || $type == 'new' || $type == 'upcoming') {
                $output .= "<a href='" . $h->url(array('page'=>'rss', 'status'=>$type)) . "' title='" . $h->lang["posts_widget_icon_anchor_title"] . "'>\n";
                $output .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_16.png' alt='RSS' /></a>\n"; // RSS icon
            }
            
            $output .= "</h2>\n"; 
            
            $output .= "<ul class='widget_body posts_widget_items'>\n";
            $output .= $this->getPostsWidgetItems($h, $posts, $type);
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
    public function getWidgetTitle($h, $type)
    {
        // FILTER TO NEW POSTS OR TOP POSTS?
        if ($type == 'new' && $h->pageName != 'latest') { 
            $title = $h->lang['posts_widget_latest_posts'];
        } elseif ($type == 'top' && $h->pageName != 'index') {
            $title = $h->lang['posts_widget_top_posts'];
        } elseif ($type == 'upcoming' && $h->pageName != 'upcoming') {
            $title = $h->lang['posts_widget_upcoming_posts'];
        } else {
            switch($type) {
                case 'top-24-hours':
                    $title = $h->lang['posts_widget_top_24_hours'];
                    break;
                case 'top-7-days':
                    $title = $h->lang['posts_widget_top_7_days'];
                    break;
                case 'top-30-days':
                    $title = $h->lang['posts_widget_top_30_days'];
                    break;
                case 'top-365-days':
                    $title = $h->lang['posts_widget_top_365_days'];
                    break;
                case 'top-all-time':
                    $title = $h->lang['posts_widget_top_all_time'];
                    break;
                default:
                    $title = "No title?";
            }
        }
        return $title;
    }
    

    /**
     * Get widget posts
     *
     * @param $type either 'top' or 'new', matching the post_status in the db.
     * return array $posts
     */
    public function getPostsWidget($h, $type, $custom = true, $limit = 10)
    {
        $h->vars['limit'] = $limit;
        $posts = '';
        
        // include sb_base_functions class:
        require_once(PLUGINS . 'sb_base/libs/SbBaseFunctions.php');
        $sbfuncs = new SbBaseFunctions();
        
        if (!$custom) 
        {
            // Show latest on front page, top stories on latest page, or both otherwise
            if ($type == 'new' && $h->pageName != 'latest') { 
                $posts = $sbfuncs->prepareList($h, 'new');
            } elseif ($type == 'top' && $h->pageName != 'index') {
                $posts = $sbfuncs->prepareList($h, 'top');
            } elseif ($type == 'upcoming' && $h->pageName != 'upcoming') {
                $posts = $sbfuncs->prepareList($h, 'upcoming');
            }
        }
        else
        {
            // Return posts regardless of what page we're viewing
            if ($type == 'new') { 
                $posts = $sbfuncs->prepareList($h, 'new');    // get latest stories
            } elseif ($type == 'top') {
                $posts = $sbfuncs->prepareList($h, 'top');    // get top stories
            } elseif ($type == 'upcoming') {
                $posts = $sbfuncs->prepareList($h, 'upcoming');    // get upcoming stories
            }
        }
        
        if ($type == 'all') {
            $posts = $sbfuncs->prepareList($h, 'all');    // get all stories
        } elseif ($type == 'top-24-hours') {
            $posts = $sbfuncs->prepareList($h, 'top-24-hours');    // get top stories from last 24 hours
        } elseif ($type == 'top-48-hours') {
            $posts = $sbfuncs->prepareList($h, 'top-48-hours');    // get top stories from last 48 hours
        } elseif ($type == 'top-7-days') {
            $posts = $sbfuncs->prepareList($h, 'top-7-days');    // get top stories from last 7 days
        } elseif ($type == 'top-30-days') {
            $posts = $sbfuncs->prepareList($h, 'top-30-days');    // get top stories from last 30 days
        } elseif ($type == 'top-365-days') {
            $posts = $sbfuncs->prepareList($h, 'top-365-days');    // get top stories from last 365 days
        } elseif ($type == 'top-all-time') {
            $posts = $sbfuncs->prepareList($h, 'top-all-time');    // get top stories from all time
        }

        if ($posts) { return $posts; } else { return false; }
    }
    
    
    /**
     * Get post widget items
     *
     * @param array $posts 
     * return string $ouput
     */
    public function getPostsWidgetItems($h, $posts = array(), $type = 'new')
    {
        if (!$posts) { return false; }
        
        $need_cache = false;
        
        // check for a cached version and use it if no recent update:
        $output = $h->smartCache('html', 'posts', 10, '', $type);
        if ($output) {
            return $output;
        } else {
            $need_cache = true;
        }
        
        $vote_settings = $h->getSerializedSettings('vote', 'vote_settings');
        $widget_votes = $vote_settings['posts_widget'];
        
        foreach ($posts as $item) {
            
            $h->post->url = $item->post_url; // used in Hotaru's url function
            $h->post->category = $item->post_category; // used in Hotaru's url function
            
            // POST TITLE
            $output .= "<li class='posts_widget_item'>\n";
            
            // show vote if enabled in Vote settings
            if ($widget_votes == 'checked') {
                $output .= "<div class='posts_widget_vote vote_color_" . $item->post_status . "'>";
                $output .= $item->post_votes_up;
                $output .= "</div>\n";
                
                $output .= "<div class='posts_widget_link posts_widget_indent'>\n";
            } else {
                $output .= "<div class='posts_widget_link'>\n";
            }
            $item_title = stripslashes(html_entity_decode(urldecode($item->post_title), ENT_QUOTES,'UTF-8'));
            $output .= "<a href='" . $h->url(array('page'=>$item->post_id)) . "'>\n" . $item_title . "\n</a></div>\n";
            $output .= "</li>\n";
        }
        
        if ($need_cache) {
            $h->smartCache('html', 'posts', 10, $output, $type); // make or rewrite the cache file
        }
        
        return $output;
    }

}
?>