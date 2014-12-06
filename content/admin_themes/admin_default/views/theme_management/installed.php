<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

        $themes = $h->getFiles(THEMES, array('404error.php', 'pages'));
?>

<p> 
    <br/>
    You can download the latest versions of themes here if your server permissions line up nicely with the web.<br/>
    <strong>Note:</strong> Servers without SuExec enabled may not work. Future versions will additionally include FTP access to solve this.
</p>

<table class="table table-bordered">
    <tr class="info">
        <td>Plugin</td>
        <td>Version</td>
        <td>Activate</td>
        <td>Update</td>
    </tr>
    
    <?php 
    if ($themes) {
        foreach ($themes as $theme) {             
            
                //$href= SITEURL . "admin_index.php?page=plugin_management&action=update&plugin=" . strtolower($plugin['folder']) . "&resourceId=" . $plugin['resourceId'] . "&versionId=" . $plugin['resourceVersionId'] . "#tab_updates";
                ?>
                    <tr>
                        <td>
                            <?php
                        if ($theme == rtrim(THEME, '/')) { $active = ' <i><small>(current)</small></i>'; } else { $active = ''; } 
                                                echo "<a href='" . SITEURL . "admin_index.php?page=theme_settings&amp;theme=" . $theme . "'>" . make_name($theme, '-') . "</a>" . $active . "\n";
                        ?>
                        </td>
                        <td><?php //echo $plugin['version']; ?></td>
                        <td><a href="admin_index.php?page=theme_settings&theme=<?php echo $theme; ?>" class="btn btn-primary btn-xs">Settings</button></a></td>
                        <td><!--<a href="<?php //echo $href; ?>" class="btn btn-warning btn-xs">Update</button></a>--></td>
                    </tr>
                
        <?php } 
    }
    ?>
</table>
