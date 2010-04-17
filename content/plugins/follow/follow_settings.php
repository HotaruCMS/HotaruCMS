<?php
/**
 * File: /plugins/follow/follow_settings.php
 * Purpose: Admin settings for the Follow plugin
 *
 * PHP version 5
 *
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
 * @author    shibuya246
 * @copyright Copyright (c) 2010, shibuya246
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 *
 * @link      http://www.hotarucms.org/
 */

class FollowSettings
{
    /**
     * Follow Settings Page
     */
    public function settings($h) {

	echo "<h1>" . $h->lang["follow_settings_header"] . "</h1>";

	 // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') {
            $this->saveSettings($h);
        }

        // Get settings from database if they exist...
        $follow_settings = $h->getSerializedSettings();

	$setting1 = $follow_settings['setting1'];
        $setting2 = $follow_settings['setting2'];

        //...otherwise set to blank:
        if (!$setting1) { $setting1 = ''; }
        if (!$setting2) { $setting2 = 0; }

	echo "<form name='follow_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&plugin=follow' method='post'>";

	// setting1
        echo "<p><input type='checkbox' name='setting1' value='setting1' " . $setting1 . " >  " . $h->lang["follow_settings_setting1"] . "</p>";

        // setting2
        echo "<p><input type='text' size=5 name='setting2' value='" . $setting2 . "' /> " . $h->lang["follow_settings_setting2"] . "</p>";

        $h->pluginHook('follow_settings_form');

        echo "<br /><br />";
        echo "<input type='hidden' name='submitted' value='true' />";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />";
        echo "</form>";

    }




    /**
     * Save admin settings
     *
     * @return true
     */
    public function saveSettings($h)
    {
        $error = 0;

        // show setting1?
        if ($h->cage->post->keyExists('setting1')) {
            $setting1 = 'checked';
        } else {
            $setting1 = '';
        }

        // number of items - setting2
        if ($h->cage->post->keyExists('setting2')) {
            if ($h->cage->post->testInt('setting2')) {
                $setting2 = $h->cage->post->testInt('setting2');
            } else {
                $setting2 = 10; $error = 1;
            }
        } else {
            $setting2 = 10; $error = 1;
        }
                

        if ($error == 1)
        {
            $h->message = $h->lang["main_settings_not_saved"];
            $h->messageType = "red";
            $h->showMessage();

            return false;
        }
        else
        {
            $follow_settings['setting1'] = $setting1;
            $follow_settings['setting2'] = $setting2;

            $h->updateSetting('follow_settings', serialize($follow_settings));

            $h->message = $h->lang["main_settings_saved"];
            $h->messageType = "green";
            $h->showMessage();

            return true;
        }
    }

}
?>
