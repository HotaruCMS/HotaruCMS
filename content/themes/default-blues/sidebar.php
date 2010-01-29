<?php 
/**
 * Theme name: Default Blues
 * Template name: sidebar.php
 * Template author: Jason F. Irwin
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

<!-- SIDEBAR -->
<div id="sidebar">

<?php
/**
	<!-- SEARCH -->
	<form action="<?php echo get_option('home'); ?>">
	<input type="text" name="s" id="s" value="<?php the_search_query(); ?>" />
	<button type="submit">Submit</button>
	</form>
	<!-- End Search -->
 */
?>

	<!-- WIDGETS -->
	<div class="box">
		<?php $h->pluginHook('widget_block', '', array(1)); ?>
	</div>
	<!-- End Widgets -->

</div>
<!-- End Sidebar -->