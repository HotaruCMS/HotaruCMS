<?php
/**
 * name: Journal
 * description: Mini-blogging
 * version: 0.1
 * folder: journal
 * class: Journal
 * type: blog
 * hooks: install_plugin, theme_index_top, theme_index_main, header_include, admin_plugin_settings, admin_sidebar_plugin_settings, journal_post_show_post, profile_navigation, breadcrumbs
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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

class Journal
{
	/**
	 * Install Submit settings if they don't already exist
	 */
	public function install_plugin($h)
	{
		// Default settings 
		$journal_settings = $h->getSerializedSettings();
		if (!isset($journal_settings['items_per_page'])) { $journal_settings['items_per_page'] = 10; }
		if (!isset($journal_settings['allowable_tags_posts'])) { $journal_settings['allowable_tags_posts'] = "<b><i><u><a><blockquote><del>"; }
		if (!isset($journal_settings['allowable_tags_replies'])) { $journal_settings['allowable_tags_replies'] = "<b><i><u><a><blockquote><del>"; }
		$h->updateSetting('journal_settings', serialize($journal_settings));
	}


	/**
	 * Determine the pageType
	 */
	public function theme_index_top($h)
	{
		if ($h->post->type == 'blog') { $h->pageName = 'journal'; }
		
		// individual user journal:
		if ($h->pageName == 'journal') { $this->userJournal($h); }
		
		// aggregated journals:
		if ($h->pageName == 'journals') { $this->allJournals($h); }
	}
	
	
	/**
	 * Prepare user journal
	 */
	public function userJournal($h)
	{
		if ($h->pageName != 'journal') { return false; }
		
		// get user name from the url. 
		$user = $h->cage->get->testUsername('user');

		// if a journal post, get the post author's name:
		if ($h->post->id) {
			$user = $h->getUserNameFromId($h->post->author);
		}

		// If still no user, use the current user's name
		if (!$user) { $user = $h->currentUser->name; }
		
		// set the page title
		$h->pageTitle = $h->lang['journal'] . "[delimiter]" . $user;
		
		// set the page types
		$h->pageType = 'user';  // use this to hide the posts filter bar
		$h->subPage = 'user';    // pageName is 'mypage', subPage is 'user'
		
		// get journal libraries
		$journal = $this->getJournalObjects($h);
		
		// create a user object and fill it with user info (user being viewed)
		$journal->getJournalOwner($h, $user);
		
		// get journal settings
		$journal->getJournalSettings($h);
		
		// if error, set error message
		$error = $h->cage->get->testAlnumLines('error');
		if ($error) {
			$error = explode('-', $error);
			foreach ($error as $err) {
				switch ($err) {
					case 'content':
						$h->messages[$h->lang['journal_error_no_content']] = 'red';
						break;
					default:
						// do nothing
				}
			}
		}
		
		if ($h->cage->post->getAlpha('post_process') == 'newpost') { $journal->doPost($h, 'newpost'); }
		if ($h->cage->post->getAlpha('post_process') == 'editpost') { $journal->doPost($h, 'editpost'); }
	}
	
	
	/**
	 * Prepare user journal
	 */
	public function allJournals($h)
	{
		// get journal libraries
		$journal = $this->getJournalObjects($h);
		
		// get journal settings
		$journal->getJournalSettings($h);
	}
	
	/**
	 * Profile menu link to "journal"
	 */
	public function profile_navigation($h)
	{
		echo "<li><a href='" . $h->url(array('page'=>'journal', 'user'=>$h->vars['user']->name)) . "'>" . $h->lang['journal'] . "</a></li>\n";
	} 
	
	
	/**
	 * Breadcrumbs for "saved-posts"
	 */
	public function breadcrumbs($h)
	{
		if ($h->pageName != 'journal') { return false; } // not "journal" so get out of here!
	
		return "<a href='" . $h->url(array('user'=>$h->vars['user']->name)) . "'>" . $h->vars['user']->name . "</a> &raquo; " . $h->lang['journal'];
	}
	
	
	/**
	 * Include css and JavaScript
	 */
	public function header_include($h)
	{ 
		$h->includeCss('journal', 'journal');
		$h->includeJs('journal', 'urldecode');
		$h->includeJs('journal', 'journal');
	}
	
	
	/**
	 * Determine which template to show and do preparation of variables, etc.
	 */
	public function theme_index_main($h)
	{
		// stop here if not a journal
		if (($h->pageName != 'journal') && ($h->pageName != 'journals')) { return false; }

		$journal = $this->getJournalObjects($h);
		
		// wrap everything in a journal_posts div
		echo "<div id='journal_posts'>\n";
		
		if ($h->pageName == 'journal') {
			echo "<h2>" . $h->lang['journal_header'] . $h->vars['user']->name . "</h2>";
		
			// show post form if 1) logged in, 2) *your* journal, 3) not an individual entry
			if ($h->currentUser->loggedIn 
				&& ($h->currentUser->id == $h->vars['user']->id)
				&& !$h->post->id) {
				$h->displayTemplate('journal_post_form');
			}
	
			// show all posts:
			if ($h->post->id) {
				$journal->showJournalPost($h, $h->post->id);
			} else {
				$journal->showJournalPosts($h, $h->vars['user']->id);
			}
		} else {
			$journal->showJournalPosts($h);
		}
			
		echo "</div>\n";

		return true;
	}


	/**
	 * Hook where comment stuff starts
	 */
	public function journal_post_show_post($h)
	{
		$journal = $this->getJournalObjects($h, 'replies');
		
		$journal->startComments($h);
	}
	
	
	/**
	 * Create an object of type JournalPosts or if specified, JournalReplies
	 *
	 * @param string $type, default is 'posts' but accepts 'replies'
	 * @return object
	 */
	public function getJournalObjects($h, $type = 'posts')
	{
		require_once(PLUGINS . 'journal/libs/JournalBase.php');
		require_once(PLUGINS . 'journal/libs/JournalPosts.php');
		require_once(PLUGINS . 'journal/libs/JournalReplies.php');
		
		if ($type == 'posts') { 
			return new JournalPosts(); 
		} else {
			return new JournalReplies();
		}
	}
}
?>
