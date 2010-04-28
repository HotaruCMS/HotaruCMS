<?php
/**
 * JOURNAL LANGUAGE
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

/* General */
$lang['journal'] = "Journal";
$lang['journals'] = "Journals";
$lang["journal_site_name"] = SITE_NAME;
$lang["journal_no_items"] = "No items found.";

/* Journal Settings */
$lang["journal_settings_header"] = "Journal Configuration";
$lang["journal_settings_need_sb_post"] = "Require one approved social bookmarking post before being able to post journal entries";
$lang["journal_settings_items_per_page"] = "Number of journal entries per page";
$lang["journal_settings_rss_items"] = "Number of journal entries in an RSS feed";
$lang["journal_settings_content_min_length"] = "Minimum number of characters in a journal entry";
$lang["journal_settings_summary"] = "Summary";
$lang["journal_settings_summary_max_length"] = "Maximum characters";
$lang["journal_settings_summary_instruct"] = "(Description truncated on list pages)";
$lang["journal_settings_allowable_tags"] = "Allowed HTML tags in journal entry:";
$lang["journal_settings_allowable_tags_example"] = "<small>(E.g. &lt;b&gt;&lt;u&gt;&lt;i&gt;&lt;blockquote&gt;)</small>";

/* Journal Post & Reply Forms */
$lang['journal_please_login'] = "You need to be logged in to comment.";
$lang['journal_no_permission'] = "You do not have permission to comment.";
$lang['journal_form_submit'] = "Post";
$lang['journal_form_edit'] = "Update";
$lang['journal_form_comment'] = "Comment";
$lang['journal_form_subscribe'] = "Subscribe to replies";
$lang['journal_form_unsubscribe'] = "To unsubscribe, uncheck the box and submit an empty comment";
$lang['journal_form_allowable_tags'] = "Allowed HTML tags:";
$lang['journal_form_set_pending'] = "Put in moderation queue";
$lang['journal_form_delete'] = "Delete this and responses";
$lang['journal_form_moderation_on'] = "<b>Note</b>: <span style='color: red;'>All comments are moderated</span>.";

/* Post */
$lang['journal_reply_by'] = "Reply by";
$lang['journal_time_ago'] = "ago";
$lang['journal_reply_link'] = "Reply";
$lang['journal_edit_link'] = "Edit";
$lang['journal_no_title'] = "No Title";

/* User page */
$lang['journal_entry'] = "Post a new journal entry:";
$lang['journal_header'] = "Journal: ";
$lang['journal_title'] = " (Title)";

/* Errors */
$lang['journal_error_csrf'] = "CSRF error. Please try again.";
$lang['journal_error_title_exists'] = "That title already exists.";
$lang['journal_error_no_title'] = "Please enter a title.";
$lang['journal_error_no_content'] = "Please write something.";
$lang['journal_error_too_short'] = "Sorry, that's too short.";
$lang['journal_error_no_perms'] = "Sorry, you need permission to post journal entries.";
$lang['journal_error_no_approved_posts'] = "Sorry, you can't start a journal until you have submitted an article and had it approved by a moderator.";
$lang['journal_error_pending'] = "Thank you!<br />Your post is awaiting moderator approval.";

/* RSS */
$lang['journal_rss_title'] = SITE_NAME . " Journals";
$lang['journal_rss_user_title'] = SITE_NAME . " Journal: ";
$lang['journal_rss_description'] = "Latest journal entries";
$lang['journal_rss_user_description'] = "Latest journal entries from ";
$lang["journal_rss_title_anchor"] = "Journal RSS";
?>