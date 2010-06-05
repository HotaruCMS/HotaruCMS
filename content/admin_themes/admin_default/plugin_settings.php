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

?>

<div id="plugin_settings">
	<?php 
		$result = '';
		if ($h->vars['settings_plugin']) {
			$result = $h->pluginHook('admin_plugin_settings', $h->vars['settings_plugin']);
		}
	
		if (!$result) {
	?>
		<h3><?php echo $h->lang["admin_theme_plugin_settings"]; ?></h3>
	<?php 
			$sb_links = $h->pluginHook('admin_sidebar_plugin_settings');
			if ($sb_links) {
				$sb_links = sksort($sb_links, $subkey="name", $type="char", true);
				foreach ($sb_links as $plugin => $details) { 
					echo "<li><a href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=" . $details['plugin'] . "'>" . $details['name'] . "</a></li>";
				}
			}
		}
	?>
</div>
