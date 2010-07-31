<?php
/**
 * Posts Widget Settings
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
 
class PostsWidgetSettings
{
     /**
     * Admin settings for the Users plugin
     */
    public function settings($h)
    {
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }    
        
        echo "<h1>" . $h->lang["posts_widget_settings_header"] . "</h1>\n";
        
        // Get settings from database if they exist...
        $pw_settings = $h->getSerializedSettings();
        $items = $pw_settings['items'];
        $length = $pw_settings['length'];
		$widgets = $pw_settings['widgets'];
        
        //...otherwise set to defaults:
        if (!$items) { $items = 10; }
        if (!$length) { $length = 0; }
		if (!$widgets) {
			$widgets['posts_widget_top'] = 'checked';
			$widgets['posts_widget_latest'] = 'checked';
			$widgets['posts_widget_upcoming'] = 'checked';
			$widgets['posts_widget_day'] = 'checked';
			$widgets['posts_widget_week'] = 'checked';
			$widgets['posts_widget_month'] = 'checked';
			$widgets['posts_widget_year'] = 'checked';
			$widgets['posts_widget_all-time'] = 'checked';
		}

		$widget_names = array('top', 'latest', 'upcoming', 'day', 'week', 'month', 'year', 'all-time');
        
        echo "<form name='posts_widget_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=posts_widget' method='post'>\n";
        
        echo "<p><input type='text' name='items' size=4 value='" . $items . "'>&nbsp;&nbsp;" . $h->lang["posts_widget_settings_items"] . "</p><br />\n";
        echo "<p><input type='text' name='length' size=4 value='" . $length . "'>&nbsp;&nbsp;" . $h->lang["posts_widget_settings_length"] . "</p><br />\n";

		echo "<p>" . $h->lang["posts_widget_settings_widgets_desc"] . "</p>";
		echo "<ul>\n";

		foreach ($widget_names as $name) {
			echo "<li><input type='checkbox' name='pw_" . $name . "' value='pw_" . $name . "' " . $widgets["posts_widget_$name"] . ">&nbsp;&nbsp;Posts Widget " . make_name($name, '-') . "</li>\n";
		}
		echo "</ul><br />";

        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form><br />\n";
    }
    
    
    /**
     * Save Settings
     */
    public function saveSettings($h)
    {
        // Number of items
        $items = $h->cage->post->testInt('items');
        if (!$items) {
            $items = 10;
        }
        
        // Character length for post titles
        $length = $h->cage->post->testInt('length');
        if (!$length) {
            $length = 0; 
        }

		// -----------------------------------------------------------------------------
		// Keep or remove widgets:
		$widgets_settings = $h->getSerializedSettings('widgets');
		$widget_names = array('top', 'latest', 'upcoming', 'day', 'week', 'month', 'year', 'all-time');
		foreach ($widget_names as $name) {
			if ($h->cage->post->keyExists("pw_$name")) {
				// add widget
				$pw_settings['widgets']["posts_widget_$name"] = 'checked';
				if (!isset($widgets_settings['widgets']["posts_widget_$name"])) {
					$h->addWidget('posts_widget', "posts_widget_$name", $name);
				}
			} else {
				// remove widget
				$pw_settings['widgets']["posts_widget_$name"] = '';
				if (isset($widgets_settings['widgets']["posts_widget_$name"])) {
					$h->deleteWidget("posts_widget_$name"); //  from widgets table
				}
				unset($widgets_settings['widgets']["posts_widget_$name"]); // form widgets settings
			}
		}
		// update widgets settings:
		$h->updateSetting('widgets_settings', serialize($widgets_settings), 'widgets');
		// -----------------------------------------------------------------------------
        
        $pw_settings['items'] = $items;
        $pw_settings['length'] = $length;
        
        $h->updateSetting('posts_widget_settings', serialize($pw_settings));
        
        $h->message = $h->lang["main_settings_saved"];
        $h->messageType = "green";
        $h->showMessage();
        
        return true;    
    }
}
?>
