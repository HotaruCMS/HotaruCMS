<?php 
/**
 * Theme name: admin_default
 * Template name: theme_settings.php
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
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

if ($h->vars['theme_settings_csrf_error']) { 
	$h->showMessage($h->lang['error_csrf'], 'red'); return false;
}

$theme = $h->vars['settings_theme'];    // theme folder name
?>

<div id="theme_settings">
	<?php 
		$result = '';
		if ($theme) {
			echo '<div id="admin_theme_theme_activate" class="power_on" name="'. $theme .'">' . make_name($theme, '-') . $h->lang['admin_theme_theme_activate'] . '</div><br/>';
			if (file_exists(THEMES . $theme . '/settings.php')) {
				$meta = $h->readThemeMeta($theme);
				foreach ($meta as $key => $value) {
					if ($key != 'author') { 
						echo ucfirst($key) . ": " . $value . "<br />\n";
					} else {
						echo ucfirst($key) . ": <a href='" . $meta['authorurl'] . "'>" . $value . "</a>";
						break;
					}
				}
				echo "<br /><br />";
				require_once(THEMES . $theme . '/settings.php');
			} else {
				echo '<i>' . make_name($theme, '-') . $h->lang['admin_theme_theme_no_settings'] . '</i>';
			}
		} else {
	?>
		<h3><?php echo $h->lang["admin_theme_theme_settings"]; ?></h3>
		<ul id="plugin_settings_list">
			<?php 
				$themes = $h->getFiles(THEMES, array('404error.php'));
				if ($themes) {
					$themes = sksort($themes, $subkey="name", $type="char", true);
					foreach ($themes as $theme) { 
						echo "<li><a href='" . BASEURL . "admin_index.php?page=theme_settings&amp;theme=" . $theme . "'>" . $theme . "</a></li>";
					}
				}
			?>
		</ul>
	<?php } ?>
</div>
