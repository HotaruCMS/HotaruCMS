<div id="plugin_management">
    
	<table class="table table-bordered">
		<tr class="success">
			<td>
				<strong><?php echo $h->lang("admin_theme_plugins_installed"); ?></strong>
			</td>
			<td><i class="icon-info-sign"></i>
				<strong><?php echo $h->lang["admin_theme_plugins_details"]; ?></strong>
			</td>
			<td><i class="icon-trash"></i>
				<strong><?php echo $h->lang["admin_theme_plugins_uninstall"]; ?></strong>
			</td>
			<td>
				<a href="<?php echo SITEURL ?>admin_index.php?page=plugin_management&action=version_check">
					<i class="icon-refresh"></i>
					<strong><?php echo $h->lang("admin_theme_check_latest_plugin_versions"); ?></strong>
				</a>
			</td>
		</tr>
	</table>
		
	<table id="plugintable_installed">
            <tr>
<?php
    // for some reason, even though we have passed the array to here it gets lost somehow in the include,so we have to get it again
	$the_plugins = isset($h->vars['installed_plugins']) ? $h->vars['installed_plugins'] : array();
	$per_column = count($the_plugins)/2;
	for($i=0; $i<2; $i++) { 
            $col_name = ($i ==0) ? 'left-col' : 'right-col'; 
?>


            <td style='width: 50%; vertical-align: top;'>

                <table class="table table-bordered table-striped table-hover">                            
                    <tbody id="<?php echo $col_name;?>">   
<?php
                        $alt = 0;	
                        if ($the_plugins){
                            foreach ($the_plugins as $plug) {	    
                                $alt++;                                                                
                                $update = false; $update_class = '';
                                if (isset($plug['latestversion']) && $plug['latestversion'] > $plug['version']) {$update=true; $update_class='info'; }
                                echo "<tr id='sort_" . $plug['id'] . "' class='" . $update_class . "'>\n";
                                    echo "<td class='table_active'>" . $plug['active'] . "</td>\n";
                                    echo "<td class='table_installed_plugin'>";
                                        if ($plug['settings']) {
                                                echo "<a href='" . SITEURL . "admin_index.php?page=plugin_settings&amp;plugin=" . $plug['folder'] . "' title='" . $h->lang("admin_theme_plugins_settings") . "'>";
                                                echo $plug['name'] . " " . $plug['version'] . "</a>\n";
                                        } else {
                                                echo $plug['name'] . " " . $plug['version'];
                                        }
                                    echo "</td>\n";
                                    //echo "<td class='table_order'>" . $plug['order_output'] . "</td>\n";
                                    echo "<td class='table_uninstall'>\n";
                                        echo "<a class='table_drop_down' href='#'><i class='icon-info-sign'></i></a>\n";
                                        echo "&nbsp;" . $plug['install'];
                                    echo "</td>\n";                                   
                                echo "</tr>\n";

                                echo "<tr class='table_tr_details' style='display:none;'>";
                                    echo "<td colspan=2 class='table_description'>\n";
                                        echo $plug['description'] . "<br />";
                                        $requires = "";
                                        foreach ($plug['requires'] as $key=>$value) {
                                                $requires .= $key . " " . $value . ", ";
                                        }
                                        if ($requires != "") { echo $h->lang("admin_theme_plugins_requires") . " " . rstrtrim($requires, ", "); } else { echo $h->lang("admin_theme_plugins_no_plugins"); }
                                        if (isset($plug['author'])) { echo "<br />" . $h->lang("admin_theme_plugins_author") . ": \n"; }
                                        if (isset($plug['authorurl'])) { echo "<a href='" . $plug['authorurl'] . "' title='" . $plug['authorurl'] . "'>"; }
                                        if (isset($plug['author'])) { echo $plug['author']; }
                                        if (isset($plug['authorurl'])) { echo "</a>\n"; }
                                        if (file_exists(PLUGINS . $plug['folder'] . "/readme.txt")) {
                                                echo "<br />" . $h->lang("admin_theme_plugins_more_info");
                                                echo ": <a href='" . SITEURL . "content/plugins/" . $plug['folder'] . "/readme.txt' title='" . $h->lang("admin_theme_plugins_readme") . "'>";
                                                echo $h->lang("admin_theme_plugins_readmetxt") . "</a>";
                                        }

                                        if ($update) { echo "<br/><a href='" . SITEURL . "admin_index.php?page=plugin_management&action=update&plugin=". $plug['folder'] . "&version=" . $plug['latestversion'] . "' title=''>Update this plugin</a>"; }
                                    echo "</td>";
                                    echo "<td class='table_description_close'><a class='table_hide_details' href='#'>";
                                        echo $h->lang("admin_theme_plugins_close") . "</a>";
                                    echo "</td>";
                                echo "</tr>\n";

                                array_shift($the_plugins);
                                if ($alt >= $per_column) { break; }
                            }
                        }
?>
                    </tbody> 
                </table> <!-- close table which contains one column of plugins -->

            </td>   <!-- close cell which contains one of three columns of smaller tables -->
<?php } ?>
            </tr>
	</table> <!-- close table which contains three columns of smaller tables -->
	<br />
