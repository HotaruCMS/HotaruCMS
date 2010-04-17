<?php
/**
 * Wall Base functions
 * Notes: This file is part of the SB Submit plugin.
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

class WallBase
{
    /**
     * Check if a post has been posted
     *
     * @return bool
     */
    public function checkPosted($h)
    {
        $new = $h->cage->post->getAlpha('comment_process') == 'newcomment';
        $edit = $h->cage->post->getAlpha('comment_process') == 'editcomment';
        
        return ($new || $edit) ? true : false;
    }
    
    
    /**
     * Fill vars for the post form
     */
    public function preparePostForm($h)
    {
        
        $h->vars['setPending'] = '';
        
        $h->comment->id = 0;
    }
    
    
    /**
     * Show posts and replies
     */
    public function showWallPosts($h)
    {
        // GET ALL PARENT COMMENTS
        $parents = $h->comment->readAllParents($h, $h->currentUser->id, $h->comment->order); // durrent user id is temporary, should be wall owner id
                
        echo "<!--  START COMMENTS_WRAPPER -->\n";
        echo "<div id='comments_wrapper'>\n";
        
        $pagedResults = $h->paginationFull($parents, $h->comment->itemsPerPage);

        if (isset($pagedResults->items)) {
        // cycle through the parents, and go get their children
            foreach($pagedResults->items as $parent) {

                    $this->displayComment($h, $parent);
                    $this->commentTree($h, $parent->comment_id, 0);
                    $h->comment->depth = 0;
            }
        }
        
        echo "</div><!-- close comments_wrapper -->\n";
        echo "<!--  END COMMENTS -->\n";
            
        if ($pagedResults) {
            echo $h->pageBar($pagedResults);
        }
    }
    
    
    /**
     * Recurse through comment tree
     *
     * @param int $item_id - id of current comment
     * @param int $depth - for comment nesting
     * @return bool
     */
    public function commentTree($h, $item_id, $depth)
    {
        while ($children = $h->comment->readAllChildren($h, $item_id)) {
            foreach ($children as $child) {
                $depth++;
                if ($depth == $h->comment->levels) { 
                    // Prevent depth exceeding nesting levels
                    // levels start at 0 so we're using -1.
                    $depth = $h->comment->levels - 1;
                }
                $h->comment->depth = $depth;
                $this->displayComment($h, $child);
                if ($this->commentTree($h, $child->comment_id, $depth)) {
                    return true;
                } else {
                    $depth--; // no more children for previous comment, come back up a level
                }
            }
            return false;
        }
        return false;
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
            $h->displayTemplate('wall_posts', 'wall', false);

            // show the reply form:
            $h->displayTemplate('wall_reply_form', 'wall', false);
        }
    }
    
    
    /**
     * Make a post
     */
    public function doPost($h)
    {
        if (!$h->currentUser->loggedIn) { return false; }
        
        // fill the comment object
        $this->fillCommentObject($h);
        
        // check if this is a new post and process it
        $this->newPost($h);

        // check and process a post being edited
        $this->editingPost($h);
        
        header("Location: " . BASEURL);    // Go to the post
        die();
    }
   

    /**
     * Fill comment object
     */
    public function fillCommentObject($h)
    {
        if ($h->cage->post->keyExists('comment_content')) {
            $h->comment->content = sanitize($h->cage->post->getHtmLawed('comment_content'), 'tags', $h->comment->allowableTags);
        }
        
        if ($h->cage->post->keyExists('comment_post_id')) {
            $h->comment->postId = $h->cage->post->testInt('comment_post_id');
        }

        if ($h->cage->post->keyExists('comment_user_id')) {
            $h->comment->author = $h->cage->post->testInt('comment_user_id');
        }
    
        if ($h->cage->post->keyExists('comment_parent')) {
            $h->comment->parent = $h->cage->post->testInt('comment_parent');
            if ($h->cage->post->getAlpha('comment_process') == 'editcomment') {
                $h->comment->id = $h->cage->post->testInt('comment_parent');
            }
        }
    }
    
    
    /**
     * New post
     */
    public function newPost($h)
    {
        if ($h->cage->post->getAlpha('comment_process') != 'newcomment') { return false; }
        
        // before posting, we need to be certain this user has permission:
        $safe = false;
        $can_comment = $h->currentUser->getPermission('can_comment');
        if ($can_comment == 'yes') { $safe = true; }
        if ($can_comment == 'mod') { $safe = true; $h->comment->status = 'pending'; }
        
        $result = array(); // holds results from addComment function
        
        // Okay, safe to add the comment...
        if ($safe) {
            if (!$h->comment->postId) { $h->comment->postId = $h->currentUser->id; }
            $result = $h->comment->addComment($h);
        }
        
        if ($result['exceeded_daily_limit']) {
            $h->messages[$h->lang['comment_moderation_exceeded_daily_limit']] = 'green';
        } elseif ($result['exceeded_url_limit']) {
            $h->messages[$h->lang['comment_moderation_exceeded_url_limit']] = 'green';
        } elseif ($result['not_enough_comments']) {
            $h->messages[$h->lang['comment_moderation_not_enough_comments']] = 'green';
        }
    }
    
    
    /**
     * Editing post
     */
    public function editingPost($h)
    {
        if ($h->cage->post->getAlpha('comment_process') != 'editcomment') { return false; }

        // before editing, we need to be certain this user has permission:
        $safe = false;
        $can_edit = $h->currentUser->getPermission('can_edit_comments');
        if ($can_edit == 'yes') { $safe = true; }
        if (($can_edit == 'own') && ($h->currentUser->id == $h->comment->author)) { $safe = true; }
        if ($safe) {
            $h->comment->editComment($h);
        }
    }
}
?>