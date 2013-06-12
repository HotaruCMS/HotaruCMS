<?php
/**
 * Pagination functions
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
class Paginator
{
	protected $limit       = 0;    // start (limit X)
	protected $itemsPerPage = 10;   // range (limit Y)
	protected $offset       = 0;    // start of each "array_slice"
	protected $pg           = 1;    // page
	protected $totalItems   = 0;
	protected $totalPages   = 0;
	public $items           = array();
	
	/**
	 * Pagination with query and row count (better for large sets of data)
	 *
	 * @param string $query - this should be a *prepared* SQL statement
	 * @param int $total_items - total row count
	 * @param int $items_per_page
	 * @param string $cache_table - must provide a table, e.g. "posts" for caching to be used
	 * @return array|false
	 */
	public function pagination($h, $query = '', $total_items = 0, $items_per_page = 10, $cache_table = '')
	{
		if (!$query) { return false; }
		
		$this->totalItems = $total_items;
		$this->itemsPerPage = $items_per_page;
		
		// get page from url
		$this->pg = $h->cage->get->testInt('pg');
		if (!$this->pg) { $this->pg = 1; }
		
		$this->limit = ($this->itemsPerPage * ($this->pg - 1));  // e.g. page 1 will start at 0, page 2 will start at 10, etc.
		
		$query .= " LIMIT " . $this->limit . ", " . $this->itemsPerPage;
		
		if ($cache_table) { $h->smartCache('on', $cache_table, 60, $query); } // start using cache
		$this->items = $h->db->get_results($query);
		if ($cache_table) { $h->smartCache('off'); } // stop using cache
		
		return $this;
	}
	
	
	/**
	 * Pagination with full dataset (easier for small sets of data)
	 *
	 * @param array $data - array of results for paginating
	 * @param int $items_per_page
	 * @return object|false - object
	 */
	public function paginationFull($h, $data = array(), $items_per_page = 10)
	{
		if (!$data) { return false; }
		
		$this->totalItems = count($data);
		$this->itemsPerPage = $items_per_page;
		
		// get page from url
		$this->pg = $h->cage->get->testInt('pg');
		if (!$this->pg) { $this->pg = 1; }
		
		$this->offset = ($this->itemsPerPage * ($this->pg - 1));  // e.g. page 1 will start at 0, page 2 will start at 10, etc.
		$this->items = array_slice($data, $this->offset, $this->itemsPerPage);
		
		return $this;
	}
	
	
	public function pageBar($h) 
	{
		// NOTE: FRIENDLY URLS ARE NOT USED IN PAGINATION (I tried, but there's always *something* that screws up. Nick)
		if ($h->adminPage == true) { $head = 'admin_index.php?'; } else { $head = 'index.php?'; }
		
		// get full url from address bar
		$host = $h->cage->server->sanitizeTags('HTTP_HOST');
		$uri = $h->cage->server->sanitizeTags('REQUEST_URI');
		$path = "http://" . $host  . $uri;
		
		// if it doesn't contain $head, then it must be a friendly url 
		if ($path != SITEURL && !strrpos($path, $head)) {
			$path = $this->friendlyToStandardUrl($path, $head, $h);
		} 
		
		// add the head if we're on the top page (which doesn't have index.php attached) 
		if ($path == SITEURL) { $path = SITEURL . $head; }
		
		// But, for pagination, we can't just add pg=8 etc to the url because there's
		// quite likely a pg=X query variable already there! We need to strip that out:
		
		$query_args = parse_url($path, PHP_URL_QUERY);  // get all query vars
		
		if ($query_args) {
			$path = str_replace($query_args, '', $path);  // strip them from original $path
			parse_str($query_args, $parsed_query_args); // split query vars into key->value pairs
			unset($parsed_query_args['pg']);   // we'll be replacing pg in the links
			$path = $path . http_build_query($parsed_query_args); // rebuild url without pg parameter
		}
		
		$currentPage = $this->pg;
		
		$str = "";
		
		$before = 4;
		$after = 3;
		
		$this->totalPages = $this->countTotalPages();
		
		//write statement that handles the previous and next phases
		//if it is not the first page then write previous to the screen
		if (!$this->isFirstPage()) {
			$previousPage = $currentPage - 1;
			$link = $path . '&pg=' . $previousPage;
			$link = str_replace('?&', '?', $link); // we don't want an ampersand directly after a question mark
			$str .= "<a class='pagi_previous' href='" . $link . "' title='" . $h->lang('pagination_previous') . "'>&laquo; " . $h->lang('pagination_previous') . "</a> \n";
		}
		
		// NOT FIRST PAGE
		if (!$this->isFirstPage() && !($currentPage <= ($before + 1))) {
			if ($currentPage != 1) {
				$link = $path . '&pg=1';
				$link = str_replace('?&', '?', $link); // we don't want an ampersand directly after a question mark
				$str .= "<a class='pagi_first' href='" . $link . "'  title='" . $h->lang('pagination_first') . "'>1</a> \n";
				if ($currentPage > ($before+1)) {
					$str .= " <span class='dots'>...</span> \n";
				}
			}
		}
		
		for ($i = $currentPage - $before; $i <= $currentPage + $after; $i++) {
			//if i is less than one then continue to next iteration        
			if ($i < 1) {
				continue;
			}
			
			if ($i > $this->totalPages) {
				break;
			}
			
			if ($i == $currentPage) {
				$str .= "<span class='pagi_current'>$i</span>\n";
			}
			else {
				$link = $path . '&pg=' . $i;
				$link = str_replace('?&', '?', $link); // we don't want an ampersand directly after a question mark
				$str .= "<a class='pagi_page pagination' href='" . $link . "'>$i</a>\n";
			}
			if ($i != $currentPage + $after && $i != $this->totalPages) { $str .= ' '; }
		} //end for
		
		if (!$this->isLastPage() && ($currentPage <= ($this->totalPages - $after))) {
			if ($currentPage != $this->totalPages && $currentPage != $this->totalPages -1 && $currentPage != $this->totalPages - $after)
			{
				if ($currentPage < ($this->totalPages - ($after + 1))) { $str .= " <span class='pagi_dots'>...</span> \n"; }
				$link = $path . '&pg=' . $this->totalPages;
				$link = str_replace('?&', '?', $link); // we don't want an ampersand directly after a question mark
				$str .= "<a class='pagi_last' href='" . $link . "'  title='" . $h->lang('pagination_last') . "'>".$this->totalPages."</a> \n";
			}
		}
		
		// NOT LAST PAGE
		if (!$this->isLastPage()) {
			$nextPage = $currentPage + 1;
			$link = $path . '&pg=' . $nextPage;
			$link = str_replace('?&', '?', $link); // we don't want an ampersand directly after a question mark
			$str .= "<a class='pagi_next' href='" . $link . "' title='" . $h->lang('pagination_next') . "'>" . $h->lang('pagination_next') . " &raquo;</a> \n";
		}
	
		// Wrap in a div
		$pagination = "<div id='pagination'>\n";
		$pagination .= $str;
		$pagination .= "</div>\n";
		
		return $pagination;
	}
	
	
	/**
	 * Converts a friendly url into a standard one
	 *
	 * @param string $url
	 * @param string $head - "index.php?" or "admin_index.php?"
	 * @param object $h
	 */
	public function friendlyToStandardUrl($url, $head, $h) 
	{
		// strip off SITEURL and trailing slash
		$url = str_replace(SITEURL, '', $url);
		$url = rtrim($url, '/');
		
		// start the standard url
		$standard_url = SITEURL . $head;
		
		// parts will hold the query vars
		$parts = array();
		$parts = explode('/', $url);
		
		// if odd number of query vars, the first is the page
		if (count($parts) % 2 == 1) {
			$page = array_shift($parts);
			$standard_url .= 'page=' . $page;
			if (!empty($parts)) { $standard_url .= '&'; }
		}
		
		// if query vars still in array, add them
		while (!empty($parts)) {
			$key = array_shift($parts);
			$value = array_shift($parts);
			$standard_url .= $key . '=' . $value;
			if (!empty($parts)) { $standard_url .= '&'; }
		}
		
		return $standard_url;
	}
	
	
	/**
	 * Is this the first page?
	 */
	public function isFirstPage() {
		return ($this->pg <= 1);
	}
	
	
	/**
	 * Is this the last page?
	 */
	public function isLastPage() {
		return ($this->pg >= $this->totalPages);
	}
	
	
	/**
	 * Total pages
	 */
	public function countTotalPages() {
		return ceil($this->totalItems / $this->itemsPerPage);
	}
}
?>