<a class="btn btn-warning" href="<?php echo SITEURL; ?>admin_index.php?page=plugin_management&amp;action=deactivate_all">
<?php echo $h->lang["admin_theme_plugins_deactivate_all"]; ?></a>
<a class="btn btn-primary" href="<?php echo SITEURL; ?>admin_index.php?page=plugin_management&amp;action=activate_all">
<?php echo $h->lang["admin_theme_plugins_activate_all"]; ?></a>
<a class="btn btn-danger pull-right" href="<?php echo SITEURL; ?>admin_index.php?page=plugin_management&amp;action=uninstall_all">
<?php echo $h->lang["admin_theme_plugins_uninstall_all"]; ?></a>


	<table>
		<tr>
			<tr>
				<td colspan=3>&nbsp;</td>
			</tr>
			<tr class='table_b'>
				<td colspan=3>
					<?php echo $h->lang("admin_theme_plugins_not_installed"); ?>
					<span class='table_key'>
						&nbsp;&nbsp;
						<i class="icon-info-sign"></i>
						<?php echo $h->lang("admin_theme_plugins_details"); ?>
						&nbsp;&nbsp;
						<i class="icon-download-alt"></i>
						<?php echo $h->lang("admin_theme_plugins_install"); ?>
					</span>
				</td>
			</tr>

<?php
	$the_plugins = $h->vars['uninstalled_plugins']; // don't remove
	$per_column = count($the_plugins)/3;
	for($i=0; $i<3; $i++) {
?>

			<td style='width: 33%; vertical-align: top;'>

				<table>

<?php
	$alt = 0;
	if (!$the_plugins) { $the_plugins = array(); }
	foreach ($the_plugins as $plug) {
		$alt++;
		$info_icon = 'info_16.png';
		$update = false;
		if (isset($plug['latestversion'])) { if ($plug['latestversion'] > $plug['version']) {$update = true; $info_icon = 'info_green_16.png'; }}
		echo "<tr id='table_tr' class='table_row_" . $alt % 2 . "'>\n";
		echo "<td class='table_uninstalled_plugin'>" . $plug['name'] . " " . $plug['version'] . "<br />\n";
		echo "<span class='table_requires'>";
		$requires = '';
		foreach ($plug['requires'] as $key=>$value) {
			$requires .= make_name($key) . " " . $value . ", ";
		}
		echo rtrim($requires, ', ') . "</span></td>\n";
		echo "<td class='table_install'>\n";
		echo "<a class='table_drop_down' href='#'><i class='icon-info-sign'></i></a>\n";
		echo "&nbsp;" . $plug['install'] . "</td>\n";
		echo "</tr>\n";
		echo "<tr class='table_tr_details' style='display:none;'><td class='table_description'>\n";
		echo $plug['description'];
		if (isset($plug['author'])) { echo "<br />" . $h->lang("admin_theme_plugins_author") . ": \n"; }
		if (isset($plug['authorurl'])) { echo "<a href='" . $plug['authorurl'] . "' title='" . $plug['authorurl'] . "'>"; }
		if (isset($plug['author'])) { echo $plug['author']; }
		if (isset($plug['authorurl'])) { echo "</a>\n"; }
		if (file_exists(PLUGINS . $plug['folder'] . "/readme.txt")) {
			echo "<br />" . $h->lang("admin_theme_plugins_more_info");
			echo ": <a href='" . SITEURL . "content/plugins/" . $plug['folder'] . "/readme.txt' title='" . $h->lang("admin_theme_plugins_readme") . "'>";
			echo $h->lang("admin_theme_plugins_readmetxt") . "</a>";			
		}
		if ($update) echo "<br/><a href='" . SITEURL . "admin_index.php?page=plugin_management&action=update&plugin=". $plug['folder'] . "&version=" . $plug['latestversion'] . "' title=''>Update this plugin</a>";
		echo "</td>\n";
		echo "<td class='table_description_close'><a class='table_hide_details' href='#'>";
		echo $h->lang("admin_theme_plugins_close") . "</a></td></tr>\n";
		array_shift($the_plugins);
		if ($alt >= $per_column) { break; }
	}

?>
				</table> <!-- close table which contains one column of plugins -->

			</td>   <!-- close cell which contains one of three columns of smaller tables -->
<?php } ?>

		</tr>
	</table> <!-- close table which contains three columns of smaller tables -->		

        <script>
            //https://github.com/nostalgiaz/bootstrap-switch
		$('.switch').on('switch-change', function (e, data) {
                    var $el = $(data.el), value = data.value
                    var id = $(this).attr('id');
                    var $d, $plugin;
                    
                    //alert(id);
                    var stringParts = id.split("#");
                    $plugin = stringParts[1];
                    //alert($plugin);
                    console.log(e, $el, value);
                    
                    if (value) $d = 'activate'; else $d = 'deactivate';
                    
                    //alert(value, $d);                    
                    
                    var sendurl = '<?php echo BASEURL; ?>admin_index.php?page=plugin_management&action='+$d+'Ajax&plugin='+$plugin;
                    
                    
                    $.ajax(
                    {
                            type: 'get',
                            url: sendurl,                                                     
                            error: 	function(XMLHttpRequest, textStatus, errorThrown) {
                                            //widget.html('ERROR');
                                            alert(errorThrown);
                            },
                            success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
                                    if (data.error === true) {
                                        // reset the switch
                                        alert(data.error);
                                    }
                                    else
                                    {
                                        //alert(data)                                        
                                    }                    

                            },
                            dataType: "json"
                        });
                    
                    
                    
                });
	</script>
        
</div>