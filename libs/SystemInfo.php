<?php
/**
 * Functions for checking system info for Hotaru installation
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
 * @author    shibuya246 <blog@shibuya246.com>
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
class SystemInfo
{	
	
	/**
	 * Calls external site to provide system info feedback report
	 *
	 * @return bool true
	 */
	public function hotaru_feedback($h)
	{
		$report = $h->generateReport("object");

		$query_vals = array(
		    'api_key' => '',
		    'format' => 'json',
		    'method' => 'hotaru.systemFeedback.add',
		    'args' => serialize($report)
		);

		$info = $this->sendApiRequest($h, $query_vals, 'http://api.hotarucms.org/index.php?page=api');
		
		return true;
	}


	/**
	 * Calls external site to get latest available hotaru version number
	 *
	 * @return string versionnumber
	 */
	public function hotaru_version($h)
	{
		$query_vals = array(
		    'api_key' => '',
		    'format' => 'json',
		    'method' => 'hotaru.version.get'
		);

		$info = $this->sendApiRequest($h, $query_vals, 'http://hotaruplugins.com/index.php?page=api');

		// save the updated version number to the local db so we can display it on the admin panel until it gets updated.
		if (isset($info['version'])) {
		    $sql = "SELECT miscdata_id FROM " . TABLE_MISCDATA ." WHERE miscdata_key = %s";
		    $query = $h->db->get_row($h->db->prepare($sql, 'hotaru_latest_version'));
		    
		    if ($query) {
			// update existing db record
			$sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s WHERE miscdata_key = %s";
			$h->db->query($h->db->prepare($sql, $info['version'], 'hotaru_latest_version'));
		    } else {			
			$sql = "INSERT INTO " . TABLE_MISCDATA . " (miscdata_value, miscdata_key) VALUES (%s, %s)";
			$h->db->query($h->db->prepare($sql, $info['version'], 'hotaru_latest_version'));	
		    }	
		    return $info['version'];
		}

		return 0;
		
	}

	/**
	 * Calls external site to get latest available hotaru version number
	 *
	 * @return bool true
	 */
	public function plugin_version_getAll($h)
	{
		$query_vals = array(
		    'api_key' => '',
		    'format' => 'json',
		    'method' => 'hotaru.plugin.version.getAll'
		);

		 $info = $this->sendApiRequest($h, $query_vals, 'http://hotaruplugins.com/index.php?page=api');

		 if ($info) {
		    // save the updated version numbers to the local db so we can display it on the plugin management panel
		    $sql = "SELECT plugin_id, plugin_name, plugin_latestversion FROM " . TABLE_PLUGINS;
		    $plugins = $h->db->get_results($h->db->prepare($sql));

		    if ($plugins) {
			foreach ($plugins as $plugin) {
			    if (array_key_exists($plugin->plugin_name, $info)) {
				$sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_latestversion = %s WHERE (plugin_id = %d)";
				$h->db->query($h->db->prepare($sql, $info[$plugin->plugin_name], $plugin->plugin_id));
				//print $plugin->plugin_name . ' ' . $info[$plugin->plugin_name] . '<br/>';
			    }
			}
		    }
		 }

		return true;
	}


	/**
	 *
	 * @param <type> $search
	 */
	public function pluginSearch($h, $search)
	{
		$query_vals = array(
		    'api_key' => '',
		    'format' => 'json',
		    'method' => 'hotaru.plugin.search',
		    'args' => $search
		);

		 $plugins = $this->sendApiRequest($h, $query_vals, 'http://hotaruplugins.com/index.php?page=api');

		 return $plugins;
	}

	/**
	 *
	 */
	public function pluginTagCloud($h, $number = 20)
	{
		$query_vals = array(
		    'api_key' => '',
		    'format' => 'json',
		    'method' => 'hotaru.plugin.tagcloud',
		    'args' => $number
		);

		 $result = $this->sendApiRequest($h, $query_vals, 'http://hotaruplugins.com/index.php?page=api');

		 return $result;
	}


	/**
	 *
	 * @param <type> $query_vals
	 * @param <type> $url
	 * @return <type>
	 */
	public function sendApiRequest($h, $query_vals, $url)
	{
		// Generate the POST string
		$ret = '';
		foreach($query_vals as $key => $value) {
		    $ret .= $key.'='.urlencode($value).'&';
		}

		$ret = rtrim($ret, '&');

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $ret);
		$response = curl_exec($ch);
		curl_close ($ch);

		return json_decode($response, true);
	}
}
?>
