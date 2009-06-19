<?php
/**
 * name: RSS Sidebar
 * description: Adds links in the sidebar to the latest posts from a specified RSS feed.
 * version: 0.1
 * folder: rss_sidebar
 * hooks: rss_sidebar, admin_sidebar_plugin_settings
 *
 * Usage: Add <?php $plugin->check_actions('rss_sidebar'); ?> to your theme, wherever you want to show the links.
 */
	
function rss_sidebar() {
    global $hotaru;
	    
    /* *********** EDIT THESE SETTINGS ****************************** */
    $feedurl = "http://hotarucms.org/external.php?type=rss2";
    $cache = true;
    $cache_duration = 10; // minutes
    $max_items = 10;
    /* ************************************************************** */
    
    $feed = $hotaru->new_simplepie($feedurl, $cache, $cache_duration);
    $feed->init();
        
    $output = "";
    $item_count = 0;
	    
    if ($feed->data) { 
        foreach ($feed->get_items() as $item) {
                $output .= '';
                $output .= '<li><a href="' . $item->get_permalink() . '">' . $item->get_title() . '</a></li>';
            $item_count++;
            if($item_count >= $max_items) { break;}
        }
    }
	
    echo $output;
}

function admin_sidebar_plugin_settings() {
	echo "<li>RSS Sidebar</li>";
}
 	
?>