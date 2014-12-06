
		<!-- Step title -->
		<legend><?php echo $lang['upgrade_step3']; ?></legend>
		
		<!-- Complete Step Progress Bar -->
		<div class='progress'>
			<div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width: 100%'>
				<span class='sr-only'>100% Complete</span>
			</div>
		</div>
		<p class='text-success'><strong><i class='fa fa-check'></i> <?php echo $lang['upgrade_step3_details']; ?></strong></p>

		<?php $h->showMessages(); ?>

		<div class='well'><?php echo $lang['upgrade_step3_instructions']; ?></div>
		
		<div class='alert alert-danger' role='alert'>
			<?php echo $lang['install_step4_installation_delete']; ?>
		</div>
			
		<p class='text-success'><?php echo $lang['upgrade_step3_go_play']; ?></p><br>
		
		<div class='form-actions'>
			<!-- Previous/Next buttons -->
			<a href='index.php?step=3&action=upgrade' class='btn btn-default' role='button'><i class='fa fa-arrow-left'></i> <?php echo $lang['install_back']; ?></a>
			<a href='<?php echo BASEURL; ?>index.php' class='btn btn-success pull-right' role='button'><?php echo $lang['upgrade_home']; ?> <i class='fa fa-arrow-right'></i></a>
		</div>