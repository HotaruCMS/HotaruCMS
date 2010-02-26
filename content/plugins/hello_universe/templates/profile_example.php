<?php 
/**
 * Plugin name: Hello Universe
 * Template name: profile_example.php
 * Template author: Nick Ramsay
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

?>

<h2>A new user page!</h2>

<p>This is the profile_example.php file in the Hello Universe plugin folder.</p>

<p>The title was set and user info retrieved in the <i>theme_index_top</i> function (Function #1).</p>
<p>The page was displayed from the <i>theme_index_main</i> function (Function #2).</p>
<p>The link to this page in the menu was done in the <i>profile_navigation</i> function (Function #6).</p>
<p>The link to this page in the menu was done in the <i>breadcrumbs</i> function (Function #7).</p>


<p><a href="<?php echo BASEURL; ?>"><?php echo $h->lang["hello_universe_back_home"]; ?></a></p>
