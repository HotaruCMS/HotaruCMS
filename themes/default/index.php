<?php 

/* ******* TEMPLATE *********
Template name: default
Template author: Nick Ramsay
Version: 0.1
Last updated: June 6th 2009
***************************** */

/* ******* USAGE ************
<?php echo $hotaru->display_template('TEMPLATE_NAME'); ?>			// e.g. header
<?php echo $hotaru->display_stories(10, 'latest'); ?>				// number, latest/popular
<?php echo $hotaru->display_story_links(10, 'upcoming', '<li>', '</li>'); ?>	// number, topstories/upcoming, before tag, after tag
***************************** */

global $hotaru; // don't remove
?>

<?php echo $hotaru->display_template('header'); ?>

<div id="doc2" class="yui-t7">
	<div id="hd" role="banner">
		<img src="<?php echo baseurl; ?>/themes/default/images/hotaru_468x60.png"><br />
		<ul id="navigation"><li>Top Stories</li><li>Upcoming</li><li><a href="<?php echo baseurl; ?>admin/admin_index.php">Admin</a></li></ul>
	</div>
	<div id="bd" role="main">
		<div class="yui-gc">
    			<div class="yui-u first">
    				<div id="index_stories">
					<?php echo $hotaru->display_stories(10, 'latest'); ?>
				</div>
	    		</div>
    			<div class="yui-u">
    				<ul>
    					<div id="sidebar_stories">
						<?php echo $hotaru->display_story_links(10, 'upcoming', '<li>', '</li>'); ?>
					</div>
				</ul>
	    		</div>
		</div>
	</div>
	<div id="ft" role="contentinfo">
		<p>Footer</p>
	</div>
</div>

<?php echo $hotaru->display_template('footer'); ?>
