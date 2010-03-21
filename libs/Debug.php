<?php
/**
 * Debugging functions
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
class Debug
{
    protected $fh = array();    // file handlers
    protected $log = array();   // file paths
    
    /**
     * Shows number of database queries and the time it takes for a page to load
     */
    public function showQueriesAndTime($h)
    {
        if ($h->isDebug) { 
        
            $mysql_version = $h->db->get_var("SELECT VERSION() AS VE");
            
            echo "<p class='debug'>";
            echo $h->lang['main_hotaru_db_queries'] . $h->db->num_queries . " | ";
            echo $h->lang['main_hotaru_page_load_time'] . timer_stop(1) . $h->lang['main_times_secs'] . " | ";
            echo $h->lang['main_hotaru_memory_usage'] . display_filesize(memory_get_usage()) . " | ";
            echo $h->lang['main_hotaru_php_version'] . phpversion() . " | ";
            echo $h->lang['main_hotaru_mysql_version'] . $mysql_version . " | ";
            echo $h->lang['main_hotaru_hotaru_version'] . $h->version; 
            echo "</p>"; 
        }

        if ($h->currentUser->loggedIn) {echo "<span id='loggedIn' class='loggedIn_true'/>"; } else {"<span id='loggedIn' class='loggedIn_false'/>";}
    }


    /**
     * Open file for logging
     *
     * @param string $type "speed", "error", etc.
     * @param string $mode e.g. 'a' or 'w'. 
     * @link http://php.net/manual/en/function.fopen.php
     */
    public function openLog($type = 'debug', $mode = 'a+')
    {
        $this->log[$type] = CACHE . "debug_logs/" . $type . ".php";
        
        // delete file if over 1MB
        if (file_exists($this->log[$type]) && (filesize($this->log[$type]) > 1000000)) {
            unlink($this->log[$type]); 
        }
        
        // If doesn't exist or rewriting, create a new file with die() at the top
        if (!file_exists($this->log[$type]) || ($mode != 'a' && $mode != 'a+')) {
            $this->fh[$type] = fopen($this->log[$type], $mode) or die("Sorry, I can't open cache/debug_logs/" . $type . ".php");
            fwrite($this->fh[$type], "<?php die(); ?>\r\n");
        } else {
            // open existing file:
            $this->fh[$type] = fopen($this->log[$type], $mode) or die("can't open file");
        }
    }
    
    
    /**
     * Log performance and errors
     *
     * @param string $type "error", "speed", etc.
     */
    public function writeLog($type = 'debug', $string = '')
    {
        if ($string) {
            $string = date('d M Y H:i:s', time()) . " " . $string . "\n";
            fwrite($this->fh[$type], $string);
        }
    }
    
    
    /**
     * Close log file
     *
     * @param string $type "speed", "error", etc.
     */
    public function closeLog($type = 'debug')
    {
        if (isset($this->fh[$type])) { fclose($this->fh[$type]); }
    }
    
    
    /**
     * Generate a System Report
     *
     * @param string $type 'log' or 'object'
     */
    public function generateReport($h, $type = 'log')
    {
        $report = $this->getSystemData($h);
        
        if ($type == 'object') { return $report; }
        
        $h->openLog('system_report', 'w');
        
        // convert object to text
        $output = $this->logSystemReport($h, $report);
        if ($output) {
            $h->writeLog('system_report', $output);
            $h->closeLog('system_report');
            
            $h->message = $h->lang['admin_maintenance_system_report_success'];
            $h->messageType = 'green';
        } else {
            $h->message = $h->lang['admin_maintenance_system_report_failure'];
            $h->messageType = 'red';
        }
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
        $report['hotaru_baseurl'] = BASEURL;
        
        $report['php_version'] = phpversion();
        $report['mysql_version'] = $h->db->get_var("SELECT VERSION() AS VE");
        $report['hotaru_version'] = $h->version;
        
        $sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
        $report['hotaru_version_db'] = $h->db->get_var($h->db->prepare($sql, 'hotaru_version'));
        
        // default permissions
        
        $sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
        $report['hotaru_permissions'] = $h->db->get_var($h->db->prepare($sql, 'permissions'));
        
        // default user settings
        
        $sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
        $report['hotaru_user_settings'] = $h->db->get_var($h->db->prepare($sql, 'user_settings'));
        
        // plugins: folder, enabled, version, order
        
        $sql = "SELECT plugin_folder, plugin_enabled, plugin_version, plugin_order FROM " . TABLE_PLUGINS . " ORDER BY plugin_order";
        $plugins = $h->db->get_results($h->db->prepare($sql));
        if ($plugins) {
            foreach ($plugins as $plugin) {
                $report['hotaru_plugins'][$plugin->plugin_folder]['enabled'] = $plugin->plugin_enabled;
                $report['hotaru_plugins'][$plugin->plugin_folder]['version'] = $plugin->plugin_version;
                $report['hotaru_plugins'][$plugin->plugin_folder]['order'] = $plugin->plugin_order;
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
//            foreach ($plugins as $plugin) {
//                if (is_serialized($plugin->plugin_value)) {
//                    $plugin->plugin_value = unserialize($plugin->plugin_value);
//                    if (is_array($plugin->plugin_value)) {
//                        foreach ($plugin->plugin_value as $key => $value) {
//                            if (is_array($value)) {
//                                foreach ($value as $k => $v) {
//                                    if (is_array($v)) {
//                                        $v = serialize($v);
//                                        $plugin->plugin_value[$key][$k] = $this->aaa($v);
//                                    } else {
//                                        $plugin->plugin_value[$key][$k] = preg_replace("/[a-zA-Z0-9]/", "*", $v);
//                                    }
//                                }
//                            } else {
//                                $plugin->plugin_value[$key] = preg_replace("/[a-zA-Z0-9]/", "*", $value);
//                            }
//                        }
//                        $plugin->plugin_value = serialize($plugin->plugin_value);
//                    }
//                } else {
//                    $plugin->plugin_value = preg_replace("/[a-zA-Z0-9]/", "*", $plugin->plugin_value);
//                }
//                $report['hotaru_plugin_settings'][$plugin->plugin_folder][$plugin->plugin_setting] = $plugin->plugin_value;
//            }

            print_r ($this->loop_through_array($h, $plugins));
            
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
                $report['hotaru_settings'][$setting->settings_name] = $setting->settings_value;
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

     function loop_through_array ($h, $array, &$out = array () ) {
        foreach ( (array) $array as $key => $value ) {
            if ( ! is_array ( $value ) ) {                
                $value = serialize($value);
                array_push ( $out, preg_replace("/[a-zA-Z0-9]/", "*", $value) );
            }
            else {
                $this->loop_through_array ( $value, &$out );
            }
        }
        return $out;
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
        $output .= "URL: " . $report['hotaru_baseurl'] . "\n";
        $output .= "Hotaru version: " . $report['hotaru_version'] . "\n";
        $output .= "Hotaru version in database: " . $report['hotaru_version_db'] . "\n";
        $output .= "PHP version: " . $report['php_version'] . "\n";
        $output .= "MySQL version: " . $report['mysql_version'] . "\n";
        
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

        $output .= "Plugin Settings: \n\n";
        if (isset($report['hotaru_plugin_settings'])) {
            foreach ($report['hotaru_plugin_settings'] as $key => $value) {
                foreach ($value as $k => $v) {
                    if (!is_serialized($v)) {
                        $output .= "Plugin settings for " . $key . ":\n" . $k . " = " . $v . " \n";
                    } else {
                        $output .= "Plugin settings for " . $key . ":\n";
                        $v = unserialize($v);
                        foreach ($v as $y => $z) {
                            $output .= "..... " . $y . ": " . $z . " \n";
                        }
                        $output .= " \n";
                    }
                }
            }
        }
        
        $output .= "\n";

        $output .= "Hotaru Settings: \n";
        if (isset($report['hotaru_settings'])) {
            foreach ($report['hotaru_settings'] as $key => $value) {
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
}
?>
