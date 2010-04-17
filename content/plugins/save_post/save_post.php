<?php
/**
 * name: Save Posts
 * description: Save posts as favorites
 * version: 0.6
 * folder: save_post
 * class: SavePost
 * requires: sb_base 0.1
 * hooks: install_plugin, header_include, header_include_raw, theme_index_top, theme_index_main, profile_navigation, breadcrumbs, sb_base_show_post_extra_fields
 * author: William Dahlheim
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
 * @author	Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link	  http://www.hotarucms.org/
 */

class SavePost
{
	
	/**
	* Register Saved Post widget
	*/
	public function install_plugin($h)
	{
		$h->addWidget('save_post', 'save_post', '');
	}

    /**
     * Determine if we're looking at a user's saved posts page
     */
    public function theme_index_top($h)
    {
        if ($h->pageName != 'saved-posts') { return false; } // not "saved-posts" so get out of here!
        
        // get user name from the url. If not present, use the current user's name
        $user = $h->cage->get->testUsername('user');
        if (!$user) { $user = $h->currentUser->name; }

        // set the page title
        $h->pageTitle = $h->lang['save_post_title'] . "[delimiter]" . $user;

        // set the page types
        $h->pageType = 'user';  // use this to hide the posts filter bar
        $h->subPage = 'user';    // pageName is 'mypage', subPage is 'user'

        // create a user object and fill it with user info (user being viewed)
        $h->vars['user'] = new UserAuth();
        $h->vars['user']->getUserBasic($h, 0, $user);
    }
    

    /**
     * Profile menu link to "saved-posts"
     */
    public function profile_navigation($h)
    {
         echo "<li><a href='" . $h->url(array('page'=>'saved-posts', 'user'=>$h->vars['user']->name)) . "'>" . $h->lang['save_post_title'] . "</a></li>\n";
    } 
    
    
    /**
     * Breadcrumbs for "saved-posts"
     */
    public function breadcrumbs($h)
    {
        if ($h->pageName != 'saved-posts') { return false; } // not "saved-posts" so get out of here!
        
        return "<a href='" . $h->url(array('user'=>$h->vars['user']->name)) . "'>" . $h->vars['user']->name . "</a> &raquo; " . $h->lang['save_post_title'];
    }
    
    
    /**
     * Display "mypage"
     */
    public function theme_index_main($h)
    {
        if ($h->pageName != 'saved-posts') { return false; } // not "saved-posts" so get out of here!

        $h->displayTemplate('save_post_page');
        return true;
    } 
    
    
	/**
	* Displays Save Post widget
	*/
	public function widget_save_post($h)
	{
		$h->displayTemplate('save_post_widget', 'save_post');
	}
	
	/**
	* Show the Save Post link in the extra field
	**/
	public function sb_base_show_post_extra_fields($h)
	{
		if ($h->currentUser->loggedIn) {
			
			$profile = $h->currentUser->getProfileSettingsData($h, 'user_profile', $h->currentUser->id);

			if ( isset($profile['saved_posts']) && in_array( $h->post->id, array_values($profile['saved_posts']) ) ) {
				echo '<li><a href="javascript://" class="save_post_link remove_post_item" id="post_' . $h->post->id . '">' . $h->lang['save_post_remove'] .'</a></li>';
			} else {
				echo '<li><a href="javascript://" class="save_post_link save_post_item" id="post_' . $h->post->id . '">' . $h->lang['save_post_save'] .'</a></li>';
			}
		}
	}

	/**
     * JS 
     */
    public function header_include_raw($h)
    {
		if ($h->currentUser->loggedIn) {
			echo '
	<script type="text/javascript">
	var save_post_label_save = "' . $h->lang['save_post_save'] . '";
	var save_post_label_remove = "' . $h->lang['save_post_remove'] . '";
	var save_post_label_empty = "' . $h->lang['save_post_empty'] . '";
	$(document).ready(function(){
		$("a.save_post_item, a.remove_post_item").live("click", function(){ save_posts( $(this).attr("id").substring(5) ); });
		$(".save_post_widget_item").live("click", function(){ save_post_remove_widget_item( $(this).parent().attr("id").substring(17) );	});
	});
	</script>';
		}
	}
}
?>