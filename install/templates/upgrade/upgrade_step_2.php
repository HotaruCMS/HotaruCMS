<!-- Step title -->
<legend><?php echo $lang['upgrade_step2']; ?></legend>

<!-- Complete Step Progress Bar -->

<div class="alert alert-success">
        <strong><?php echo $lang['install_step4_installation_complete']; ?></strong>
        <!-- Complete Step Progress Bar -->
        <div class="progress progress-success">
                <div class="bar" style="width: 66.66%;"></div>
        </div>
</div>

<!-- Step content -->

<?php $h->showMessages(); ?>

<div class="form-actions">
<!--  Previous/Next buttons -->
<a class='btn' href='index.php?step=1&action=upgrade'><?php echo $lang['install_back']; ?></a>
<a class='btn pull-right' href='index.php?step=3&action=upgrade'><?php echo $lang['install_next']; ?></a>
</div>