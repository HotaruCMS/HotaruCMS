<?php
/**
 * name: Link Bar Example
 * description: Coding example for extending link Bar
 * version: 0.1
 * folder: link_bar_example
 * class: LinkBarExample
 * requires: link_bar 0.1
 * hooks: install_plugin, link_bar_settings_form, link_bar_save_settings, link_bar_post
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
 * @copyright Copyright (c) 2010
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://hotarucms.org
 */
class LinkBarExample
{
    /**
     * Install the plugin, add setting to Link Bar settings
     */
    public function install_plugin($h)
    {
        // get settings
        $link_bar_settings = $h->getSerializedSettings('link_bar');
        
        // add default settingif not already set
        if (!isset($link_bar_settings['link_bar_example_text'])) { $link_bar_settings['link_bar_example_text'] = "Hello World!"; }
        
        // update link bar settings
        $h->updateSetting('link_bar_settings', serialize($link_bar_settings), 'link_bar');
    }
    
    
    /**
     * Get current setting and show form field
     */
    public function link_bar_settings_form($h)
    {
        // if for some reason our default isn't found, set it here:
        if (!isset($h->vars["link_bar_example_text"])) { $h->vars["link_bar_example_text"] = "Hello World!"; }
        
        echo "<p>" . $h->lang["link_bar_example_text"] . "&nbsp;";
        echo "<input type='text' name='lbe_text' size=40 value='" . $h->vars["link_bar_example_text"] . "'></p>\n";
    }
    
    
    /**
     * Check form result and save or show error
     */
    public function link_bar_save_settings($h)
    {
        // get submitted data
        $example_text = $h->cage->post->getHtmLawed('lbe_text');
        if ($example_text) {
            $h->vars["link_bar_example_text"] = $example_text;
        } else {
            $h->vars['link_bar_error'] = true;
            $h->messages[$h->lang["link_bar_example_error"]] = "red";
        }
    }
    

    /**
     * Display value entered on the settings page in the bar
     */
    public function link_bar_post($h)
    {
        echo $h->vars['link_bar_settings']['link_bar_example_text'];
    }
}