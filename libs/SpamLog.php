<?php
/**
 * Functions for checking spam log for Hotaru installation
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
 * @author    shibuya246 <blog@shibuya246.com>
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
namespace Libs;

class SpamLog extends Prefab
{
    const registration = 1;
    const comments = 2;
    const adminTest = 10;
    const otherTest = 11;
    
    /**
     * Add to spam log
     *
     */
    public function add($h, $pluginFolder, $type, $email = '')
    {
            if (!$pluginFolder) {
                return false;
            }

            $result = \Hotaru\Models2\SpamLog::add($h, $pluginFolder, $type, $email);
            return $result;
    }
    
    
    /**
     * Get all from spam log
     *
     */
    public function getAll($h)
    {
            $result = \Hotaru\Models2\SpamLog::getAll($h);
            return $result;
    }
    
    
    /**
     * Get all from spam log
     *
     */
    public function get($h, $pluginFolder)
    {
            if (!$pluginFolder) {
                $pluginFolder = $h->plugin;
            }
            
            $result = \Hotaru\Models2\SpamLog::get($h, $pluginFolder);
            return $result;
    }
    
    
    /**
     * Get all from spam log
     *
     */
    public function count($h, $pluginFolder)
    {
            if (!$pluginFolder) {
                $pluginFolder = $h->plugin;
            }
            
            $result = \Hotaru\Models2\SpamLog::count($h, $pluginFolder);
            return $result;
    }
   
}
