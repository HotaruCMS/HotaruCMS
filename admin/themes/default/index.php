<?php 

/* ******* ADMIN TEMPLATE *********
Theme name: default
Template name: index.php
Template author: Nick Ramsay
Version: 0.1
Last updated: June 15th 2009
***************************** */

/* ******* USAGE ************
<?php echo $hotaru->display_admin_template('TEMPLATE_NAME'); ?>		// e.g. header
<?php echo $hotaru->display_admin_links(); ?>				// Admin menu
***************************** */

global $hotaru; // don't remove
?>

<?php echo $hotaru->display_admin_template('header'); ?>

<div id="doc2" class="yui-t7">
	<div id="hd" role="banner">
		<img src="<?php echo baseurl; ?>admin/themes/default/images/hotaru_468x60.png"><br />
		<ul id="navigation"><li><a href="<?php echo baseurl; ?>index.php">Top Stories</a></li><li>Upcoming</li></ul>
	</div>
	<div id="bd" role="main">
		<div class="yui-gf">
    			<div class="yui-u first">
    				<ul id="sidebar_links">
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
