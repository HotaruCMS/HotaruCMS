
		<!-- Step title -->
		<legend><?php echo $lang['upgrade_step2']; ?></legend>
		
		<p class='text-primary'><strong><i class='fa fa-check'></i> <?php echo $lang['install_step4_installation_complete']; ?></strong></p>
		<!-- Complete Step Progress Bar -->
		<div class='progress'>
			<div class='progress-bar progress-bar-info' role='progressbar' aria-valuenow='50' aria-valuemin='0' aria-valuemax='100' style='width: 50%'>
				<span class='sr-only'>50% Complete</span>
			</div>
		</div>

		<!-- Step content -->

		<?php $h->showMessages(); ?>

		<div class='form-actions'>
			<!-- Previous/Next buttons -->
			<a href='index.php?step=1&action=upgrade' class='btn btn-default' role='button'><i class='fa fa-arrow-left'></i> <?php echo $lang['install_back']; ?></a>
			<a href='index.php?step=3&action=upgrade' class='btn btn-default pull-right' role='button'><?php echo $lang['install_next']; ?> <i class='fa fa-arrow-right'></i></a>
		</div>