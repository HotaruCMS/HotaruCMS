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
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
namespace Libs;

class SystemInfo extends Prefab
{
    protected $apiUrl = "http://api.hotarucms.org/index.php?page=api";
    protected $pluginUrl = "http://hotaruplugins.com/index.php?page=api";
    protected $forumUrl = "http://forums.hotarucms.org/api.php";

    /**
	 * Calls external site to provide system info feedback report
	 *
	 * @return bool true
	 */
	public function hotaru_feedback($h, $format = 'json')
	{
		$report = $h->generateReport("object", "lite");
		
		$query_vals = array(
			'api_key' => '',
			'format' => $format,
			'method' => 'hotaru.systemFeedback.add',
			'args' => serialize($report)
		);
		$info = $this->sendApiRequest($h, $query_vals, $this->apiUrl, 1);
                
		return true;
	}
        
        /**
	 * Calls external site to get latest available hotaru version number
	 *
	 * @return string versionnumber
	 */
	public function hotaru_version($h)
	{
//		$query_vals = array(
//		    'api_key' => '',
//		    'format' => 'json',
//		    'method' => 'hotaru.version.get'
//		);
		
		$query_vals = array(
                        'hash' => 'r2FBq73aY1dD3yA604cG25AU30HfKyEE',
                        'format' => 'json',
                        'action' => 'getResource',
                        'value' => 4
                    );
                    
		$info = $this->sendApiRequest($h, $query_vals, $this->forumUrl);

		// save the updated version number to the local db so we can display it on the admin panel until it gets updated.
		if (isset($info['version_string'])) {
                    
                    //$data = \Hotaru\Models\Miscdata::where('miscdata_key', '=', 'hotaru_latest_version')->first();
                    $data = \Hotaru\Models2\Miscdata::getLatestVersion($h, 'miscdata_key');
                    
		    if ($data) {
			// update existing db record
			$sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s WHERE miscdata_key = %s";
			$h->db->query($h->db->prepare($sql, $info['version_string'], 'hotaru_latest_version'));
		    } else {			
			$sql = "INSERT INTO " . TABLE_MISCDATA . " (miscdata_value, miscdata_key) VALUES (%s, %s)";
			$h->db->query($h->db->prepare($sql, $info['version_string'], 'hotaru_latest_version'));	
		    }	
		    return $info['version_string'];
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
//		$query_vals = array(
//		    'api_key' => '',
//		    'format' => 'json',
//		    'method' => 'hotaru.plugin.version.getAll'
//		);

                //$info = $this->sendApiRequest($h, $query_vals, $this->pluginUrl);
                // temp until api can handle children of resources
                //  make array
                $resourceIds = array(1,2,3,4,6,12,14,16);
		$resourceList = array();
		
                foreach($resourceIds as $resourceId) {
                    $query_vals = array(
                        'hash' => 'r2FBq73aY1dD3yA604cG25AU30HfKyEE',
                        'format' => 'json',
                        'action' => 'getResources',
                        'category_id' => $resourceId
                    );
                    $info = $this->sendApiRequest($h, $query_vals, $this->forumUrl);

                    if ($info) { 
			foreach ($info['resources'] as $resource) { 
			    $resourceList[$resource['title']] = $resource;
			}						
		   }
		}
                
                $updateQuery = "";
                // Update the plugin in db using resourceName and the array for that from above
                foreach($resourceList as $plugin) {
                    // was hoping to check first whether version number changed but forums returns plugin name not folder
                    //if (!isset($h->allPluginDetails['pluginData'][$plugin['title']])) { continue; }
                    
                    //if ($plugin['version_string'] > $h->allPluginDetails['pluginData'][$plugin['title']]->plugin_version) {
                        //$result = \Hotaru\Models\Plugin::makeUpdate($plugin['title'], $plugin, $h->currentUser->id);
                        $result = \Hotaru\Models2\Plugin::makeUpdate($h, $plugin['title'], $plugin, $h->currentUser->id);
                    //}
                }
                
		return true;
	}


	/**
	 *
	 * @param <type> $search
	 */
	public function pluginSearch($h, $search)
	{
//		$query_vals = array(
//		    'api_key' => '',
//		    'format' => 'json',
//		    'method' => 'hotaru.plugin.search',
//		    'args' => $search
//		);
                $resourceIds = array(1,2,3,4,6,12,14,16);
		$resourceList = array();
            
                foreach($resourceIds as $resourceId) {
                    $query_vals = array(
                        'hash' => 'r2FBq73aY1dD3yA604cG25AU30HfKyEE',
                        'format' => 'json',
                        'action' => 'getResources',
                        'category_id' => $resourceId
                    );

                    $info = $this->sendApiRequest($h, $query_vals, $this->forumUrl);

                    if ($info) {
                        $resources = $info['resources'];					
                        $resourceList = array_merge($resourceList, $resources);
		    }
                }    
				
		return $resourceList;
	}
        
        /**
	 *
	 * @param <type> $search
	 */
	public function themeSearch($h, $search)
	{
//		$query_vals = array(
//		    'api_key' => '',
//		    'format' => 'json',
//		    'method' => 'hotaru.plugin.search',
//		    'args' => $search
//		);
                $resourceIds = array(5);
		$resourceList = array();
            
                foreach($resourceIds as $resourceId) {
                    $query_vals = array(
                        'hash' => 'r2FBq73aY1dD3yA604cG25AU30HfKyEE',
                        'format' => 'json',
                        'action' => 'getResources',
                        'category_id' => $resourceId
                    );

                    $info = $this->sendApiRequest($h, $query_vals, $this->forumUrl);

                    if ($info) {
                        $resources = $info['resources'];					
                        $resourceList = array_merge($resourceList, $resources);
		    }
                }    
				
		return $resourceList;
	}

	/**
	 *
	 */
	public function pluginTagCloud($h, $number = 20)
	{
//		$query_vals = array(
//		    'api_key' => '',
//		    'format' => 'json',
//		    'method' => 'hotaru.plugin.tagcloud',
//		    'args' => $number
//		);
                
                $resourceIds = array(1,2,3,4,6,12,14);
		$resourceList = array();
	    
                foreach($resourceIds as $resourceId) {
                    $query_vals = array(
                        'hash' => 'r2FBq73aY1dD3yA604cG25AU30HfKyEE',
                        'format' => 'json',
                        'action' => 'getResources',
                        'category_id' => $resourceId
                    );

		    $info = $this->sendApiRequest($h, $query_vals, $this->forumUrl);		    
		    
		    if ($info) {
                        $resources = $info['resources'];					
                        $resourceList = array_merge($resourceList, $resources);
		    }
		}
		
		return array("count" => count($resourceList), "resources" => $resourceList);
	}


        /**
	 *
	 */
	public function themeTagCloud($h, $number = 20)
	{
//		$query_vals = array(
//		    'api_key' => '',
//		    'format' => 'json',
//		    'method' => 'hotaru.plugin.tagcloud',
//		    'args' => $number
//		);
                
                $resourceIds = array(5);
		$resourceList = array();
	    
                foreach($resourceIds as $resourceId) {
                    $query_vals = array(
                        'hash' => 'r2FBq73aY1dD3yA604cG25AU30HfKyEE',
                        'format' => 'json',
                        'action' => 'getResources',
                        'category_id' => $resourceId
                    );

		    $info = $this->sendApiRequest($h, $query_vals, $this->forumUrl);		    
		    
		    if ($info) {
                        $resources = $info['resources'];					
                        $resourceList = array_merge($resourceList, $resources);
		    }
		}
		
		return array("count" => count($resourceList), "resources" => $resourceList);
	}
        
	/**
	 *
	 * @param <type> $query_vals
	 * @param <type> $url
	 * @return <type>
	 */
	public function sendApiRequest($h, $query_vals, $url, $timeout = 0)
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
                //curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                if ($timeout != 0) {
                    //too short to make request?
                    //curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1);
                }
		$response = curl_exec($ch);
		curl_close ($ch);
		//print $url . ' ** ' . $response . "</br>";
		return json_decode($response, true);
	}

        
        /**
         * 
         * @param type $username
         * @param type $password
         * @return type
         */
        public function loginForum($h, $username, $password)
        {
                $loginUrl = "http://forums.hotarucms.org/index.php?login/login";
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_NOBODY, false);
                curl_setopt($ch, CURLOPT_URL, $loginUrl);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

                curl_setopt($ch, CURLOPT_COOKIESESSION, true);
                curl_setopt($ch, CURLOPT_COOKIE, "cookiename=0");
                curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
                curl_setopt ($ch, CURLOPT_COOKIEFILE, 'cookie.txt');

                curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:11.0) Gecko/20100101 Firefox/11.0");
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
                $post_array = array('login' => $username, 'password' => $password, 'cookie_check' => 1, 'redirect' => 'http://forums.hotarucms.org/login/login', 'register' => 0, 'remember' => 1);
                
                curl_setopt($ch,CURLOPT_POST, count($post_array));
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_array));
                $output = curl_exec ($ch);
                
                //$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // for login we are getting a redirect which is a 303 code - unusual but that is what we get
                
                return $ch;
        }
        
        
	/**
	 * Get system data
	 *
	 * @param string $type 'log' or 'object'
	 * @return object
	 */
	public function getSystemData($h, $level = '')
	{
		// essentials:
                //$data = \Hotaru\Models\Miscdata::getCurrentSettings();
                //print_r($data);
            
		$report['hotaru_site_name'] = SITE_NAME;
		$report['hotaru_SITEURL'] = SITEURL;
		
		$report['php_version'] = phpversion();
		$report['mysql_version'] = $h->db->get_var("SELECT VERSION() AS VE");
		$report['hotaru_version'] = $h->version;
		$report['php_extensions'] = get_loaded_extensions();
		   
		$sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
		$report['hotaru_version_db'] = $h->db->get_var($h->db->prepare($sql, 'hotaru_version'));
		
		// default permissions
                if ($level !== 'lite') {
                    $sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
                    $report['hotaru_permissions'] = $h->db->get_var($h->db->prepare($sql, 'permissions'));
                }
		
		// default user settings		
		$sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
		$report['hotaru_user_settings'] = $h->db->get_var($h->db->prepare($sql, 'user_settings'));
		
                // Settings: Name, value (excluding SMTP PASSWORD)
                $settings = \Hotaru\Models2\Setting::getValues($h);
                //$settings = \Hotaru\Models\Setting::getValues();
		
		if ($settings) {
                    foreach ($settings as $setting) {
                        // mask sensitive data
                        switch ($setting->settings_name) {					
                            case 'SMTP_HOST':
                            case 'SMTP_PORT':
                            case 'SMTP_USERNAME':
                            case 'SMTP_PASSWORD':
                            case 'FORUM_PASSWORD':
                                    $setting->settings_value = preg_replace("/[a-zA-Z0-9]/", "*", $setting->settings_value);
                                    break;
                        }
                        $report['settings'][$setting->settings_name] = $setting->settings_value;
                    }
		}
                
                // Counts for all tables		
		foreach ($h->db->get_col("SHOW TABLES",0) as $table_name) {
			$report['hotaru_table_count'][$table_name] = $h->db->get_var("SELECT COUNT(*) FROM " . $table_name);
		}
                
//                $sql = 'SELECT s.schema_name,t.table_name, CONCAT(IFNULL(ROUND(SUM(t.data_length)/1024/1024,2),0.00),"Mb") data_size,CONCAT(IFNULL(ROUND(SUM(t.index_length)/1024/1024,2),0.00),"Mb") index_size, t.ENGINE ENGINE, t.table_rows TABLE_ROWS,t.row_format TABLE_ROW_FORMAT,date(t.update_time) FROM INFORMATION_SCHEMA.SCHEMATA s LEFT JOIN INFORMATION_SCHEMA.TABLES t ON s.schema_name = t.table_schema WHERE s.schema_name not in ("mysql","information_schema") GROUP BY s.schema_name,t.table_name,TABLE_ROW_FORMAT,ENGINE ORDER BY TABLE_ROWS DESC,data_size DESC,index_size DESC';
// 
//                print_r($h->db->get_results($sql));
                
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
		
                if ($level !== 'lite') {
                    $sql = "SELECT phook_id, plugin_folder, plugin_hook FROM " . TABLE_PLUGINHOOKS;
                    $plugins = $h->db->get_results($h->db->prepare($sql));
                    if ($plugins) {
                        foreach ($plugins as $plugin) {
                            $report['hotaru_plugin_hooks'][$plugin->phook_id]['folder'] = $plugin->plugin_folder;
                            $report['hotaru_plugin_hooks'][$plugin->phook_id]['hook'] = $plugin->plugin_hook;
                        }
                    }
                }
		
		// plugin settings: folder, setting (can't use value because might include passwords)		
                if ($level !== 'lite') {
                    $sql = "SELECT plugin_folder, plugin_setting, plugin_value FROM " . TABLE_PLUGINSETTINGS;
                    $plugins = $h->db->get_results($h->db->prepare($sql));
                    if ($plugins) {
                        foreach ($plugins as $plugin) {
                            if (is_serialized($plugin->plugin_value)) {
                                $plugin->plugin_value = unserialize($plugin->plugin_value);
                            }
                            $report['hotaru_plugin_settings'][$plugin->plugin_folder][$plugin->plugin_setting] = $this->applyMaskToArrays($h, $plugin->plugin_value);
                        }
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
                //$data = \Hotaru\Models\Miscdata::getCurrentValue($key);
                $data = \Hotaru\Models2\Miscdata::getCurrentValue($h, $key, $cache);
                return $data;
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
                if (isset($report['hotaru_permissions'])) {
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
                }
                
		$output .= "\n";
		
		$output .= "Default user settings: \n";
		$user_settings = unserialize($report['hotaru_user_settings']);
                if ($user_settings) {
                    foreach ($user_settings as $key => $value) {
                        $output .= $key . " => " . $value . "\n";
                    }
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
