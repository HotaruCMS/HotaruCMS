<?php
/**
 * Language functions
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
class Language
{
    /**
     * Include language pack
     *
     * @param string $pack - either "main" or "admin"
     * @return array $lang_array
     */
    public function includeLanguagePack($lang_array = array(), $pack = 'main')
    {
        if ($pack == 'install') {
            include_once(INSTALL . 'install_language.php');    // language file for install
        } 
        elseif (file_exists(BASE . 'content/' . $pack . '_language.php'))
        {
            // include language file
            include_once(BASE . 'content/' . $pack . '_language.php');
        }
        
        // Add new language to our lang property
        if (isset($lang)) {
            foreach($lang as $l => $text) {
                $lang_array[$l] = $text;
            }
        }
        
        return $lang_array;
    }
    
    
    /**
     * Include a language file in a plugin
     *
     * @param string $folder name of plugin folder
     * @param string $filename optional filename without file extension
     *
     * Note: the language file should be in a plugin folder named 'languages'.
     * '_language.php' is appended automatically to the folder of file name.
     */    
    public function includeLanguage($h, $folder = '', $filename = '')
    {
        if (!$folder) { $folder = $h->plugin->folder; }
        
        if ($folder) {
        
            // If not filename given, make the plugin name the file name
            if (!$filename) { $filename = $folder; }
            
            // First, look in the user's theme languages folder for a language file...
            if (file_exists(THEMES . THEME . 'languages/' . $filename . '_language.php')) {
                include_once(THEMES . THEME . 'languages/' . $filename . '_language.php');
                
            // If still not found, look in the plugin folder for a language file... 
            } elseif (file_exists(PLUGINS . $folder . '/languages/' . $filename . '_language.php')) {
                include_once(PLUGINS . $folder . '/languages/' . $filename . '_language.php');
            }
            
            // Add new language to our lang property
            if (isset($lang)) {
                foreach($lang as $l => $text) {
                    $h->lang[$l] = $text;
                }
            }
        }
    }
    
    
    /**
     * Include a language file for a theme
     *
     * @param string $filename optional filename without '_language.php' file extension
     *
     * Note: the language file should be in a plugin folder named 'languages'.
     * '_language.php' is appended automatically to the folder of file name.
     */    
    public function includeThemeLanguage($h, $filename = 'main')
    {
        // Look in the current theme a language file...
        if (file_exists(THEMES . THEME . 'languages/' . $filename . '_language.php')) {
            include_once(THEMES . THEME . 'languages/' . $filename . '_language.php');

            // Add new language to our lang property
            if (isset($lang)) {
                foreach($lang as $l => $text) {
                    $h->lang[$l] = $text;
                }
            }
        }
    }
}
?>
