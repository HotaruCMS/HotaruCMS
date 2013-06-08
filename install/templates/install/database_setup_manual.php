<!-- Step title -->
<legend><?php echo $lang['install_step1']; ?></legend>

<!-- Complete Step Progress Bar -->

<div class="alert">
        <strong><?php echo $lang['install_step1_instructions_create_db']; ?></strong>
        <!-- Complete Step Progress Bar -->
        <div class="progress progress-info">
                <div class="bar" style="width: 25%"></div>
        </div>
</div>

<!-- Step content -->
<div class='install_content'><?php echo $lang['install_step1_instructions'];?>:</div>

<ul id="install_db_manual">
    <li><?php echo $lang['install_step1_instructions1'];?></li>
    <li><?php echo $lang['install_step1_instructions2'];?></li>
    <li><?php echo $lang['install_step1_instructions3'];?></li>
    <li><?php echo $lang['install_step1_instructions4'];?></li>
    <li><?php echo $lang['install_step1_instructions5'];?></li>
</ul>

<br/>
<div class="alert alert-info">
    Once you have created your database and your settings file is ready, click "Next".
</div>
<!-- Warning message -->	
<div class="alert alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><?php echo $lang['install_step1_warning']; ?></h4>
        <?php echo $lang['install_step1_warning_note']; ?>
</div>

<div clasS="form-actions">
<!-- Previous/Next buttons -->
<div class='btn'><a href='index.php?step=1'><?php echo $lang['install_back']; ?></a></div>
<div class='btn pull-right'><a href='index.php?step=2'><?php echo $lang['install_next']; ?></a></div>
</div>