<?php 

/* ******* ADMIN TEMPLATE ************************************************************************** 
 * Theme name: admin_default
 * Template name: home.php
 * Template author: Nick Ramsay
 * Version: 0.1
 * License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */

global $hotaru; // don't remove
?>

<h2><a href="<?php echo baseurl . url(array(), 'admin'); ?>"><?php echo site_name;?> Admin Control Panel</a> &raquo; Admin Home</h2>

<div class="admin_header admin_header_space"><a href="http://feeds2.feedburner.com/hotarucms"><img src="<?php echo baseurl ?>admin/themes/admin_default/images/rss_16.gif"></a>&nbsp; Latest from Hotaru CMS</div>
<?php echo admin_news(); ?>
