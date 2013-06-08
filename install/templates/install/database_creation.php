<!-- step title -->
<legend><?php echo $lang['install_step2']; ?></legend>

    <!-- Complete Step Progress Bar -->
    <div class="progress progress-info">
            <div class="bar" style="width: 50%;"></div>
    </div>
	
   
	
		<!-- Warning message -->
		
		<div class="alert alert-error">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<h4><?php echo $lang['install_step1_warning']; ?></h4>
			<?php echo $lang['install_step2_existing_db']; ?>
		</div>
		<div class="text-error"><?php echo $lang['install_step2_existing_confirm']; ?></div>

		<!-- Confirm delete and continue install -->
		<form name='install_admin_reg_form' action='index.php?step=2' method='get'>
                    <div class='center clearfix'>&nbsp; 
                        <input type='text' size=10 name='del' value='' />
                        <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
                    <input type='hidden' name='step' value='2' />	    	    
                    <input class='btn btn-danger' class='button' type='submit' value='<?php echo $lang['install_step2_form_delete_confirm']; ?>' />
                    </div>
                </form>

		<div class='install_content'>
                    <?php echo $lang['install_step2_existing_go_upgrade1']; ?>
                    <a class="" href='index.php?step=1&action=upgrade'>
                        <?php echo $lang['install_step2_existing_go_upgrade2']; ?>
                    </a>
                </div>
                <br/>
                
                <div clasS="form-actions">
                <a class='btn' href='index.php?step=1'><?php echo $lang['install_back']; ?></a>
                <?php 
                if ($show_next) { ?>
                    <a class="btn pull-right" href='index.php?step=3'><?php echo $lang['install_next']; ?></a>
                <?php } else { ?>		
                    <a class='btn disabled pull-right'><?php echo $lang['install_next']; ?></a>
                <?php } ?>
                </div>