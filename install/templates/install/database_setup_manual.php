		
		<!-- Step title -->
		<legend><?php echo $lang['install_step1']; ?></legend>

		<div class='alert alert-warning' role='alert'>
			<strong><?php echo $lang['install_step1_instructions_create_db']; ?></strong>
			<!-- Complete Step Progress Bar -->
			<div class='progress'>
				<div class='progress-bar progress-bar-info' role='progressbar' aria-valuenow='25' aria-valuemin='0' aria-valuemax='100' style='width: 25%'>
					<span class='sr-only'>25% Complete</span>
				</div>
			</div>
		</div>

		<!-- Step content -->
		<div class='install_content'><?php echo $lang['install_step1_instructions'];?>:</div>

		<ul id='install_db_manual'>
			<li><?php echo $lang['install_step1_instructions1'];?></li>
			<li><?php echo $lang['install_step1_instructions2'];?></li>
			<li><?php echo $lang['install_step1_instructions3'];?></li>
			<li><?php echo $lang['install_step1_instructions4'];?></li>
			<li><?php echo $lang['install_step1_instructions5'];?></li>
		</ul>

		<br/>
		<div class='alert alert-info'>
			Once you have created your database and your settings file is ready, click "Next".
		</div>
		<!-- Warning message -->	
		<div class='alert alert-danger alert-dismissible'>
			<button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
			<h4><i class='fa fa-exclamation-triangle'></i> <?php echo $lang['install_step1_warning']; ?></h4>
			<?php echo $lang['install_step1_warning_note']; ?>
		</div>

		<div class='form-actions'>
			<!-- Previous/Next buttons -->
			<a href='index.php?step=1' class='btn btn-default' role='button'><i class='fa fa-arrow-left'></i> <?php echo $lang['install_back']; ?></a>
			<a href='index.php?step=2' class='btn btn-default pull-right' role='button'><?php echo $lang['install_next']; ?> <i class='fa fa-arrow-right'></i></a>
		</div>