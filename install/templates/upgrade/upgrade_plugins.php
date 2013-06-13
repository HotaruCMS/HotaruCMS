<!-- Step title -->
	<legend><?php echo $lang['upgrade_step3']; ?></legend>

	<!-- Complete Step Progress Bar -->
	
	<div class="alert alert-success">
		<strong><?php echo $lang['upgrade_step3_details']; ?></strong>
		<!-- Complete Step Progress Bar -->
		<div class="progress progress-success">
			<div class="bar" style="width: 100%;"></div>
		</div>
	</div>
        
        <?php $h->showMessages(); ?>

	<div class='well'><?php echo $lang['upgrade_step3_instructions']; ?></div>
	
        <div class="alert alert-error">
            <?php echo $lang['install_step4_installation_delete']; ?>
        </div>
        
	<br/><?php echo $lang['upgrade_step3_go_play']; ?><br/><br/>

        <div class="form-actions">
	<!-- Previous/Next buttons -->
	<a class="btn" href='index.php?step=2&action=upgrade'><?php echo $lang['install_back']; ?></a>
	<a class="btn pull-right btn-success" href='<?php echo BASEURL; ?>index.php'><?php echo $lang['upgrade_home']; ?></a>
        </div>