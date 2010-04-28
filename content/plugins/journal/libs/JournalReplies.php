<?php
/**
 * Journal comment functions
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

class JournalReplies extends Comments
{
	/**
	 * Start overriding the default Comments plugin functions
	 */
	public function startComments($h)
	{
		$this->prepareShowComments($h);
		
		if (!$h->isPage('submit3'))
		{
			$comments_settings = $h->getSerializedSettings('comments');
			$h->comment->pagination = $comments_settings['comment_pagination'];
			$h->comment->order = $comments_settings['comment_order'];
			$h->comment->itemsPerPage = $comments_settings['comment_items_per_page'];
			
			$this->showComments($h);
		}
		
		$this->checkCommentDetails($h);
	}
	
	
    /**
     * Show comments
     */
	public function showComments($h)
    {        
        // GET ALL PARENT COMMENTS
        $parents = $h->comment->readAllParents($h, $h->post->id, $h->comment->order);
                
        echo "<!--  START COMMENTS_WRAPPER -->\n";
        echo "<div class='comments_wrapper'>\n";
        echo "<h2>" . $h->countComments(false, $h->lang['comments_leave_comment']) . "</h2>\n";
            
        // IF PAGINATING COMMENTS:
        if ($h->comment->pagination)
        {
            $pagedResults = $h->paginationFull($parents, $h->comment->itemsPerPage);

            if (isset($pagedResults->items)) {
            // cycle through the parents, and go get their children
                foreach($pagedResults->items as $parent) {
    
                        $this->displayComment($h, $parent);
                        $this->commentTree($h, $parent->comment_id, 0);
                        $h->comment->depth = 0;
                }
            }
        }
        // IF NO PAGINATION:
        else
        {
            if ($parents) { 
                // cycle through the parents, and go get their children
                foreach ($parents as $parent) {
                    $this->displayComment($h, $parent);
                    $this->commentTree($h, $parent->comment_id, 0);
                    $h->comment->depth = 0;
                }
            }
        }

        echo "</div><!-- close comments_wrapper -->\n";
        echo "<!--  END COMMENTS -->\n";
        
        if ($h->comment->pagination && $pagedResults) {
            echo $h->pageBar($pagedResults);
        }
	}
	
	/**
	 * Display a comment
	 *
	 * @param array $item - current comment
	 */
	public function displayComment($h, $item, $all = false)
	{
		if ($h->isPage('submit2')) { return false; }
		
		$h->comment->readComment($h, $item);
		if ($h->comment->status == 'approved') {
			$h->displayTemplate('journal_show_comments', 'journal', false);
			
			// don't show the reply form in these cases:
			//if ($all) { return false; } // we're looking at the main comments page
			if ($h->currentUser->getPermission('can_comment') == 'no') { return false; }
			if (!$h->currentUser->loggedIn) { return false; }
			if ($h->comment->thisForm == 'closed') { return false; }
			if ($h->comment->allForms != 'checked') { return false; }
			
			// show the reply form:
			$h->vars['subscribe'] = ($h->comment->subscribe) ? 'checked' : '';
			$h->displayTemplate('journal_comment_form', 'journal', false);
		}
	}
	
	
    /**
     * Show last comment form
     */
    public function lastCommentForm($h)
    {
        // force non-reply form to have parent "0" and depth "0"
        $h->comment->id = 0;
        $h->comment->depth = 0;
        $h->vars['subscribe'] = ($h->comment->subscribe) ? 'checked' : '';
        $h->displayTemplate('journal_comment_form', 'journal', false);
        
        $h->pluginHook('comments_post_last_form');
    }
}
?>