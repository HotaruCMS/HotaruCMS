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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
class Feeds {

	 /**
	 * Create an RSS Feed
	 *
	 * @param string $title - feed title
	 * @param string $link - url feed title should point to
	 * @param string $description - feed description
	 * @param array $items - $items[0] = array('title'=>TITLE, 'link'=>URL, 'date'=>TIMESTAMP, 'description'=>DESCRIPTION)
	 * @param string $content_type e.g. 'application/xml' or 'text/plain'
	 */
	public function rss($h, $title = '', $link = '', $description = '', $items = array(), $content_type = 'application/xml') {
		require_once(EXTENSIONS.'RSSWriterClass/rsswriter.php');
		
		$feed = new RSS($h->url(array('page'=>'rss')));
		$feed->title = stripslashes(html_entity_decode(urldecode($title), ENT_QUOTES, 'UTF-8'));
		$feed->link = html_entity_decode($link, ENT_QUOTES, 'UTF-8');
		$feed->description = $description;
		
		if ($items) {
			$feed->addItem($items);
		}
				
		echo $feed->out($content_type);
	}

				
	/**
	 * Includes the SimplePie RSS file and sets the cache
	 *
	 * @param string $feed
	 * @param bool $cache
	 * @param int $cache_duration
	 *
	 * @return object|false $sp
	 */
	public function newSimplePie($feed='', $cache=RSS_CACHE, $cache_duration=RSS_CACHE_DURATION) {
		include_once(EXTENSIONS."SimplePie/simplepie.inc");
		
		if ($feed != '') {
			$sp = new SimplePie();
			$sp->set_feed_url($feed);
			$sp->set_cache_location(CACHE."rss_cache/");
			$sp->set_cache_duration($cache_duration);
			$sp->enable_cache(($cache === 'true'));
			$sp->handle_content_type();
			return $sp;
		}
		return FALSE;
	}
	
	 /**
	 * Display Hotaru forums feed on Admin front page
	 *
	 * @param int $max_items
	 * @param int $items_with_content
	 * @param int $max_chars
	 */
	public function adminNews($lang, $max_items = 10, $items_with_content = 3, $max_chars = 300) {
		$feedurl = 'http://feeds2.feedburner.com/hotarucms';
		$feed = $this->newSimplePie($feedurl);
		$feed->init();
		
		$output = "";
		$item_count = 0;
		
		if ($feed->data) { 
			foreach ($feed->get_items() as $item) {
				$output .= "<div class='admin_news'>";
				
				// Title
				$output .= "<a href='".$item->get_permalink()."'>".sanitize($item->get_title(), 'tags')."</a><br />";
				
				if ($item_count < $items_with_content) {
					// Posted by
					$output .= "<small>".$lang["admin_news_posted_by"]." ";
					
					foreach ($item->get_authors() as $author) {
					    $output .= $author->get_name(); 
					}
					
					// Date
					$output .= " ".$lang["admin_news_on"]." ".$item->get_date('j F Y')."</small><br />";
					
					// Content
					$output .= truncate(sanitize($item->get_content(), 'tags'), $max_chars, TRUE);
					
					// Read more
					$output .= "<small><a href='".$item->get_permalink()."' title='".sanitize($item->get_title(), 'tags')."'>[".$lang["admin_news_read_more"]."]</a></small>";
				}
				
				$output .= "</div>";
				if ($item_count < $items_with_content) {
					$output .="<br />";
				}
				
				if ($item_count == ($items_with_content - 1)) {
					$output .= "<h3>".$lang["admin_news_more_threads"]."</h3>";
				}

				$item_count++;
				if ($item_count >= $max_items) {
					break;
			}
		}
		}
		
		echo $output;
	}

}

?>
