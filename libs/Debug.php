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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
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
			echo $h->lang('main_hotaru_page_load_time') . timer_stop(2) . $h->lang('main_times_secs') . " | ";
			echo $h->lang('main_hotaru_memory_usage') . display_filesize(memory_get_usage()) . " | ";
			echo $h->lang('main_hotaru_php_version') . phpversion() . " | ";
			echo $h->lang('main_hotaru_mysql_version') . $mysql_version . " | ";
			echo $h->lang('main_hotaru_hotaru_version') . $h->version; 
                        echo ' (' . $h->vars['debug']['db_driver'] . ') ';
                        $h->pluginHook('debug_footer');
                        
			echo "</p>"; 
		} else {		
                    if (!$h->adminPage && $h->pageTemplate && function_exists('file_get_contents'))
                    {
                        $filename = THEMES . THEME . $h->pageTemplate . '.php';
                        if (file_exists($filename)) {
                            $template = file_get_contents($filename);

                            $hlink1 = stripos($template,"href='http://hotarucms.org'");
                            $hlink2 = stripos($template,"href=\"http://hotarucms.org\"");
                            if (($hlink1 === FALSE) && ($hlink2 === FALSE)) {
                                    // Hotaru link removed from footer so put it back in:
                                    echo "<p><small><a href='http://hotarucms.org' title='HotaruCMS.org'>Powered by HotaruCMS</a></small></p>";
                            }
                        }
                    }
                }
                if ($h->isDebug && $h->currentUser->perms['can_access_admin'] == 'yes') { echo $this->hvars($h); }
	
		if ($h->currentUser->loggedIn) {echo "<span id='loggedIn' class='loggedIn_true'/>"; } else {"<span id='loggedIn' class='loggedIn_false'/>";}
	}
        
        
        /**
         * Creates a pull down menu in the nav bar for help with debugging
         * @param type $h
         */
         public function debugNav($h)
        {
             $mysql_version = $h->db->get_var("SELECT VERSION() AS VE");
			
             $debug = array(
                 $h->lang('main_hotaru_php_version') => phpversion(),
                 $h->lang('main_hotaru_mysql_version') => $mysql_version,
                 'Hotaru CMS: ' => $h->version,
                 'DB driver: ' => isset($h->vars['debug']['db_driver']) ? $h->vars['debug']['db_driver'] : '',
                  'divider'=>''                 
              );
             ?>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $h->lang("main_theme_navigation_debug"); ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu debug">
                    <?php
                        foreach ($debug as $item => $value) {                      
                            if ($item == 'divider')
                                echo  '<li class="divider"></li>'; 
                            else
                                if (is_array($value)) 
                                    echo '<li><a href="' . $value[1] . '">' . $item . '<strong>' . $value[0] . '</strong></a></li>';
                                else                                    
                                    echo '<li><a href="#">' . $item . '<strong>' . $value . '</strong></a></li>';
                        }
                        // Make these separate as we are using javascript to fill them later
                        
                        echo '<li><a href="#">' . $h->lang('main_hotaru_db_queries') . '<strong><span id="debug_nav_db_queries"></span></strong></a></li>';
                        echo '<li><a href="#">' . $h->lang('main_hotaru_memory_usage') . '<strong><span id="debug_nav_memory_usage"></span></strong></a></li>';
                        //echo '<li><a href="#modal_hvars" data-toggle="modal">' . "h->vars: " . '<strong><span id="debug_nav_memory_usage"></span></strong></a></li>';
                        echo '<li class="divider"></li>';
                        echo '<li><a href="' . BASEURL . 'admin_index.php?page=maintenance&debug=error_log.php">' . "Error log" . '<strong></strong></a></li>';
                        
//                 $h->lang('main_hotaru_page_load_time') => timer_stop(2) . $h->lang('main_times_secs'),
//                 $h->lang('main_hotaru_memory_usage') => display_filesize(memory_get_usage()),
//                 '$h->vars: ' => array('(' . count($h->vars) . ') ' . display_filesize(strlen(serialize($h->vars))), $h->url(array('debug'=>'hvars' ,'admin'))),
//                 'divider'=>'',                 
//                 'Error log' => array('', BASEURL . 'admin_index.php?page=maintenance&debug=error_log.php')
//                        
//                  ?>          
                    </ul>
                  </li>                        
            <?php 
            
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
		
                // TODO
                // remove by 1.6.0
//		if ($type == 'email') {
//			$to = "admin@hotarucms.org"; // do not change!
//			$subject = "System Report from " . SITE_NAME;
//			$body = $sysinfo->logSystemReport($h, $report);
//			$h->email($to, $subject, $body);
//			$h->message = $h->lang('admin_maintenance_system_report_emailed');
//			$h->messageType = 'green';
//			return true;
//		}
		
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
        
        
        /**
         * dumps out the contents of $h->vars in a modal box
         * used in context with the debug nav bar menu above
         * @param type $h
         */
        function hvars($h)
        {
            ?> 
            <div id="modal_hvars" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h3 id="myModalLabel">$h->vars</h3>
            </div>
            <div class="modal-body">
              
              
              <?php         
                         //print_r($h->vars);
              foreach ($h->vars as $key => $value) {
                  if (is_array($value)) {                      
                      echo '<h4>' . $key . '</h4>';
                      foreach ($value as $subKey => $subValue) {
                          //echo '<p>' . $subKey . ' = '  . $subValue . '</p>';
                          echo '<p>' . htmlentities($subKey) . ' = ';
                          
                          if (is_object($subValue) || is_array($subValue)) print_r($subValue); else print htmlentities($subValue);
                          echo '</p>';
                      }
                      echo '<hr>';
                  } else {                     
                     echo '<p>';
                     if (is_object($key)) print_r($key); else print htmlentities($key);                     
                     print ' = ';
                     if (is_object($value)) print_r($value); else print htmlentities($value);
                     print '</p>';
                     echo '<hr>';
                  }
                  
              } ?>
            </div>
                
            <div class="modal-footer">
              <button class="btn" data-dismiss="modal">Close</button>
            </div>
          </div>
                                                  
            <?php
        }
}
?>