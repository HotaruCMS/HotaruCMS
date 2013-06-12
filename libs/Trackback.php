<?php
/**
 * Trackback functions
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
class Trackback
{
	/**
	 * Prepares and calls functions to send a trackback
	 */
	public function sendTrackback($h)
	{
		// Scan content for trackback urls
		$tb_array = array();
		
		$trackback = $this->detectTrackback($h);
		
		if (!$trackback) { return false; } // No trackback url found
		
		// Clean up the title and description...
		$title = htmlspecialchars(strip_tags($h->post->title));
		$title = (strlen($title) > 150) ? substr($title, 0, 150) . '...' : $title;
		$excerpt = strip_tags($h->post->content);
		$excerpt = (strlen($excerpt) > 200) ? substr($excerpt, 0, 200) . '...' : $excerpt;
		
		// we don't want friendly urls in case the title or category is edited after submission, thus
		// changing and therefore breaking the trackback link posted on other sites. So...
		$url = SITEURL . 'index.php?page=' . $h->post->id; 
		
		if ($this->ping($h, $trackback, $url, $title, $excerpt)) {
			//echo "Trackback sent successfully...";
			return true;
		} else {
			//echo "Error sending trackback....";
			return false;
		}
	}
	
	
	
	/**
	 * Scan content of source url for a trackback url
	 *
	 * @return str - trackback url
	 *
	 * Adapted from Pligg.com and SocialWebCMS.com
	 */
	private function detectTrackback($h)
	{
		// Fetch the content of the original url...
		$url = $h->post->origUrl;
		$content = ($url != 'http://' && $url != '') ? hotaru_http_request($url) : '';
		$trackback = '';

		if (preg_match('/trackback:ping="([^"]+)"/i', $content, $matches) ||
				preg_match('/trackback:ping +rdf:resource="([^>]+)"/i', $content, $matches) ||
				preg_match('/<trackback:ping>([^<>]+)/i', $content, $matches)) {
			$trackback = trim($matches[1]);
		} elseif (preg_match('/<a[^>]+rel="trackback"[^>]*>/i', $content, $matches)) {
			if (preg_match('/href="([^"]+)"/i', $matches[0], $matches2)) {
				$trackback = trim($matches2[1]);
			}
		} elseif (preg_match('/<a[^>]+href=[^>]+>trackback<\/a>/i', $content, $matches)) {
			if (preg_match('/href="([^"]+)"/i', $matches[0], $matches2)) {
				$trackback = trim($matches2[1]);
			}
		} elseif (preg_match('/http:([^ ]+)trackback.php([^<|^ ]+)/i', $content, $matches)) {
			$trackback = trim($matches[1]);
		} elseif (preg_match('/trackback:ping="([^"]+)"/', $content, $matches)) {
			$trackback = trim($matches[1]);
		}

		return $trackback;
	}
	
	
	
	/**
	 * Send a trackback to the source url
	 *
	 * @param str $trackback - url of source
	 * @param str $url - of Hotaru post
	 * @param str $title
	 * @param str $excerpt
	 * @link http://phptrackback.sourceforge.net/docs/
	 */
	public function ping($h, $trackback, $url, $title = "", $excerpt = "")
	{
		$response = "";
		$reason = ""; 
		
		// Set default values
		if (empty($title)) {
			$title = SITE_NAME;
		} 
		
		if (empty($excerpt)) {
			// If no excerpt show "This article has been featured on Site Name".
			$excerpt = $h->lang('submit_trackback_excerpt') . " " . SITE_NAME;
		} 
		
		// Parse the target
		$target = parse_url($trackback);
		
		if ((isset($target["query"])) && ($target["query"] != "")) {
			$target["query"] = "?" . $target["query"];
		} else {
			$target["query"] = "";
		} 
		
		if ((isset($target["port"]) && !is_numeric($target["port"])) || (!isset($target["port"]))) {
			$target["port"] = 80;
		} 
		// Open the socket
		$tb_sock = fsockopen($target["host"], $target["port"]); 
		// Something didn't work out, return
		if (!is_resource($tb_sock)) {
			return '$post->ping: Tring to send a trackback but can\'t connect to: ' . $tb_sock . '.';
			exit;
		} 
		
		// Put together the things we want to send
		$tb_send = "url=" . rawurlencode($url) . "&title=" . rawurlencode($title) . "&blog_name=" . rawurlencode(SITE_NAME) . "&excerpt=" . rawurlencode($excerpt); 
		 
		// Send the trackback
		fputs($tb_sock, "POST " . $target["path"] . $target["query"] . " HTTP/1.1\r\n");
		fputs($tb_sock, "Host: " . $target["host"] . "\r\n");
		fputs($tb_sock, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($tb_sock, "Content-length: " . strlen($tb_send) . "\r\n");
		fputs($tb_sock, "Connection: close\r\n\r\n");
		fputs($tb_sock, $tb_send); 
		// Gather result
		while (!feof($tb_sock)) {
			$response .= fgets($tb_sock, 128);
		} 
		
		// Close socket
		fclose($tb_sock); 
		// Did the trackback ping work
		strpos($response, '<error>0</error>') ? $return = true : $return = false;
		// send result
		
		return $return;
	} 
}
?>
