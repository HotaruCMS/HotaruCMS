<!-- 

Please do not edit this file as it will be overwritten on future updates of Hotaru CMS
Any files you create yourself in this folder are editable and will not be overwritten

-->

<?php

if(!$h->numActivePlugins()) { ?>

<div style="padding:15px 25px;" class="hero-unit">
        <h2><?php echo $h->lang('main_welcome'); ?></h2>
        <p><?php echo $h->lang('main_welcome_getting_started'); ?></p>
        <p><?php echo $h->lang('main_welcome_looking_forward'); ?></p>
        <p><?php echo $h->lang('main_welcome_install_plugins'); ?></p>
        <p><br/><a href="<?php echo $h->url(array(), 'admin'); ?>" class="btn btn-primary"><?php echo $h->lang('main_theme_button_admin_login'); ?></a></p>
</div>

<?php }

else { ?>

<div style="padding:15px 25px;" class="hero-unit">
        <h2><?php echo $h->lang('main_welcome'); ?></h2>
        <p><?php echo $h->lang('main_welcome_looking_forward'); ?></p>
            
</div>

<?php } ?>
