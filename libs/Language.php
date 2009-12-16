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
     * @param string $pack - either "main" or "default"
     * @return array $lang_array
     */
    public function includeLanguagePack($lang_array = array(), $pack = 'main')
    {
        if ($pack == 'install') {
            include_once(INSTALL . 'install_language.php');    // language file for install
        } 
        elseif (file_exists(LANGUAGES . LANGUAGE_PACK . $pack . '_language.php'))
        {
            // language file from the chosen language pack
            include_once(LANGUAGES . LANGUAGE_PACK . $pack . '_language.php');
        }
        else 
        {
           // try the default language pack
            include_once(LANGUAGES . 'language_default/' . $pack . '_language.php'); 
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
    public function includeLanguage($hotaru, $folder = '', $filename = '')
    {
        if (!$folder) { $folder = $hotaru->plugin->folder; }
        
        if ($folder) {
        
            // If not filename given, make the plugin name the file name
            if (!$filename) { $filename = $folder; }
            
            // First, look in the user's language_pack folder for a language file...
            if (file_exists(LANGUAGES . LANGUAGE_PACK . $filename . '_language.php')) {
                include_once(LANGUAGES . LANGUAGE_PACK . $filename . '_language.php');
                
            // If not there, look in the default language_pack folder for a language file...
            } elseif (file_exists(LANGUAGES . 'language_default/' . $filename . '_language.php')) {
                include_once(LANGUAGES . 'language_default/' . $filename . '_language.php');
    
            // If still not found, look in the plugin folder for a language file... 
            } elseif (file_exists(PLUGINS . $folder . '/languages/' . $filename . '_language.php')) {
                include_once(PLUGINS . $folder . '/languages/' . $filename . '_language.php');
            
            // If STILL not found, include the user's main language file...
            } elseif (file_exists(LANGUAGES . LANGUAGE_PACK . 'main_language.php')) {
                include_once(LANGUAGES . LANGUAGE_PACK . 'main_language.php');
    
            // Finally, give up and include the main default language file...
            } else {
                include_once(LANGUAGES . 'language_default/main_language.php');
            }
            
            
            // Add new language to our lang property
            if (isset($lang)) {
                foreach($lang as $l => $text) {
                    $hotaru->lang[$l] = $text;
                }
            }
        }
    }
}
?>
