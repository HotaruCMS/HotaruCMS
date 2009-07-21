<?php 

/* ******* ADMIN TEMPLATE ************************************************************************** 
 * Theme name: admin_default
 * Template name: index.php
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

global $hotaru, $admin, $plugin, $current_user, $lang; // don't remove

// plugin hook
$result = $plugin->check_actions('admin_theme_index_replace');
if(!isset($result) || !is_array($result)) {
?>
		<!-- HEADER-->
		<?php
			// plugin hook
			$result = $plugin->check_actions('admin_theme_index_header');
			if(!isset($result) || !is_array($result)) {
				$admin->display_admin_template('header');
			}
		?>
	
		<div id="bd" role="main">  			
			<?php if($admin->sidebar) { ?>
				<div class='yui-gf'> 
				<div class="yui-u"'>
			<?php } else { ?>
				<div class='yui-g''>
	    			<div class="yui-u" style='width: 100%;'>
	    		<?php } ?>
	    				<!-- MAIN -->
    					<div id="main">
    					<?php
    						// plugin hook
						$result = $plugin->check_actions('admin_theme_index_display');
						if(!isset($result) || !is_array($result)) {
		    					$page = $hotaru->get_page_name();
		    					if($page == 'admin_login') {
		    						if($current_user->logged_in) {
		    							$admin->display_admin_template('main');
		    						} else {
		    							admin_login_form();
		    						}
		    					} else {
								$admin->display_admin_template($page);
							} 
						} 	
					?>	
					</div>		
		    		</div>
		    		<?php if($hotaru->sidebar) { ?>
		    			<div class="yui-u first">
						<!-- SIDEBAR -->
						<?php
							// plugin hook
							$result = $plugin->check_actions('admin_theme_index_sidebar');
							if(!isset($result) || !is_array($result)) {
								$admin->display_admin_template('sidebar');
							}
						?>
			    		</div>
		    		<?php } ?>
			</div>
		</div>
		<!-- FOOTER -->
		<?php
			// plugin hook
			$result = $plugin->check_actions('admin_theme_index_footer');
			if(!isset($result) || !is_array($result)) {
				$admin->display_admin_template('footer');
			}
		?>
<?php	} ?>