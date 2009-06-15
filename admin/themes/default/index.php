<?php 

/* ******* ADMIN TEMPLATE *********
Theme name: default
Template name: index.php
Template author: Nick Ramsay
Version: 0.1
Last updated: June 15th 2009
***************************** */

/* ******* USAGE ************
<?php echo $hotaru->display_admin_template('TEMPLATE_NAME.php'); ?>		// e.g. header.php
<?php echo $hotaru->display_admin_links(); ?>					// Admin menu
***************************** */

global $hotaru; // don't remove
?>

<?php echo $hotaru->display_admin_template('header.php'); ?>

<div id="doc2" class="yui-t7">
	<div id="hd" role="banner">
		<img src="<?php echo baseurl; ?>admin/themes/default/images/hotaru_468x60.png"><br />
		<ul id="navigation"><li><a href="<?php echo baseurl; ?>index.php">Top Stories</a></li><li>Upcoming</li></ul>
	</div>
	<div id="bd" role="main">
		<div class="yui-gf">
    			<div class="yui-u first">
    				<ul id="sidebar_links">
						<?php echo $hotaru->display_admin_template('sidebar.php'); ?>
				</ul>
	    		</div>
    			<div class="yui-u">
    				<div id="index_stories">
    					<?php if($hotaru->get_page() == 'index.php') { ?>
						<h2>Hotaru Admin Control Panel</h2>
						<p>You're in ADMIN INDEX.PHP</p>
					<?php } elseif($hotaru->get_page() == 'plugins.php') { ?>
						<h2>Hotaru Admin Control Panel</h2>
						<p>You're in ADMIN PLUGINS.PHP</p>
						<?php echo $hotaru->display_admin_template('plugins.php'); ?>
					<?php } ?>
				</div>    				
	    		</div>
		</div>
	</div>
	<div id="ft" role="contentinfo">
		<p>Footer</p>
	</div>
</div>

<?php echo $hotaru->display_admin_template('footer.php'); ?>
