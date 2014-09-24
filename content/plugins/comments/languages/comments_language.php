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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

$lang['comments'] = "Comments";

/* Comment Link - see libs/Comment.php -> count_comments function */
$lang['comments_singular_link'] = "comment";
$lang['comments_plural_link'] = "comments";
$lang['comments_none_link'] = "No comments";
$lang['comments_leave_comment'] = "Leave a comment";
$lang['comments_access_comment_manager'] = "Access Comment Manager";

/* Comment */
$lang['comments_written_by'] = "Written by";
$lang['comments_time_ago'] = "ago";
$lang['comments_reply_link'] = "Reply";
$lang['comments_edit_link'] = "Edit";
$lang['comments_show_hide'] = "Show / Hide";

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

/* Comment Submission */
$lang['comment_moderation_unsubscribed'] = "You have unsubscribed from this thread.";
$lang['comment_moderation_exceeded_daily_limit'] = "You've exceeded the daily comment limit. This and any further comments will need approval.";
$lang['comment_moderation_exceeded_url_limit'] = "You used too many URLs so your comment will need approval.";
$lang['comment_moderation_not_enough_comments'] = "Your comment is awaiting approval.";

/* All Comments */
$lang['comments_posted_on'] = " on ";
$lang['comments_all'] = "All Comments";
$lang['comments_user_no_comments'] = "This user hasn't made any comments yet.";

/* Admin Settings */
$lang['comments_admin_sidebar'] = "Comments";
$lang["comments_settings_header"] = "Comments Settings";
$lang["comments_settings_instructions"] = "Options for comments:";
$lang["comments_settings_form"] = "Enable the comment form <small>(Unchecked closes all forms. Checked opens all forms unless individually closed)</small>";
$lang["comments_settings_avatars"] = "Enable avatars on comments (requires an avatar plugin, e.g. <i>Gravatar</i>)";
$lang["comments_settings_avatar_size"] = "Avatar size in pixels <small>(default: 16)</small>";
$lang["comments_settings_votes"] = "Enable votes on comments (requires a comment voting plugin)";
$lang["comments_settings_hide"] = "Hide comment content with this many down votes or more <small>(default: 3)</small>";
$lang["comments_settings_bury"] = "Bury comments completely with this many down votes or more <small>(default: 10)</small>";
$lang["comments_settings_levels"] = "Comment nesting levels <small>(default: 5)</small>";
$lang["comments_settings_pagination"] = "Pagination (spread comments over pages)";
$lang["comments_settings_per_page"] = "Comments per page <small>(default: 20)</small>";
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
$lang["comments_settings_email_notify"] = "When a new comment is posted, send an email to admins, supermods and moderators who have permission to access the Comment Manager...";
$lang["comments_settings_email_notify_all"] = "All new comments";
$lang["comments_settings_email_notify_pending"] = "Pending comments only";
$lang["comments_settings_email_notify_none"] = "None";
$lang["comments_settings_url_limit"] = "Max links per comment <small>(enter 0 for no limit)</small>";
$lang["comments_settings_daily_limit"] = "Max comments a user can post in 24 hours <small>(enter 0 for no limit)</small>";
$lang["comments_settings_limit_note"] = "<small>Comments with more links than allowed, or comments that exceed the daily limit will go into moderation. These restrictions only apply to users with 'member' status.</small>";
$lang["comments_settings_no_limit"] = "No limit";
$lang["comments_settings_save"] = "Save";
$lang["comments_settings_saved"] = "Settings saved";

/* Edit Post */
$lang['submit_form_enable_comments'] = "Enable comment form";

/* RSS */
$lang["comment_rss_latest_comments"] = "Latest comments from ";
$lang["comment_rss_comments_from_user"] = "Latest comments by ";
$lang["comment_rss_comment_on"] = "Comment on: ";
$lang["comment_rss_commented_on"] = " commented on ";

/* Admin Stats */
$lang["comments_admin_stats_comments"] = "Comments";
$lang["comments_admin_stats_all"] = "Total";
$lang["comments_admin_stats_approved"] = "Approved";
$lang["comments_admin_stats_pending"] = "Pending";
$lang["comments_admin_stats_archived"] = "Archived";

/* Email to comment subscribers */
$lang["comment_email_subject"] = " has commented on ";
$lang["comment_email_intro"] = " has commented on a story you are subscribed to at ";
$lang["comment_email_story_title"] = "Story Title: ";
$lang["comment_email_story_link"] = "Story Link: ";
$lang["comment_email_comment"] = "Comment: ";
$lang["comment_email_do_not_reply"] = "Do not reply to this email. Please visit the above link and comment there.";
$lang["comment_email_unsubscribe"] = "To unsubscribe, uncheck the \"Subscribe to comments\" box and submit an empty comment.";
?>