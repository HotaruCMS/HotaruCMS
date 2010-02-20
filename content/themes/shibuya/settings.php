<?php
/**
 * Theme name: shibuya
 * Template name: settings.php
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
 * @author    shibuya246 <blog@shibuya246.com>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.shibuya246.com/
 */

// If the form has been submitted, save the data...
if ($h->cage->post->getAlpha('submitted') == 'true')
{    
    $shibuya['site_color'] = $h->cage->post->testAlnum('site_color');
    $h->updateThemeSettings($shibuya, 'shibuya');
    $h->showMessage('Settings updated', 'green');
}

// If the user wants to revert to the defaults...
if ($h->cage->get->getAlpha('reset') == 'true')
{
    $shibuya = $h->getThemeSettings($theme, 'default');
    $h->updateThemeSettings($shibuya, 'shibuya', 'value');
    $h->showMessage('Reverted to default settings', 'green');
}

// Default settings:
$defaults['site_color'] = 'ffcc00';


// Get settings from database if they exist...
$shibuya = $h->getThemeSettings($theme);

if (!$shibuya) {  
    $h->updateThemeSettings($defaults, 'shibuya', 'both');     // inserts settings for the first time
    $shibuya = $defaults;                                      // use the defaults
}
?>

<form name='theme_settings_form' action='<?php echo BASEURL; ?>admin_index.php?page=theme_settings&amp;theme=<?php echo $theme; ?>' method='post'>

<p><label for="site_color">Base color for your Site&nbsp;#</label><input id='site_color' type='text' name='site_color' value='<?php echo $shibuya['site_color']; ?>' <?php echo $shibuya['site_color']; ?>>
&nbsp;&nbsp;<a href="http://www.colorpicker.com/" target="blank">Online color picker.</a></p>
<br />
<input type='hidden' name='submitted' value='true' />
<input type='submit' value='<?php echo $h->lang["main_form_save"]; ?>' />
<input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
</form>



<br />
<a href="<?php echo BASEURL; ?>admin_index.php?page=theme_settings&amp;theme=<?php echo $theme; ?>&amp;reset=true">Revert to <?php echo make_name($theme, '-'); ?> default settings</a>

