<?php
/**
 * name: Category Manager
 * description: Manage categories.
 * version: 0.6
 * folder: category_manager
 * class: CategoryManager
 * requires: submit 0.7
 * hooks: hotaru_header, install_plugin, admin_header_include, admin_plugin_settings, admin_sidebar_plugin_settings
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
 
return false; die(); // die on direct access.

class CategoryManager extends PluginFunctions
{
    /**
     * Install and create a "categories" table if not already there
     */
    public function install_plugin()
    {
        // Create a new empty table called "categories"
        $exists = $this->db->table_exists('categories');
        if (!$exists) {
            //echo "table doesn't exist. Stopping before creation."; exit;
            $sql = "CREATE TABLE `" . DB_PREFIX . "categories` (
              `category_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `category_parent` int(11) NOT NULL DEFAULT '1',
              `category_name` varchar(64) NOT NULL DEFAULT '',
              `category_safe_name` varchar(64) NOT NULL DEFAULT '',
              `rgt` int(11) NOT NULL DEFAULT '0',
              `lft` int(11) NOT NULL DEFAULT '0',
              `category_order` int(11) NOT NULL DEFAULT '0',
              `category_desc` text NULL,
              `category_keywords` varchar(255) NOT NULL,
              `category_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
              `category_updateby` int(20) NOT NULL DEFAULT 0, 
              UNIQUE KEY `key` (`category_name`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Categories';";
            $this->db->query($sql); 
        
        // Insert default settings...
            
        $sql = "INSERT INTO " . DB_PREFIX . "categories (category_name, category_safe_name) VALUES (%s, %s)";
        $this->db->query($this->db->prepare($sql, urlencode('all'), urlencode('all')));
        
        }
            
        // 'checked' means that categories are enabled by the Submit plugin.
        $this->updateSetting('submit_categories', 'checked', 'submit');    
    
        // Include language file. Also included in hotaru_header, but needed here so 
        // that the link in the Admin sidebar shows immediately after installation.
        $this->includeLanguage();
        
    }
    
    
    /**
     * Define a global "table_categories" and include language
     *
     *@return true
     */
    public function hotaru_header()
    {
        $this->includeLanguage();
        
        if (!defined('TABLE_CATEGORIES')) { define("TABLE_CATEGORIES", DB_PREFIX . 'categories'); }
        return true;
    }

}

?>