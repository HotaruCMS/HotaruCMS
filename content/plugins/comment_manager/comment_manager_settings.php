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
    
class CommentManagerSettings
{
    /**
     * Main function that calls others
     *
     * @return bool
     */
    public function settings($h)
    {    
        // grab the number of pending comments:
        $sql = "SELECT COUNT(comment_id) FROM " . TABLE_COMMENTS . " WHERE comment_status = %s";
        $num_pending = $h->db->get_var($h->db->prepare($sql, 'pending'));
        if (!$num_pending) { $num_pending = "0"; } 
        $h->vars['num_pending'] = $num_pending; 
        
        // clear variables:
        $h->vars['search_term'] = '';
        $h->vars['comment_status_filter'] = 'newest';
        
        require_once(LIBS . 'Comment.php');
        $h->comment = new Comment();
        
        // approve comment
        if ($h->cage->get->getAlpha('action') == 'approve') 
        {
            $cid = $h->cage->get->testInt('comment_id');
            $comment = $h->comment->getComment($h, $cid); // get comment from database
            $h->comment->readComment($h, $comment); // read comment into $c
            $h->comment->status = 'approved';

            // Akismet uses this to report Akismet mistakes (actually we do this for ANY comment approval)
            $h->pluginHook('com_man_approve_comment', '', array($h->comment));
            
            $h->comment->editComment($h);
            
            // email comment subscribers
            if ($h->isActive('comments')) {
                require_once(PLUGINS . 'comments/comments.php');
                $c = new Comments($h);
                $c->emailCommentSubscribers($h, $h->comment->postId);
            }
                        
            $h->message = $h->lang['com_man_comment_approved'];
            $h->messageType = 'green';
        }
        
        
        // approve all comments
        if ($h->cage->get->testAlnumLines('action') == 'approve_all') 
        {
            $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_status = %s WHERE comment_status = %s";
            $h->db->query($h->db->prepare($sql, 'approved', 'pending'));
            
            $h->pluginHook('com_man_approve_all_comments');
            
            // No need for a message because the "There are no comments pending message (see further down) shows anyway.
        }
        
    
        // delete comment
        if ($h->cage->get->getAlpha('action') == 'delete') 
        {
           // before deleting a comment, we need to be certain this user has permission:
            if ($h->currentUser->getPermission('can_delete_comments') == 'yes') {
                $cid = $h->cage->get->testInt('comment_id'); // comment id
                $comment = $h->comment->getComment($h, $cid); // get comment from database
                $h->comment->readComment($h, $comment); // read comment into $c
                
                // Akismet uses this to report Akismet mistakes 
                $h->pluginHook('com_man_delete_comment', '', array($h->comment));
                
                $h->comment->deleteComment($h); // delete this comment
                $h->comment->deleteCommentTree($h, $cid);   // delete all responses, too.
                $h->message = $h->lang['com_man_comment_delete'];
                $h->messageType = 'green';
            } else {
                $h->message = $h->lang['com_man_comment_delete_denied'];
                $h->messageType = 'red';
            }
        }
        
        
        // delete all PENDING comments and their replies
        if ($h->cage->get->testAlnumLines('action') == 'delete_all_pending') 
        {
           // before deleting a comment, we need to be certain this user has permission:
            if ($h->currentUser->getPermission('can_delete_comments') == 'yes') {
                $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_status = %s";
                $allpending = $h->db->get_results($h->db->prepare($sql, 'pending'));
                if ($allpending) {
                    foreach ($allpending as $pending) {
                        $cid = $pending->comment_id; // comment id
                        $comment = $h->comment->getComment($h, $cid); // get comment from database
                        $h->comment->readComment($h, $comment); // read comment into $c
                        $h->comment->deleteComment($h); // delete this comment
                        $h->comment->deleteCommentTree($h, $cid);   // delete all responses, too.
                    }
                    // No need for a message because the "There are no comments pending message (see further down) shows anyway.
                }
            } else {
                $h->message = $h->lang['com_man_comment_delete_denied'];
                $h->messageType = 'red';
            }
        }
        
        
        // delete all BURIED comments and their replies
        if ($h->cage->get->testAlnumLines('action') == 'delete_all_buried') 
        {
           // before deleting a comment, we need to be certain this user has permission:
            if ($h->currentUser->getPermission('can_delete_comments') == 'yes') {
                $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_status = %s";
                $allburied = $h->db->get_results($h->db->prepare($sql, 'buried'));
                if ($allburied) {
                    foreach ($allburied as $buried) {
                        $cid = $buried->comment_id; // comment id
                        $comment = $h->comment->getComment($h, $cid); // get comment from database
                        $h->comment->readComment($h, $comment); // read comment into $c
                        $h->comment->deleteComment($h); // delete this comment
                        $h->comment->deleteCommentTree($h, $cid);   // delete all responses, too.
                    }
                    // No need for a message because the "There are no comments pending message (see further down) shows anyway.
                }
            } else {
                $h->message = $h->lang['com_man_comment_delete_denied'];
                $h->messageType = 'red';
            }
        }
        
        
        // edit comment
        if ($h->cage->post->getAlpha('type') == 'edit') 
        {
            // Get settings from database if they exist...
            $comments_settings = $h->getSerializedSettings('comments');
            $h->comment->allowableTags = $comments_settings['comment_allowable_tags'];
        
            $cid = $h->cage->post->testInt('cid');
            $comment = $h->comment->getComment($h, $cid);
            $h->comment->readComment($h, $comment);
            // before editing, we need to be certain this user has permission:
            $safe = false;
            $can_edit = $h->currentUser->getPermission('can_edit_comments');
            if ($can_edit == 'yes') { $safe = true; }
            if (($can_edit == 'own') && ($h->currentUser->id == $h->comment->author)) { $safe = true; }
            if ($safe) {
                $h->comment->content = sanitize($h->cage->post->getHtmLawed('com_man_edit_content'), 'tags', $h->comment->allowableTags);
                $h->comment->editComment($h);
            } else {
                $h->message = $h->lang["com_man_edit_form_denied"];
                $h->messageType = 'red';
            }
        }
        
                
        // if search
        $search_term = '';
        if ($h->cage->get->getAlpha('type') == 'search') {
           $search_term = $h->cage->get->sanitizeTags('search_value');        
            if (strlen($search_term) < 4) {
                $h->message = $h->lang["com_man_search_too_short"];
                $h->messageType = 'red';
            } else {
                $h->vars['search_term'] = $search_term; // used to refill the search box after a search
                
                $select_clause = "SELECT *, MATCH(comment_content) AGAINST ('%s') AS relevance FROM " . TABLE_COMMENTS . " ";
                $sort_clause = "ORDER BY relevance DESC ";        
                $where_clause = "WHERE MATCH (comment_content) AGAINST (%s IN BOOLEAN MODE) "; 

                $search_term_like = '%' . $search_term . '%';
                $count_sql = "SELECT count(*) AS number, MATCH(comment_content) AGAINST ('%s') AS relevance FROM " . TABLE_COMMENTS . " " . $where_clause;
                $count = $h->db->get_var($h->db->prepare($count_sql, $search_term, $search_term_like));

                $sql = $select_clause . $where_clause . $sort_clause;
                $query = $h->db->prepare($sql, $search_term, $search_term_like);
            }
        }
        
        
        // if filter
        $filter = '';
        if ($h->cage->get->getAlpha('type') == 'filter') {
            $filter = $h->cage->get->testAlnumLines('comment_status_filter');
            $h->vars['comment_status_filter'] = $filter;  // used to refill the filter box after use
            switch ($filter) {
                case 'pending':
                    $where_clause = " WHERE comment_status = %s"; 
                    $sort_clause = ' ORDER BY comment_date DESC';  // same as "all"
                    $count_sql = "SELECT count(*) AS number FROM " . TABLE_COMMENTS . $where_clause;
                    $count = $h->db->get_var($h->db->prepare($count_sql, 'pending'));
                    $sql = "SELECT * FROM " . TABLE_COMMENTS . $where_clause . $sort_clause;
                    $query = $h->db->prepare($sql, 'pending');
                    break;
                case 'buried':
                    $where_clause = " WHERE comment_status = %s"; 
                    $sort_clause = ' ORDER BY comment_date DESC';  // same as "all"
                    $count_sql = "SELECT count(*) AS number FROM " . TABLE_COMMENTS . $where_clause;
                    $count = $h->db->get_var($h->db->prepare($count_sql, 'buried'));
                    $sql = "SELECT * FROM " . TABLE_COMMENTS . $where_clause . $sort_clause;
                    $query = $h->db->prepare($sql, 'buried');
                    break;
                case 'approved': 
                    $where_clause = " WHERE comment_status = %s"; 
                    $sort_clause = ' ORDER BY comment_date DESC'; // ordered newest first for convenience
                    $count_sql = "SELECT count(*) AS number FROM " . TABLE_COMMENTS . $where_clause;
                    $count = $h->db->get_var($h->db->prepare($count_sql, 'approved'));
                    $sql = "SELECT * FROM " . TABLE_COMMENTS . $where_clause . $sort_clause;
                    $query = $h->db->prepare($sql, 'approved');
                    break;
                case 'oldest':
                    $sort_clause = ' ORDER BY comment_date ASC'; // ordered oldest first
                    $count_sql = "SELECT count(*) AS number FROM " . TABLE_COMMENTS;
                    $count = $h->db->get_var($h->db->prepare($count_sql));
                    $sql = "SELECT * FROM " . TABLE_COMMENTS . $sort_clause;
                    $query = $h->db->prepare($sql);
                    break;
                case 'all': 
                case 'newest':
                default:
                    $sort_clause = ' ORDER BY comment_date DESC'; // ordered newest first for convenience
                    $count_sql = "SELECT count(*) AS number FROM " . TABLE_COMMENTS;
                    $count = $h->db->get_var($h->db->prepare($count_sql));
                    $sql = "SELECT * FROM " . TABLE_COMMENTS . $sort_clause;
                    $query = $h->db->prepare($sql); 
                    break;
            }
        }

        if(!isset($query)) {
            // default list
            if ($h->vars['comment_status_filter'] == 'pending') {
                $where_clause = " WHERE comment_status = %s";
                $sort_clause = ' ORDER BY comment_date DESC'; 
                $count_sql = "SELECT count(*) AS number FROM " . TABLE_COMMENTS . $where_clause;
                $count = $h->db->get_var($h->db->prepare($count_sql, 'pending'));
                $sql = "SELECT * FROM " . TABLE_COMMENTS . $where_clause . $sort_clause;
                $query = $h->db->prepare($sql, 'pending'); 
            } else {
                $sort_clause = ' ORDER BY comment_date DESC';  // same as "all"
                $count_sql = "SELECT count(*) AS number FROM " . TABLE_COMMENTS;
                $count = $h->db->get_var($h->db->prepare($count_sql));
                $sql = "SELECT * FROM " . TABLE_COMMENTS . $sort_clause;
                $query = $h->db->prepare($sql);
            }
        }
        
        $pagedResults = $h->pagination($query, $count, 20, 'comments');
        
        if ($pagedResults) { 
            $h->vars['com_man_rows'] = $this->drawRows($h, $pagedResults, $filter, $search_term);
        } elseif ($h->vars['comment_status_filter'] == 'pending') {
            $h->message = $h->lang['com_man_no_pending_comments'];
            $h->messageType = 'green';
        }
        
        // Show template:
        $h->displayTemplate('com_man_main', 'comment_manager');
    }
    
    
    public function drawRows($h, $pagedResults, $filter = '', $search_term = '')
    {
        $output = "";
        $alt = 0;
        $pg = $h->cage->get->getInt('pg');
        
        if (!$pagedResults->items) { return ""; }
        
        foreach ($pagedResults->items as $comments) 
        {
            $alt++;
            
            // We need user for the post author's name:
            $user = new UserBase();
            $user->getUserBasic($h, $comments->comment_user_id);
            
            // need to read the comment into the Comment object.
            $h->comment->readComment($h, $comments);

            $h->post->readPost($h, $h->comment->postId);
            $post_link = $h->url(array('page'=>$h->post->id)) . "#c" . $h->comment->id;
            
            // COMMENT CONTENT
            $original_content = stripslashes(urldecode($h->comment->content)); // clean comment
            // since the whole comment can be seen in the edit box, we'll just use a summary in the main comment area:
            if ($h->currentUser->getPermission('can_edit_comments') == 'yes') { 
                $content = truncate($original_content, 140); // truncating strips tags, so we have to do this before we use Smilies, etc.
            } else {
                $content = $original_content;
            } 
            $h->comment->content = $content ; // make it available to other plugins
            $h->pluginHook('comment_manager_comment_content'); // hook for other plugins to edit the comment
            $content = $h->comment->content; // assign edited or unedited comment back to $content.
            
            
            $approve_link = BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=comment_manager&amp;action=approve&amp;comment_id=" . $h->comment->id; 
            if ($filter) { $approve_link .= "&amp;type=filter&amp;comment_status_filter=" . $filter; }
            if ($search_term) { $approve_link .= "&amp;type=search&amp;search_value=" . $search_term; }
            if ($pg) { $approve_link .= "&amp;pg=" . $pg; }
            
            $delete_link = BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=comment_manager&amp;action=delete&amp;comment_id=" . $h->comment->id; 
            if ($filter) { $delete_link .= "&amp;type=filter&amp;comment_status_filter=" . $filter; }
            if ($search_term) { $delete_link .= "&amp;type=search&amp;search_value=" . $search_term; }
            if ($pg) { $delete_link .= "&amp;pg=" . $pg; }
            
            if ($h->currentUser->getPermission('can_delete_comments') == 'yes') {
                $colspan = 7;
            } else {
                $colspan = 6;
            }
            
            // put icons next to the username with links to User Manager
            $h->vars['user_manager_name_icons'] = array($user->name, ''); // second param is "output"
            $h->pluginHook('comment_manager_user_name');
            $icons = $h->vars['user_manager_name_icons'][1]; // 1 is the second param: output
            
            $output .= "<tr class='table_row_" . $alt % 2 . " cm_details_" . $alt % 2 . "'>\n";
            $output .= "<td class='cm_id'>" . $h->comment->id . "</td>\n";
            $output .= "<td class='cm_status'><b>" . ucfirst($h->comment->status) . "</b></td>\n";
            $output .= "<td class='cm_date'>" . date('d M \'y H:i:s', strtotime($h->comment->date))  . "</a></td>\n";
            $output .= "<td class='cm_author'><a href='" . $h->url(array('user'=>$user->name)) . "' title='User Profile'>" . $user->name . $icons . "</td>\n";
            $output .= "<td class='cm_post'><a href='" . $post_link . "'>" . $h->post->title . "</a></td>\n";
            $output .= "<td class='cm_approve'>" . "<a href='" . $approve_link . "'>\n";
            $output .= "<img src='" . BASEURL . "content/plugins/comment_manager/images/approve.png'>" . "</a></td>\n";
            if ($h->currentUser->getPermission('can_delete_comments') == 'yes') {
                $output .= "<td class='cm_delete'>" . "<a href='" . $delete_link . "'>\n";
                $output .= "<img src='" . BASEURL . "content/plugins/comment_manager/images/delete.png'>" . "</a></td>\n";
            }
            $output .= "</tr>\n";
            
            $output .= "<tr class='table_tr_details table_row_" . $alt % 2 . "'>\n";
            $output .= "<td class='table_description cm_summary_" . $alt % 2 . "' colspan=" . $colspan . ">";
            $output .= "<blockquote>" . nl2br($content) . "</blockquote>";
            
            if ($h->currentUser->getPermission('can_delete_comments') == 'yes') {
                $output .= " <small>[<a class='table_drop_down' href='#' title='" . $h->lang["com_man_show_content"] . "'>" . $h->lang["com_man_show_form"] . "</a>]</small>\n";
            }
            $output .= "</td>\n";
            $output .= "</tr>\n";
            
            if ($h->currentUser->getPermission('can_edit_comments') == 'yes') {
                $output .= "<tr class='table_tr_details' style='display:none;'>\n";
                $output .= "<td colspan=" . $colspan . " class='table_description cm_description_" . $alt % 2 . "'>\n";
                $output .= "<form name='com_man_edit_form' action='" . BASEURL . "admin_index.php?plugin=comment_manager' method='post'>\n";
                $output .= "<table><tr>\n";
                $output .= "<td><textarea name='com_man_edit_content' cols=80 rows=7>" . $original_content . "</textarea></td>\n";
                $output .= "</tr>\n";
                $output .= "<td><input class='submit' type='submit' value='" . $h->lang['com_man_edit_form_update'] . "' /></td>\n";
                $output .= "</tr></table>\n";
                $output .= "<input type='hidden' name='cid' value='" . $h->comment->id . "' />\n";
                $output .= "<input type='hidden' name='page' value='plugin_settings' />\n";
                $output .= "<input type='hidden' name='type' value='edit' />\n";
                $output .= "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
                $output .= "</form>\n";
                $output .= "</tr>";
            }
        }
        
        if ($pagedResults) {
            $h->vars['com_man_navi'] = $h->pageBar($pagedResults);
        }
        
        return $output;
    }
}
?>
