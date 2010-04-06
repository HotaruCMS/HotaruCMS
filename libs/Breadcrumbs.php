<?php
/**
 * Breadcrumb functions
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
class Breadcrumbs
{
	/**
	 * Build breadcrumbs
	 */
	public function buildBreadcrumbs($h)
	{
		$output = '';
		$output .= "<a href='" . BASEURL . "'>" . $h->lang['main_theme_breadcrumbs_home'] . "</a>\n"; 
		
		// Admin only:
		if ($h->isAdmin) {
			$output .= " &raquo; <a href='" . $h->url(array(), 'admin') . "'>";
			$output .= $h->lang['admin_theme_main_admin_cp'] . "</a>\n";
		}
		
		// plugin hook:
		$crumbs = $h->pluginHook('breadcrumbs');
		if ($crumbs) {
			$crumbs = array_reverse($crumbs); // so the last one gets used.
			foreach ($crumbs as $key => $value) {
				$output .= " &raquo; " . $value;
				return $output; // we only want the first result so return now.
			}
		} 
		
		// in case of no plugins:
		$output .= " &raquo; " . $h->pageTitle;
		return $output;
	}
	
	
	/**
	 * prepares the RSS breadcrumbs link
	 *
	 * @param string $status - post status, e.g. new, top, etc.
	 * @param array $vars - array of key -> value pairs
	 * @return string
	 */    
	public function rssBreadcrumbsLink($h, $status = '', $vars)
	{
		if ($status) {
			$url_array = array('page'=>'rss', 'status'=>$status);
		} else {
			$url_array = array('page'=>'rss'); // defaults to all
		}
		
		foreach ($vars as $k => $v) {
			$url_array[$k] = $v;
		}
		$rss = "<a href='" . $h->url($url_array) . "'>";
		$rss .= " <img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png' alt='" . $h->pageTitle . " RSS' /></a>";
		return $rss;
	}
}
?>
