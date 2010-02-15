<?php
/**
 * name: Text Widget
 * description: Paste text or code into a blank widget
 * version: 0.5
 * folder: text_widget
 * class: TextWidget
 * requires: widgets 0.6
 * hooks: install_plugin, hotaru_header, admin_sidebar_plugin_settings, admin_plugin_settings
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
 *
 * EXAMPLE OF USING A TEMPLATE:
 * To use the example template, create a new text widget, check the PHP box and add this code:
 * $h->displayTemplate('example', 'text_widget');
 * Activate "Text Widget" in Widgets and you should see a new search box widget in your widget block
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

class TextWidget
{
    /**
     * Register text widget
     */
    public function install_plugin($h, $id)
    {
        if (!$id || !is_int($id)) { $id = 1; }
        
        $text_widget_settings['text_widget_title'] = 'New Text Widget';
        $text_widget_settings['text_widget_php'] = '';
        $text_widget_settings['text_widget_content'] = '';
        
        // parameters: plugin folder name, setting name, setting value
        if (!$h->getSetting('text_widget_' . $id . '_settings')) { 
            $h->updateSetting('text_widget_' . $id . '_settings', serialize($text_widget_settings), 'text_widget');
        }

        // widget
        $h->addWidget('text_widget', 'text_widget_' . $id, $id);  // plugin name, function name, optional arguments
    } 
    
    
    /**
     * Redirects to the main TextWidget function
     */
    public function widget_text_widget($h, $args)
    {
        $this->text_widget($h, array($args));
    }
    
    
    /**
     * Displays the text widget for the given ID
     *
     * @param array $ids
     */
    public function text_widget($h, $ids)
    {
        // if no widget id is specified, we default to 1.
        if (empty($ids)) { $ids[0] = 1; }
                   
        foreach ($ids as $id) { 
            
            // Get settings from the database:
            $settings = unserialize($h->getSetting('text_widget_' . $id . '_settings', 'text_widget')); 
            $title = html_entity_decode(stripslashes($settings['text_widget_title']), ENT_QUOTES,'UTF-8');
            $content = html_entity_decode(stripslashes($settings['text_widget_content']), ENT_QUOTES,'UTF-8');

            if ($settings['text_widget_title']) {
                echo "<h2 class='widget_head' id='text_widget_" . $id . "_head'>" . stripslashes($title) . "</h2>\n";
            }

            if ($settings['text_widget_php']) {
                echo "<div class='widget_body' id='text_widget_" . $id . "_body'>"; eval($content); echo "</div>\n";
            } else {
                echo "<div class='widget_body' id='text_widget_" . $id . "_body'>"; echo $content; echo "</div>\n";
            }

        }
        
    }
    
    
    /**
     * Display the contents of the plugin settings page.
     */
    public function admin_plugin_settings($h)
    {
        $this->get_params($h);    // get any arguments passed from the form
        $h->showMessage();    // display any success or failure messages

        require_once(PLUGINS . 'text_widget/text_widget_settings.php');
        $tw = new TextWidgetSettings();
        $tw->settings($h);
        return true;
    }


    /**
     * Get parameters passed by URL, e.g. a saved feed url. then save
     */
    public function get_params($h)
    {
        if ($action = $h->cage->get->testAlnumLines('action')) {
            if ($action == 'new_widget') {
                $id = $h->cage->get->getInt('id');
                $this->install_plugin($h, $id);
                $h->message = $h->lang["text_widget_added"];
                $h->messageType = "green";
                
            } elseif ($action == 'delete_widget') {
                $id = $h->cage->get->getInt('id');
                
                // delete from pluginsettings table:
                $h->deleteSettings('text_widget_' . $id . '_settings'); // setting
                
                // delete from widgets table:
                $h->deleteWidget('text_widget_' . $id); // function suffix
                
                // delete from "widgets_settings" in pluginsettings table;
                $widgets_settings = $h->getSerializedSettings('widgets'); 
                unset($widgets_settings['widgets']['text_widget_' . $id]);
                $h->updateSetting('widgets_settings', serialize($widgets_settings), 'widgets');
                
                $h->message = $h->lang["text_widget_removed"];
                $h->messageType = "green";
            }
        } elseif ($id = $h->cage->post->getInt('text_widget_id')) {
            $parameters = array();
            if ($h->cage->post->keyExists('text_widget_php')) { 
                $parameters['text_widget_php'] = 'checked';
            } else {
                $parameters['text_widget_php'] = '';
            }
            $parameters['text_widget_title'] = sanitize($h->cage->post->noTags('text_widget_title'), 'all');
            
            if (!get_magic_quotes_gpc()) {
                $parameters['text_widget_content'] = htmlentities($h->cage->post->getRaw('text_widget_content'), ENT_QUOTES,'UTF-8');
            }
            else {
                $parameters['text_widget_content'] = stripslashes(htmlentities($h->cage->post->getRaw('text_widget_content'), ENT_QUOTES,'UTF-8'));
            }
            
            $this->save_settings($h, $id, $parameters);
        }
    }
    
    
    /**
     * Save new or modified settings for this plugin
     *
     * @param int $id
     * @param array $parameters - an array of key-value pairs
     */
    public function save_settings($h, $id, &$parameters)
    {
        $h->message = "";
        if ($parameters) {
            if ($h->message == "") {
                $values = serialize($parameters);
                $h->message = $h->lang["text_widget_updated"];
                $h->messageType = "green";
                $h->updateSetting('text_widget_' . $id . '_settings', $values, 'text_widget');    
            }
        }
        
    }
    
}
?>