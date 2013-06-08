<!-- Step title -->
<legend><?php echo $lang['upgrade_step1']; ?></legend>

<!-- Complete Step Progress Bar -->
<div class="alert alert-info"
    <?php if (isset($old_version))  { ?>
         <strong><?php echo $lang['upgrade_step1_old_version'] . $old_version; ?></strong>                
    <?php } else { ?>
        <strong><?php echo $lang['upgrade_step1_old_no_version']; ?></strong>
    <?php } ?>

    <!-- Complete Step Progress Bar -->
        <div class="progress progress-info">
                <div class="bar" style="width: 33.33%;"></div>
        </div>
    <?php
    if ($h->version > $old_version)
        echo $lang['upgrade_step1_details'];
    else
        echo $lang['upgrade_step1_current_version'];
    ?>
        
</div>

<?php $h->showMessages(); ?>

<div class="form-actions">
<!-- Previous/Next buttons -->
<a class='btn' href='index.php?step=0&action=upgrade'><?php echo $lang['install_back']; ?></a>

<?php if ($show_next) { ?>		
        <a class="btn pull-right" href='?step=2&action=upgrade'><?php echo $lang['install_next']; ?></a>
<?php } else { ?>		
        <a class='btn pull-right disabled'><?php echo $lang['install_next']; ?></a>
<?php } ?>
</div>