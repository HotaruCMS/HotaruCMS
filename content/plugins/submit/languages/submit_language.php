<?php
/**
 * SUBMIT LANGUAGE
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

/* Submit Form */
$lang["submit_home"] = "Home";
$lang["submit_step1"] = "Submit a Story 1/3";
$lang["submit_step2"] = "Submit a Story 2/3";
$lang["submit_step3"] = "Submit a Story 3/3";
$lang["submit_url"] = "Source URL:";
$lang["submit_title"] = "Title:";
$lang["submit_content"] = "Description:";
$lang["submit_instructions_1"] = "Enter a url to submit:";
$lang["submit_post_without_link"] = "Post without a URL"; 
$lang["submit_not_found"] = "No title found";
$lang["submit_instructions_2"] = "Complete the fields below.";
$lang['submit_subscribe'] = "Subscribe to comments";
$lang["submit_instructions_3"] = "Your submission will look like this. Use the buttons below to modify or confirm it.";
$lang["submit_nothing_submitted"] = "Nothing submitted...";
$lang["submit_url_not_present_error"] = "Invalid url";
$lang["submit_url_already_exists_error"] = "Sorry, that url has already been submitted.";
$lang["submit_no_post_permission"] = "Sorry, you don't have permission to post links.";
$lang["submit_no_edit_permission"] = "Sorry, you don't have permission to edit this post.";
$lang["submit_posting_closed"] = "Sorry, post submission is currently closed";
$lang['submit_url_blocked'] = "Sorry, this url or domain is on the blocked list.";
$lang['submit_daily_limit_exceeded'] = "Sorry, you have reached the 24 hour submission limit.";
$lang['submit_freq_limit_error'] = "Please wait a while before you submit another story.";
$lang['submit_content_too_many_links'] = "There are too many links in the description.";
$lang["submit_title_not_present_error"] = "No title was provided.";
$lang["submit_title_already_exists_error"] = "Sorry, that title has already been used.";
$lang["submit_content_not_present_error"] = "No description entered.";
$lang["submit_content_too_short_error"] = "Sorry, that description is too short.";
$lang["submit_submit_button"] = "Submit";
$lang['submit_submit_edit_button'] = "Edit";
$lang['submit_submit_confirm_button'] = "Confirm";
$lang['submit_submit_next_button'] = "Next";
$lang['submit_accidental_click'] = "Wait, you have not submitted your story yet!";
$lang['submit_allowable_tags'] = "Allowed HTML tags:";
$lang['submit_moderation'] = "Thank you, your post has been placed in the moderation queue.";
$lang["submit_category"] = "Category";
$lang['submit_category_select'] = "Select a category";
$lang['submit_category_error'] = "Please choose a category.";
$lang["submit_tags"] = "Tags:";
$lang['submit_tags_not_present_error'] = "No tags entered.";
$lang['submit_tags_length_error'] = "Sorry, you've entered too many tags.";

/* Edit Post */
$lang["submit_edit_admin_only"] = "Admin Only...";
$lang["submit_edit_title"] = "Edit Post";
$lang["submit_edit_instructions"] = "Edit the fields below.";
$lang["submit_edit_no_title_error"] = "No title was provided.";
$lang["submit_edit_content_not_present_error"] = "No description entered.";
$lang["submit_edit_status"] = "Post status:";
$lang['submit_edit_source_url_error'] = "Source URL error...";
$lang["submit_edit_delete"] = "Physically delete this post, its votes and comments";
$lang["submit_edit_save"] = "Save";
$lang["submit_edit_deleted"] = "This post and associated tags, votes and comments have been deleted.";

/* Submission Disabled */
$lang['submit_disabled'] = "Sorry, post submission is disabled at this time.";

/* Submit Trackback */
$lang['submit_trackback_excerpt'] = "This article has been featured on";

/* Navigation */
$lang['submit_submit_a_story'] = "Submit";

/* Submit Settings */
$lang["submit_settings_header"] = "Submit Configuration";
$lang["submit_settings_post_components"] = "<b>Displaying Posts</b>";
$lang["submit_settings_submission_settings"] = "<b>Submission Settings</b> (for users with 'member' roles)";
$lang["submit_settings_content"] = "Description";
$lang["submit_settings_instructions"] = "Check the components you want submissions to have:";
$lang["submit_settings_enable"] = "Enable post submission";
$lang["submit_settings_enable_instruct"] = "<small>(Uncheck this to prevent new submissions, but still allow post editing)</small>";
$lang["submit_settings_content_min_length"] = "Minimum characters";
$lang["submit_settings_summary"] = "Summary";
$lang["submit_settings_summary_max_length"] = "Maximum characters";
$lang["submit_settings_summary_instruct"] = "(Description truncated on list pages)";
$lang["submit_settings_latest"] = "Split posts into 'Top' and 'Latest'";
$lang["submit_settings_allowable_tags"] = "Allowed HTML tags in post description:";
$lang["submit_settings_allowable_tags_example"] = "<small>(E.g. &lt;b&gt;&lt;u&gt;&lt;i&gt;&lt;blockquote&gt;)</small>";
$lang["submit_settings_url_limit"] = "Max links within a post <small>(enter 0 for no limit)</small>";
$lang["submit_settings_daily_limit"] = "Max posts a user can submit in 24 hours <small>(enter 0 for no limit)</small>";
$lang["submit_settings_frequency_limit"] = "Minutes before a user can submit another post <small>(enter 0 for no limit)</small>";
$lang["submit_settings_limit_note"] = "<small>Those rules apply to \"member\" and \"undermod\" users.</small>";
$lang["submit_settings_auto_approve"] = "Automatically approve all posts";
$lang["submit_settings_some_pending_1"] = "Put first";
$lang["submit_settings_some_pending_2"] = " posts in the moderation queue";
$lang["submit_settings_all_pending"] = "Put all new posts in the moderation queue";
$lang["submit_settings_email_notify"] = "When a new post is submitted, email admins, supermods and moderators with \"edit posts\" permissions...";
$lang["submit_settings_email_notify_all"] = "All new posts";
$lang["submit_settings_email_notify_pending"] = "Pending posts only";
$lang["submit_settings_email_notify_none"] = "None";
$lang["submit_settings_categories"] = "Categories";
$lang["submit_settings_tags"] = "Tags";
$lang["submit_settings_max_tags"] = "Maximum characters";

?>