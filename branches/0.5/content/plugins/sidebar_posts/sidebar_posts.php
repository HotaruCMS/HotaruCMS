<?php
 
/* ********** PLUGIN *********************************************************************************
 * name: Sidebar Posts
 * description: Adds links in the sidebar to the latest posts and top stories on the site.
 * version: 0.1
 * folder: sidebar_posts
 * prefix: sp
 * requires: sidebar 0.1, submit 0.1
 * hooks: install_plugin, hotaru_header
 *
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
 
return false; die(); // We don't want to just drop into the file.


/* ******************************************************************** 
 *  Function: sp_install_plugin
 *  Parameters: None
 *  Purpose: Adds default settings for Sidebar Posts plugin
 *  Notes: ---
 ********************************************************************** */
 
function sp_install_plugin() {
	global $db, $plugin;
	
	// Default settings
	$plugin->plugin_settings_update('sidebar_widgets', 'sidebar_posts_top', 'top');
	$plugin->plugin_settings_update('sidebar_widgets', 'sidebar_posts_latest', 'new');
		
}

function sp_hotaru_header() {
	// NOthing to do but this hook and function forces the file to be included during check_actions().
}


/* ******************************************************************** 
 *  Function: sidebar_widget_sidebar_posts
 *  Parameters: None
 *  Purpose: Displays the RSS feed.
 *  Notes: Uses Hotaru's built-in SimplePie library, but extra customization 
 *         to the feed is possible by inserting SimplePie calls before $feed->init();
 ********************************************************************** */

function sidebar_widget_sidebar_posts($type = 'top') {
	global $hotaru, $plugin, $post, $lang;
    
    	$plugin->include_language('sidebar_posts');
    	//$hotaru->title = $hotaru->get_page_name();
	
	// FILTER TO NEW POSTS OR TOP POSTS?
	if($type == 'new' && $hotaru->title != 'latest') { 
		$posts = $post->get_posts($post->filter(array('post_status = %s' => 'new'), 10));	// get latest stories
		$title = $lang['sidebar_posts_latest_posts'];
	} elseif($type == 'top' && $hotaru->title != 'top') {
		$posts = $post->get_posts($post->filter(array('post_status = %s' => 'top'), 10));	// get top stories
		$title = $lang['sidebar_posts_top_posts'];
	}
	
	if(isset($posts) && !empty($posts)) {
		
		$output = "<h2 class='sidebar_posts_title'>";
		$output .= "<a href='" . url(array('page'=>'rss', 'status'=>$type)) . "' title='" . $lang["sidebar_posts_icon_anchor_title"] . "'><img src='" . baseurl . "content/themes/" . theme . "images/rss_16.png'></a>&nbsp;"; // RSS icon
		$link = baseurl;
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
	if(isset($output)) { echo $output . "</ul>"; }
}
 	
?>