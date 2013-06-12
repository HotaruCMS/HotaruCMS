<?php 
/**
 * Theme name: admin_default
 * Template name: plugin_settings.php
 * Template author: shibuya246
 *
 * PHP version 5
 *
 * LICENSE: Hotaru CMS is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version. 
 *
 * Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE. 
 *
 * You should have received a copy of the GNU General Public License along 
 * with Hotaru CMS. If not, see http://www.gnu.org/licenses/.
 * 
 * @category  Content Management System
 * @package   HotaruCMS
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

if ($h->vars['plugin_settings_csrf_error']) { 
	$h->showMessage($h->lang('error_csrf'), 'red'); return false;
}

$plugin = $h->vars['settings_plugin'];    // plugin folder name

$meta = $h->readPluginMeta($plugin);
$pluginData = $h->readPlugin($plugin);


if (version_compare($pluginData->plugin_latestversion, $pluginData->plugin_version) == 1) { 
    $href= SITEURL . "admin_index.php?page=plugin_management&action=update&plugin=" . strtolower($pluginData->plugin_folder) . "&version=" . $pluginData->plugin_latestversion . "#tab_updates";
    $h->showMessage('There is a newer version of this plugin, version ' . $pluginData->plugin_latestversion . '. <a href="' . $href . '">upgrade now</a>', 'alert-info'); 
    // show version number in the message
}

if ($pluginData->plugin_latestversion == '0.0') {     
    $h->showMessage('No version information could be found on the plugin server ', ''); 
    // show version number in the message
}

?>

<div id="plugin_settings">
	<?php 
		$result = '';
		if ($h->vars['settings_plugin']) {
                    
                    echo '
                    <ul class="nav nav-tabs" id="Admin_Plugins_Tab">';
                    $h->pluginHook('admin_plugin_tabLabel_pre_first', $plugin);
                  echo '<li class="active"><a href="#settings" data-toggle="tab">Settings</a></li>
                        <li><a href="#home" data-toggle="tab">Overview</a></li>
                        <li><a href="#support" data-toggle="tab">Support</a></li>
                        <li><a href="#about" data-toggle="tab">About</a></li> 
                        <li class="pull-right btn btn-info disable">' . ucfirst(make_name($plugin)) . '</li>';
                  $h->pluginHook('admin_plugin_tabLabel_after_last', $plugin);
                  echo '</ul>';

                echo '<div class="tab-content">';
                
                    $h->pluginHook('admin_plugin_tabContent_pre_first', $plugin);
                
                    echo '<div class="tab-pane" id="home">';
                    
                        //echo 'Active status';
			//echo '<br/>'; 
                        
                        echo 'Latest Version : ' . $pluginData->plugin_latestversion; echo '<br/>';
                        echo 'Your Version : ' . $pluginData->plugin_version; echo '<br/>';
                        //echo 'Last checked for newer version : need to add field for this in db';
                        echo '<br/><br/>';
                                              
                        echo "<div class='well'><div class='lead'>Screenshots";
                        //echo "<div class='btn btn-primary btn-small pull-right'>Check for Updates</div>";
                        echo "</div>";
                        if (is_dir(PLUGINS . $plugin . '/screenshot')) { $screenshotDir = "/screenshot/"; }                        
                        else { $screenshotDir = ""; }
                        
                        if ($screenshotDir) {                              
                            $files = glob(PLUGINS . $plugin . $screenshotDir . '*.{jpg,png,gif}', GLOB_BRACE);
                            
                            foreach($files as $file) {
                                echo '<img src="' . SITEURL . "content/plugins/" . $plugin . $screenshotDir . basename($file) . '"/>';
                            }

                        } else {
                            print $h->lang('admin_theme_theme_no_screenshots');
                        }
                        echo "</div>";
                        
                    echo '</div>';
                
                    echo '<div class="form tab-pane active" id="settings">';
                    
                        $result = $h->pluginHook('admin_plugin_settings', $plugin);
                        if (!$result) {
                            echo "No settings found for this plugin";                 			
                        }
                    
                    echo '</div>';
                  
                    echo '<div class="tab-pane" id="support">';                        
                        echo 'Rating: N/A<br/><br/>';
                                                
                        // Plugin hook for adding content to support tab
                        echo $h->pluginHook('admin_plugin_support', $plugin);
                    
                echo '</div>';
                    
                    echo '<div class="tab-pane" id="about">'; 
                    
			if (isset($meta) && is_array($meta)) {
				foreach ($meta as $key => $value) {
					if ($key == 'author') { 
                                                $authorUrl = isset($meta['authorurl']) ? "<a href='" . $meta['authorurl'] . "'>" . $value . "</a>" : $value;
                                                echo "<b>" . ucfirst($key) . "</b>: " . $authorUrl;
						break;
                                        } elseif ($key == 'help') {
                                            // do nothing						
					} else {
						echo "<b>" . ucfirst($key) . "</b>: " . $value . "<br />\n";
					}
				}
				echo "<hr/>";
				
				
			} else {
				echo 'No information to show.<br/>';
			}										                                                
                        
                        // TODO
                        // Access the ReadMe file from the $metaReader file                        
                        //$readMe = $metaReader->readText(PLUGINS . "piwik/readme.txt");
                        $fn = PLUGINS . $plugin . "/readme.txt";
                        $size = 4096;
                        
                        if (file_exists($fn) and ($f = fopen($fn, "r"))) {
                            $src = fread($f, $size);
                            fclose($f);
                            $readMe = $src;
                         }
                         // file not found/readable
                         else {
                           $readMe = "The file '" . $fn . "' couldn't be found in your plugins folder.";                
                         }
                         
                        if ($readMe) {                                
				echo nl2br($readMe) . "<br /><br />";								
                        }
                        
                    echo '</div>';
                    
                    $h->pluginHook('admin_plugin_tabContent_after_last', $plugin);
                                        
                echo '</div>';                                
                
		}
	
		
	?>
</div>
