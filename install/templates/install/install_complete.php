<!-- Step Title -->
<legend><?php echo $lang['install_step4']; ?> </legend>

<div class="alert alert-success">
        <strong><?php echo $lang['install_step4_installation_complete']; ?></strong><br />
        <!-- Complete Step Progress Bar -->
        <div class="progress progress-success">
                <div class="bar" style="width: 100%"></div>
        </div>
</div>

<!-- Step content -->
	
<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $lang['install_step4_installation_delete']; ?>
</div>

<?php
    if ($phpinfo) {
        echo '<br/>';
        $h->showMessages();        
    } else {
        ?>	
        <form name='install_admin_reg_form' action='index.php?step=4' method='post'>    
            <input type='hidden' name='phpinfo' value='true' />
            <input type='hidden' name='step' value='4' />
            <input class='update btn' type='submit' value='<?php echo $lang['install_step4_form_check_php']; ?>' />
        </form>
<?php } ?>


<div class='well'><?php echo $lang['install_step4_installation_go_play']; ?></div>
<br/>

<div class="form-actions">
    <!-- Previous/Next buttons -->
    <a class='btn' href='index.php?step=3'><?php echo$lang['install_back']; ?></a>
    <a class='btn btn-success pull-right' href='<?php echo BASEURL; ?>index.php'><?php echo $lang['install_home']; ?></a>
</div>
        
        