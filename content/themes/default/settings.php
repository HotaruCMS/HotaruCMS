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
		$theme_settings['leftSpan'] = '9';
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
		$h->showMessage($error, 'alert-danger');
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
 
<form role="form" class="form" name='theme_settings_form' action='<?php echo BASEURL; ?>admin_index.php?page=theme_settings&amp;theme=<?php echo $vTheme; ?>#tab_settings' method='post'>

    <div class="form-group">
        <label for="tagline">Tagline</label>
        <input class="form-control" type='text' size="70" name='tagline' value='<?php echo $theme_settings['tagline']; ?>'/>
    </div>

    <div class="form-group">
        <label for="site_color">Base color for your Site</label>
        <div class="input-group">
            <span class="input-group-addon">#</span>
            <input id='site_color' class="form-control" type='text' name='link_color' value='<?php echo $theme_settings['link_color']; ?>' <?php echo $theme_settings['link_color']; ?>>
        </div>
        <div class="help-block"><a href="http://www.colorpicker.com/" target="blank">Online color picker</a></div>
    </div>

    <div class="checkbox">
        <label>
            <input type='checkbox' id='fullWidth' name='fullWidth' value='fullWidth' <?php echo $theme_settings['fullWidth']; ?> />
            Full width UI
        </label>
    </div>

    <div class="checkbox">
        <label>
            <input type='checkbox' id='userProfile_tabs' name='userProfile_tabs' value='userProfile_tabs' <?php echo $theme_settings['userProfile_tabs']; ?> />
            Tabs on user profile
        </label>
    </div>
     
    <div class="form-group">
        <label for="left_col">Left Column Ratio</label>
        <input class="form-control" type='text' size="20" name='leftSpan' value='<?php echo $theme_settings['leftSpan']; ?>'/>
        <div class="help-block">Note: The default setting is 9, the max is 12</div>
    </div>

    <div class="form-actions">
        <input type='hidden' name='submitted' value='true' />
        <input type='submit' class="btn btn-primary"value='<?php echo $h->lang("main_form_save"); ?>' />
        <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
    </div>
</form>
 
<br />
<a href="<?php echo BASEURL; ?>admin_index.php?page=theme_settings&amp;theme=<?php echo $vTheme; ?>&amp;reset=true"><?php echo $h->lang("admin_theme_theme_revert_settings"); ?></a>
