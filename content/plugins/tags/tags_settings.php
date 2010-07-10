<?php
/**
 * File: /plugins/tags/tags_settings.php
 * Purpose: Admin settings for the tags plugin
 *
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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

class TagsSettings
{
    /**
     * Tags Settings Page
     */
    public function settings($h) {

	echo "<h1>" . $h->lang["tags_settings_header"] . "</h1>";

	 // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') {
            $this->saveSettings($h);
        }
              
	// Get settings from database if they exist...
        $tags_settings = $h->getSerializedSettings();

	$settings = array( 'tags_setting_exclude_active' => '',
			'tags_setting_exclude_words' => '',
	    );

	foreach ($settings as $setting => $value) {
	    $$setting = $tags_settings[$setting];
	    if (!$tags_settings[$setting]) { $$setting = $value; }
	}

	echo "<form name='tags_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&plugin=tags' method='post'>";

	// setting1
        echo "<p>" . $h->lang['tags_setting_exclude_active'] . " <input type='checkbox' name='tags_setting_exclude_active' value='tags_setting_exclude_active' " . $tags_setting_exclude_active . " ></p>";

        // setting2
        echo "<p><label for='tags_setting_exclude_words'>" . $h->lang['tags_setting_exclude_words'] . "</label><br/>";
	echo "<textarea rows=8 cols=80 name='tags_setting_exclude_words' >";
	echo $tags_setting_exclude_words;
	echo "</textarea></p>";

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
        if ($h->cage->post->keyExists('tags_setting_exclude_active')) {
            $tags_setting_exclude_active = 'checked';
        } else {
            $tags_setting_exclude_active = '';
        }

        // tags_setting_exclude_words
        if ($h->cage->post->keyExists('tags_setting_exclude_words')) {
            if ($h->cage->post->getHtmLawed('tags_setting_exclude_words')) {
                $tags_setting_exclude_words = $h->cage->post->getHtmLawed('tags_setting_exclude_words');
            } else {
                $tags_setting_exclude_words = ''; $error = 1;
            }
        } else {
            $tags_setting_exclude_words = ''; $error = 1;
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
            $tags_settings['tags_setting_exclude_active'] = $tags_setting_exclude_active;
            $tags_settings['tags_setting_exclude_words'] = $tags_setting_exclude_words;

            $h->updateSetting('tags_settings', serialize($tags_settings));

            $h->message = $h->lang["main_settings_saved"];
            $h->messageType = "green";
            $h->showMessage();

            return true;
        }
    }

}
?>