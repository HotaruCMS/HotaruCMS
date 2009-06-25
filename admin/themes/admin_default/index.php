<?php 

/* ******* ADMIN TEMPLATE *********
Theme name: admin_default
Template name: index.php
Template author: Nick Ramsay
Version: 0.1
***************************** */

/* ******* USAGE ************
<?php echo $hotaru->display_admin_template('TEMPLATE_NAME'); ?>		// e.g. header
***************************** */

global $hotaru, $plugin; // don't remove
?>

<!-- WHOLE PAGE-->
<?php
	$result = $plugin->check_actions('admin_theme_index_replace');
	if(!isset($result) || !is_array($result)) {
?>
		<!-- HEADER-->
		<?php
			$result = $plugin->check_actions('admin_theme_index_header');
			if(!isset($result) || !is_array($result)) {
				$hotaru->display_admin_template('header');
			}
		?>
	
		<div id="bd" role="main">  			
			<?php if($hotaru->sidebar) { ?>
				<div class='yui-gf'> 
				<div class="yui-u"'>
			<?php } else { ?>
				<div class='yui-g''>
	    			<div class="yui-u" style='width: 100%;'>
	    		<?php } ?>
	    				<!-- MAIN -->
    					<div id="main">
    					<?php
						$result = $plugin->check_actions('admin_theme_index_display');
						if(!isset($result) || !is_array($result)) {
		    					$page = $hotaru->get_page_name();
		    					if($page == 'admin_login') {
		    						admin_login();
		    					} else {
								$hotaru->display_admin_template($page);
							} 
						} 	
					?>	
					</div>		
		    		</div>
		    		<?php if($hotaru->sidebar) { ?>
		    			<div class="yui-u first">
						<!-- SIDEBAR -->
						<?php
							$result = $plugin->check_actions('admin_theme_index_sidebar');
							if(!isset($result) || !is_array($result)) {
								$hotaru->display_admin_template('sidebar');
							}
						?>
			    		</div>
		    		<?php } ?>
			</div>
		</div>
		<!-- FOOTER -->
		<?php
			$result = $plugin->check_actions('admin_theme_index_footer');
			if(!isset($result) || !is_array($result)) {
				$hotaru->display_admin_template('footer');
			}
		?>
<?php	} ?>