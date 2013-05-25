<?php // settings.php

$cTheme = rtrim(THEME, '/'); // currently active theme, not necessarily the one we're viewing.
$vTheme = $h->cage->get->testAlnumLines('theme'); // theme we're viewing the settings for.

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
		$h->updateThemeSettings($theme_settings, $vTheme);
		$h->showMessage('Settings updated', 'green');
	}
}
 
// If the user wants to revert to the defaults...
if ($h->cage->get->getAlpha('reset') == 'true')
{ 
	$theme_settings = $h->getThemeSettings($vTheme, 'default');
	$h->updateThemeSettings($theme_settings, $vTheme, 'value');
	$h->showMessage('Reverted to default settings', 'green');
}
 
// Default settings:
$defaults['tagline'] = "Possibly the greatest site on the Internet.";
 
// Get settings from database if they exist...
$theme_settings = $h->getThemeSettings($vTheme);
if (!$theme_settings) { 
	$h->updateThemeSettings($defaults, $vTheme, 'both');     // inserts settings for the first time
	$theme_settings = $defaults;                                      // use the defaults
}
?>
 
<form name='theme_settings_form' action='<?php echo BASEURL; ?>admin_index.php?page=theme_settings&amp;theme=<?php echo $vTheme; ?>' method='post'>

<p>Tagline: <input type='text' size="70" name='tagline' value='<?php echo $theme_settings['tagline']; ?>'></p>
 
<br />
<input type='hidden' name='submitted' value='true' />
<input type='submit' value='<?php echo $h->lang("main_form_save"); ?>' />
<input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
</form>
 
<br />
<a href="<?php echo BASEURL; ?>admin_index.php?page=theme_settings&amp;theme=<?php echo $vTheme; ?>&amp;reset=true"><?php echo $h->lang("admin_theme_theme_revert_settings"); ?></a>
