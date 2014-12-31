<h2><?php echo $h->lang("admin_theme_maintenance_hook_settings"); ?></h2>
<?php echo $h->lang("admin_theme_maintenance_hook_settings_explanation"); ?><br /><br />
<h3>Site Hooks</h3>
<ul>
<?php if ($h->allPluginDetails['hookdata']) { ?>
	<?php foreach ($h->allPluginDetails['hookdata'] as $hook => $folder) { ?>
        <?php if (substr($hook,0,5) != 'admin' &&  substr($hook,0,7) != 'install') { ?>
            <li>
                <?php echo "<h4>" . $hook . "</h4>"; ?>
                <?php foreach ($folder as $key => $value) {            
                    echo '<span class="label label-default">' . $key . '</span> ';
                } ?>
            </li>
        <?php }} ?>
<?php } else { ?>
	<i><?php echo $h->lang("admin_theme_maintenance_no_hook_settings"); ?></i>
<?php } ?>
</ul>


<h3>Admin Hooks</h3>
<ul>
<?php if ($h->allPluginDetails['hookdata']) { ?>
	<?php foreach ($h->allPluginDetails['hookdata'] as $hook => $folder) { ?>
        <?php if (substr($hook,0,5) == 'admin' ||  substr($hook,0,7) == 'install') { ?>
            <li>
                <?php echo "<h4>" . $hook . "</h4>"; ?>
                <?php foreach ($folder as $key => $value) {            
                    echo '<span class="label label-default">' . $key . '</span> ';
                } ?>
            </li>
        <?php }} ?>
<?php } else { ?>
	<i><?php echo $h->lang("admin_theme_maintenance_no_hook_settings"); ?></i>
<?php } ?>
</ul>