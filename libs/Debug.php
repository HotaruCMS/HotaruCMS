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
 * @copyright Copyright (c) 2010, Hotaru CMS
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
			echo $h->lang('main_hotaru_db_queries') . $h->db->num_queries . " | ";
			echo $h->lang('main_hotaru_page_load_time') . timer_stop(1) . $h->lang('main_times_secs') . " | ";
			echo $h->lang('main_hotaru_memory_usage') . display_filesize(memory_get_usage()) . " | ";
			echo $h->lang('main_hotaru_php_version') . phpversion() . " | ";
			echo $h->lang('main_hotaru_mysql_version') . $mysql_version . " | ";
			echo $h->lang('main_hotaru_hotaru_version') . $h->version; 
                        
                        $h->pluginHook('debug_footer');
                        
			echo "</p>"; 
		}
		elseif ($h->pageTemplate && function_exists('file_get_contents'))
		{
			$template = file_get_contents(THEMES . THEME . $h->pageTemplate . '.php');

			$hlink1 = stripos($template,"href='http://hotarucms.org'");
			$hlink2 = stripos($template,"href=\"http://hotarucms.org\"");
			if (($hlink1 === FALSE) && ($hlink2 === FALSE)) {
				// Hotaru link removed from footer so put it back in:
				echo "<p><small><a href='http://hotarucms.org' title='HotaruCMS.org'>Powered by HotaruCMS</a></small></p>";
			}
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
		
		// delete file if over 500KB
		if (file_exists($this->log[$type]) && (filesize($this->log[$type]) > 500000)) {
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
	 * @param string $type 'log', 'email' or 'object'
	 */
	public function generateReport($h, $type = 'log', $level = '')
	{
		$sysinfo = new SystemInfo();

		$report = $sysinfo->getSystemData($h, $level);
		
		if ($type == 'object') { return $report; }
		
		if ($type == 'email') {
			$to = "admin@hotarucms.org"; // do not change!
			$subject = "System Report from " . SITE_NAME;
			$body = $sysinfo->logSystemReport($h, $report);
			$h->email($to, $subject, $body);
			$h->message = $h->lang('admin_maintenance_system_report_emailed');
			$h->messageType = 'green';
			return true;
		}
		
		$h->openLog('system_report', 'w');
		
		// convert object to text
		$output = $sysinfo->logSystemReport($h, $report);
		if ($output) {
			$h->writeLog('system_report', $output);
			$h->closeLog('system_report');
			
			$h->message = $h->lang('admin_maintenance_system_report_success');
			$h->messageType = 'green';
			return true;
		} else {
			$h->message = $h->lang('admin_maintenance_system_report_failure');
			$h->messageType = 'red';
			return false;
		}
	}
}
?>