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

global $hotaru; // don't remove
?>
<!-- HEADER-->
<?php echo $hotaru->display_admin_template('header'); ?>

	<div id="bd" role="main">
		<div class="yui-gf">
    			<div class="yui-u">
    				<!-- MAIN -->
					<?php if($hotaru->is_admin_plugins) { ?>
						<?php echo $hotaru->display_admin_template('plugins'); ?>
					<?php } elseif($hotaru->is_admin_plugin_settings) { ?>
						<?php echo $hotaru->display_admin_template('plugin_settings'); ?>
					<?php } else {?>
						<?php echo $hotaru->display_admin_template('home'); ?>
					<?php } ?>    				
	    		</div>
    			<div class="yui-u first">
    					<!-- SIDEBAR -->
					<?php echo $hotaru->display_admin_template('sidebar'); ?>
	    		</div>
		</div>
	</div>
	<!-- FOOTER -->
	<?php echo $hotaru->display_admin_template('footer'); ?>