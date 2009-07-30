<?php

/* **************************************************************************************************** 
 *  File: /admin/admin_news.php
 *  Purpose: Displays a list of summaries from the Hotaru CMS Forums' RSS feed
 *  Notes: ---
 *  License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */


 /* ******************************************************************** 
 *  Function: admin_news
 *  Parameters: None
 *  Purpose: Displays an RSS feed with summaries from the last few posts in the Hotaru CMS forums
 *  Notes: Shown on the Admin front page.
 ********************************************************************** */
 
function admin_news() {
    global $hotaru, $plugin, $lang;
	    
    $cache = true;
    $cache_duration = 60; // minutes
    $max_items = 5;
    
    $feedurl = 'http://feeds2.feedburner.com/hotarucms';
    $feed = $hotaru->new_simplepie($feedurl, $cache, $cache_duration);
    $feed->init();
        
    $output = "";
    $item_count = 0;
	    
    if ($feed->data) { 
        foreach ($feed->get_items() as $item) {
                $output .= "<div>";
                $output .= "<a href='" . $item->get_permalink() . "'>" . $item->get_title() . "</a><br />";
                $output .= "<small>" . $lang["admin_news_posted_by"] . " ";
                foreach ($item->get_authors() as $author)  {
			$output .= $author->get_name(); 
		}
		$output .= " " . $lang["admin_news_on"] . " " . $item->get_date('j F Y');
		$output .= "</small><br />";
		$output .= substr(strip_tags($item->get_content()), 0, 300);
		$output .= "... ";
		$output .= "<small><a href='" . $item->get_permalink() . "' title='" . $item->get_title() . "'>[" . $lang["admin_news_read_more"] . "]</a>";
		$output .= "</small>";
		$output .= "</div><br />";

		$item_count++;
            	if($item_count >= $max_items) { break;}
        }
    }
	
    echo $output;
}

?>