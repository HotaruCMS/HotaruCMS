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
}
?>