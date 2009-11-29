<?php
/**
 * name: Bookmarklet
 * description: A way for users to submit posts on the fly
 * version: 0.1
 * folder: bookmarklet
 * class: Bookmarklet
 * hooks: hotaru_bookmarklet
 * requires: submit: 1.7
 *
 * Usage: Put <?php $hotaru->plugins->pluginHook('hotaru_bookmarklet'); ?> 
 *        in your template where you want the bookmarklet link to show.
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


class Bookmarklet extends PluginFunctions
{
    public function hotaru_bookmarklet()
    {
        $this->includeLanguage();
        $this->hotaru->displayTemplate('bookmarklet', 'bookmarklet');
    }

}