<?php
/**
 * RSS Feed functions
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
class Feeds
{
    /**
     * Includes the SimplePie RSS file and sets the cache
     *
     * @param string $feed
     * @param bool $cache
     * @param int $cache_duration
     *
     * @return object|false $sp
     */
    public function newSimplePie($feed='', $cache=RSS_CACHE_ON, $cache_duration=RSS_CACHE_DURATION)
    {
        include_once(EXTENSIONS . "SimplePie/simplepie.inc");
        
        if ($feed != '') {
            $sp = new SimplePie();
            $sp->set_feed_url($feed);
            $sp->set_cache_location(CACHE . "rss_cache/");
            $sp->set_cache_duration($cache_duration);
            if ($cache == "true") { 
                $sp->enable_cache(true);
            } else {
                $sp->enable_cache(false);
            }
            $sp->handle_content_type();
            return $sp;
        } else { 
            return false; 
        }
    }
    
    
     /**
     * Display Hotaru forums feed on Admin front page
     *
     * @param int $max_items
     * @param int $items_with_content
     * @param int $max_chars
     */
    public function adminNews($lang, $max_items = 10, $items_with_content = 3, $max_chars = 300)
    {
        $feedurl = 'http://feeds2.feedburner.com/hotarucms';
        $feed = $this->newSimplePie($feedurl);
        $feed->init();
            
        $output = "";
        $item_count = 0;
            
        if ($feed->data) { 
            foreach ($feed->get_items() as $item)
            {
                $output .= "<div>";
                
                // Title
                $output .= "<a href='" . $item->get_permalink() . "'>" . $item->get_title() . "</a><br />";
                
                if ($item_count < $items_with_content)
                {
                    // Posted by
                    $output .= "<small>" . $lang["admin_news_posted_by"] . " ";
                    
                    foreach ($item->get_authors() as $author) 
                    {
                        $output .= $author->get_name(); 
                    }
                    
                    // Date
                    $output .= " " . $lang["admin_news_on"] . " " . $item->get_date('j F Y');
                    $output .= "</small><br />";
                    
                    // Content
                    $output .= substr(strip_tags($item->get_content()), 0, $max_chars);
                    $output .= "... ";
                    
                    // Read more
                    $output .= "<small><a href='" . $item->get_permalink() . "' title='" . $item->get_title() . "'>[" . $lang["admin_news_read_more"] . "]</a>";
                    $output .= "</small>";
                }
                
                $output .= "</div>";
                if ($item_count < $items_with_content) { $output .="<br />"; }
                if ($item_count == ($items_with_content - 1)) { $output .= "<h3>" . $lang["admin_news_more_threads"] . "</h3>"; }
                
                $item_count++;
                if ($item_count >= $max_items) { break;}
            }
        }
        
        echo $output;
    }
}
?>