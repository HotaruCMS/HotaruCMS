<?php
/**
 * name: Comments
 * description: Enables logged-in users to comment on posts
 * version: 1.5
 * folder: comments
 * class: Comments
 * type: comments
 * requires: sb_base 0.1, users 1.1
 * hooks: install_plugin, theme_index_top, header_include, admin_header_include_raw, theme_index_main, sb_base_show_post_extra_fields, sb_base_post_show_post, admin_plugin_settings, admin_sidebar_plugin_settings, submit_2_fields, submit_edit_admin_fields, post_delete_post, profile_usage, , admin_theme_main_stats, breadcrumbs, submit_functions_process_submitted, submit_2_process_submission
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

class Comments
{
    /**
     * Install or Upgrade
     */
    public function install_plugin($h)
    {
        // ************
        // PERMISSIONS
        // ************
        
        $site_perms = $h->getDefaultPermissions('all');
        if (!isset($site_perms['can_comment'])) { 
            $perms['options']['can_comment'] = array('yes', 'no', 'mod');
            $perms['options']['can_edit_comments'] = array('yes', 'no', 'own');
            $perms['options']['can_set_comments_pending'] = array('yes', 'no');
            $perms['options']['can_delete_comments'] = array('yes', 'no');
            $perms['options']['can_comment_manager_settings'] = array('yes', 'no');
            
            $perms['can_comment']['admin'] = 'yes';
            $perms['can_comment']['supermod'] = 'yes';
            $perms['can_comment']['moderator'] = 'yes';
            $perms['can_comment']['member'] = 'yes';
            $perms['can_comment']['undermod'] = 'mod';
            $perms['can_comment']['default'] = 'no';
            
            $perms['can_edit_comments']['admin'] = 'yes';
            $perms['can_edit_comments']['supermod'] = 'yes';
            $perms['can_edit_comments']['moderator'] = 'yes';
            $perms['can_edit_comments']['member'] = 'own';
            $perms['can_edit_comments']['undermod'] = 'own';
            $perms['can_edit_comments']['default'] = 'no';
            
            $perms['can_set_comments_pending']['admin'] = 'yes';
            $perms['can_set_comments_pending']['supermod'] = 'yes';
            $perms['can_set_comments_pending']['moderator'] = 'yes';
            $perms['can_set_comments_pending']['default'] = 'no';
            
            $perms['can_delete_comments']['admin'] = 'yes';
            $perms['can_delete_comments']['supermod'] = 'yes';
            $perms['can_delete_comments']['default'] = 'no';
            
            $perms['can_comment_manager_settings']['admin'] = 'yes';
            $perms['can_comment_manager_settings']['supermod'] = 'yes';
            $perms['can_comment_manager_settings']['moderator'] = 'yes';
            $perms['can_comment_manager_settings']['default'] = 'no';
            
            $h->updateDefaultPermissions($perms);
        }


        // ************
        // SETTINGS 
        // ************
        
        // Get settings from database if they exist...
        $comments_settings = $h->getSerializedSettings();
        
        // Default settings 
        if (!isset($comments_settings['comment_all_forms'])) { $comments_settings['comment_all_forms'] = "checked"; }
        if (!isset($comments_settings['comment_voting'])) { $comments_settings['comment_voting'] = ""; }
        if (!isset($comments_settings['comment_levels'])) { $comments_settings['comment_levels'] = 5; }
        if (!isset($comments_settings['comment_email'])) { $comments_settings['comment_email'] = SITE_EMAIL; }
        if (!isset($comments_settings['comment_allowable_tags'])) { $comments_settings['comment_allowable_tags'] = "<b><i><u><a><blockquote><del>"; }
        if (!isset($comments_settings['comment_set_pending'])) { $comments_settings['comment_set_pending'] = ""; }
        if (!isset($comments_settings['comment_order'])) { $comments_settings['comment_order'] = 'asc'; }
        if (!isset($comments_settings['comment_pagination'])) { $comments_settings['comment_pagination'] = ''; }
        if (!isset($comments_settings['comment_items_per_page'])) { $comments_settings['comment_items_per_page'] = 20; }
        if (!isset($comments_settings['comment_x_comments'])) { $comments_settings['comment_x_comments'] = 1; }
        if (!isset($comments_settings['comment_email_notify'])) { $comments_settings['comment_email_notify'] = ""; }
        if (!isset($comments_settings['comment_email_notify_mods'])) { $comments_settings['comment_email_notify_mods'] = array(); }
        if (!isset($comments_settings['comment_url_limit'])) { $comments_settings['comment_url_limit'] = 0; }
        if (!isset($comments_settings['comment_daily_limit'])) { $comments_settings['comment_daily_limit'] = 0; }
        if (!isset($comments_settings['comment_avatar_size'])) { $comments_settings['comment_avatar_size'] = "16"; }
        if (!isset($comments_settings['comment_hide'])) { $comments_settings['comment_hide'] = "3"; }
        if (!isset($comments_settings['comment_bury'])) { $comments_settings['comment_bury'] = "10"; }
        
        if ($h->isActive('avatar')) {
            if (!isset($comments_settings['comment_avatars'])) { $comments_settings['comment_avatars'] = "checked"; }
        } else {
            if (!isset($comments_settings['comment_avatars'])) { $comments_settings['comment_avatars'] = ""; }
        }
        
        $h->updateSetting('comments_settings', serialize($comments_settings));
    }
    
    
    /**
     * Define table name, include language file and creat global Comments object
     */
    public function theme_index_top($h)
    {
        // Create a new global object called "comment".
        require_once(LIBS . 'Comment.php');
        $h->comment = new Comment();
        
        // Get settings from database if they exist...
        $comments_settings = $h->getSerializedSettings();
    
        // Assign settings to class member
        $h->comment->avatars = $comments_settings['comment_avatars'];
        $h->comment->avatarSize = $comments_settings['comment_avatar_size'];
        $h->comment->voting = $comments_settings['comment_voting'];
        $h->comment->email = $comments_settings['comment_email'];
        $h->comment->allowableTags = $comments_settings['comment_allowable_tags'];
        $h->comment->levels = $comments_settings['comment_levels'];
        $h->comment->setPending = $comments_settings['comment_set_pending'];
        $h->comment->allForms = $comments_settings['comment_all_forms'];
        $h->vars['comment_hide'] = $comments_settings['comment_hide'];
        
        
        if ($h->pageName == 'rss_comments') {
            $this->rssFeed($h);
            return true;
        }
        
        if ($h->pageName == 'comments') {
            $h->pageTitle = $h->lang['comments'];
            if ($h->cage->get->keyExists('user')) {
                $h->pageTitle .= '[delimiter]' . $h->cage->get->testUsername('user');
            }
        }

        // Is the comment form open on this thread? 
        $h->comment->thisForm = $h->comment->formStatus($h, 'select'); // returns 'open' or 'closed'

        if (   ($h->pageType == 'post') 
            && ($h->comment->thisForm == 'open')
            && ($h->comment->allForms == 'checked')) {
            
            if ($h->currentUser->loggedIn) {

                if (($h->cage->post->getAlpha('comment_process') == 'newcomment') || 
                    ($h->cage->post->getAlpha('comment_process') == 'editcomment'))
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
                    
                    if ($h->cage->post->keyExists('comment_subscribe')) {
                        $h->comment->subscribe = 1;
                    } else {
                        $h->comment->subscribe = 0;
                        $h->comment->unsubscribe($h, $h->comment->postId);
                    }
                    
                    if ($h->cage->post->getAlpha('comment_process') == 'newcomment')
                    {
                        // before posting, we need to be certain this user has permission:
                        $safe = false;
                        $can_comment = $h->currentUser->getPermission('can_comment');
                        if ($can_comment == 'yes') { $safe = true; }
                        if ($can_comment == 'mod') { $safe = true; $h->comment->status = 'pending'; }
                        
                        $result = array(); // holds results from addComment function
                        
                        // Okay, safe to add the comment...
                        if ($safe) {
                            // A user can unsubscribe by submitting an empty comment, so...
                            if ($h->comment->content != '') {
                                $result = $h->comment->addComment($h);
            
                                // notify chosen mods of new comment by email if enabled and UserFunctions file exists
                                if (($comments_settings['comment_email_notify']) && (file_exists(PLUGINS . 'users/libs/UserFunctions.php')))
                                {
                                    require_once(PLUGINS . 'users/libs/UserFunctions.php');
                                    $uf = new UserFunctions();
                                    $uf->notifyMods($h, 'comment', $h->comment->status, $h->comment->postId, $h->comment->id);
                                }
                    
                                // email comment subscribers if this comment has 'approved' status:
                                if ($h->comment->status == 'approved') {
                                    $this->emailCommentSubscribers($h, $h->comment->postId);
                                }
                            } else {
                                //comment empty so just check subscribe box:
                                $h->comment->updateSubscribe($h, $h->comment->postId);
                                $h->messages[$h->lang['comment_moderation_unsubscribed']] = 'green';
                            }
                        }
                        
                        if ($result['exceeded_daily_limit']) {
                            $h->messages[$h->lang['comment_moderation_exceeded_daily_limit']] = 'green';
                        } elseif ($result['exceeded_url_limit']) {
                            $h->messages[$h->lang['comment_moderation_exceeded_url_limit']] = 'green';
                        } elseif ($result['not_enough_comments']) {
                            $h->messages[$h->lang['comment_moderation_not_enough_comments']] = 'green';
                        }
                    }
                    elseif($h->cage->post->getAlpha('comment_process') == 'editcomment')
                    {
                        // before editing, we need to be certain this user has permission:
                        $safe = false;
                        $can_edit = $h->currentUser->getPermission('can_edit_comments');
                        if ($can_edit == 'yes') { $safe = true; }
                        if (($can_edit == 'own') && ($h->currentUser->id == $h->comment->author)) { $safe = true; }
                        if ($safe) {
                            $h->comment->editComment($h);
                        }
                    }
                   
                    if ($h->comment->status == 'pending') {
                        return false;
                    }
                    
                    header("Location: " . $h->url(array('page'=>$h->comment->postId)));    // Go to the post
                    die();
                    
                }
                
                // set current comment and responses to pending:
                if ($h->cage->get->getAlpha('action') == 'setpending') { 
                
                    // before setting pending, we need to be certain this user has permission:
                    if ($h->currentUser->getPermission('can_set_comments_pending') == 'yes') {
                        $cid = $h->cage->get->testInt('cid'); // comment id
                        $comment = $h->comment->getComment($h, $cid);
                        $h->comment->readComment($h, $comment); // read comment
                        $h->comment->status = 'pending'; // set to pending
                        $h->comment->editComment($h);  // update this comment
    
                        $h->comment->postId = $h->cage->get->testInt('pid');  // post id
                        $h->comment->setPendingCommentTree($h,$cid);   // set all responses to 'pending', too.
                        
                        // redirect back to thread:
                        $h->post = new Post();
                        $h->readPost($h->comment->postId);
                        header("Location: " . $h->url(array('page'=>$h->post->id)));    // Go to the post
                        die();
                    }
                }
                
                // delete current comment and responses:
                if ($h->cage->get->getAlpha('action') == 'delete') { 
                
                    // before deleting a comment, we need to be certain this user has permission:
                    if ($h->currentUser->getPermission('can_delete_comments') == 'yes') {
                        $cid = $h->cage->get->testInt('cid'); // comment id
                        $comment = $h->comment->getComment($h, $cid);
                        $h->comment->readComment($h, $comment); // read comment
                        
                        $h->pluginHook('comments_delete_comment');
                        
                        $h->comment->deleteComment($h, $cid); // delete this comment
                        $h->comment->deleteCommentTree($h, $cid);   // delete all responses, too.
                        
                        $h->clearCache('html_cache', false); // clear HTML cache to refresh Comments and Activity widgets
                        
                        $h->comment->postId = $h->cage->get->testInt('pid');  // post id
                        
                        // redirect back to thread:
                        $h->readPost($h->comment->postId);
                        header("Location: " . $h->url(array('page'=>$h->comment->postId)));    // Go to the post
                        die();
                    }
                }
    
            }
            
        }
    
        return false;
    }
    
    
    /**
     * Include css and JavaScript
     */
    public function header_include($h)
    { 
        $h->includeCss('comments', 'comments');
        $h->includeJs('comments', 'urldecode.min');
        $h->includeJs('comments', 'comments');
    }
    
    
    /**
     * Include jQuery for hiding and showing email options in plugin settings
     */
    public function admin_header_include_raw($h)
    {
        if ($h->isSettingsPage('comments')) {
            echo "<script type='text/javascript'>\n";
            echo "$(document).ready(function(){\n";
                echo "$('#email_notify').click(function () {\n";
                echo "$('#email_notify_options').slideToggle();\n";
                echo "});\n";
            echo "});\n";
            echo "</script>\n";
        }
    }
    
    
    /**
     * Link to comments
     */
    public function sb_base_show_post_extra_fields($h)
    {
        echo '<li><a class="comment_link" href="' . $h->url(array('page'=>$h->post->id)) . '">' . $h->countComments() . '</a></li>' . "\n";
    }
    
    
    /**
     * Prepare and display comments wrapper and form
     */
    public function sb_base_post_show_post($h)
    {
        // set default
        $h->vars['subscribe_check'] = ''; 
        
        // Check if the currentUser is the post author
        if ($h->post->author == $h->currentUser->id) {
            // Check if the user subscribed to comments as a submitter
            if ($h->post->subscribe == 1) { 
                $h->vars['subscribe_check'] = 'checked';
            } 
        } 
        
        // Check if the user subscribed to comments as a commenter
        $sql = "SELECT COUNT(comment_subscribe) FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d AND comment_user_id = %d AND comment_subscribe = %d";
        $subscribe_result = $h->db->get_var($h->db->prepare($sql, $h->post->id, $h->currentUser->id, 1));
        
        if ($subscribe_result > 0) { 
            $h->vars['subscribe_check'] = 'checked';
        } 

        if (!$h->isPage('submit3')) {
        
            $comments_settings = $h->getSerializedSettings();
            $h->comment->pagination = $comments_settings['comment_pagination'];
            $h->comment->order = $comments_settings['comment_order'];
            $h->comment->itemsPerPage = $comments_settings['comment_items_per_page'];
            
            // GET ALL PARENT COMMENTS
            $parents = $h->comment->readAllParents($h, $h->post->id, $h->comment->order);
                    
            echo "<!--  START COMMENTS_WRAPPER -->\n";
            echo "<div id='comments_wrapper'>\n";
            echo "<h2>" . $h->countComments(false) . "</h2>\n";
                
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
        }
        
        if ($h->comment->pagination && $pagedResults) {
            echo $h->pageBar($pagedResults);
        }
        
        // determine where to return the user to after logging in:
        if (!$h->cage->get->keyExists('return')) {
            $host = $h->cage->server->sanitizeTags('HTTP_HOST');
            $uri = $h->cage->server->sanitizeTags('REQUEST_URI');
            $return = 'http://' . $host . $uri;
            $return = urlencode(htmlentities($return,ENT_QUOTES,'UTF-8'));
        } else {
            $return = $h->cage->get->testUri('return'); // use existing return parameter
        }
                
        if (!$h->currentUser->loggedIn) {
            echo "<div class='comment_form_off'>";
            echo "<a href='" . BASEURL . "index.php?page=login&amp;return=" . $return . "'>";
            echo $h->lang['comments_please_login'] . "</a></div>";
            return false;
        }
        
        if ($h->currentUser->getPermission('can_comment') == 'no') {
            echo "<div class='comment_form_off'>" . $h->lang['comments_no_permission'] . "</div>";
            return false;
        }
        
        if (!$h->isPage('submit3') && ($h->comment->thisForm == 'closed') 
            || ($h->comment->allForms != 'checked')) {
            echo "<div class='comment_form_off'>" . $h->lang['comments_form_closed'] . "</div>";
            return false;
        }

        if (!$h->isPage('submit3')) {
            // force non-reply form to have parent "0" and depth "0"
            $h->comment->id = 0;
            $h->comment->depth = 0;
            $h->vars['subscribe'] = ($h->comment->subscribe) ? 'checked' : '';
            $h->displayTemplate('comment_form', 'comments', false);
            
            $h->pluginHook('comments_post_last_form');
            
            if ($h->currentUser->getPermission('can_comment_manager_settings') == 'yes') {
                echo "<a id='comment_manager_link' href='" . $h->url(array('page'=>'plugin_settings', 'plugin'=>'comment_manager'), 'admin') . "'>";
                echo $h->lang['comments_access_comment_manager'] . "</a>";
            }
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
            if ($all) {
                $h->displayTemplate('all_comments', 'comments', false);
            } else {
                $h->displayTemplate('show_comments', 'comments', false);
            }
            
            // don't show the reply form in these cases:
            //if ($all) { return false; } // we're looking at the main comments page
            if ($h->currentUser->getPermission('can_comment') == 'no') { return false; }
            if (!$h->currentUser->loggedIn) { return false; }
            if ($h->comment->thisForm == 'closed') { return false; }
            if ($h->comment->allForms != 'checked') { return false; }
    
            // show the reply form:
            $h->vars['subscribe'] = ($h->comment->subscribe) ? 'checked' : '';
            $h->displayTemplate('comment_form', 'comments', false);
        }
    }
    
    
    /**
     * Show all comments list on a main "Comments" page
     */
    public function theme_index_main($h)
    {
        if (!$h->isPage('comments')) { return false; }
        
        if ($h->cage->get->keyExists('user')) {
            $user = $h->cage->get->testUsername('user');
            $userid = $h->getUserIdFromName($user);
        } else {
            $userid = 0;
        }

        $comments_settings = $h->getSerializedSettings();
        $h->comment->itemsPerPage = $comments_settings['comment_items_per_page'];
        
        if ($userid) {
            $comments_count = $h->comment->getAllCommentsCount($h, '', $userid);
            $comments_query = $h->comment->getAllCommentsQuery($h, 'DESC', $userid);
        } else {
            $comments_count = $h->comment->getAllCommentsCount($h);
            $comments_query = $h->comment->getAllCommentsQuery($h, 'DESC');
        }
        
        if (!$comments_count) {
            $h->showMessage($h->lang['comments_user_no_comments'], 'red');
            return true; 
        }
            
        $pagedResults = $h->pagination($comments_query, $comments_count, $h->comment->itemsPerPage, 'comments');
        
        if (isset($pagedResults->items)) {
            foreach ($pagedResults->items as $comment) {
                $h->readPost($comment->comment_post_id);
                // don't show this comment if its post is buried or pending:
                if ($h->post->status == 'buried' || $h->post->status == 'pending') { continue; }
                
                $this->displayComment($h, $comment, true);
            }
            
            echo $h->pageBar($pagedResults);
        }
        return true;
    }
    
    
    /**
     * Add Comments RSS link to breadcrumbs
     */
    public function breadcrumbs($h)
    {
        if ($h->pageName != 'comments') { return false; }
        
        if ($h->subPage == 'user') {
            $user = $h->cage->get->testUsername('user');
            $userlink = "<a href='" . $h->url(array('user'=>$user)) . "'>";
            $userlink .= $user . "</a> &raquo ";
            $rss = "<a href='" . $h->url(array('page'=>'rss_comments', 'user'=>$h->cage->get->testUsername('user'))) . "'> ";
            $crumbs = $userlink . $h->lang['comments'] . $rss;
        } else {
            $crumbs = $h->lang ['comments'] . "<a href='" . $h->url(array('page'=>'rss_comments')) . "'> ";
        }
        $crumbs .= " <img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png' alt='" . $h->pageTitle . " RSS' /></a>\n ";
        
        return $crumbs;
    }
    
    
    /**
     * Show post_subscribe option in Submit step 2 and Post Edit
     */
    public function submit_2_fields($h)
    {
        if ($h->post->subscribe) { $subscribe = 'checked'; } else { $subscribe = ''; } 
        echo "<tr><td colspan='3'>\n";
        echo "<input id='post_subscribe' name='post_subscribe' type='checkbox' " . $subscribe . "> " . $h->lang['submit_subscribe']; 
        echo "</tr>";
    }
    
    
    /**
     * Show Enable comment form option in Post Edit
     */
    public function submit_edit_admin_fields($h)
    {
        if ($h->post->comments == 'open') { $form_open = 'checked'; } else { $form_open = ''; }

        echo "<tr><td colspan='3'>\n";
        echo "<input id='enable_comments' name='enable_comments' type='checkbox' " . $form_open . "> " . $h->lang['submit_form_enable_comments']; 
        echo "</tr>";
    }
    
    
    /**
     * Check and update post_submit in Submit step 2 and Post Edit pages
     */
    public function submit_functions_process_submitted($h)
    {
        if (($h->pageName != 'submit2') && ($h->pageName != 'edit_post')) { return false; }

        // SUBSCRIBE TO COMMENTS
        
        if ($h->cage->post->keyExists('post_subscribe')) {
            $h->post->subscribe = 1; 
            $subscribe = 'checked'; 
        } else {
            // use existing setting:
            $subscribe = ($h->post->subscribe) ? 'checked' : ''; 
        }

        $h->vars['submitted_data']['submit_subscribe'] = $h->post->subscribe;
        
        // ENABLE / DISABLE COMMENT FORM
        
        // check on edit post
        if ($h->pageName == 'edit_post') {
            if ($h->cage->post->keyExists('enable_comments')) { 
                $h->post->comments = 'open'; 
                $comments = 'open';
            } else { 
                if ($h->currentUser->getPermission('can_edit_posts') == 'yes') {
                    $h->post->comments = 'closed';
                    $comments = 'closed'; 
                } else {
                    $comments = $h->post->comments; // keep existing setting
                }
            }
        } else {
            // open for submit 2
            $h->post->comments = 'open'; 
            $comments = 'open';
        }
        
        $h->vars['submitted_data']['submit_comments'] = $h->post->comments;
    }
    
    
    /**
     * Save subscribe to the database during post update
     */
    public function submit_2_process_submission($h)
    {
        $h->post->subscribe = $h->vars['submitted_data']['submit_subscribe'];
        $h->post->comments = $h->vars['submitted_data']['submit_comments'];
    }
    
    
    /**
     * Delete comments when post deleted
     */
    public function post_delete_post($h)
    {
        if (!$h->post->id) { return false; }
        
        $sql = "DELETE FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d";
        $h->db->query($h->db->prepare($sql, $h->post->id));
    }
    
    
    /**
     * Add all comments link to Profile
     */
    public function profile_usage($h)
    {
        echo "<a id='profile_see_comments' href='" . $h->url(array('page'=>'comments', 'user'=>$h->vars['user']->name)) . "'>";
        echo $h->lang['comments_profile_see_comments'] . ".";
        echo "</a>";
        
    }
    
    
    /**
     * Publish content as an RSS feed
     * Uses the 3rd party RSS Writer class.
     */    
    public function rssFeed($h)
    {
        require_once(EXTENSIONS . 'RSSWriterClass/rsswriter.php');
        
        $select = '*';

        $limit = $h->cage->get->getInt('limit');
        $user = $h->cage->get->testUsername('user');

        if (!$limit) { $limit = 10; }
        if ($user) { 
            $userid = $h->getUserIdFromName($user);
        } else {
            $userid = 0;
        }
        
        $h->pluginHook('comments_rss_feed');
        
        $feed           = new RSS();
        $feed->title    = SITE_NAME;
        $feed->link     = BASEURL;
        
        if ($user) { 
            $feed->description = $h->lang["comment_rss_comments_from_user"] . " " . $user; 
        } else {
            $feed->description = $h->lang["comment_rss_latest_comments"] . SITE_NAME;
        }
        
        // fetch comments from the database        
        $comments = $h->comment->getAllComments($h, 0, "desc", $limit, $userid);
        
        if ($comments) {
            foreach ($comments as $comment) 
            {
                $h->readPost($comment->comment_post_id);
                
                $author = $h->getUserNameFromId($comment->comment_user_id);
                
                $item = new RSSItem();
                if ($user) { 
                    $title = $h->lang["comment_rss_comment_on"] . html_entity_decode(urldecode($h->post->title), ENT_QUOTES,'UTF-8');
                } else {
                    $title = $author . $h->lang["comment_rss_commented_on"] . html_entity_decode(urldecode($h->post->title), ENT_QUOTES,'UTF-8');
                }
                $item->title = stripslashes($title);
                $item->link  = $h->url(array('page'=>$comment->comment_post_id)) . "#c" . $comment->comment_id;
                $item->setPubDate($comment->comment_date); 
                $item->description = "<![CDATA[ " . stripslashes(urldecode($comment->comment_content)) . " ]]>";
                $feed->addItem($item);
            }
        }
        echo $feed->serve();
    }
    
    
    /**
     * Show stats on Admin home page
     */
    public function admin_theme_main_stats($h, $vars)
    {
        echo "<li>&nbsp;</li>";
    
        foreach ($vars as $stat_type) {
            require_once(LIBS . 'Comment.php');
            $c = new Comment();
            $comments = $c->stats($h, $stat_type);
            if (!$comments) { $comments = 0; }
            $lang_name = 'comments_admin_stats_' . $stat_type;
            echo "<li>" . $h->lang[$lang_name] . ": " . $comments . "</li>";
        }
    }
    
    
    /**
    * Send an email to thread subscribers
    *
    * @param int $post_id
    */
    function emailCommentSubscribers($h, $post_id)
    {
        $h->readPost($post_id);
    
        // build a list of subscribers
        $subscriber_ids = array();
        
        // Get id of post author if subscribed
        if ($h->post->subscribe == 1) {
            array_push($subscriber_ids, $h->post->author);
        }
        
        // Get ids of comment authors if subscribed
        $sql = "SELECT comment_user_id FROM " . TABLE_COMMENTS . " WHERE comment_subscribe = %d AND comment_post_id = %d";
        $comment_subscribers = $h->db->get_results($h->db->prepare($sql, 1, $h->post->id));
        if ($comment_subscribers) {
            foreach ($comment_subscribers as $comment_subscriber) {
                array_push($subscriber_ids, $comment_subscriber->comment_user_id); 
            }
        }
        
        // Use the ids to make an array of unique email addresses
        $subscribers = array();
        $subscriber_ids = array_unique($subscriber_ids);
        foreach ($subscriber_ids as $subscriber_id) {
            // remove the current comment author so he/she doesn't get emailed his own comment
            if ($subscriber_id != $h->comment->author) {
                $email = $h->db->get_var($h->db->prepare("SELECT user_email FROM " . TABLE_USERS . " WHERE user_id = %d", $subscriber_id));
                array_push($subscribers, $email);
            }
        }
        
        $send_to = trim(implode(",", $subscribers),",");
        
        $comment_author = $h->getUserNameFromId($h->comment->author);
        
        //clean up content:
        $story_title = stripslashes(html_entity_decode(urldecode($h->post->title), ENT_QUOTES,'UTF-8'));
        $comment_content = stripslashes($h->comment->content);
        
        $subject = $comment_author . ' ' . $h->lang["comment_email_subject"]  . ' ' . $story_title;
        
        $message =  $comment_author . $h->lang["comment_email_intro"] . SITE_NAME . ": \r\n\r\n";
        $message .= $h->lang["comment_email_story_title"] . $story_title . "\r\n"; 
        $message .= $h->lang["comment_email_story_link"] . $h->url(array('page'=>$h->post->id)) . "\r\n\r\n";
        $message .= $h->lang["comment_email_comment"] . $comment_content . "\r\n\r\n";
        $message .= "************************ \r\n";
        $message .= $h->lang["comment_email_do_not_reply"] . " \r\n";
        $message .= $h->lang["comment_email_unsubscribe"];
        
        $from = SITE_EMAIL;
        $to = $h->comment->email;  // send email to address specified in Comment Settings; 
        if($send_to != "") {
            $bcc = "\r\nBCC: " . $send_to;    // BCC individual addresses;
        } else {
            $bcc = "";
        }
        $headers = "From: " . $from . $bcc . "\r\nReply-To: " . $from . "\r\nX-Priority: 3\r\n";
    
        /*
        echo "to: " . $to . "<br />";
        echo "bcc: " . $bcc . "<br />";
        echo "subject: " . $subject . "<br />";
        echo "message: " . $message . "<br />";
        echo "headers: " . $headers . "<br />";
        exit;
        */
    
        mail($to, $subject, $message, $headers);
    }
}

?>
