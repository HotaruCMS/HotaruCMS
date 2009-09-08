<?php
/**
 * Gateway to the rest of Hotaru
 *
 * Displays the index template in the chosen theme
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
// includes
require_once('hotaru_header.php');

// Include "main" language file
if (file_exists(languages . language_pack . 'main/main_language.php'))
{
    // language file from the chosen language pack
    include_once(languages . language_pack . 'main/main_language.php');
}
else 
{
   // try the default language pack
    require_once(languages . 'language_default/main/main_language.php'); 
}

$hotaru->display_template('index');
?>