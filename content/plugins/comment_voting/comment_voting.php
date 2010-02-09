<?php
/**
 * name: Comment Voting
 * description: Adds voting ability to posted stories.
 * version: 0.2
 * folder: comment_voting
 * class: CommentVoting
 * type: comment_vote
 * requires: comments 1.3
 * hooks: header_include, show_comments_votes
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

class CommentVoting
{
    /**
     * Includes css and javascript for the comment vote buttons.
     */
    public function header_include($h)
    {
        $h->includeCss('comment_voting');
        $h->includeJs('comment_voting');
        $h->includeJs('comment_voting', 'json2.min');
    }
    
    
    /**
     * Show comment vote template
     */
    public function show_comments_votes($h)
    {
        $h->vars['already_voted'] = false;
        if ($h->currentUser->loggedIn) {
            $sql = "SELECT cvote_user_id FROM " . TABLE_COMMENTVOTES . " WHERE cvote_comment_id = %d AND cvote_user_id = %d";
            $h->vars['already_voted'] = $h->db->get_var($h->db->prepare($sql, $h->comment->id, $h->currentUser->id));
        }
        
        $h->displayTemplate('comment_votes', 'comment_voting', false);
    }
}

?>