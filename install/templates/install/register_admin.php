	
		<!-- Step title -->
		<legend><?php echo $lang['install_step3']; ?></legend>
		
		<!-- Complete Step Progress Bar -->
		<div class='progress'>
			<div class='progress-bar progress-bar-info' role='progressbar' aria-valuenow='75' aria-valuemin='0' aria-valuemax='100' style='width: 75%'>
				<span class='sr-only'>75% Complete</span>
			</div>
		</div>

		<?php $h->showMessages(); ?>

		<!--  Step content -->
		<div class='panel panel-primary'>
			<div class='panel-heading'>
				<h3 class='panel-title'><?php echo $lang['install_step3_instructions'];?></h3>
			</div>
			<div class='panel-body'>
		
				<!-- Make note of password message -->
				<p class='text-info'><?php echo $lang['install_step3_make_note']; ?></p>

				<!-- Registration form -->
				<form name='install_admin_reg_form' action='index.php?step=3' method='post' class='form-horizontal' role='form'>
					
					<div class='form-group'>
						<label for='inputUsername' class='col-sm-2 control-label'><?php echo $lang['install_step3_username'];?></label>
						<div class='col-sm-4'>
							<input type='text' class='form-control' id='inputUsername' name='username' value='<?php echo $user_name;?>'>
						</div>
					</div>
					
					<div class='form-group'>
						<label for='inputEmail' class='col-sm-2 control-label'><?php echo $lang['install_step3_email'];?></label>
						<div class='col-sm-4'>
							<input type='email' class='form-control' id='inputEmail' name='email' value='<?php echo $user_email;?>'>
						</div>
					</div>
					
					<div class='form-group'>
						<label for='inputPassword' class='col-sm-2 control-label'><?php echo $lang['install_step3_password'];?></label>
						<div class='col-sm-4'>
							<input type='password' class='form-control' id='inputPassword' name='password' value=''>
						</div>
					</div>
					
					<div class='form-group'>
						<label for='inputPassword2' class='col-sm-2 control-label'><?php echo $lang['install_step3_password_verify'];?></label>
						<div class='col-sm-4'>
							<input type='password' class='form-control' id='inputPassword2' name='password2' value=''>
						</div>
					</div>
					
					<div class='form-group'>
						<div class='col-sm-offset-2 col-sm-10'>
							<input type='hidden' name='csrf' value='<?php echo $h->csrfToken;?>'>
							<input type='hidden' name='step' value='4'>
							<input type='hidden' name='updated' value='true'>
							<input class='btn btn-primary' type='submit' value='<?php echo $lang['install_step3_form_update'];?>'>
						</div>
					</div>

				</form>
				
			</div>
		</div>
		
		<div class='form-actions'>
			<!-- Previous/Next buttons -->
			<a href='index.php?step=2' class='btn btn-default' role='button'><i class='fa fa-arrow-left'></i> <?php echo $lang['install_back']; ?></a>
			<?php if ($h->cage->post->getAlpha('updated') == 'true' && isset($next_button)) { ?>
			<!-- active 'next' link if user has been updated -->
				<a href='index.php?step=4' class='btn btn-default pull-right' role='button'><?php echo $lang['install_next']; ?> <i class='fa fa-arrow-right'></i></a>
			<?php } else { ?>
			<!-- link disbaled until 'update' button pressed -->
				<a class='btn btn-default disabled pull-right' href='#' role='button'><?php echo $lang['install_next']; ?> <i class='fa fa-arrow-right'></i></a>
			<?php } ?>
		</div>