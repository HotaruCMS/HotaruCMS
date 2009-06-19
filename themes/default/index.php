<?php 

/* ******* DEFAULT TEMPLATE *********
Theme name: default
Template name: index.php
Template author: Nick Ramsay
Version: 0.1
***************************** */

/* ******* USAGE ************
<?php echo $hotaru->display_template('TEMPLATE_NAME'); ?>			// e.g. header
<?php echo $hotaru->display_stories(10, 'latest'); ?>				// number, latest/popular
<?php echo $hotaru->display_story_links(10, 'upcoming', '<li>', '</li>'); ?>	// number, topstories/upcoming, before tag, after tag
***************************** */

global $hotaru, $plugin; // don't remove
?>
<!-- HEADER-->
<?php echo $hotaru->display_template('header'); ?>

	<div id="bd" role="main">
		<div class="yui-gc">
    			<div class="yui-u first">
    				<div id="index_stories">
					<?php echo $hotaru->display_stories(10, 'latest'); ?>
				</div>
	    		</div>
    			<div class="yui-u">
    				<ul>
    					<div id="sidebar">
    						<!-- SIDEBAR -->
						<?php echo $hotaru->display_template('sidebar'); ?>
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
