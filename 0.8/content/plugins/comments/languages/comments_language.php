<?php
/**
 * COMMENTS LANGUAGE
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

/* Comment Link - see libs/Comment.php -> count_comments function */
$lang['comments_singular_link'] = "comment";
$lang['comments_plural_link'] = "comments";
$lang['comments_none_link'] = "No comments";
$lang['comments_leave_comment'] = "Leave a comment";

/* Comment */
$lang['comments_written_by'] = "Written by";
$lang['comments_time_ago'] = "ago";
$lang['comments_reply_link'] = "REPLY";
$lang['comments_edit_link'] = "EDIT";

/* Comment Form */
$lang['comments_please_login'] = "You need to be logged in to comment.";
$lang['comments_no_permission'] = "You do not have permission to comment.";
$lang['comments_form_closed'] = "Comments are closed.";
$lang['comments_form_submit'] = "Submit";
$lang['comments_form_edit'] = "Update";
$lang['comments_form_subscribe'] = "Subscribe to comments";
$lang['comments_form_unsubscribe'] = "To unsubscribe, uncheck the box and submit an empty comment";
$lang['comments_form_allowable_tags'] = "Allowed HTML tags:";
$lang['comments_form_set_pending'] = "Put in moderation queue";
$lang['comments_form_delete'] = "Delete this and responses";
$lang['comments_form_moderation_on'] = "<b>Note</b>: <span style='color: red;'>All comments are moderated</span>.";

/* All Comments */
$lang['comments_posted_on'] = " on ";
$lang['comments_all'] = "All Comments";

/* Admin Settings */
$lang['comments_admin_sidebar'] = "Comments";
$lang["comments_settings_header"] = "Comments Settings";
$lang["comments_settings_instructions"] = "Options for comments:";
$lang["comments_settings_form"] = "Enable the comment form <small>(Unchecked closes all forms. Checked opens all forms unless individually closed)</small>";
$lang["comments_settings_avatars"] = "Enable avatars on comments (requires an avatar plugin, e.g. <i>Gravatar</i>)";
$lang["comments_settings_votes"] = "Enable votes on comments (requires a comment voting plugin)";
$lang["comments_settings_levels"] = "Comment nesting levels";
$lang["comments_settings_pagination"] = "Pagination (spread comments over pages)";
$lang["comments_settings_per_page"] = "Comments per page";
$lang["comments_settings_per_page_note"] = "<small>This is the number of comments shown per page on the <i>main</i> comments page. It's also the number of <i>parent</i> comments shown on individual threads if pagination is enabled.</small>";
$lang["comments_settings_email"] = "Send comments to:";
$lang["comments_settings_email_desc"] = "(Subscribers are BCC'd in the email)";
$lang["comments_settings_allowable_tags"] = "Allowed HTML tags in comments:";
$lang["comments_settings_allowable_tags_example"] = "<small>(E.g. &lt;b&gt;&lt;u&gt;&lt;i&gt;&lt;blockquote&gt;)</small>";
$lang["comments_settings_setpending"] = "Set all new comments to \"pending\"";
$lang["comments_settings_ascending"] = "Order comments oldest first";
$lang["comments_settings_descending"] = "Order comments newest first";
$lang["comments_settings_auto_approve"] = "Automatically approve all comments";
$lang["comments_settings_some_pending_1"] = "Put first";
$lang["comments_settings_some_pending_2"] = " comments in the moderation queue";
$lang["comments_settings_all_pending"] = "Put all new comments in the moderation queue";
$lang["comments_settings_email_notify"] = "When a new comment is posted, email admins, supermods and moderators with \"access admin\" permissions:";
$lang["comments_settings_email_notify_all"] = "All new comments";
$lang["comments_settings_email_notify_pending"] = "Pending comments only";
$lang["comments_settings_email_notify_none"] = "None";
$lang["comments_settings_save"] = "Save";
$lang["comments_settings_saved"] = "Settings saved";

/* Edit Post */
$lang['submit_form_enable_comments'] = "Enable comment form";

/* RSS */
$lang["comment_rss_latest_comments"] = "Latest comments from ";
$lang["comment_rss_comments_from_user"] = "Latest comments by ";
$lang["comment_rss_comment_on"] = "Comment on: ";
$lang["comment_rss_commented_on"] = " commented on ";

?>