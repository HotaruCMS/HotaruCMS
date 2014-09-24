<?php
/**
 * The UserFunctions class contains some more useful methods for working with users
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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
    
class UserFunctions
{    
    /**
     * Send an email to admins/supermods chosen to receive emails about new user signups
     *
     * @param string $type - notification type, e.g. 'post', 'user', 'comment'
     * @param string $status - role or status new user, post or comment
     * @param string $id - post or user id
     * @param string $commentid - comment id
     */
    public function notifyMods($h, $type, $status, $id = 0, $commentid = 0)
    {
        $h->includeLanguage('users'); // in some cases, we don't already have the language file so need to include it.

        $line_break = "\r\n\r\n";
        $next_line = "\r\n";
        
        $user = new UserBase();
        
        switch ($type) {
            case 'user':
                $user->getUserBasic($h, $id);
                $user_signin_settings = $h->getSerializedSettings('user_signin');
                $email_mods = $user_signin_settings['email_notify_mods'];
                $subject = $h->lang['userfunctions_notifymods_subject_user'];
                $about = $h->lang['userfunctions_notifymods_body_about_user'];
                break;
            case 'post':
                $user->getUserBasic($h, $h->post->author);
                $submit_settings = $h->getSerializedSettings('submit');
                $email_mods = $submit_settings['email_notify_mods'];
                $subject = $h->lang['userfunctions_notifymods_subject_post'];
                $about = $h->lang['userfunctions_notifymods_body_about_post'];
                $h->readPost($id); // If you're having problems, the caching used in an earlier readPost might be the cause
                // emails were still saying new posts were "pending" and sending notification, so let's forcefully get the status:
                $sql = "SELECT post_status FROM " . TABLE_POSTS . " WHERE post_id = %d";
                $status = $h->db->get_var($h->db->prepare($sql, $id));
                $h->post->status = $status;
                break;
            case 'comment':
                $user->getUserBasic($h, $h->comment->author);
                $comments_settings = $h->getSerializedSettings('comments');
                $email_mods = $comments_settings['comment_email_notify_mods'];
                $subject = $h->lang['userfunctions_notifymods_subject_comment'];
                $about = $h->lang['userfunctions_notifymods_body_about_comment'];
                $h->readPost($id); // If you're having problems, the caching used in an earlier readPost might be the cause
                $comment_array = $h->getComment($commentid);
                $comment = $h->readComment($comment_array);
                break;
            default:
        }
        
        // send email

        foreach ($email_mods as $mod)
        {
            if ($mod['type'] == 'none') { continue; } // skip rest of this iteration
            if (($mod['type'] == 'pending') && ($status != 'pending')) { continue; } // skip rest of this iteration
                
            $body = $h->lang['userfunctions_notifymods_hello'] . $h->getUserNameFromId($mod['id']);
            $body .= $line_break;
            $body .= $about;
            
            if ($type == 'post') {
                $body .= $line_break;
                $body .= $h->lang['userfunctions_notifymods_body_post_status'] . $h->post->status . $next_line;
                $body .= $h->lang['userfunctions_notifymods_body_post_title'] . stripslashes(html_entity_decode(urldecode($h->post->title), ENT_QUOTES,'UTF-8')) . $next_line;
                $body .= $h->lang['userfunctions_notifymods_body_post_content'] . stripslashes(html_entity_decode(urldecode($h->post->content), ENT_QUOTES,'UTF-8')) . $next_line;
                $body .= $h->lang['userfunctions_notifymods_body_post_page'] . $h->url(array('page'=>$h->post->id)) . $next_line; // edit post page
                $body .= $h->lang['userfunctions_notifymods_body_post_orig'] . $h->post->origUrl . $next_line; // edit post page
                $body .= $h->lang['userfunctions_notifymods_body_post_edit'] . BASEURL . "index.php?page=edit_post&post_id=" . $id . $next_line; // edit post page
                $body .= $h->lang['userfunctions_notifymods_body_post_management'] . BASEURL . "admin_index.php?post_status_filter=" . $h->post->status . "&plugin=post_manager&page=plugin_settings&type=filter";
            
            }
            
            if ($type == 'comment') {
                $body .= $line_break;
                $body .= $h->lang['userfunctions_notifymods_body_post_title'] . stripslashes(html_entity_decode(urldecode($h->post->title), ENT_QUOTES,'UTF-8')) . $next_line;
                $body .= $h->lang['userfunctions_notifymods_body_comment_status'] . $comment->status . $next_line;
                $body .= $h->lang['userfunctions_notifymods_body_comment_content'] . stripslashes(html_entity_decode(urldecode($h->comment->content), ENT_QUOTES,'UTF-8')) . $next_line;
                $body .= $h->lang['userfunctions_notifymods_body_post_page'] . $h->url(array('page'=>$h->post->id)) . $next_line; // edit post page
                $body .= $h->lang['userfunctions_notifymods_body_comment_management'] . BASEURL . "admin_index.php?comment_status_filter=" . $comment->status . "&plugin=comment_manager&page=plugin_settings&type=filter";
            
            }
            
            $body .= $line_break;
            $body .= $h->lang['userfunctions_notifymods_body_user_name'] . $user->name . $next_line;
            $body .= $h->lang['userfunctions_notifymods_body_user_role'] . $user->role . $next_line;
            $body .= $h->lang['userfunctions_notifymods_body_user_email'] . $user->email . $next_line;
            $body .= $h->lang['userfunctions_notifymods_body_user_account'] . BASEURL . "index.php?page=account&user=" . $user->name . $next_line;
            $body .= $h->lang['userfunctions_notifymods_body_user_management'] . BASEURL . "admin_index.php?search_value=" . $user->name . "&plugin=user_manager&page=plugin_settings&type=search";
            
            $body .= $line_break;
            $body .= $h->lang['userfunctions_notifymods_body_regards'];
            $body .= $next_line;
            $body .= $h->lang['userfunctions_notifymods_body_sign'];
            $to = $mod['email'];
            
            $h->email($to, $subject, $body);
        }
        
        return true;
    }
    
}

?>