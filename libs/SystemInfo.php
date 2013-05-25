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
	public function hotaru_feedback($h, $format = 'json')
	{
		$report = $h->generateReport("object");
		
		$query_vals = array(
			'api_key' => '',
			'format' => $format,
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
                 print_r($info);
                 die();
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
                        
                        return true;
		    }                                        
		 }

		return false;
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


	/**
	 * Get system data
	 *
	 * @param string $type 'log' or 'object'
	 * @return object
	 */
	public function getSystemData($h)
	{
		// essentials:
		
		$report['hotaru_site_name'] = SITE_NAME;
		$report['hotaru_SITEURL'] = SITEURL;
		
		$report['php_version'] = phpversion();
		$report['mysql_version'] = $h->db->get_var("SELECT VERSION() AS VE");
		$report['hotaru_version'] = $h->version;
		$report['php_extensions'] = get_loaded_extensions();
		
		$sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
		$report['hotaru_version_db'] = $h->db->get_var($h->db->prepare($sql, 'hotaru_version'));
		
		// default permissions
		
		$sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
		$report['hotaru_permissions'] = $h->db->get_var($h->db->prepare($sql, 'permissions'));
		
		// default user settings
		
		$sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
		$report['hotaru_user_settings'] = $h->db->get_var($h->db->prepare($sql, 'user_settings'));
		
		// plugins: folder, enabled, version, order
		
		$sql = "SELECT plugin_folder, plugin_enabled, plugin_version, plugin_order, plugin_latestversion FROM " . TABLE_PLUGINS . " ORDER BY plugin_order";
		$plugins = $h->db->get_results($h->db->prepare($sql));
		if ($plugins) {
			foreach ($plugins as $plugin) {
				$report['hotaru_plugins'][$plugin->plugin_folder]['enabled'] = $plugin->plugin_enabled;
				$report['hotaru_plugins'][$plugin->plugin_folder]['version'] = $plugin->plugin_version;
				$report['hotaru_plugins'][$plugin->plugin_folder]['order'] = $plugin->plugin_order;
				$report['hotaru_plugins'][$plugin->plugin_folder]['plugin_latestversion'] = $plugin->plugin_latestversion;
			}
		}
		
		// plugin hooks: id, folder, hook name
		
		$sql = "SELECT phook_id, plugin_folder, plugin_hook FROM " . TABLE_PLUGINHOOKS;
		$plugins = $h->db->get_results($h->db->prepare($sql));
		if ($plugins) {
			foreach ($plugins as $plugin) {
				$report['hotaru_plugin_hooks'][$plugin->phook_id]['folder'] = $plugin->plugin_folder;
				$report['hotaru_plugin_hooks'][$plugin->phook_id]['hook'] = $plugin->plugin_hook;
			}
		}
		
		// plugin settings: folder, setting (can't use value because might include passwords)
		
		$sql = "SELECT plugin_folder, plugin_setting, plugin_value FROM " . TABLE_PLUGINSETTINGS;
		$plugins = $h->db->get_results($h->db->prepare($sql));
		if ($plugins) {
			foreach ($plugins as $plugin) {
				if (is_serialized($plugin->plugin_value)) { $plugin->plugin_value = unserialize($plugin->plugin_value); }
				$report['hotaru_plugin_settings'][$plugin->plugin_folder][$plugin->plugin_setting] = $this->applyMaskToArrays($h, $plugin->plugin_value);
			}
		}
		
		// Settings: Name, value (excluding SMTP PASSWORD)
		
		$sql = "SELECT settings_name, settings_value FROM " . TABLE_SETTINGS;
		$settings = $h->db->get_results($h->db->prepare($sql));
		if ($settings) {
			foreach ($settings as $setting) {
				// mask sensitive data
				switch ($setting->settings_name) {
					case 'SITE_EMAIL':
					case 'SMTP_HOST':
					case 'SMTP_PORT':
					case 'SMTP_USERNAME':
					case 'SMTP_PASSWORD':
						$setting->settings_value = preg_replace("/[a-zA-Z0-9]/", "*", $setting->settings_value);
						break;
				}
				$report['settings'][$setting->settings_name] = $setting->settings_value;
			}
		}
		
		// Widgets: plugin, function, args
		
		$sql = "SELECT widget_plugin, widget_function, widget_args FROM " . TABLE_WIDGETS;
		$widgets = $h->db->get_results($h->db->prepare($sql));
		if ($widgets) {
			foreach ($widgets as $widget) {
				$report['hotaru_widgets'][$widget->widget_plugin]['function'] = $widget->widget_function;
				$report['hotaru_widgets'][$widget->widget_plugin]['args'] = $widget->widget_args;
			}
		}
		
		// Counts for all tables
		
		foreach ( $h->db->get_col("SHOW TABLES",0) as $table_name )
		{
			$report['hotaru_table_count'][$table_name] = $h->db->get_var("SELECT COUNT(*) FROM " . $table_name);
		}
		
		return $report;
	}
	
	
	/**
	 * Recurse through arrays, applying * mask to all values, but not keys
	 *
	 * @param array $array
	 * @return array
	 */
	 public function applyMaskToArrays($h, $array)
	 {
		//echo "<pre>"; print_r($array); echo "</pre>"; exit;
		if (!is_array($array) && !is_object($array)) { return false; }
		
		foreach ($array as $key => $value) {
			if (is_array($value) || is_object($value)) {
				$array[$key] = $this->applyMaskToArrays($h, $value);
			} else {
				$array[$key] = preg_replace("/[a-zA-Z0-9]/", "*", $value);
			}
		}
		return $array;
	}
	
        
        /**
         * 
         * @param type $h
         * @param type $type
         */
        public function miscdata($h, $key = '', $cache = 'true')
        {
                $sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA ." WHERE miscdata_key = %s";
		$query = $h->db->prepare($sql, $key);                
                
                if ($cache)
                    $h->smartCache('on', 'miscdata_value_' . $key, 60, $query); // start using cache
                
                $value = $h->db->get_var($query);
			
                if ($cache)
                    $h->smartCache('off'); // stop using cache

		return $value;
        }
        
        
	/**
	 * Convert report object to text for logging to file
	 *
	 * @param object $report
	 */
	public function logSystemReport($h, $report = NULL)
	{
		$output = "\n\n";
		
		$output .= "Name: " . $report['hotaru_site_name'] . "\n";
		$output .= "URL: " . $report['hotaru_SITEURL'] . "\n";
		$output .= "Hotaru version: " . $report['hotaru_version'] . "\n";
		$output .= "Hotaru version in database: " . $report['hotaru_version_db'] . "\n";
		$output .= "PHP version: " . $report['php_version'] . "\n";
		$output .= "MySQL version: " . $report['mysql_version'] . "\n";
		$output .= "PHP extensions: " . implode(', ', $report['php_extensions']) . "\n";
		
		$output .= "\n";
		
		$output .= "Default site permissions: \n";
		$perms = unserialize($report['hotaru_permissions']);
		unset($perms['options']); // don't need to display these
		foreach ($perms as $key => $value) {
			$output .= $key . " => (";
			foreach ($value as $k => $v) {
				$output .= $k . ": " . $v . ", ";
			}
			$output = rtrim($output, ", ");
			$output .= ")\n";
		}
		
		$output .= "\n";
		
		$output .= "Default user settings: \n";
		$user_settings = unserialize($report['hotaru_user_settings']);
		foreach ($user_settings as $key => $value) {
			$output .= $key . " => " . $value . "\n";
		}
		
		$output .= "\n";
		
		$output .= "Plugins: \n";
		if (isset($report['hotaru_plugins'])) {
			foreach ($report['hotaru_plugins'] as $key => $value) {
				$output .= $value['order'] . ". " . $key . " v." . $value['version'] . " ";
				if ($value['enabled']) { $output .= "[enabled] \n"; } else { $output .= "[disabled] \n"; }				
			}
		}
		
		$output .= "\n";
		
		$output .= "Plugin Hooks: \n";
		if (isset($report['hotaru_plugin_hooks'])) {
			foreach ($report['hotaru_plugin_hooks'] as $key => $value) {
				$output .= $key . ". " . $value['folder'] . " => " . $value['hook'] . " \n";
			}
		}
		
		$output .= "\n";
		
		$output .= "Plugin Settings: \n";
		if (isset($report['hotaru_plugin_settings'])) {
			foreach ($report['hotaru_plugin_settings'] as $key => $value) {
				foreach ($value as $k => $v) {
					if (!is_array($v)) {
						$output .= "\nPlugin settings for " . $key . ":\n...." . $k . " = " . $v . " \n";
					} else {
						$output .= "\nPlugin settings for " . $key . ":\n";
						$output = $this->outputArrays($h, $v, $output);
					}
				}
			}
		}
		
		$output .= "\n";
		
		$output .= "Hotaru Settings: \n";
		if (isset($report['settings'])) {
			foreach ($report['settings'] as $key => $value) {
				$output .= $key . " => " . $value . " \n";
			}
		}
		
		$output .= "\n";
		
		$output .= "Widgets: \n";
		if (isset($report['hotaru_widgets'])) {
			foreach ($report['hotaru_widgets'] as $key => $value) {
				$output .= $key . " => " . $value['function'];
				if ($value['args']) { $output .= " (args: " . $value['args'] . ")"; }
				$output .= "\n";
			}
		}
		
		$output .= "\n";
		
		$output .= "Number of rows in each table: \n";
		if (isset($report['hotaru_table_count'])) {
			foreach ($report['hotaru_table_count'] as $key => $value) {
				$output .= $key . " => " . $value . " \n";
			}
		}
		
		return $output;
	}
	
	
	/**
	 * Recurse through arrays, adding them to $output for display
	 *
	 * @param array $array
	 * @return array
	 */
	 public function outputArrays($h, $array = array(), $output = '')
	 {
		if (!is_array($array) && !is_object($array)) { return $output; }
		
		foreach ($array as $key => $value) {
			if (is_array($value) || is_object($array)) {
				$output .= "..... " . $key . ":\n";
				$output = $this->outputArrays($h, $value, $output);
			} else {
				$output .= "..... " . $key . ": " . $value . " \n";
			}
		}
		return $output;
	}
	
}
?>