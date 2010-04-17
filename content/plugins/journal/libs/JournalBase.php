<?php
/**
 * Journal Base functions
 * Notes: This file is part of the Journal plugin.
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

class JournalBase
{
	protected $postsLimit = 0;	// max posts per page
	
	/**
	 * get journal settings
	 */
	public function getJournalSettings($h)
	{
		// Create a new global object called "comment".
		if (!is_object($h->comment)) { 
			require_once(LIBS . 'Comment.php');
			$h->comment = new Comment();
		}
		
		// get settings for the database
		$journal_settings = $h->getSerializedSettings('journal');
		$this->postsLimit = $journal_settings['items_per_page'];
		$h->post->allowableTags = $journal_settings['allowable_tags_replies'];
		$h->comment->allowableTags = $journal_settings['allowable_tags_replies'];
		
		// other settings
		$h->comment->order = "DESC";
	}
	

	/**
	 * get journal owner
	 */
	public function getJournalOwner($h, $user = '')
	{
		if (!$user) {
			// get user name from the url. If not present, use the current user's name
			$user = $h->cage->get->testUsername('user');
			if (!$user) { $user = $h->currentUser->name; }
		}
		
		$h->vars['user'] = new UserAuth();
		$h->vars['user']->getUserBasic($h, 0, $user);
	}
}
?>