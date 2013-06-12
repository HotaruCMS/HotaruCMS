<?php
/**
 * Database Class
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
 
class Database extends ezSQL_mysql
{
	protected $select   = array();
	protected $table         = '';
	protected $where         = array();
	protected $orderby       = '';
	protected $limit         = '';
	protected $prepare_array = array();
	protected $cache         = true;
	protected $query_only    = false;
	
	/**
	 * Access modifier to set protected properties
	 */
	public function __set($var, $val)
	{
		$this->$var = $val;
	}
    
    
	/**
	 * Access modifier to get protected properties
	 * The & is necessary (http://bugs.php.net/bug.php?id=39449)
	 */
	public function &__get($var)
	{
		return $this->$var;
	}
	
	
	/**
	 * Fill Database Object
	 *
	 * @param array $select - associative array of select terms
	 * @param string $table - full table name including prefix
	 * @param array $where - associative array of where terms, e.g. array('id = %d' = 5, 'name = %s' = 'tony')
	 * @param string $orderby - e.g. "post_date DESC"
	 * @param string $limit - e.g. "10" or "5, 10"
	 * @param bool $cache - cache results
	 * @param bool $query_only - return just the query, not the results?
	 */
	public function fillObject($select = array(), $table = '', $where = array(), $orderby = '', $limit = '', $cache, $query_only)
	{
		if ($select)  { $this->select = $select; }
		if ($table)   { $this->table = $table; }
		if ($where)   { $this->where = $where; }
		if ($orderby) { $this->orderby = $orderby; }
		if ($limit)   { $this->limit = $limit; }
		if ($cache)   { $this->cache = true; } else { $this->cache = false; }
		if ($query_only)   { $this->query_only = true; } else { $this->query_only = false; }
	}
	
	/**
	 * Empty Database Object
	 */
	public function emptyObject()
	{
		$this->select   = array();
		$this->table         = '';
		$this->where         = array();
		$this->orderby       = '';
		$this->limit         = '';
		$this->prepare_array = array();
		$this->cache         = true;
		$this->query_only    = false;
	}
	
	
	/**
	 * Build an SQL "SELECT" query
	 *
	 * @param array $select - associative array of select terms
	 * @param string $table - abbreviated table name, e.g. posts, users, comments
	 * @param array $where - associative array of where terms, e.g. array('id = %d' = 5, 'name = %s' = 'tony')
	 * @param string $orderby - e.g. post_date DESC
	 * @param string $limit - "X, Y"
	 * @param bool $cache - cache results
	 * @param bool $query_only - return just the query, not the results?
	 * @return array|false
	 */
	public function select($h, $select = array(), $table = '', $where = array(), $orderby = '', $limit = '', $cache = true, $query_only = false)
	{
		// for flexibility, we want to use object properties:
		$this->fillObject($select, $table, $where, $orderby, $limit, $cache, $query_only);
		
		// plugin hook
		$h->pluginHook('database_select');
		
		$this->prepare_array = array();
		$this->prepare_array[0] = "temp";    // placeholder to be later filled with the SQL query.
		
		// set SELECT:
		$select = ($this->select) ? $this->buildSelect() : '';
		
		// set TABLE:
		$table = ($this->table) ? DB_PREFIX . $this->table : TABLE_POSTS; // defaults to TABLE_POSTS
		
		// set WHERE
		$where = ($this->where) ? $this->buildWhere() : '';
		
		// set ORDER BY
		$orderby = ($this->orderby) ? ' ORDER BY ' . $this->orderby : '';
		
		// set LIMIT
		$limit = ($this->limit) ? ' LIMIT ' . $this->limit : '';
		
		// Build query:
		$sql = "SELECT " . $select . " FROM " . $table . $where . $orderby . $limit;
		
		$this->prepare_array[0] = $sql;
		
		if ($this->query_only) { return $this->prepare_array; }
		
		/*	Example:
			$this->prepare_array[0] is "SELECT user_id FROM hotaru_users WHERE user_id = %d"
			$this->prepare_array[1] is "5", where 5 fills the %d
		*/
		
		// get the data and return it
		return $this->getData($h, $this->table);
	}
	
	
	/**
	 * Build the SELECT string
	 *
	 * @return string
	 */
	public function buildSelect()
	{
		if (!$this->select) { return ''; }

		$select = ""; // the new select string we make from the $this->select array

		foreach ($this->select as $key => $value) {
			// e.g.
			// $select[0] = 'post_id';
			// $select[1] = array('blah %s blah'=>'value for %s');

			// Push the values of %s and %d into the prepare_array
			if (is_array($value)) {
				foreach ($value as $k => $v) {
					$select .= $k . ', ';
					array_push($this->prepare_array, $v);
				}
			} else {
				// otherwise add the single value to the select string
				$select .= $value . ', ';
			}
	
		}
		$select = rstrtrim($select, ", "); // strip off trailing AND
		
		return $select;
	}


	/**
	 * Build the WHERE string
	 *
	 * @return string
	 */
	public function buildWhere()
	{
		if (!$this->where) { return ''; }
		
		$filter = " WHERE ";
		foreach ($this->where as $key => $value) {
			$filter .= $key . " AND ";    // e.g. " post_tags LIKE %s "
			
			// Push the values of %s and %d into the prepare_array
			
			// sometimes the filter might contain multiple values, eg.
			// WHERE post_status = %s OR post_status = %s. In that case,
			// the values are stored in an array, e.g. array('top', 'new').
			if (is_array($value)) {
				foreach ($value as $v) {
					array_push($this->prepare_array, $v);
				}
			} else {
				// otherwise, push the single value into $this->prepare_array:
				array_push($this->prepare_array, $value);
			}
	
		}
		$filter = rstrtrim($filter, " AND "); // strip off trailing AND
		
		return $filter;
	}
	
	
	/**
	 * Gets data from the database
	 *
	 * @param string $table - table name without a prefix.
	 * @param array $prepare array - optional if calling directly
	 * @return array|false - object array of data
	 */
	public function getData($h, $table = 'posts', $prepare_array = array())
	{
		if ($prepare_array) {
			$this->prepare_array = $prepare_array;
		}
		
		if (!$this->prepare_array) { return false; }
		
		if (empty($this->prepare_array[1])) {
			// there aren't any %d or %s parameters to fill in, so we'll skip the prepare function
			if ($this->cache) { $h->smartCache('on', $table, 60, $this->prepare_array[0]); } // start using cache
			$data = $h->db->get_results($this->prepare_array[0]); // ignoring the prepare function.
		} else {
			$query = $h->db->prepare($this->prepare_array);
			if ($this->cache) { $h->smartCache('on', $table, 60, $query); } // start using cache
			$data = $h->db->get_results($query);
		}
		
		$h->smartCache('off'); // stop using cache
		
		$this->emptyObject(); // reset the object or we'll confuse subsequent DB calls.
		
		if ($data) { return $data; } else { return false; }
	}
}

?>
