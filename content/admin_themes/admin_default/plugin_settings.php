<?php 
/**
 * Theme name: admin_default
 * Template name: plugin_settings.php
 * Template author: Nick Ramsay
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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

if ($h->vars['plugin_settings_csrf_error']) { 
	$h->showMessage($h->lang['error_csrf'], 'red'); return false;
}

$plugin = $h->vars['settings_plugin'];    // theme folder name
//$meta = $h->readPluginMeta($plugin);

// TODO
// Access this information from an internal hotaru function
//$meta = $h->readPluginMeta('piwik');
require_once(EXTENSIONS . 'GenericPHPConfig/class.metadata.php');
$metaReader = new generic_pmd();
$meta = $metaReader->read(PLUGINS . $plugin . "/" . $plugin . ".php");

?>

<div id="plugin_settings">
	<?php 
		$result = '';
		if ($h->vars['settings_plugin']) {
                    
                    echo '
                    <ul class="nav nav-tabs" id="Admin_Plugins_Tab">                         
                        <li class="active"><a href="#home" data-toggle="tab">Overview</a></li>
                        <li><a href="#settings" data-toggle="tab">Settings</a></li>
                        <li><a href="#support" data-toggle="tab">Support</a></li>
                        <li><a href="#about" data-toggle="tab">About</a></li> 
                        <li class="pull-right btn btn-info disable">' . ucfirst($plugin) . '</li>
                    </ul>';

                echo '<div class="tab-content">';
                
                    echo '<div class="tab-pane active" id="home">';
                    
                        echo 'Active status';
			echo '<br/><br/>';  
                                              
                        echo "<div class='well'><div class='lead'>Screenshots";
                        //echo "<div class='btn btn-primary btn-small pull-right'>Check for Updates</div>";
                        echo "</div>";
                        if (is_dir(PLUGINS . $plugin . '/screenshot')) { $screenshotDir = "/screenshot/"; }
                        elseif (is_dir(PLUGINS . $plugin . '/screenshots')) { $screenshotDir = "/screenshots/"; }
                        else { $screenshotDir = ""; }
                        
                        if ($screenshotDir) {                              
                            $files = glob(PLUGINS . $plugin . $screenshotDir . '*.{jpg,png,gif}', GLOB_BRACE);
                            
                            foreach($files as $file) {
                                echo '<img src="' . SITEURL . "content/plugins/" . $plugin . $screenshotDir . basename($file) . '"/>';
                            }

                        } else {
                            print $h->lang['admin_theme_theme_no_screenshots'];
                        }
                        echo "</div>";
                        
                    echo '</div>';
                
                    echo '<div class="form tab-pane" id="settings">';
                    
                        $result = $h->pluginHook('admin_plugin_settings', $h->vars['settings_plugin']);
                    
                    echo '</div>';
                  
                    echo '<div class="tab-pane" id="support">';
                        echo 'Support<br/>';
                        echo 'Rating: N/A<br/><br/>';
                        
                        // Plugin hook for old versions of donate button
                        echo $h->pluginHook('admin_topright');
                        echo $h->pluginHook('admin_settings_support_content');
                    
                echo '</div>';
                    
                    echo '<div class="tab-pane" id="about">'; 
                    
			if (isset($meta)) {
				foreach ($meta as $key => $value) {
					if ($key == 'author') { 
                                                echo "<b>" . ucfirst($key) . "</b>: <a href='" . $meta['authorurl'] . "'>" . $value . "</a>";
						break;
                                        } elseif ($key == 'help') {
                                            // do nothing						
					} else {
						echo "<b>" . ucfirst($key) . "</b>: " . $value . "<br />\n";
					}
				}
				echo "<hr/>";
				
				
			} else {
				echo 'No information to show';
			}										                                                
                        
                        // TODO
                        // Access the ReadMe file from the $metaReader file
                        // Hotaru CMS v.1.5.0
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
                                        
                echo '</div>';                                
                
		}
	
		if (!$result) {
	?>
		<h3><?php echo $h->lang["admin_theme_plugin_settings"]; ?></h3>
	<?php 
			$sb_links = $h->pluginHook('admin_sidebar_plugin_settings');
			if ($sb_links) {
				echo "<ul>\n";
				$sb_links = sksort($sb_links, $subkey="name", $type="char", true);
				foreach ($sb_links as $plugin => $details) { 
					echo "<li><a href='" . SITEURL . "admin_index.php?page=plugin_settings&amp;plugin=" . $details['plugin'] . "'>" . $details['name'] . "</a></li>";
				}
				echo "</ul>\n";
			}
		}
	?>
</div>
