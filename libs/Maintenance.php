<?php
/**
 * Functions for maintaining the health of Hotaru CMS
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
class Maintenance
{
    /**
     * Calls the delete_files function, then displays a message.
     *
     * @param string $folder - path to the cache folder
     * @param string $msg - show "cleared" message or not
     */
    public function clearCache($hotaru, $folder, $msg = true)
    {
        $success = $this->deleteFiles(CACHE . $folder);
        if (!$msg) { return true; }
        if ($success) {
            $hotaru->message = $hotaru->lang['admin_maintenance_clear_cache_success'];
            $hotaru->messageType = 'green';
        } else {
            $hotaru->message = $hotaru->lang['admin_maintenance_clear_cache_failure'];
            $hotaru->messageType = 'red';    
        }
    }


    /**
     * Delete all files in the specified directory except placeholder.txt
     *
     * @param string $dir - path to the cache folder
     * @return bool
     */    
    public function deleteFiles($dir)
    {
        $handle=opendir($dir);
    
        while (($file = readdir($handle))!==false) {
            if ($file != 'placeholder.txt') {
                if (@unlink($dir.'/'.$file)) {
                    $success = true;
                } else {
                    $success = false;
                }
            }
        }
        closedir($handle);
        return $success;
    }
    
    
    /**
     * Optimize all database tables
     */
    public function optimizeTables($hotaru)
    {
        $hotaru->db->select(DB_NAME);
        
        foreach ( $hotaru->db->get_col("SHOW TABLES",0) as $table_name )
        {
            $hotaru->db->query("OPTIMIZE TABLE " . $table_name);
        }
        
        $hotaru->message = $hotaru->lang['admin_maintenance_optimize_success'];
        $hotaru->messageType = 'green';
;
    }
    
    
    /**
     * Empty plugin database table
     *
     * @param string $table_name - table to empty
     * @param string $msg - show "emptied" message or not
     */
    public function emptyTable($hotaru, $table_name, $msg = true)
    {
        $hotaru->db->query("TRUNCATE TABLE " . $table_name);
        
        if ($msg) {
            $hotaru->message = $hotaru->lang['admin_maintenance_table_emptied'];
            $hotaru->messageType = 'green';
        }
    }
    
    
    /**
     * Delete plugin database table
     *
     * @param string $table_name - table to drop
     */
    public function dropTable($hotaru, $table_name, $msg = true)
    {
        $hotaru->db->query("DROP TABLE " . $table_name);
        
        if ($msg) {
            $hotaru->message = $hotaru->lang['admin_maintenance_table_deleted'];
            $hotaru->messageType = 'green';
        }
    }
    
    
    /**
     * Remove plugin settings
     *
     * @param string $plugin_name - settings to remove
     */
    public function removeSettings($hotaru, $plugin_name, $msg = true)
    {
        $sql = "DELETE FROM " . DB_PREFIX . "pluginsettings WHERE plugin_folder = %s";
        $hotaru->db->get_results($hotaru->db->prepare($sql, $plugin_name));
    
        if ($msg) {
            $hotaru->message = $hotaru->lang['admin_maintenance_settings_removed'];
            $hotaru->messageType = 'green';
        }
    }
    
    
    /**
     * Open or close the site for maintenance
     *
     * @param object $hotaru
     * @param string $switch - 'open' or 'close'
     */
    public function openCloseSite($hotaru, $switch = 'open')
    {
        if ($switch == 'open') { 
            // open
            $sql = "UPDATE " . TABLE_SETTINGS . " SET settings_value = %s WHERE settings_name = %s";
            $hotaru->db->query($hotaru->db->prepare($sql, 'true', 'SITE_OPEN'));
            $hotaru->message = $hotaru->lang['admin_maintenance_site_opened'];
            $hotaru->messageType = 'green';
        } else {
            //close
            $sql = "UPDATE " . TABLE_SETTINGS . " SET settings_value = %s WHERE settings_name = %s";
            $hotaru->db->query($hotaru->db->prepare($sql, 'false', 'SITE_OPEN'));
            $hotaru->message = $hotaru->lang['admin_maintenance_site_closed'];
            $hotaru->messageType = 'green';
        }
    }
    
    
    /**
     * Site closed: Exit
     *
     * @param object $hotaru
     */
    public function siteClosed($lang)
    {
        // site closed and access not granted, so exit with a message:
        echo "<HTML>\n<HEAD>\n";
        echo "<link rel='stylesheet' href='" . BASEURL . "content/themes/" . THEME . "css/style.css' type='text/css'>\n";
        echo "</HEAD>\n<BODY>\n";
        echo "<div id='site_closed'>\n";
        echo $lang['main_hotaru_site_closed'];
        echo "\n</div>\n</BODY>\n</HTML>\n";
        die(); exit;
    }
}
?>