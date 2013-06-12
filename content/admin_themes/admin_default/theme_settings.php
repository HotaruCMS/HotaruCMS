<?php 
/**
 * Theme name: admin_default
 * Template name: theme_settings.php
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

if ($h->vars['theme_settings_csrf_error']) { 
	$h->showMessage($h->lang('error_csrf'), 'red'); return false;
}

$theme = $h->vars['settings_theme'];    // theme folder name
$meta = $h->readThemeMeta($theme);

?>

<div id="theme_settings">
	<?php 
		$result = '';
		if ($theme)
		{
                    echo '
                        <ul class="nav nav-tabs" id="Admin_Themes_Tab">                         
                            <li class="active"><a href="#home" data-toggle="tab">Overview</a></li>
                            <li><a href="#settings" data-toggle="tab">Settings</a></li>
                            <li><a href="#support" data-toggle="tab">Support</a></li>
                            <li><a href="#about" data-toggle="tab">About</a></li> 
                            <li class="pull-right btn btn-info disable">' . ucfirst($theme) . '</li>
                        </ul>';

                echo '<div class="tab-content">';
                
                    echo '<div class="tab-pane active" id="home">';
                        
                        if ($theme == rtrim(THEME, '/')) {
				$span = "current";
				$instruct = $h->lang('admin_theme_theme_activate_current');
			} else {
				$span = "activate btn btn-success";
				$instruct = $h->lang('admin_theme_theme_activate');
			}

			echo '<div id="admin_theme_theme_activate" class="power_on" name="'. $theme .'">' .
                                '<span class="' . $span . '">' . $instruct . '</span>';
                                                      
                            
                        echo '<br/><br/></div>';  
                                              
                        echo "<div class='well'><div class='lead'>Screenshots";
                        //echo "<div class='btn btn-primary btn-small pull-right'>Check for Updates</div>";
                        echo "</div>";
                        if (is_dir(THEMES . $theme . '/screenshot')) { $screenshotDir = "/screenshot/"; }                        
                        else { $screenshotDir = ""; }
                        
                        if ($screenshotDir) {                              
                            $files = glob(THEMES . $theme . $screenshotDir . '*.{jpg,png,gif}', GLOB_BRACE);
                            
                            foreach($files as $file) {
                                echo '<img src="' . SITEURL . "content/themes/" . $theme . $screenshotDir . basename($file) . '"/>';
                            }

                        } else {
                            print $h->lang('admin_theme_theme_no_screenshots');
                        }
                        echo "</div>";
                    
                    echo '</div>';
                
                    echo '<div class="form tab-pane" id="settings">';
                       
                       if (file_exists(THEMES . $theme . '/settings.php')) {
                                require_once(THEMES . $theme . '/settings.php');
                        } else {
                                echo $h->lang('admin_theme_theme_no_settings');
                        }
                    echo '</div>';
                  
                    echo '<div class="tab-pane" id="support">';                        
                        echo 'Rating: N/A<br/><br/>';
                        
                        if (file_exists(THEMES . $theme . '/support.php')) {                            
                                require_once(THEMES . $theme . '/support.php');
                        } 
                    echo '</div>';
                    
                    echo '<div class="tab-pane" id="about">';                        
			if ($meta) {
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
				echo "<br /><br />";				
			} else {
				echo $h->lang('admin_theme_theme_no_about');
			}											                                                
                        
                    echo '</div>';
                                        
                echo '</div>';                                        
                    
			
			
		} 
		else 
		{
	?>
		<h3><?php echo $h->lang("admin_theme_theme_settings"); ?></h3>
		<ul id="plugin_settings_list">
			<?php 
				$themes = $h->getFiles(THEMES, array('404error.php'));
				if ($themes) {
					$themes = sksort($themes, $subkey="name", $type="char", true);
					foreach ($themes as $theme) { 
						echo "<li><a href='" . SITEURL . "admin_index.php?page=theme_settings&amp;theme=" . $theme . "'>" . $theme . "</a></li>";
					}
				}
			?>
		</ul>
	<?php } ?>
</div>


