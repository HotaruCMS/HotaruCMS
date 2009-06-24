<?php 

/* ******* DEFAULT TEMPLATE *********
Theme name: default
Template name: index.php
Template author: Nick Ramsay
Version: 0.1
***************************** */

/* ******* USAGE ************
<?php echo $hotaru->display_template('TEMPLATE_NAME'); ?>	// e.g. header
***************************** */

global $hotaru, $plugin; // don't remove
?>

<!-- WHOLE PAGE-->
<?php
	$result = $plugin->check_actions('theme_index_replace');
	if(!isset($result) || !is_array($result)) {
?>
		<!-- HEADER-->
		<?php
			$result = $plugin->check_actions('theme_index_header');
			if(!isset($result) || !is_array($result)) {
				$hotaru->display_template('header');
			}
		?>
		
		<div id="bd" role="main">
			<?php if($hotaru->sidebar) { ?>
				<div class='yui-gc'> 
				<div class="yui-u first"'>
			<?php } else { ?>
				<div class='yui-g''>
	    			<div class="yui-u first" style='width: 100%;'>
	    		<?php } ?>
	    				<!-- MAIN -->
	    				<div id="main">
	    				<?php 	
						$result = $plugin->check_actions('theme_index_main');
						if(!isset($result) || !is_array($result)) {
		    					$page = $hotaru->get_page_name();
							$hotaru->display_template($page); 
						}
					?>
					</div>
		    		</div>
		    		<?php if($hotaru->sidebar) { ?>
		    			<div class="yui-u">
		    				
							<!-- SIDEBAR -->
							<?php
								$result = $plugin->check_actions('theme_index_sidebar');
								if(!isset($result) || !is_array($result)) {
									$hotaru->display_template('sidebar');
								}
							?>
						</ul>
			    		</div>
		    		<?php } ?>
			</div>
		</div>
		<!-- FOOTER -->
		<?php
			$result = $plugin->check_actions('theme_index_footer');
			if(!isset($result) || !is_array($result)) {
				$hotaru->display_template('footer');
			}
		?>
<?php	} ?>
