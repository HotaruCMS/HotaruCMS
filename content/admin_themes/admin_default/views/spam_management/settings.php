<?php 
/**
 * Theme name: admin_default
 * Template name: settings.php
 * Template author: shibuya246
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
?>
<br/>
<p>You currently have the following spam plugins installed:</p>

<?php 
if (isset($h->allPluginDetails['pluginData'])) {
    //print_r($h->allPluginDetails['pluginData']);
    foreach ($h->allPluginDetails['pluginData'] as $plugin) {
        if ($plugin->plugin_type == 'antispam' || $plugin->plugin_type == 'captcha') {
            echo '<a href="' . SITEURL . 'admin_index.php?page=plugin_settings&plugin=' . $plugin->plugin_folder . '">' . $plugin->plugin_name . '</a>&nbsp;<span class="label label-success">' . $plugin->plugin_version . '</span>'  .  '<br/>';
        }
    }
} else {
    echo "no plugins found";
}
