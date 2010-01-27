<?php
/**
 * file: admin_themes/admin_default/theme_settings_functions.php
 * purpose: Activate a new theme
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

//$json_array = array('result'=>'test_okay');
//echo json_encode($json_array); exit;
//print $_SERVER['DOCUMENT_ROOT'];

require_once('../../../hotaru_settings.php');
require_once('../../../Hotaru.php');    // Not the cleanest way of getting to the root...

$h = new Hotaru();
$h->start();
 
 if ($h->cage->post->testAlnumLines('admin') == 'theme_settings' ) {
    $h->includeLanguage('admin');
    $theme = strtolower($h->cage->post->testAlpha('theme') . "/" );
   
    adminSettingUpdate($h, 'THEME', $theme);
    
    $json_array = array('activate'=>'true', 'message'=>'success', 'color'=>'green');
    //$lang["admin_theme_theme_activate_success"] = " Theme was activated successfully.";

	// Send back result data
	echo json_encode($json_array);
}

 function getAllAdminSettings($db)
    {
        $sql = "SELECT * FROM " . TABLE_SETTINGS;
        $results = $db->get_results($db->prepare($sql));
        if ($results) { return $results; } else { return false; }
    }

function adminSettingUpdate($h, $setting = '', $value = '')
    {
        $exists = adminSettingExists($h->db, $setting);

        if (!$exists) {
            $sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_value, settings_updateby) VALUES (%s, %s, %d)";
            $h->db->query($h->db->prepare($sql, $setting, $value, $h->currentUser->id));
        } else {
            $sql = "UPDATE " . TABLE_SETTINGS . " SET settings_name = %s, settings_value = %s, settings_updateby = %d WHERE (settings_name = %s)";
            $h->db->query($h->db->prepare($sql, $setting, $value, $h->currentUser->id, $setting));
        }
    }

    function adminSettingExists($db, $setting = '')
    {
        $sql = "SELECT settings_name FROM " . TABLE_SETTINGS . " WHERE (settings_name = %s)";
        $returned_setting = $db->get_var($db->prepare($sql, $setting));
        if ($returned_setting) { return $returned_setting; } else { return false; }
    }
?>
