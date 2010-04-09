<?php
/**
 * name: Link Bar
 * description: Bare bones link bar with an iframe
 * version: 0.4
 * folder: link_bar
 * class: LinkBar
 * type: bar
 * requires: sb_base 0.8
 * hooks: install_plugin, user_settings_pre_save, user_settings_fill_form, user_settings_extra_settings, sb_base_show_post_pre_title, sb_base_show_post_title, sb_base_pre_rss_forward, admin_plugin_settings, admin_sidebar_plugin_settings
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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
 * @copyright Copyright (c) 2010
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://shibuya246.com
 */
class LinkBar
{
    /**
     * Install plugin
     */
    public function install_plugin($h)
    {
        // Add "external_link_bar" option to the default user settings
        $base_settings = $h->getDefaultSettings('base'); // originals from plugins
        $site_settings = $h->getDefaultSettings('site'); // site defaults updated by admin
        if (!isset($base_settings['external_link_bar'])) { 
            $base_settings['external_link_bar'] = "checked";
            $site_settings['external_link_bar'] = "checked";
            $h->updateDefaultSettings($base_settings, 'base');
            $h->updateDefaultSettings($site_settings, 'site');
        }
        
        // get settings
        $link_bar_settings = $h->getSerializedSettings('link_bar');
        
        // add default settingif not already set
        if (!isset($link_bar_settings['show_logged_in'])) { $link_bar_settings['show_logged_in'] = "checked"; }
        if (!isset($link_bar_settings['show_logged_out'])) { $link_bar_settings['show_logged_out'] = "checked"; }
        
        // update link bar settings
        $h->updateSetting('link_bar_settings', serialize($link_bar_settings), 'link_bar');
    }
    
    
	/**
	 * User Settings - fill the form
	 */
	public function user_settings_fill_form($h)
	{
		if (!isset($h->vars['settings']) || !$h->vars['settings']) { return false; }
	
		// if bar is disabled for logged in users, return false
		$lb_settings = $h->getSerializedSettings('link_bar');
		if (!$lb_settings['show_logged_in']) { return false; }
		
		if ($h->vars['settings']['external_link_bar']) {
			$h->vars['link_bar_yes'] = "checked"; 
			$h->vars['link_bar_no'] = ""; 
		} else { 
			$h->vars['link_bar_yes'] = ""; 
			$h->vars['link_bar_no'] = "checked"; 
		}
	}
        
        
    /**
     * User Settings - html for form
     */
    public function user_settings_extra_settings($h)
    {
        if (!isset($h->vars['settings']) || !$h->vars['settings']) { return false; }
        
		// if bar is disabled for logged in users, return false
		$lb_settings = $h->getSerializedSettings('link_bar');
		if (!$lb_settings['show_logged_in']) { return false; }
		
        // Use the external link bar?
        echo "<tr>\n";
        echo "<td>" . $h->lang['link_bar_user_settings'] . "</td>\n";
        echo "<td><input type='radio' name='link_bar' value='yes' " . $h->vars['link_bar_yes'] . "> " . $h->lang['link_bar_yes'] . " &nbsp;&nbsp;\n";
        echo "<input type='radio' name='link_bar' value='no' " . $h->vars['link_bar_no'] . "> " . $h->lang['link_bar_no'] . "</td>\n";
        echo "</tr>\n";
    } 
    
    
    /**
     * User Settings - before saving
     */
    public function user_settings_pre_save($h)
    {
		// if bar is disabled for logged in users, return false
		$lb_settings = $h->getSerializedSettings('link_bar');
		if (!$lb_settings['show_logged_in']) { return false; }
		
        // Use the external link bar?
        if ($h->cage->post->getAlpha('link_bar') == 'yes') {
            $h->vars['settings']['external_link_bar'] = "checked"; 
        } else { 
            $h->vars['settings']['external_link_bar'] = "";
        }
    }  
    
    
    /**
     * Check to see if we should redirect to the link bar page
     */
    public function sb_base_pre_rss_forward($h)
    {
        $access= false;
    
        // if using rss forwarding, enable use of the link bar
        if ($h->cage->get->keyExists('forward')) {
            $post_id = $h->cage->get->testInt('forward');
            $access = true;
        }
        
        // if coming from a post with a link parameter, enable use of the link bar
        if ($h->cage->get->keyExists('link')) {
            $post_id = $h->cage->get->testInt('link');
            $access = true;
        }
        
        // if neither RSS forwarding or "link" URL, get out of here!
        if (!$access) { return false; }
        
        if (!$this->linkBarEnabled($h)) { return false; }
        
        // read the post into $h and display the link bar
        if ($post_id) {
            $h->readPost($post_id);
            
            // if the source url contains the BASEURL, don't use the bar
            // This is so we don't show the bar on posts submitted without a link
            if (strpos($h->post->origUrl, BASEURL) !== false) { return false; }
            
            // get the settings
            $h->vars['link_bar_settings'] = $h->getSerializedSettings('link_bar');
            
            // plugin hook
            $h->pluginHook('link_bar_pre_template');
            
            // display link bar template
            $h->displayTemplate('link_bar_top');
            die(); exit;
        }
    }


    /**
     * Modify the post URL with an appended "link" parameter
     */
    public function sb_base_show_post_pre_title($h)
    {
        if (!$this->linkBarEnabled($h)) { return false; }
        
        $h->vars['link_bar_source'] = $h->post->origUrl;
        $h->post->origUrl = $h->url(array('page'=>$h->post->id, 'link'=>$h->post->id));
    }
    
    
    /**
     * Change $h->post->origUrl back so other plugins like Video Inc can use it.
     */
    public function sb_base_show_post_title($h)
    {
        if (!isset($h->vars['link_bar_source'])) { return false; }
        
        $h->post->origUrl = $h->vars['link_bar_source'];
    }
    
	/**
	 * Check if the user has link bar enabled in user settings, and if globally enabled
	 *
	 * @return bool
	 */
	public function linkBarEnabled($h)
	{
		// Get settings from database if they exist...
		$lb_settings = $h->getSerializedSettings('link_bar');
		
		if ($h->currentUser->loggedIn)
		{ 
			// if bar is disabled for logged in users, return false
			if (!$lb_settings['show_logged_in']) { return false; }
			
			$user_settings = $h->currentUser->getProfileSettingsData($h, 'user_settings');
			if (isset($user_settings['external_link_bar']) && (!$user_settings['external_link_bar'])) { 
				return false; 
			}
		}
		else
		{
			// if bar is disabled for logged out users, return false
			if (!$lb_settings['show_logged_out']) { return false; }
		}
		
		return true;
	}
}