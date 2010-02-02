<?php
/**
 * Theme name: bars
 * Template name: settings.php
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

// If the form has been submitted, save the data...
if ($h->cage->post->getAlpha('submitted') == 'true')
{ 
    $bars['left'] = ($h->cage->post->keyExists('left_bar')) ? 'checked' : '';
    $bars['right'] = ($h->cage->post->keyExists('right_bar')) ? 'checked' : '';
    $h->updateThemeSettings($bars, 'bars');
    $h->showMessage('Settings updated', 'green');
}

// If the user wants to revert to the defaults...
if ($h->cage->get->getAlpha('reset') == 'true')
{ 
    $bars = $h->getThemeSettings($theme, 'default');
    $h->updateThemeSettings($bars, 'bars', 'value');
    $h->showMessage('Reverted to default settings', 'green');
}

// Default settings:
$defaults['left'] = '';
$defaults['right'] = 'checked';

// Get settings from database if they exist...
$bars = $h->getThemeSettings($theme);
if (!$bars) { 
    $h->updateThemeSettings($defaults, 'bars', 'both');     // inserts settings for the first time
    $bars = $defaults;                                      // use the defaults
}
?>

<form name='theme_settings_form' action='<?php echo BASEURL; ?>admin_index.php?page=theme_settings&amp;theme=<?php echo $theme; ?>' method='post'>

<p><input type='checkbox' name='left_bar' value='left_bar' <?php echo $bars['left']; ?>>&nbsp;&nbsp;Enable left sidebar</p>
<p><input type='checkbox' name='right_bar' value='right_bar' <?php echo $bars['right']; ?>>&nbsp;&nbsp;Enable right sidebar</p>
        
<br />
<input type='hidden' name='submitted' value='true' />
<input type='submit' value='<?php echo $h->lang["main_form_save"]; ?>' />
<input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
</form>

<br />
<a href="<?php echo BASEURL; ?>admin_index.php?page=theme_settings&amp;theme=<?php echo $theme; ?>&amp;reset=true">Revert to <?php echo make_name($theme, '-'); ?> default settings</a>