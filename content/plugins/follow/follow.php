<?php
/**
 * name: Follow
 * description: Basic Follower/Following plugin
 * version: 0.1
 * folder: follow
 * class: Follow
 * type: Follow
 * hooks: install_plugin,admin_plugin_settings,admin_sidebar_plugin_settings, profile_navigation, theme_index_top, breadcrumbs, theme_index_main, header_include
 * author: shibuya246
 * authorurl: http://shibuya246.com
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
 * @author    shibuya246
 * @copyright Copyright (c) 2010, shibuya246
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

class Follow
{

	/**
	 * Add follow settings fields to the db.
	 */
	public function install_plugin($h)
	{
		// Default settings
		$follow_settings = $h->getSerializedSettings();

		/**
		*
		* Type in your settings to be saved / retrieved from the db table
		* e.g. for a simple checked box  if (!isset($follow_settings['setting_var_to_save'])) { $follow_settings['setting_var_to_save'] = "checked"; }
		*
		*/
		
		$h->updateSetting('follow_settings', serialize($follow_settings));
    }
    

    /**
     * Profile menu link to "follow"
     */
    public function profile_navigation($h)
    {	
	 echo "<li><a href='" . $h->url(array('page'=>'followers', 'user'=>$h->vars['user']->name)) . "'>" . $h->lang['follow_list_followers'] . "</a> <small>(" . $h->countFollowers($h->vars['user']->id) . ")</small></li>\n";
	 echo "<li><a href='" . $h->url(array('page'=>'following', 'user'=>$h->vars['user']->name)) . "'>" . $h->lang['follow_list_following'] . "</a> <small>(" . $h->countFollowing($h->vars['user']->id) . ")</small></li>\n";

	 if ($h->vars['user']->name != $h->currentUser->name) {
	    // check if already following
	    $follow = $h->isFollowing($h->vars['user']->id);
	    if ($follow == 0)
		echo "<li><a href='" . $h->url(array('page'=>'follow', 'user'=>$h->vars['user']->name)) . "'>" . $h->lang['follow_follow_user'] . "</a></li>\n";
	    else
		echo "<li><a href='" . $h->url(array('page'=>'unfollow', 'user'=>$h->vars['user']->name)) . "'>" . $h->lang['follow_unfollow_user'] . "</a></li>\n";
	 }
    }


    /**
     * Determine page and get user details
     */
    public function theme_index_top($h)
    {
        $user = $h->cage->get->testUsername('user');
	
        if (!$user) { $user = $h->currentUser->name; }

	$follow_page = false;

	switch ($h->pageName)
	{ 
	    case 'followers':
		$follow_page = true;
		$h->pageTitle = $h->lang['follow_list_followers'] . "[delimiter]" . $user;
		break;
	    case 'following':
		$follow_page = true;
		$h->pageTitle = $h->lang['follow_list_followers'] . "[delimiter]" . $user;
		break;
	    case 'follow':
	    case 'unfollow':
		$follow_page = true;
		$h->pageTitle = $h->lang['follow_list_following'] . "[delimiter]" . $user;
		break;
	}


	// set page types & create UserAuth and MessagingFuncs objects
        if ($follow_page) {
	    $h->pageType = 'user';  // this setting hides the posts filter bar
	    $h->subPage = 'user';	    	   

	    // create a user object and fill it with user info (user being viewed)
	    $h->vars['user'] = new UserAuth();
	    $h->vars['user']->getUserBasic($h, 0, $user);

	    switch ($h->pageName)
	    {
		case 'followers':		    
		    $query = $h->getFollowers($h->vars['user']->id, 'query');
		    $h->vars['follow_list'] = $h->pagination($query, count($query), 20);
		    // how to also include the latest actvitiy for this person and a follow/unfollow button
		    break;
		case 'following':
		    $query = $h->getFollowing($h->vars['user']->id, 'query');
		    $h->vars['follow_list'] = $h->pagination($query, count($query), 20);
		    break;
		case 'follow':
		    $result = $h->follow($h->vars['user']->id);
		    $h->messages[$h->lang['follow_newfollow']] = 'green';
		    $query = $h->getFollowing($h->vars['user']->id, 'query');
		    $h->vars['follow_list'] = $h->pagination($query, count($query), 20);
		    break;
		case 'unfollow':
		    $result = $h->unfollow($h->vars['user']->id);
		    $h->messages[$h->lang['follow_unfollow']] = 'green';
		    $query = $h->getFollowing($h->vars['user']->id, 'query');
		    $h->vars['follow_list'] = $h->pagination($query, count($query), 20);
		    break;
		}
	}
    }

    /**
     * Breadcrumbs for follow pages
     */
    public function breadcrumbs($h)
    {
        $user = $h->cage->get->testUsername('user');
        if (!$user) { $user = $h->currentUser->name; }

        switch ($h->pageName)
        {
            case 'followers':
                return "<a href='" . $h->url(array('user'=>$user)) . "'>" . $user . "</a> &raquo; " . $h->lang['follow_list_followers'];
                break;
            case 'following':
		return "<a href='" . $h->url(array('user'=>$user)) . "'>" . $user . "</a> &raquo; " . $h->lang['follow_list_following'];
		break;
	    case 'follow':
	    case 'unfollow':
                return $h->lang['follow_list_following'];
                break;            
        }
    }

    /**
     * Display pages
     */
    public function theme_index_main($h)
    {
        if (isset($h->vars['user']->id) && ($h->currentUser->id != $h->vars['user']->id)) { return false; }

        switch ($h->pageName)
        {
            case 'followers':		
                $h->displayTemplate('follow_followers');
                return true;
                break;
            case 'following':
                $h->displayTemplate('follow_followers');
                return true;
                break;
            case 'follow':
	    case 'unfollow':
                $h->displayTemplate('follow_followers');
                return true;
                break;
        }
    }
  
}
?>
