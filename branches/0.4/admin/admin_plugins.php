<?php
/**
 * Determines what actions to process from Plugin Management.
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
 
 /**
 * Call functions based on user actions in Plugin Management
 */
function plugins()
{
    global $lang, $cage, $hotaru, $plugin;
    
    require_once('class.admin.php');
    $admin = New Admin();
    
    $action = $cage->get->testAlpha('action');
    $pfolder = $cage->get->testAlnumLines('plugin');
    $order = $cage->get->testAlnumLines('order');
    
    switch ($action) {
        case "activate":
            $plugin->activate_deactivate_plugin($pfolder, 1);
            break;
        case "deactivate":
            $plugin->activate_deactivate_plugin($pfolder, 0);
            break;    
        case "install":
            $plugin->install_plugin($pfolder);
            break;
        case "uninstall":
            $plugin->uninstall_plugin($pfolder);
            break;    
        case "upgrade":
            $plugin->upgrade_plugin($pfolder);
            break;    
        case "orderup":
            $plugin->plugin_order($pfolder, $order, "up");
            break;    
        case "orderdown":
            $plugin->plugin_order($pfolder, $order, "down");
            break;    
        default:
            // do nothing...
            return false;
            break;
    }

    return true;
}
?>
