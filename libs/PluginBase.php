<?php

/**
 * PluginBase functions
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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
namespace Hotaru\Plugins;

abstract class PluginBase extends \Hotaru\Base implements PluginInterface
{
        protected $id;                          // id
        protected $folder;                      // folder name
	protected $class;                       // class
	protected $settings;                    // serialised settings
	
	/**
	 * Initialize Plugin with the essentials
	 */
	public function __construct()
	{print 'pluginbase here';
            $this->settings = isset($h->pluginSettings[$this->folder]) ? $h->pluginSettings[$this->folder] : null;
        }
}

interface PluginInterface
{
    public function install_plugin($h);
    
            
}