<?php
/**
 * name: Text Widget
 * description: Paste text or code into a blank widget
 * version: 0.3
 * folder: text_widget
 * class: TextWidget
 * requires: sidebar_widgets 0.5
 * hooks: install_plugin, hotaru_header, admin_sidebar_plugin_settings, admin_plugin_settings
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

class TextWidget extends PluginFunctions
{
    /**
     * Register text widget
     */
    public function install_plugin($id)
    {
        if (!$id || !is_int($id)) { $id = 1; }
        
        $text_widget_settings['text_widget_title'] = 'New Text Widget';
        $text_widget_settings['text_widget_php'] = '';
        $text_widget_settings['text_widget_content'] = '';
        
        // parameters: plugin folder name, setting name, setting value
        if (!$this->getSetting('text_widget_' . $id . '_settings')) { 
            $this->updateSetting('text_widget_' . $id . '_settings', serialize($text_widget_settings), 'text_widget');
        }
        
        require_once(PLUGINS . 'sidebar_widgets/libs/Sidebar.php');
        $sidebar = new Sidebar($this->hotaru);
        $sidebar->addWidget('text_widget', 'text_widget_' . $id, $id); // plugin name, function name, optional arguments
    } 
    
    
    /**
     * Redirects to the main TextWidget function
     */
    public function sidebar_widget_text_widget($args)
    {
        $this->text_widget(array($args));
    }
    
    
    /**
     * Displays the text widget for the given ID
     *
     * @param array $ids
     */
    public function text_widget($ids)
    {
        // if no widget id is specified, we default to 1.
        if (empty($ids)) { $ids[0] = 1; }
                   
        foreach ($ids as $id) { 
            
            // Get settings from the database:
            $settings = unserialize($this->getSetting('text_widget_' . $id . '_settings', 'text_widget')); 
            $content = html_entity_decode(stripslashes($settings['text_widget_content']), ENT_QUOTES,'UTF-8');

            if ($settings['text_widget_title']) {
                echo "<h2 class='sidebar_widget_head'>" . stripslashes($settings['text_widget_title']) . "</h2>\n";
            }

            if ($settings['text_widget_php']) {
                echo "<div class='sidebar_widget_body'>"; eval($content); echo "</div>\n";
            } else {
                echo "<div class='sidebar_widget_body'>"; echo $content; echo "</div>\n";
            }

        }
        
    }
    
    
    /**
     * Display the contents of the plugin settings page.
     */
    public function admin_plugin_settings()
    {
        $this->get_params();    // get any arguments passed from the form
        $this->hotaru->showMessage();    // display any success or failure messages

        require_once(PLUGINS . 'text_widget/text_widget_settings.php');
        $tw = new TextWidgetSettings($this->folder, $this->hotaru);
        $tw->settings();
        return true;
    }


    /**
     * Get parameters passed by URL, e.g. a saved feed url. then save
     */
    public function get_params()
    {
        if ($action = $this->cage->get->testAlnumLines('action')) {
            if ($action == 'new_widget') {
                $id = $this->cage->get->getInt('id');
                $this->install_plugin($id);
                $this->hotaru->message = $this->hotaru->lang["text_widget_added"];
                $this->hotaru->messageType = "green";
                
            } elseif ($action == 'delete_widget') {
                $id = $this->cage->get->getInt('id');
                
                // delete from pluginsettings table:
                $this->deleteSettings('text_widget_' . $id . '_settings'); // setting
                
                // delete from widgets table:
                require_once(PLUGINS . 'sidebar_widgets/libs/Sidebar.php');
                $sidebar = new Sidebar($this->hotaru);
                $sidebar->deleteWidget('text_widget_' . $id); // function suffix
                
                // delete from "sidebar_settings" in pluginsettings table;
                $sidebar_settings = $sidebar->getSidebarSettings();
                unset($sidebar_settings['sidebar_widgets']['text_widget_' . $id]);
                $this->updateSetting('sidebar_settings', serialize($sidebar_settings), 'sidebar_widgets');
                
                $this->hotaru->message = $this->hotaru->lang["text_widget_removed"];
                $this->hotaru->messageType = "green";
            }
        } elseif ($id = $this->cage->post->getInt('text_widget_id')) {
            $parameters = array();
            if ($this->cage->post->keyExists('text_widget_php')) { 
                $parameters['text_widget_php'] = 'checked';
            } else {
                $parameters['text_widget_php'] = '';
            }
            $parameters['text_widget_title'] = $this->cage->post->noTags('text_widget_title');
            $parameters['text_widget_content'] = htmlentities(stripslashes($this->cage->post->getRaw('text_widget_content')), ENT_QUOTES,'UTF-8');
            $this->save_settings($id, $parameters);
        }
    }
    
    
    /**
     * Save new or modified settings for this plugin
     *
     * @param int $id
     * @param array $parameters - an array of key-value pairs
     */
    public function save_settings($id, &$parameters)
    {
        $this->hotaru->message = "";
        if ($parameters) {
            if ($this->hotaru->message == "") {
                $values = serialize($parameters);
                $this->hotaru->message = $this->hotaru->lang["text_widget_updated"];
                $this->hotaru->messageType = "green";
                $this->updateSetting('text_widget_' . $id . '_settings', $values, 'text_widget');    
            }
        }
        
    }
    
}
?>