<!-- Step title -->
<legend><?php echo $lang['install_step1']; ?></legend>
		
    <!-- Complete Step Progress Bar -->

    <div class="alert">
            <strong><?php echo $lang['install_step1_instructions_create_db']; ?></strong>
            <!-- Complete Step Progress Bar -->
            <div class="progress progress-info">
                    <div class="bar" style="width: 25%;"></div>
            </div>
    </div>
	    
    <!-- Manual creation link -->
    <div class='install_content'>
        <?php echo $lang['install_step1_instructions_manual_setup']; ?>&nbsp;<a href='?step=1&action=install&type=manual'><?php echo $lang['install_step1_instructions_manual_setup_click']; ?></a>.

	<?php $h->showMessages(); ?>
        
        <?php
            if ($cage->post->getAlpha('updated') != 'true' && SETTINGS) { ?>
		<!-- Alert if Settings file already exists -->
		
		<div class="alert alert-block">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo $lang['install_step1_settings_file_already_exists']; ?>
		</div>
	<?php } ?>

        <?php
	    if (isset($table_exists) && ($table_exists)) { ?>
		<!-- Alert if database already exists -->
		
		<div class="alert alert-error">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo $lang['install_step1_settings_db_already_exists']; ?>
		</div>
	<?php } ?>
		
        <!-- Registration form -->
        <form name='install_admin_reg_form' action='../install/index.php?step=1' method='post'>

	<br/><table>

	    <!-- BASEURL -->
	    <tr><td><?php echo $lang["install_step1_baseurl"]; ?>&nbsp;</td><td><input type='text' size=30 name='baseurl' value='<?php echo $baseurl_name; ?>' />&nbsp;<small><?php echo $lang["install_step1_baseurl_explain"]; ?></small></td></tr>

	    <!-- DB_USER -->
	    <tr><td><?php echo $lang["install_step1_dbuser"]; ?>&nbsp; </td><td><input type='text' size=30 name='dbuser' value='<?php echo $dbuser_name; ?>' />&nbsp;<small><?php echo $lang["install_step1_dbuser_explain"]; ?></small></td></tr>

	    <!-- DB_PASSWORD -->
	    <tr><td><?php echo $lang["install_step1_dbpassword"]; ?>&nbsp; </td><td><input type='password' size=30 name='dbpassword' value='<?php echo $dbpassword_name; ?>' />&nbsp;<small><?php echo $lang["install_step1_dbpassword_explain"]; ?></small></td></tr>

	    <!-- DB_NAME -->
	    <tr><td><?php echo $lang["install_step1_dbname"]; ?>&nbsp; </td><td><input type='text' size=30 name='dbname' value='<?php echo $dbname_name; ?>' />&nbsp;<small><?php echo $lang["install_step1_dbname_explain"]; ?></small></td></tr>

	    <!-- DB_PREFIX -->
	    <tr><td><?php echo $lang["install_step1_dbprefix"]; ?>&nbsp; </td><td><input type='text' size=30 name='dbprefix' value='<?php echo $dbprefix_name; ?>' />&nbsp;<small><?php echo $lang["install_step1_dbprefix_explain"]; ?></small></td></tr>

	    <!-- DB_HOST -->
	    <tr><td><?php echo $lang["install_step1_dbhost"]; ?>&nbsp; </td><td><input type='text' size=30 name='dbhost' value='<?php echo $dbhost_name; ?>' />&nbsp;<small><?php echo $lang["install_step1_dbhost_explain"]; ?></small></td></tr>


	    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
	    <input type='hidden' name='step' value='2' />
	    <input type='hidden' name='updated' value='true' />

	    <!-- Update button -->
	    <tr><td>&nbsp;</td><td style='text-align:right;'><input class='btn btn-primary' type='submit' value='<?php echo $lang['install_step3_form_update']; ?>' /></td></tr>

	    </table>
	    </form>

	   <div class="form-actions">
	    <!-- Previous/Next buttons -->
	    <div class='btn'><a href='index.php?step=0'><?php echo $lang['install_back']; ?></a></div>
	                
            <?php
	    if ($show_next) { // and if db was connected ok ?>
		    <div class='btn pull-right pull-right'><a href='index.php?step=2'><?php echo $lang['install_next']; ?></a></div>
	    <?php } else { // link disbaled ?>		    
		    <div class='btn disabled pull-right'><?php echo $lang['install_next']; ?></div>
	    <?php } ?>
            </div>