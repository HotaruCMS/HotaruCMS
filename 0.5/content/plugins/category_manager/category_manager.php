<?php
/**
 * name: Category Manager
 * description: Manage categories.
 * version: 0.1
 * folder: category_manager
 * prefix: cats
 * requires: submit 0.1
 * hooks: hotaru_header, admin_index, install_plugin, admin_header_include, admin_plugin_settings, admin_sidebar_plugin_settings
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

/**
 * Install and create a "categories" table if not already there
 */
function cats_install_plugin()
{
    global $db, $plugin, $post, $lang;
    
    // Create a new empty table called "categories"
    $exists = $db->table_exists('categories');
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
        $db->query($sql); 
    
    // Insert default settings...
        
    $sql = "INSERT INTO " . DB_PREFIX . "categories (category_name, category_safe_name) VALUES (%s, %s)";
    $db->query($db->prepare($sql, urlencode('all'), urlencode('all')));
    
    }
        
    // 'checked' means that categories are enabled by the Submit plugin.
    $plugin->plugin_settings_update('submit', 'submit_categories', 'checked');    

    // Include language file. Also included in hotaru_header, but needed here so 
    // that the link in the Admin sidebar shows immediately after installation.
    $plugin->include_language('category_manager');
    
}


/**
 * Define a global "table_categories" and include language
 *
 *@return true
 */
function cats_hotaru_header()
{
    global $hotaru, $lang, $plugin;
    
    $plugin->include_language('category_manager');
    
    if (!defined('TABLE_CATEGORIES')) { define("TABLE_CATEGORIES", DB_PREFIX . 'categories'); }
    return true;
}


/**
 * Include jquery and css for Category Manager
 */
function cats_admin_header_include()
{
    global $plugin, $admin, $cage;

    $plugin->include_css('category_manager');
    $plugin->include_js('category_manager');
}


/**
 * Put a link to the settings page in the Admin sidebar under Plugin Settings
 */
function cats_admin_sidebar_plugin_settings()
{
    global $lang;
    echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>'category_manager'), 'admin') . "'>". $lang["cat_man_admin_sidebar"] . "</a></li>";
}


 /**
 * Display pages from Category Manager
 *
 * @return true
 */
function cats_admin_plugin_settings()
{
    global $hotaru, $plugin, $lang;
    
    require_once(PLUGINS . 'category_manager/cat_man_engine.php');
    cat_man_main();
    return true;
}


 /**
 * Display category tree.
 *
 * @param array $the_cats
 */
function cat_man_tree($the_cats)
{
    echo "<div class='cat_man_tree'>";
    foreach ($the_cats as $cat) {
        if ($cat['category_name'] != "all") {
            if ($cat['category_parent'] > 1) {
                for($i=1; $i<$cat['category_level']; $i++) {
                    echo "--- ";
                }
                 echo $cat['category_name'] . " <span style='font-size: 0.7em; color: #888;'>[" . $cat['category_id'] . "]</span><br />";
            } else {
                 echo $cat['category_name'] . " <span style='font-size: 0.7em; color: #888;'>[" . $cat['category_id'] . "]</span><br />";
            }
        }
    }
    echo "</div>";
}

?>