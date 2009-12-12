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
    /**
     * Shows number of database queries and the time it takes for a page to load
     */
    public function showQueriesAndTime($hotaru)
    {
        if ($hotaru->isDebug) { 
            echo "<p class='debug'>" . $hotaru->db->num_queries . " " . $hotaru->lang['main_hotaru_queries_time'] . " " . timer_stop(1) . " " . 
            $hotaru->lang['main_hotaru_seconds'] . $hotaru->lang['main_hotaru_memory_usage1'] . display_filesize(memory_get_usage()) . $hotaru->lang['main_hotaru_memory_usage2'] . "</p>"; 
        }
    }
}
?>