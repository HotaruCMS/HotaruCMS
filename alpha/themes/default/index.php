<?php 

/* ******* TEMPLATE ******************************************************************************** 
 * Theme name: default
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

global $hotaru, $plugin; // don't remove

// plugin hook
$result = $plugin->check_actions('theme_index_replace');
if(!isset($result) || !is_array($result)) {
?>
		<!-- HEADER-->
		<?php
			// plugin hook
			$result = $plugin->check_actions('theme_index_header');
			if(!isset($result) || !is_array($result)) {
				$hotaru->display_template('header');
			}
		?>
		
		<div id="bd" role="main">
			<?php if($hotaru->sidebar) { ?>
				<div class='yui-gc'> 
				<div class="yui-u first"'>
			<?php } else { ?>
				<div class='yui-g''>
	    			<div class="yui-u first" style='width: 100%;'>
	    		<?php } ?>
	    				<!-- MAIN -->
	    				<div id="main">
	    				<?php 	
	    					// plugin hook
						$result = $plugin->check_actions('theme_index_main');
						if(!isset($result) || !is_array($result)) {
		    					$page = $hotaru->get_page_name();
							$hotaru->display_template($page); 
						}
					?>
					</div>
		    		</div>
		    		<?php if($hotaru->sidebar) { ?>
		    			<div class="yui-u">
		    				
							<!-- SIDEBAR -->
							<?php
								// plugin hook
								$result = $plugin->check_actions('theme_index_sidebar');
								if(!isset($result) || !is_array($result)) {
									$hotaru->display_template('sidebar');
								}
							?>
						</ul>
			    		</div>
		    		<?php } ?>
			</div>
		</div>
		<!-- FOOTER -->
		<?php
			// plugin hook
			$result = $plugin->check_actions('theme_index_footer');
			if(!isset($result) || !is_array($result)) {
				$hotaru->display_template('footer');
			}
		?>
<?php	} ?>