		
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
                        <?php echo $lang['install_step1_instructions_manual_setup']; ?>&nbsp;<a href='?step=1&action=install&type=manual'><?php echo $lang['install_step1_instructions_manual_setup_click']; ?></a>.

		</div>
	
		<!-- Manual creation link -->
		<div class='install_content'>
			
			<?php showMessages($h); ?>
			
			<?php
				if ($cage->post->getAlpha('updated') != 'true' && $settings_file_exists) { ?>
			
				<!-- Alert if Settings file already exists -->
				<div class='alert alert-info alert-dismissible'>
					<button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
					<i class='fa fa-info-circle'></i> <?php echo $lang['install_step1_settings_file_already_exists']; ?>
				</div>
			<?php } ?>

			<?php
			if (isset($table_exists) && ($table_exists)) { ?>
			
				<!-- Alert if database already exists -->
				<div class='alert alert-danger alert-dismissible' role='alert'>
					<button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
					<i class='fa fa-exclamation-triangle'></i> <?php echo $lang['install_step1_settings_db_already_exists']; ?>
				</div>
			<?php } ?>
			
			<div class='panel panel-primary'>
				<div class='panel-heading'>
					<h3 class='panel-title'>Database Setup Information</h3>
				</div>
				<div class='panel-body'>
				
					<!-- Registration form -->
					<form class='form-horizontal' role='form' name='install_admin_reg_form' action='../install/index.php?step=1' method='post'>

						<!-- BASEURL -->
						<div class='form-group'>
							<label for='inputBaseURL' class='col-sm-2 control-label'><?php echo $lang['install_step1_baseurl']; ?></label>
							<div class='col-sm-5'>
								<input type='text' class='form-control' name='baseurl' id='inputBaseURL' value='<?php echo $baseurl_name; ?>'>
								<p class='help-block'><?php echo $lang['install_step1_baseurl_explain']; ?></p>
							</div>
						</div>
						
						<!-- DB_USER -->
						<div class='form-group'>
							<label for='inputDBuser' class='col-sm-2 control-label'><?php echo $lang['install_step1_dbuser']; ?></label>
							<div class='col-sm-5'>
								<input type='text' class='form-control' name='dbuser' id='inputDBuser' value='<?php echo $dbuser_name; ?>'>
								<p class='help-block'><?php echo $lang['install_step1_dbuser_explain']; ?></p>
							</div>
						</div>
						
						<!-- DB_PASSWORD -->
						<div class='form-group'>
							<label for='inputDBpassword' class='col-sm-2 control-label'><?php echo $lang['install_step1_dbpassword']; ?></label>
							<div class='col-sm-5'>
								<input type='password' class='form-control' name='dbpassword' id='inputDBpassword' value='<?php echo $dbpassword_name; ?>'>
								<p class='help-block'><?php echo $lang['install_step1_dbpassword_explain']; ?></p>
							</div>
						</div>
					
						<!-- DB_NAME -->
						<div class='form-group'>
							<label for='inputDBname' class='col-sm-2 control-label'><?php echo $lang['install_step1_dbname']; ?></label>
							<div class='col-sm-5'>
								<input type='text' class='form-control' name='dbname' id='inputDBname' value='<?php echo $dbname_name; ?>'>
								<p class='help-block'><?php echo $lang['install_step1_dbname_explain']; ?></p>
							</div>
						</div>

						<!-- DB_PREFIX -->
						<div class='form-group'>
							<label for='inputDBprefix' class='col-sm-2 control-label'><?php echo $lang['install_step1_dbprefix']; ?></label>
							<div class='col-sm-5'>
								<input type='text' class='form-control' name='dbprefix' id='inputDBprefix' value='<?php echo $dbprefix_name; ?>'>
								<p class='help-block'><?php echo $lang['install_step1_dbprefix_explain']; ?></p>
							</div>
						</div>
						
						<!-- DB_HOST -->
						<div class='form-group'>
							<label for='inputDBhost' class='col-sm-2 control-label'><?php echo $lang['install_step1_dbhost']; ?></label>
							<div class='col-sm-5'>
								<input type='text' class='form-control' name='dbhost' id='inputDBhost' value='<?php echo $dbhost_name; ?>'>
								<p class='help-block'><?php echo $lang['install_step1_dbhost_explain']; ?></p>
							</div>
						</div>
						
						<!-- Update button -->
						<div class='form-group'>
							<div class='col-sm-offset-2 col-sm-10'>
								<input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
								<input type='hidden' name='step' value='2' />
								<input type='hidden' name='updated' value='true' />
								<input class='btn btn-primary' type='submit' value='<?php echo $lang['install_step3_form_update']; ?>' />
							</div>
						</div>
						
					</form>
				</div>
			</div>

			<div class='form-actions'>
				<!-- Previous/Next buttons -->
				<a href='index.php?step=0' class='btn btn-default' role='button'><i class='fa fa-arrow-left'></i> <?php echo $lang['install_back']; ?></a>
				<?php if ($show_next) { // and if db was connected ok ?>
					<a href='index.php?step=2' class='btn btn-default pull-right' role='button'><?php echo $lang['install_next']; ?> <i class='fa fa-arrow-right'></i></a>
				<?php } else { // link disbaled ?>		    
					<a class='btn btn-default disabled pull-right' href='#' role='button'><?php echo $lang['install_next']; ?> <i class='fa fa-arrow-right'></i></a>
				<?php } ?>
			</div>
		</div>