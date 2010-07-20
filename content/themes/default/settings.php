<?php // settings.php

$theme = rtrim(THEME, '/');

// If the form has been submitted, save the data...
if ($h->cage->post->getAlpha('submitted') == 'true')
{ 
	$error = '';
	
	if ($h->cage->post->keyExists('tagline')) {
		$theme_settings['tagline'] = $h->cage->post->getHtmLawed('tagline');
	} else {
		$error = 'Invalid tagline';
	}
	
	if ($error) {
		$h->showMessage($error, 'red');
	} else {
		$h->updateThemeSettings($theme_settings, $theme);
		$h->showMessage('Settings updated', 'green');
	}
}
 
// If the user wants to revert to the defaults...
if ($h->cage->get->getAlpha('reset') == 'true')
{ 
	$theme_settings = $h->getThemeSettings($theme, 'default');
	$h->updateThemeSettings($theme_settings, $theme, 'value');
	$h->showMessage('Reverted to default settings', 'green');
}
 
// Default settings:
$defaults['tagline'] = "Social Bookmarking";
 
// Get settings from database if they exist...
$theme_settings = $h->getThemeSettings($theme);
if (!$theme_settings) { 
	$h->updateThemeSettings($defaults, $theme, 'both');     // inserts settings for the first time
	$theme_settings = $defaults;                                      // use the defaults
}
?>
 
<form name='theme_settings_form' action='<?php echo BASEURL; ?>admin_index.php?page=theme_settings&amp;theme=<?php echo $theme; ?>' method='post'>

<p>Tagline: <input type='text' size="70" name='tagline' value='<?php echo $theme_settings['tagline']; ?>'></p>
 
<br />
<input type='hidden' name='submitted' value='true' />
<input type='submit' value='<?php echo $h->lang["main_form_save"]; ?>' />
<input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
</form>
 
<br />
<a href="<?php echo BASEURL; ?>admin_index.php?page=theme_settings&amp;theme=<?php echo $theme; ?>&amp;reset=true">Revert to <?php echo make_name($theme, '-'); ?> default settings</a>
