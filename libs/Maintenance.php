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
     * System Report is in libs/Debug.php 
     */
     

    /**
     * Calls the delete_files function, then displays a message.
     *
     * @param string $folder - path to the cache folder
     * @param string $msg - show "cleared" message or not
     */
    public function clearCache($h, $folder, $msg = true)
    {
        $success = $this->deleteFiles(CACHE . $folder);
        if (!$msg) { return true; }
        if ($success) {
            $h->message = $h->lang['admin_maintenance_clear_cache_success'];
            $h->messageType = 'green';
        } else {
            $h->message = $h->lang['admin_maintenance_clear_cache_failure'];
            $h->messageType = 'red';    
        }
    }


    /**
     * Remove plugin settings
     *
     * @param string $folder - plugin folder name
     * @param bool $msg - show "Removed" message or not
     */
    public function removeSettings($h, $folder, $msg = true)
    {
        if (!$folder) { $folder = $h->plugin->folder; }
        $sql = "DELETE FROM " . DB_PREFIX . "pluginsettings WHERE plugin_folder = %s";
        $h->db->get_results($h->db->prepare($sql, $folder));
    
        if ($msg) {
            $h->message = $h->lang['admin_maintenance_settings_removed'];
            $h->messageType = 'green';
        }
    }
    
    
    /**
     * Deletes rows from pluginsettings that match a given setting or plugin
     *
     * @param string $setting name of the setting to remove
     * @param string $folder name of plugin folder
     */
    public function deleteSettings($h, $setting = '', $folder = '')
    {
        if ($setting) {
            $sql = "DELETE FROM " . TABLE_PLUGINSETTINGS . " WHERE plugin_setting = %s";
            $h->db->query($h->db->prepare($sql, $setting));
        } 
        elseif ($folder) 
        {
            $sql = "DELETE FROM " . TABLE_PLUGINSETTINGS . " WHERE plugin_folder = %s";
            $h->db->query($h->db->prepare($sql, $folder));
        }
        
        // optimize the table
        $h->db->query("OPTIMIZE TABLE " . TABLE_PLUGINSETTINGS);
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
    
        $success = false;
        while (($file = readdir($handle))!==false) {
            if ($file != 'placeholder.txt') {
                if (@unlink($dir.'/'.$file)) {
                    // ignore setting $success for the JavascriptConstants file which is ALWAYS present (even gets regenerated after deletion)
                    if ($file != 'JavascriptConstants.js') { $success = true; }
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
    public function optimizeTables($h)
    {
        $h->db->select(DB_NAME);
        
        foreach ( $h->db->get_col("SHOW TABLES",0) as $table_name )
        {
            $h->db->query("OPTIMIZE TABLE " . $table_name);
        }
        
        $h->message = $h->lang['admin_maintenance_optimize_success'];
        $h->messageType = 'green';
    }
    
    
    /**
     * Empty plugin database table
     *
     * @param string $table_name - table to empty
     * @param string $msg - show "emptied" message or not
     */
    public function emptyTable($h, $table_name, $msg = true)
    {
        $h->db->query("TRUNCATE TABLE " . $table_name);
        
        if ($msg) {
            $h->message = $h->lang['admin_maintenance_table_emptied'];
            $h->messageType = 'green';
        }
    }
    
    
    /**
     * Delete plugin database table
     *
     * @param string $table_name - table to drop
     */
    public function dropTable($h, $table_name, $msg = true)
    {
        $h->db->query("DROP TABLE " . $table_name);
        
        if ($msg) {
            $h->message = $h->lang['admin_maintenance_table_deleted'];
            $h->messageType = 'green';
        }
    }
    
    
    /**
     * Open or close the site for maintenance
     *
     * @param object $h
     * @param string $switch - 'open' or 'close'
     */
    public function openCloseSite($h, $switch = 'open')
    {
        if ($switch == 'open') { 
            // open
            $sql = "UPDATE " . TABLE_SETTINGS . " SET settings_value = %s WHERE settings_name = %s";
            $h->db->query($h->db->prepare($sql, 'true', 'SITE_OPEN'));
            $h->message = $h->lang['admin_maintenance_site_opened'];
            $h->messageType = 'green';
        } else {
            //close
            $sql = "UPDATE " . TABLE_SETTINGS . " SET settings_value = %s WHERE settings_name = %s";
            $h->db->query($h->db->prepare($sql, 'false', 'SITE_OPEN'));
            $h->message = $h->lang['admin_maintenance_site_closed'];
            $h->messageType = 'green';
        }
    }
    
    
    /**
     * Site closed: Exit
     *
     * @param object $h
     */
    public function siteClosed($h, $lang)
    {
        // site closed and access not granted
        echo "<HTML>\n<HEAD>\n";
        echo "<link rel='stylesheet' href='" . BASEURL . "content/themes/" . THEME . "css/style.css' type='text/css'>\n";
        echo "</HEAD>\n<BODY>\n";
        echo "<div id='site_closed'>\n";
        
        // show custom maintenance page if one exists:
        if (file_exists(THEMES . THEME . 'closed.php'))
        {
            $h->displayTemplate('closed');
        } 
        else
        {
            // show default maintenance page:
            echo $lang['main_hotaru_site_closed'];
            echo "<br /><span id='site_closed_admin_link'>[<a href='" . BASEURL . "admin_index.php?page=admin_login'>Admin Login</a>]</span>";
        }
        
        echo "\n</div>\n</BODY>\n</HTML>\n";
        
        die(); exit;
    }
    
    
    /**
     * Get Site Annoucement for Maintenance Page (AdminPages.php)
     */
    public function getSiteAnnouncement($h)
    {
        // get announcement from database
        $sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA ." WHERE miscdata_key = %s";
        $query = $h->db->prepare($sql, 'site_announcement');
        
        if ($h->pageName != 'maintenance') {
            $h->smartCache('on', 'miscdata', 60, $query); // start using cache
        }

        $result = $h->db->get_var($query);
        
        if ($h->pageName != 'maintenance') {
            $h->smartCache('off'); // stop using cache 
        }
        
        // assign results to $h
        if ($result) {
            $result = unserialize($result);
            $h->vars['admin_announcement'] = urldecode($result['announcement']);
            $h->vars['admin_announcement_enabled'] = $result['enabled'];
        } else {
            $h->vars['admin_announcement'] = "";
            $h->vars['admin_announcement_enabled'] = "";
        }

    }
    
    
    /**
     * Add Site Annoucement from Maintenance Page (AdminPages.php)
     *
     * @param object $announcement_exists - result from getSiteAnnouncement()
     */
    public function addSiteAnnouncement($h)
    {
        $allowable_tags = "<div><p><span><b><i><u><a><img><blockquote><del><br>";
        $h->vars['admin_announcement'] = sanitize($h->cage->get->getHtmLawed('announcement_text'), 'tags', $allowable_tags);
        if ($h->cage->get->keyExists('announcement_enabled')) {
            $h->vars['admin_announcement_enabled'] = "checked";
        } else {
            $h->vars['admin_announcement_enabled'] = "";
        }
        
        // prepare annoucment for database entry:
        $value = array('announcement'=>urlencode($h->vars['admin_announcement']), 'enabled'=>$h->vars['admin_announcement_enabled']);
        $value = serialize($value);
        
        // update existing db record
        $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s, miscdata_updateby = %d WHERE miscdata_key = %s";
        $h->db->query($h->db->prepare($sql, $value, $h->currentUser->id, 'site_announcement'));
        
        // clear the database cache:
        $h->clearCache('db_cache', false);

        $h->message = $h->lang['admin_maintenance_announcement_updated'];
        $h->messageType = 'green';
    }
    
    
    /**
     * Get all files in the specified directory except placeholder.txt
     *
     * @param string $dir - path to the folder
     * @param array $exclude - array of file/folder names to exclude
     * @return array
     */    
    public function getFiles($dir, $exclude = array())
    {
        $files = array();
        $exceptions = array('.svn', '.', '..', 'placeholder.txt');
        $exceptions = array_merge($exceptions, $exclude);
        
        $handle=opendir($dir);
    
        while (($file = readdir($handle))!==false) {
            if (!in_array($file, $exceptions)) {
                array_push($files, $file);
            }
        }
        closedir($handle);
        
        if ($files) { return $files; } else { return false; }
    }
}
?>
