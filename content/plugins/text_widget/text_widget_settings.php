<?php 

/* ******* PLUGIN TEMPLATE ******************************************************************************** 
 * Plugin name: Text Widget
 * Template name: text_widget_settings.php
 * License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */

class TextWidgetSettings
{
    public function settings($h)
    {
        // Cycle through the text widgets, displaying their settings...
        $id = 1;

        while($settings = unserialize($h->getSetting('text_widget_' . $id . '_settings', 'text_widget'))) 
        {
            echo "<h1>" . $h->lang["text_widget_settings_header"] . " [ id:" .  $id . " ]</h1>";
            
            // Get settings from the database if they exist...
            $php_check = $settings['text_widget_php'];
            $title = html_entity_decode(stripslashes($settings['text_widget_title']), ENT_QUOTES,'UTF-8');
            $content = html_entity_decode(stripslashes($settings['text_widget_content']), ENT_QUOTES,'UTF-8');
        
            //...otherwise set to blank:
            if (!$php_check) { $php_check = ''; }
              
            // The form should be submitted to the admin_index.php page:
            echo '<form name="text_widget_settings_form" action="' . BASEURL . 'admin_index.php?plugin=text_widget" method="post">' . "\n";
            
            echo '<input type="hidden" name="submitted" value="true" />' . "\n";
            echo '<input type="hidden" name="page" value="plugin_settings" />' . "\n";
            echo '<input type="hidden" name="text_widget_id" value="' . $id . '" />' . "\n";
            
            echo $h->lang["text_widget_title"] . ' <input type="text" size=30 name="text_widget_title" value="' . $title . '" /><br /><br />' . "\n";
            echo '<input type="checkbox" name="text_widget_php" ' . $php_check . ' /> ' . $h->lang['text_widget_php'] . '<br /><br />' . "\n";
            echo '<textarea name="text_widget_content" cols=60 rows = 15>' . $content . '</textarea>' . "\n";
            
            echo "<br /><br />";
            echo '<input type="submit" value="' . $h->lang['main_form_save'] . '" />' . "\n";
            echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
            echo '</form>' . "\n";
            
            $id++;
        }
        
        echo "<br />";
        
        echo "<a href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=text_widget&amp;action=delete_widget&amp;id=" . ($id-1) . "' style='color: red;'>" . $h->lang["text_widget_delete"] . "</a> | <a href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=text_widget&amp;action=new_widget&amp;id=" . ($id) . "'>" . $h->lang["text_widget_add"]  ."</a><br /><br />";
    }
}
?>