<!-- Step title -->
	<legend><?php echo $lang['install_step3']; ?></legend>

	<!-- Complete Step Progress Bar -->
	<div class="progress progress-info">
		<div class="bar" style="width: 75%;"></div>
        </div>
	
        <div>
            <?php $h->showMessages(); ?>
        </div>
        
	<!--  Step content -->
	<div class='well'><h4><?php echo $lang['install_step3_instructions'];?></h4>
	
            <!-- Make note of password message -->
            <?php echo $lang["install_step3_make_note"]; ?><br />

            <!-- Registration form -->
            <form name='install_admin_reg_form' action='index.php?step=3' method='post'>

                <table>
                    <tr><td><?php echo $lang["install_step3_username"];?>&nbsp; </td><td><input type='text' size=30 name='username' value='<?php echo $user_name;?>' /></td></tr>	
                    <tr><td><?php echo $lang["install_step3_email"];?>&nbsp; </td><td><input type='text' size=30 name='email' value='<?php echo $user_email;?>' /></td></tr>
                    <tr><td><?php echo $lang["install_step3_password"];?>&nbsp; </td><td><input type='password' size=30 name='password' value='' /></td></tr>
                    <tr><td><?php echo $lang["install_step3_password_verify"];?>&nbsp; </td><td><input type='password' size=30 name='password2' value='' /></td></tr>

                    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken;?>' />
                    <input type='hidden' name='step' value='4' />
                    <input type='hidden' name='updated' value='true' />

                    <tr><td>&nbsp;</td><td style='text-align:right;'><input class='btn btn-primary' type='submit' value='<?php echo $lang['install_step3_form_update'];?>' /></td></tr>
                </table>
                
            </form>
	</div>
		
        <div class="form-actions">
	<a class='btn' href='index.php?step=2'><?php echo $lang['install_back'];?></a>
	<?php if ($h->cage->post->getAlpha('updated') == 'true' && isset($next_button)) { ?>
		<!-- active "next" link if user has been updated -->
		<a class='btn pull-right' href='index.php?step=4'><?php echo $lang['install_next'];?></a>
	<?php } else { ?>
		<!-- link disbaled until "update" button pressed -->
		<a class='btn pull-right disabled'><?php echo $lang['install_next'];?></a>
	<?php } ?>
        </div>