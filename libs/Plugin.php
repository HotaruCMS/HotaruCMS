<?php
/**
 * Plugin Class
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
class Plugin
{
	protected $id               = 0;
	protected $folder           = '';
	protected $enabled          = 0;
	protected $name             = '';
	protected $class            = '';
	protected $extends          = '';
	protected $type             = '';
	protected $desc             = '';
	protected $version          = 0;
	protected $order            = 0;
	protected $author           = '';
	protected $authorurl        = '';
	protected $requires         = '';
	protected $dependencies     = array();
	protected $hooks            = array();
	protected $latest_version   = 0.0;
	
	/**
	 * Access modifier to set protected properties
	 */
	public function __set($var, $val)
	{
		$this->$var = $val;
	}
	
	
	/**
	 * Access modifier to get protected properties
	 * The & is necessary (http://bugs.php.net/bug.php?id=39449)
	 */
	public function &__get($var)
	{
		return $this->$var;
	}
}
?>
