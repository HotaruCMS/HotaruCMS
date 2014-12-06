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

$plugin_latest_version = isset($pluginData->plugin_latestversion ) ? $pluginData->plugin_latestversion : '0.0';

if (isset($pluginData->plugin_version) && version_compare($plugin_latest_version, $pluginData->plugin_version) == 1) { 
    $href= SITEURL . "admin_index.php?page=plugin_management&action=update&plugin=" . strtolower($pluginData->plugin_folder) . "&resourceId=" . $pluginData->plugin_resourceId . "&versionId=" . $pluginData->plugin_resourceVersionId . "#tab_updates";
    $h->showMessage('There is a newer version of this plugin, version ' . $pluginData->plugin_latestversion . '. <a class="alert-link" href="' . $href . '">upgrade now</a>', 'alert-info'); 
    // show version number in the message
}

if ($plugin_latest_version == '0.0') {     
    $h->showMessage('No version information could be found on the plugin server ', 'alert-info'); 
    // show version number in the message
}

?>

<div id="plugin_settings">
	<?php
		$result = '';
                $forumLink = "http://forums.hotarucms.org/resources/";
               
		if ($h->vars['settings_plugin']) {
                    
                    $displayName = ucfirst(make_name($plugin)) . ' v.' . $pluginData->plugin_version;
                    $urlLink = $pluginData->plugin_resourceId != 0 ? $forumLink . $pluginData->plugin_resourceId : null;
                    $urlHref = $urlLink ? '<a href="' . $urlLink. '" target="_blank" class="btn btn-primary">' . $displayName . '&nbsp;&nbsp;<i class="fa fa-comments"></i></a>' : '<a href="#" class="btn btn-info">' . $displayName . '</a>';
                    
                    echo '<ul class="nav nav-tabs" id="Admin_Plugins_Tab">';
                    
                    echo '<li>' . $urlHref . '</li>';           
                    $h->pluginHook('admin_plugin_tabLabel_pre_first', $plugin);
                    echo '<li class="active"><a href="#settings" data-toggle="tab">Settings</a></li>
                        <li><a href="#home" data-toggle="tab">Overview</a></li>
                        <li><a href="#about" data-toggle="tab">About</a></li> 
                        <li class="pull-right dropdown">';
                            echo \Libs\PluginSettings::getSettingsDropdownList($h, "Other Plugins");
                    echo '</li>';
                         
                  $h->pluginHook('admin_plugin_tabLabel_after_last', $plugin);
                  echo '</ul>';

                  echo '<div class="tab-content">';
                    echo '<br/>';
                    $h->pluginHook('admin_plugin_tabContent_pre_first', $plugin);
                                        
                    echo '<div class="form tab-pane active" id="settings">';
                    
                        $result = $h->pluginHook('admin_plugin_settings', $plugin);
                        if (!$result) {
                            echo "No settings found for this plugin";                 			
                        }
                    
                    echo '</div>';
                    
                    echo '<div class="tab-pane" id="home">';
                    
                        echo 'Latest Version : ' . $plugin_latest_version; echo '<br/>';
                        echo 'Your Version : ' . $pluginData->plugin_version; echo '<br/>';
                        //echo 'Last checked for newer version : need to add field for this in db';
                        echo '<br/>';
                        
                        echo '<div class="tab-pane" id="support">';                        
                            echo 'Rating: ' . $pluginData->plugin_rating . '&nbsp;';
                            echo '<div class="vers column-rating">';
                    
                                $rating = $pluginData->plugin_rating;
                    
                                echo '<div class="star-rating" title="' . $rating . ' rating">';

                                    for ($x=1; $x<=5; $x++) {
                                        $star ='fa-star-o';
                                        if ($pluginData->plugin_rating >= $x) $star = 'fa-star';
                                        elseif ($pluginData->plugin_rating >= $x - .5) $star = 'fa-star-half-o';

                                        echo '<div class="fa ' . $star . '"></div>';
                                    }
                                    
                                //echo '&nbsp;<span class="num-ratings">(' . $pluginData->times_rated . ')</span>';
                                echo '</div>';
                            echo '</div>';
                            
                            if (1==0) {
                                echo '<div class="column-downloaded">';
                                echo $tag['times_downloaded'] . ' downloads';
                                echo '</div>';
                            }
                                                
                            // Plugin hook for adding content to support tab
                            echo $h->pluginHook('admin_plugin_support', $plugin);

                            // Plugin hook for adding content to support tab
                            echo $h->pluginHook('admin_plugin_support', $plugin);
                    
                            echo "<hr/>";
                        
                            echo "<div class='well'>";
                                echo "<div class='lead'>Screenshots";
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
