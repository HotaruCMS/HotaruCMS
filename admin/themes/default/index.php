<?php 

/* ******* TEMPLATE *********
Template name: default
Template author: Nick Ramsay
Version: 0.1
Last updated: June 6th 2009
***************************** */

/* ******* USAGE ************
<?php echo $hotaru->display_template('TEMPLATE_NAME.php'); ?>			// e.g. header.php
<?php echo $hotaru->display_stories(10, 'latest'); ?>				// number, latest/popular
<?php echo $hotaru->display_story_links(10, 'upcoming', '<li>', '</li>'); ?>	// number, topstories/upcoming, before tag, after tag
***************************** */

global $hotaru; // don't remove
?>

<?php echo $hotaru->display_admin_template('header.php'); ?>

<div id="doc2" class="yui-t7">
	<div id="hd" role="banner">
		<img src="<?php echo baseurl; ?>/admin/themes/default/images/hotaru_468x60.png"><br />
		<ul id="navigation"><li><a href="<?php echo baseurl; ?>/index.php">Top Stories</a></li><li>Upcoming</li></ul>
	</div>
	<div id="bd" role="main">
		<div class="yui-gf">
    			<div class="yui-u first">
    				<ul>
    					<div id="sidebar_links">
						<?php echo $hotaru->display_admin_links(); ?>
					</div>
				</ul>
	    		</div>
    			<div class="yui-u">
    				<div id="index_stories">
					<h2>Hotaru Admin Control Panel</h2>
					<p>You've found the admin section...</p>
				</div>    				
	    		</div>
		</div>
	</div>
	<div id="ft" role="contentinfo">
		<p>Footer</p>
	</div>
</div>

<?php echo $hotaru->display_admin_template('footer.php'); ?>
