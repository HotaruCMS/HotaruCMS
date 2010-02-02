<?php
/**
 * name: Goodbye World
 * description: Displays "Goodbye World!"
 * version: 0.1
 * folder: goodbye_world
 * class: GoodbyeWorld
 * extends: HelloWorld
 * hooks: hello_world
 * requires: hello_world 0.5
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
 *
 * Usage: Add <?php $h->pluginHook('hello_world'); ?> to your theme, wherever you want to show "Goodbye World".
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

class GoodbyeWorld extends HelloWorld
{
    /**
     * Displays "Goodbye World!" wherever the plugin hook is.
     */
    public function hello_world()
    {
        echo "Goodbye World!";
    }
}

?>
