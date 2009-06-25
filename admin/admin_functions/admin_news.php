<?php

 /* ******************************************************************** 
 *  Function: admin_news
 *  Parameters: None
 *  Purpose: Displays an RSS feed with summaries from the last few posts in the Hotaru CMS forums
 *  Notes: Shown on the Admin front page.
 ********************************************************************** */
 
function admin_news() {
    global $hotaru, $plugin;
	    
    $cache = true;
    $cache_duration = 20; // minutes
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
                $output .= "<small>Posted by ";
                foreach ($item->get_authors() as $author)  {
			$output .= $author->get_name(); 
		}
		$output .= " on " . $item->get_date('j F Y');
		$output .= "</small><br />";
		$output .= substr(strip_tags($item->get_description()), 0, 300);
		$output .= "... ";
		$output .= "<small><a href='" . $item->get_permalink() . "' title='" . $item->get_title() . "'>[Read More]</a>";
		$output .= "</small>";
		$output .= "</div><br />";

		$item_count++;
            	if($item_count >= $max_items) { break;}
        }
    }
	
    echo $output;
}

?>