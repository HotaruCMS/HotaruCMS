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
    			<div class="yui-u first">
    				<ul id="sidebar">
    					<!-- SIDEBAR -->
					<?php echo $hotaru->display_admin_template('sidebar'); ?>
				</ul>
	    		</div>
    			<div class="yui-u">
    				<div id="index_stories">
     					<?php if($hotaru->is_admin_home) { ?>
						<h2>Hotaru Admin Control Panel &raquo; Admin Home</h2>
					<?php } elseif($hotaru->is_admin_plugins) { ?>
						<h2>Hotaru Admin Control Panel &raquo; Plugin Management</h2>
						<?php echo $hotaru->display_admin_template('plugins'); ?>
					<?php } ?>
				</div>    				
	    		</div>
		</div>
	</div>
	<div id="ft" role="contentinfo">
		<p>Footer</p>
	</div>
</div>

<?php echo $hotaru->display_admin_template('footer'); ?>
