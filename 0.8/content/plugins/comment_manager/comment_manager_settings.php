<?php
/**
 * File: plugins/comment_manager/comment_manager_settings.php
 * Purpose: The functions that do the hard work such as adding, deleting and sorting categories.
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
    
class CommentManagerSettings extends CommentManager
{
    /**
     * Main function that calls others
     *
     * @return bool
     */
    public function settings()
    {    
        // clear variables:
        $this->hotaru->vars['search_term'] = '';
        $this->hotaru->vars['comment_status_filter'] = 'pending';
        
        // Get unique statuses for Filter form:
        $this->hotaru->vars['statuses'] = $this->hotaru->post->getUniqueStatuses(); 
        
        require_once(PLUGINS . 'comments/libs/Comment.php');
        $c = new Comment($this->hotaru);
        
        
        // approve comment
        if ($this->cage->get->getAlpha('action') == 'approve') 
        {
            $cid = $this->cage->get->testInt('comment_id');
            $comment = $c->getComment($cid); // get comment from database
            $c->readComment($comment); // read comment into $c
            $c->status = 'approved';

            // Akismet uses this to report Akismet mistakes (actually we do this for ANY comment approval)
            $this->pluginHook('com_man_approve_comment', true, '', array($c));
            
            $c->editComment();
            
            // email comment subscribers
            $c->emailCommentSubscribers($c->postId);
                        
            $this->hotaru->message = $this->lang['com_man_comment_approved'];
            $this->hotaru->messageType = 'green';
        }
        
        
        // approve all comments
        if ($this->cage->get->testAlnumLines('action') == 'approve_all') 
        {
            $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_status = %s WHERE comment_status = %s";
            $this->db->query($this->db->prepare($sql, 'approved', 'pending'));
            
            $this->pluginHook('com_man_approve_all_comments');
            
            // No need for a message because the "There are no comments pending message (see further down) shows anyway.
        }
        
    
        // delete comment
        if ($this->cage->get->getAlpha('action') == 'delete') 
        {
           // before deleting a comment, we need to be certain this user has permission:
            if ($this->hotaru->current_user->getPermission('can_delete_comments') == 'yes') {
                $cid = $this->cage->get->testInt('comment_id'); // comment id
                $comment = $c->getComment($cid); // get comment from database
                $c->readComment($comment); // read comment into $c
                
                // Akismet uses this to report Akismet mistakes 
                $this->pluginHook('com_man_delete_comment', true, '', array($c));
                
                $c->deleteComment(); // delete this comment
                $this->hotaru->comment->deleteCommentTree($cid);   // delete all responses, too.
                $this->hotaru->message = $this->lang['com_man_comment_delete'];
                $this->hotaru->messageType = 'green';
            } else {
                $this->hotaru->message = $this->lang['com_man_comment_delete_denied'];
                $this->hotaru->messageType = 'red';
            }
        }
        
        
        // delete all comments and their replies
        if ($this->cage->get->testAlnumLines('action') == 'delete_all') 
        {
           // before deleting a comment, we need to be certain this user has permission:
            if ($this->hotaru->current_user->getPermission('can_delete_comments') == 'yes') {
                $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_status = %s";
                $allpending = $this->db->get_results($this->db->prepare($sql, 'pending'));
                if ($allpending) {
                    foreach ($allpending as $pending) {
                        $cid = $pending->comment_id; // comment id
                        $comment = $c->getComment($cid); // get comment from database
                        $c->readComment($comment); // read comment into $c
                        $c->deleteComment(); // delete this comment
                        $this->hotaru->comment->deleteCommentTree($cid);   // delete all responses, too.
                    }
                    // No need for a message because the "There are no comments pending message (see further down) shows anyway.
                }
            } else {
                $this->hotaru->message = $this->lang['com_man_comment_delete_denied'];
                $this->hotaru->messageType = 'red';
            }
        }
        
        
        // edit comment
        if ($this->cage->post->getAlpha('type') == 'edit') 
        {
            $cid = $this->cage->post->testInt('cid');
            $comment = $c->getComment($cid);
            $c->readComment($comment);
            // before editing, we need to be certain this user has permission:
            $safe = false;
            $can_edit = $this->hotaru->current_user->getPermission('can_edit_comments');
            if ($can_edit == 'yes') { $safe = true; }
            if (($can_edit == 'own') && ($this->hotaru->current_user->id == $c->author)) { $safe = true; }
            if ($safe) {
                $c->content = sanitize($this->cage->post->getHtmLawed('com_man_edit_content'), 2, $c->allowableTags);
                $c->editComment();
            } else {
                $this->hotaru->message = $this->lang["com_man_edit_form_denied"];
                $this->hotaru->messageType = 'red';
            }
        }
        
                
        // if search
        if ($this->cage->get->getAlpha('type') == 'search') {
           $search_term = $this->cage->get->getMixedString2('search_value');        
            if (strlen($search_term) < 4) {
                $this->hotaru->message = $this->lang["com_man_search_too_short"];
                $this->hotaru->messageType = 'red';
            } else {
                $this->hotaru->vars['search_term'] = $search_term; // used to refill the search box after a search
                
                $select_clause = "SELECT *, MATCH(comment_content) AGAINST ('%s') AS relevance FROM " . TABLE_COMMENTS . " ";
                $sort_clause = "ORDER BY relevance DESC ";        
                $where_clause = "WHERE MATCH (comment_content) AGAINST (%s IN BOOLEAN MODE) "; 

                $sql = $select_clause . $where_clause . $sort_clause;
                $search_term_like = '%' . $search_term . '%';
                $results = $this->db->get_results($this->db->prepare($sql, $search_term, $search_term_like)); 
            }
            
            if (isset($results)) { $comments = $results; } else {  $comments = array(); }
        }
        
        
        // if filter
        if ($this->cage->get->getAlpha('type') == 'filter') {
            $filter = $this->cage->get->testAlnumLines('comment_status_filter');
            $this->hotaru->vars['comment_status_filter'] = $filter;  // used to refill the filter box after use
            switch ($filter) {
                case 'pending':
                    $where_clause = " WHERE comment_status = %s"; 
                    $sort_clause = ' ORDER BY comment_date DESC';  // same as "all"
                    $sql = "SELECT * FROM " . TABLE_COMMENTS . $where_clause . $sort_clause;
                    $filtered_results = $this->db->get_results($this->db->prepare($sql, 'pending')); 
                    break;
                case 'approved': 
                    $where_clause = " WHERE comment_status = %s"; 
                    $sort_clause = ' ORDER BY comment_date DESC'; // ordered newest first for convenience
                    $sql = "SELECT * FROM " . TABLE_COMMENTS . $where_clause . $sort_clause;
                    $filtered_results = $this->db->get_results($this->db->prepare($sql, 'approved')); 
                    break;
                case 'oldest':
                    $sort_clause = ' ORDER BY comment_date ASC'; // ordered oldest first
                    $sql = "SELECT * FROM " . TABLE_COMMENTS . $sort_clause;
                    $filtered_results = $this->db->get_results($this->db->prepare($sql)); 
                    break;
                case 'all': 
                case 'newest':
                default:
                    $sort_clause = ' ORDER BY comment_date DESC'; // ordered newest first for convenience
                    $sql = "SELECT * FROM " . TABLE_COMMENTS . $sort_clause;
                    $filtered_results = $this->db->get_results($this->db->prepare($sql)); 
                    break;
            }

            if (isset($filtered_results)) { $comments = $filtered_results; } else {  $comments = array(); }
        }

        if(!isset($comments)) {
            // default list (pending only)
            $where_clause = " WHERE comment_status = %s"; 
            $sort_clause = ' ORDER BY comment_date DESC';  // same as "all"
            $sql = "SELECT * FROM " . TABLE_COMMENTS . $where_clause . $sort_clause;
            $comments = $this->db->get_results($this->db->prepare($sql, 'pending')); 
        }
        
        if ($comments) { 
            $this->hotaru->vars['com_man_rows'] = $this->drawRows($c, $comments, $filter, $search_term);
        } else {
            $this->hotaru->message = $this->lang['com_man_no_pending_comments'];
            $this->hotaru->messageType = 'green';
        }
        
        // Show template:
        $this->hotaru->displayTemplate('com_man_main', 'comment_manager');
    }
    
    
    public function drawRows($c, $comments, $filter = '', $search_term = '')
    {
        // prepare for showing comments, 20 per page
        $pg = $this->cage->get->getInt('pg');
        $items = 20;
        
        require_once(EXTENSIONS . 'Paginated/Paginated.php');
        require_once(EXTENSIONS . 'Paginated/DoubleBarLayout.php');
        $pagedResults = new Paginated($comments, $items, $pg);
        
        $output = "";
        $alt = 0;
        while($comments = $pagedResults->fetchPagedRow()) {    //when $story is false loop terminates    
            $alt++;
            
            // We need user for the post author's name:
            $user = new UserBase($this->hotaru);
            $user->getUserBasic($comments->comment_user_id);
            
            // need to read the comment into the Comment object.
            $c->readComment($comments);
            $this->hotaru->comment = $c;

            $post = new Post($this->hotaru);
            $this->hotaru->post = $post;
            $post->readPost($c->postId);
            $post_link = $this->hotaru->url(array('page'=>$post->id)) . "#c" . $c->id;
            
            // COMMENT CONTENT
            $original_content = stripslashes(urldecode($c->content)); // clean comment
            // since the whole comment can be seen in the edit box, we'll just use a summary in the main comment area:
            if ($this->current_user->getPermission('can_edit_comments') == 'yes') { 
                $content = truncate($original_content, 140); // truncating strips tags, so we have to do this before we use Smilies, etc.
            } else {
                $content = $original_content;
            } 
            $this->hotaru->comment->content = $content ; // make it available to other plugins
            $this->pluginHook('comment_manager_comment_content'); // hook for other plugins to edit the comment
            $content = $this->hotaru->comment->content; // assign edited or unedited comment back to $content.
            
            
            $approve_link = BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=comment_manager&amp;action=approve&amp;comment_id=" . $c->id; 
            if ($filter) { $approve_link .= "&amp;type=filter&amp;comment_status_filter=" . $filter; }
            if ($search_term) { $approve_link .= "&amp;type=search&amp;search_value=" . $search_term; }
            if ($pg) { $approve_link .= "&amp;pg=" . $pg; }
            
            $delete_link = BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=comment_manager&amp;action=delete&amp;comment_id=" . $c->id; 
            if ($filter) { $delete_link .= "&amp;type=filter&amp;comment_status_filter=" . $filter; }
            if ($search_term) { $delete_link .= "&amp;type=search&amp;search_value=" . $search_term; }
            if ($pg) { $delete_link .= "&amp;pg=" . $pg; }
            
            if ($this->current_user->getPermission('can_delete_comments') == 'yes') {
                $colspan = 7;
            } else {
                $colspan = 6;
            }
            
            $output .= "<tr class='table_row_" . $alt % 2 . " cm_details_" . $alt % 2 . "'>\n";
            $output .= "<td class='cm_id'>" . $c->id . "</td>\n";
            $output .= "<td class='cm_status'><b>" . ucfirst($c->status) . "</b></td>\n";
            $output .= "<td class='cm_date'>" . date('d M \'y H:i:s', strtotime($c->date))  . "</a></td>\n";
            $output .= "<td class='cm_author'>" . $user->name . "</td>\n";
            $output .= "<td class='cm_post'><a href='" . $post_link . "'>" . $post->title . "</a></td>\n";
            $output .= "<td class='cm_approve'>" . "<a href='" . $approve_link . "'>\n";
            $output .= "<img src='" . BASEURL . "content/plugins/comment_manager/images/approve.png'>" . "</a></td>\n";
            if ($this->current_user->getPermission('can_delete_comments') == 'yes') {
                $output .= "<td class='cm_delete'>" . "<a href='" . $delete_link . "'>\n";
                $output .= "<img src='" . BASEURL . "content/plugins/comment_manager/images/delete.png'>" . "</a></td>\n";
            }
            $output .= "</tr>\n";
            
            $output .= "<tr class='table_tr_details table_row_" . $alt % 2 . "'>\n";
            $output .= "<td class='table_description cm_summary_" . $alt % 2 . "' colspan=" . $colspan . ">";
            $output .= "<blockquote>" . nl2br($content) . "</blockquote>";
            
            if ($this->current_user->getPermission('can_delete_comments') == 'yes') {
                $output .= " <small>[<a class='table_drop_down' href='#' title='" . $this->lang["com_man_show_content"] . "'>" . $this->hotaru->lang["com_man_show_form"] . "</a>]</small>\n";
            }
            $output .= "</td>\n";
            $output .= "</tr>\n";
            
            if ($this->current_user->getPermission('can_edit_comments') == 'yes') {
                $output .= "<tr class='table_tr_details' style='display:none;'>\n";
                $output .= "<td colspan=" . $colspan . " class='table_description cm_description_" . $alt % 2 . "'>\n";
                $output .= "<form name='com_man_edit_form' action='" . BASEURL . "admin_index.php?plugin=comment_manager' method='post'>\n";
                $output .= "<table><tr>\n";
                $output .= "<td><textarea name='com_man_edit_content' cols=80 rows=7>" . $original_content . "</textarea></td>\n";
                $output .= "</tr>\n";
                $output .= "<td><input class='submit' type='submit' value='" . $this->lang['com_man_edit_form_update'] . "' /></td>\n";
                $output .= "</tr></table>\n";
                $output .= "<input type='hidden' name='cid' value='" . $c->id . "' />\n";
                $output .= "<input type='hidden' name='page' value='plugin_settings' />\n";
                $output .= "<input type='hidden' name='type' value='edit' />\n";
                $output .= "</form>\n";
                $output .= "</tr>";
            }
        }
        
        if ($pagedResults) {
            $pagedResults->setLayout(new DoubleBarLayout());
            $this->hotaru->vars['com_man_navi'] = $pagedResults->fetchPagedNavigation('', $this->hotaru);
        }
        
        return $output;
    }
}
?>