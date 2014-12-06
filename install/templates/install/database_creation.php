		
		<!-- step title -->
		<legend><?php echo $lang['install_step2']; ?></legend>
		
		<!-- Complete Step Progress Bar -->
		<div class='progress'>
			<div class='progress-bar progress-bar-info' role='progressbar' aria-valuenow='50' aria-valuemin='0' aria-valuemax='100' style='width: 50%'>
				<span class='sr-only'>50% Complete</span>
			</div>
		</div>

		<!-- Warning message -->
		<div class='alert alert-danger alert-dismissible' role="alert">
			<button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
			<h4><i class='fa fa-exclamation-triangle'></i> <?php echo $lang['install_step1_warning']; ?></h4>
			<?php echo $lang['install_step2_existing_db']; ?>
		</div>

		<p class='text-danger'><?php echo $lang['install_step2_existing_confirm']; ?></p>

		<!-- Confirm delete and continue install -->
		<form name='install_admin_reg_form' action='index.php?step=2' method='get' role='form'>
			<div class='col-sm-4'>
				<input type='text' class='form-control' name='del' value=''>
			</div>
			<input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
			<input type='hidden' name='step' value='2' />
			<input class='btn btn-danger' class='button' type='submit' value='<?php echo $lang['install_step2_form_delete_confirm']; ?>' />
		</form>
		<br/>

		<p>
			<?php echo $lang['install_step2_existing_go_upgrade1']; ?>
			<a href='index.php?step=1&action=upgrade'><?php echo $lang['install_step2_existing_go_upgrade2']; ?></a>
		</p>
		<br/>

		<div class='form-actions'>
			<!-- Previous/Next buttons -->
			<a href='index.php?step=1' class='btn btn-default' role='button'><i class='fa fa-arrow-left'></i> <?php echo $lang['install_back']; ?></a>
			<?php if ($show_next) { // and if db was connected ok ?>
				<a href='index.php?step=3' class='btn btn-default pull-right' role='button'><?php echo $lang['install_next']; ?> <i class='fa fa-arrow-right'></i></a>
			<?php } else { // link disbaled ?>
				<a class='btn btn-default disabled pull-right' href='#' role='button'><?php echo $lang['install_next']; ?> <i class='fa fa-arrow-right'></i></a>
			<?php } ?>
		</div>