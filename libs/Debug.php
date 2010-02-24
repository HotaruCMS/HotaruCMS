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
        $this->log[$type] = CACHE . "debug_logs/" . $type . ".txt";
        
        // auto-delete the file after 1 week:
        /*
        $last_modified = filemtime($this->log[$type]);
        $expire = (7 * 24 * 60 * 60); // 1 week
        if ($last_modified < (time() - $expire)) { unlink ($this->log[$type]); }
        */
        
        // open/create a file:
        $this->fh[$type] = fopen($this->log[$type], $mode) or die("can't open file");
    }
    
    
    /**
     * Log performance and errors
     *
     * @param string $type "error", "speed", etc.
     */
    public function writeLog($type = 'debug', $string = '')
    {
        if ($string) {
            $string = date('d M Y H:i:s', time()) . ": " . $string . "\n";
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
}
?>
