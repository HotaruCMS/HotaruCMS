<?php // settings.php

$cTheme = rtrim(THEME, '/'); // currently active theme, not necessarily the one we're viewing.
$vTheme = $h->cage->get->testAlnumLines('theme'); // theme we're viewing the settings for.

// Default settings:
$defaults['tagline'] = "Possibly the greatest site on the Internet.";
$defaults['fullWidth'] = '0';  // must be something otherwise it wont save
$defaults['link_color'] = 'ffcc00';
$defaults['leftSpan'] = 9;
$defaults['userProfile_tabs'] = false;

// If the form has been submitted, save the data...
if ($h->cage->post->getAlpha('submitted') == 'true')
{ 
	$error = '';
	
	if ($h->cage->post->keyExists('tagline')) {
		$theme_settings['tagline'] = $h->cage->post->getHtmLawed('tagline');
	} else {
		$error = 'Invalid tagline';
	}
        
        // full width UI:
        if ($h->cage->post->keyExists('fullWidth')) { 
            $theme_settings['fullWidth'] = 'checked';
        } else {
            $theme_settings['fullWidth'] = '0';  // must be something otherwise it wont save
	}
        
        // full width UI:
        if ($h->cage->post->keyExists('leftSpan')) {
		$theme_settings['leftSpan'] = $h->cage->post->getInt('leftSpan');
	} else {
		$error = 'Invalid integer for left column span';
	}
        
        // link color
        if ($h->cage->post->keyExists('link_color')) { 
                $theme_settings['link_color'] = $h->cage->post->testAlnum('link_color');
        } else {
		$error = 'Invalid color';
	}
        
        // userProfile_tabs
        if ($h->cage->post->keyExists('userProfile_tabs')) { 
            $theme_settings['userProfile_tabs'] = 'checked';
        } else {
            $theme_settings['userProfile_tabs'] = '0';  // must be something otherwise it wont save
	}
	
	if ($error) {
		$h->showMessage($error, 'alert-error');
	} else {
		$h->updateThemeSettings($theme_settings, $vTheme);
		$h->showMessage('Settings updated', 'alert-success');
	}
        
        $h->clearCache('html_cache', false);
        
}
 
// If the user wants to revert to the defaults...
if ($h->cage->get->getAlpha('reset') == 'true')
{ 
        $h->updateThemeSettings($defaults, $vTheme, 'both'); 	
        $theme_settings = $defaults;
	$h->showMessage('Reverted to default settings', 'alert-success');
}
 

 
// Get settings from database if they exist...
$theme_settings = $h->getThemeSettings($vTheme);
if (!$theme_settings) { 
	$h->updateThemeSettings($defaults, $vTheme, 'both');     // inserts settings for the first time
	$theme_settings = $defaults;                                      // use the defaults
}

?>
 
<form class="form" name='theme_settings_form' action='<?php echo BASEURL; ?>admin_index.php?page=theme_settings&amp;theme=<?php echo $vTheme; ?>#tab_settings' method='post'>

<p>Tagline: <input type='text' size="70" name='tagline' value='<?php echo $theme_settings['tagline']; ?>'/></p>
 
<p><label for="site_color">Base color for your Site&nbsp;#</label><input id='site_color' type='text' name='link_color' value='<?php echo $theme_settings['link_color']; ?>' <?php echo $theme_settings['link_color']; ?>>
&nbsp;&nbsp;<a href="http://www.colorpicker.com/" target="blank">Online color picker.</a></p>

<p><input type='checkbox' name='fullWidth' value='<?php echo $theme_settings['fullWidth']; ?>' />&nbsp;Full width UI&nbsp;&nbsp;

<p><input type='checkbox' name='userProfile_tabs' value='<?php echo $theme_settings['userProfile_tabs']; ?>' />&nbsp;Tabs on user profile&nbsp;&nbsp;
    
<p>Left Column Span: <input type='text' size="20" name='leftSpan' value='<?php echo $theme_settings['leftSpan']; ?>'/>
<br/>Note: The default setting is 9, the max is 12
</p>
 
    
<br />
    <div class="form-actions">
        <input type='hidden' name='submitted' value='true' />
        <input type='submit' class="btn btn-primary"value='<?php echo $h->lang("main_form_save"); ?>' />
        <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
    </div>
</form>
 
<br />
<a href="<?php echo BASEURL; ?>admin_index.php?page=theme_settings&amp;theme=<?php echo $vTheme; ?>&amp;reset=true"><?php echo $h->lang("admin_theme_theme_revert_settings"); ?></a>
