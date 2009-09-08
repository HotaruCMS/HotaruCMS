<?php
/**
 * Displays a list of summaries from the Hotaru CMS Forums' RSS feed
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
 * Display Hotaru forums feed on Admin front page
 *
 * @return string Returns the html output for the feed 
 */
function admin_news()
{
    global $hotaru, $plugins, $lang;
        
    $max_items = 5;
    
    $feedurl = 'http://feeds2.feedburner.com/hotarucms';
    $feed = $hotaru->newSimplePie($feedurl);
    $feed->init();
        
    $output = "";
    $item_count = 0;
        
    if ($feed->data) { 
        foreach ($feed->get_items() as $item)
        {
            $output .= "<div>";
            
            // Title
            $output .= "<a href='" . $item->get_permalink() . "'>" . $item->get_title() . "</a><br />";
            
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
            $output .= substr(strip_tags($item->get_content()), 0, 300);
            $output .= "... ";
            
            // Read more
            $output .= "<small><a href='" . $item->get_permalink() . "' title='" . $item->get_title() . "'>[" . $lang["admin_news_read_more"] . "]</a>";
            $output .= "</small>";
            
            $output .= "</div><br />";
            
            $item_count++;
            if ($item_count >= $max_items) { break;}
        }
    }
    
    echo $output;
}

?>