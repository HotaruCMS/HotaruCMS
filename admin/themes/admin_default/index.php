<?php 

/* ******* ADMIN TEMPLATE *********
Theme name: admin_default
Template name: index.php
Template author: Nick Ramsay
Version: 0.1
***************************** */

/* ******* USAGE ************
<?php echo $hotaru->display_admin_template('TEMPLATE_NAME'); ?>		// e.g. header
<?php echo $hotaru->display_admin_links(); ?>				// Admin menu
***************************** */

global $hotaru, $plugin; // don't remove
?>
<!-- HEADER-->
<?php echo $hotaru->display_admin_template('header'); ?>

	<div id="bd" role="main">
		<div class="yui-gf">
    			<div class="yui-u">
    				<!-- MAIN -->
    					<?php
						$result = $plugin->check_actions('admin_theme_index_display');
						if(!isset($result) || !is_array($result)) {
		    					$page = $hotaru->get_page_name();
							$hotaru->display_admin_template($page); 
						} 	
					?>			
	    		</div>
    			<div class="yui-u first">
    					<!-- SIDEBAR -->
					<?php echo $hotaru->display_admin_template('sidebar'); ?>
	    		</div>
		</div>
	</div>
	<!-- FOOTER -->
	<?php echo $hotaru->display_admin_template('footer'); ?>