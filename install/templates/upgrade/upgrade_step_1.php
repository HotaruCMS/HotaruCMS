		
		<!-- Step title -->
		<legend><?php echo $lang['upgrade_step1']; ?></legend>
		
		<div class='alert alert-info' role='alert'>
			<?php if (isset($old_version))  { ?>
				<strong><?php echo $lang['upgrade_step1_old_version'] . $old_version; ?></strong>                
			<?php } else { ?>
				<strong><?php echo $lang['upgrade_step1_old_no_version']; ?></strong>
			<?php } ?>
			<!-- Complete Step Progress Bar -->
			<div class='progress'>
				<div class='progress-bar progress-bar-info' role='progressbar' aria-valuenow='25' aria-valuemin='0' aria-valuemax='100' style='width: 25%'>
					<span class='sr-only'>25% Complete</span>
				</div>
			</div>
			<?php
			if ($h->version > $old_version)
				echo $lang['upgrade_step1_details'];
			else
				echo $lang['upgrade_step1_current_version'];
			?>
		</div>

		<?php $h->showMessages(); ?>
		
		<div class='form-actions'>
			<!-- Previous/Next buttons -->
			<a href='index.php?step=0&action=upgrade' class='btn btn-default' role='button'><i class='fa fa-arrow-left'></i> <?php echo $lang['install_back']; ?></a>
			<?php if ($show_next) { // and if db was connected ok ?>
				<a href='?step=2&action=upgrade' class='btn btn-default pull-right' role='button'><?php echo $lang['install_next']; ?> <i class='fa fa-arrow-right'></i></a>
			<?php } else { // link disbaled ?>
				<a class='btn btn-default disabled pull-right' href='#' role='button'><?php echo $lang['install_next']; ?> <i class='fa fa-arrow-right'></i></a>
			<?php } ?>
		</div>