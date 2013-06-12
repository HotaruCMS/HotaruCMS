<?php 
/**
 * Theme name: admin_default
 * Template name: access_denied.php
 * Template author: shibuya246
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

?>
<?php // turn off admin so we can jump to THEME templates instead ?>
<?php $h->adminPage = false; ?>
<?php $h->template('header'); ?>

<div class="container">
            <div class="row">
		<div id="content">
			
				<?php echo $h->showMessages(); ?>
			
		</div>
            </div>
</div>

<!-- FOOTER -->
<?php //$h->template('footer'); ?>
