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
    				<!-- MAIN -->
    				<?php 	
					$result = $plugin->check_actions('theme_index_display');
					if(!isset($result) || !is_array($result)) {
	    					$page = $hotaru->get_page_name();
						$hotaru->display_template($page); 
					}
				?>
	    		</div>
    			<div class="yui-u">
    				
					<!-- SIDEBAR -->
					<?php echo $hotaru->display_template('sidebar'); ?>
				</ul>
	    		</div>
		</div>
	</div>
	<!-- FOOTER -->
	<?php echo $hotaru->display_template('footer'); ?>
