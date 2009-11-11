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
$lang["submit_form_home"] = "Home";
$lang["submit_form_step1"] = "Submit a Story 1/3";
$lang["submit_form_step2"] = "Submit a Story 2/3";
$lang["submit_form_step3"] = "Submit a Story 3/3";
$lang["submit_form_url"] = "Source URL:";
$lang["submit_form_title"] = "Title:";
$lang["submit_form_content"] = "Description:";
$lang["submit_form_instructions_1"] = "Enter a url to submit:";
$lang["submit_form_not_found"] = "No title found";
$lang["submit_form_instructions_2"] = "Complete the fields below.";
$lang['submit_form_subscribe'] = "Subscribe to comments";
$lang["submit_form_instructions_3"] = "Your submission will look like this. Use the buttons below to modify or confirm it.";
$lang["submit_form_url_not_present_error"] = "Invalid url";
$lang["submit_form_url_already_exists_error"] = "Sorry, that url has already been submitted.";
$lang["submit_form_no_permission"] = "Sorry, you don't have permission to post links.";
$lang['submit_form_url_blocked'] = "Sorry, this url or domain is on the blocked list.";
$lang['submit_form_daily_limit_exceeded'] = "Sorry, you have reached the 24 hour submission limit.";
$lang['submit_form_freq_limit_error'] = "Please wait a while before you submit another story.";
$lang['submit_form_content_too_many_links'] = "There are too many links in the description.";
$lang["submit_form_title_not_present_error"] = "No title was provided.";
$lang["submit_form_title_already_exists_error"] = "Sorry, that title has already been used.";
$lang["submit_form_content_not_present_error"] = "No description entered.";
$lang["submit_form_content_too_short_error"] = "Sorry, that description is too short.";
$lang["submit_form_submit_button"] = "Submit";
$lang['submit_form_submit_edit_button'] = "Edit";
$lang['submit_form_submit_confirm_button'] = "Confirm";
$lang['submit_form_submit_next_button'] = "Next";
$lang['submit_form_submit_accidental_click'] = "Wait, you have not submitted your story yet!";
$lang['submit_form_allowable_tags'] = "Allowed HTML tags:";
$lang['submit_form_moderation'] = "Thank you, your post has been placed in the moderation queue.";

/* Edit Post */
$lang["submit_edit_post_admin_only"] = "Admin Only...";
$lang["submit_edit_post_title"] = "Edit Post";
$lang["submit_edit_post_instructions"] = "Edit the fields below.";
$lang["submit_edit_post_title_not_present_error"] = "No title was provided.";
$lang["submit_edit_post_content_not_present_error"] = "No description entered.";
$lang["submit_edit_post_status"] = "Post status:";
$lang['submit_form_url_not_complete_error'] = "Source URL error...";
$lang["submit_edit_post_delete"] = "Physically delete this post, its votes and comments";
$lang["submit_edit_post_save"] = "Save";
$lang["submit_edit_post_deleted"] = "This post and associated tags, votes and comments have been deleted.";

/* Submission Disabled */
$lang['submit_disabled'] = "Sorry, story submission is disabled at this time.";

/* Submit Trackback */
$lang['submit_trackback_excerpt'] = "This article has been featured on";

/* Navigation */
$lang['submit_submit_a_story'] = "Submit a Story";

/* Submit Settings */
$lang["submit_settings_header"] = "Submit Configuration";
$lang["submit_settings_post_components"] = "<b>Post Components</b>";
$lang["submit_settings_submission_settings"] = "<b>Submission Settings</b> (for users with 'member' roles)";
$lang["submit_settings_post_archiving"] = "<b>Post Archiving</b>";
$lang["submit_settings_content"] = "Description";
$lang["submit_settings_instructions"] = "Check the components you want submissions to have:";
$lang["submit_settings_save"] = "Save";
$lang["submit_settings_enable"] = "Enable story submission";
$lang["submit_settings_title"] = "Title";
$lang["submit_settings_author"] = "Author";
$lang["submit_settings_date"] = "Date";
$lang["submit_settings_content"] = "Description";
$lang["submit_settings_content_min_length"] = "Minimum characters";
$lang["submit_settings_summary"] = "Summary";
$lang["submit_settings_summary_max_length"] = "Maximum characters";
$lang["submit_settings_summary_instruct"] = "(Description truncated on list pages)";
$lang["submit_settings_tags"] = "Tags";
$lang["submit_settings_max_tags"] = "Maximum characters";
$lang["submit_settings_latest"] = "Split posts into 'Top' and 'Latest'";
$lang["submit_settings_posts_per_page"] = "posts per page <small>(default: 10)</small>";
$lang["submit_settings_allowable_tags"] = "Allowed HTML tags in post description:";
$lang["submit_settings_allowable_tags_example"] = "<small>(E.g. &lt;b&gt;&lt;u&gt;&lt;i&gt;&lt;blockquote&gt;)</small>";
$lang["submit_settings_saved"] = "Settings Saved.";
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
$lang["submit_settings_post_archive_desc"] = "On the Maintenance page, there's a link you can use to archive old posts (or remove an existing archive). Archiving speeds up your site by excluding old posts and their comments, votes and tags from most database usage. Old posts can still be accessed via the search box or when loaded directly (e.g. from Google). <i>Note: Archiving is not automatic. You will need to update the archive periodically from the Maintenance page.</i>";
$lang["submit_settings_post_archive"] = "Posts older than this will be archived. <small>(Default: No archive)</small>";
$lang["submit_settings_post_archive_no_archive"] = "No archive";
$lang["submit_settings_post_archive_180"] = "6 months";
$lang["submit_settings_post_archive_365"] = "1 year";
$lang["submit_settings_post_archive_730"] = "2 years";
$lang["submit_settings_post_archive_1095"] = "3 years";

/* Maintenance page */
$lang["submit_maintenance_update_archive"] = "Update the post archive";
$lang["submit_maintenance_update_archive_remove"] = "Move any archived posts out of the archive. <small>(See Submit Settings)</small>";
$lang["submit_maintenance_update_archive_desc_1"] = "Posts older than ";
$lang["submit_maintenance_update_archive_desc_2"] = " will be moved into the archive. <small>(See Submit Settings)</small>";
$lang["submit_maintenance_archive_removed"] = "Done. There are currently no archived posts.";
$lang["submit_maintenance_archive_updated"] = "Archive Updated";

/* Submit Breadcrumbs */
$lang["post_breadcrumbs_all"] = "All Posts";
$lang["post_breadcrumbs_top"] = "Top Posts";
$lang["post_breadcrumbs_latest"] = "Latest Posts";
$lang["post_breadcrumbs_tag"] = "Tag";
$lang["post_breadcrumbs_category"] = "Category";
$lang["post_breadcrumbs_user"] = "User";
$lang["post_breadcrumbs_upcoming"] = "Upcoming Posts";
$lang["post_breadcrumbs_top_24_hours"] = "Top Posts (last 24 Hours)";
$lang["post_breadcrumbs_top_48_hours"] = "Top Posts (last 48 Hours)";
$lang["post_breadcrumbs_top_7_days"] = "Top Posts (last 7 Days)";
$lang["post_breadcrumbs_top_30_days"] = "Top Posts (last 30 Days)";
$lang["post_breadcrumbs_top_365_days"] = "Top Posts (last 365 Days)";
$lang["post_breadcrumbs_top_all_time"] = "Top Posts (All-Time)";

/* Submit Post */
$lang["submit_page_title_main"] = "top";
$lang["submit_page_title_latest"] = "latest";
$lang["submit_page_title_all"] = "all";
$lang["submit_post_edit"] = "Edit";
$lang["submit_post_read_more"] = "[Read More]";
$lang["submit_post_posted"] = "Posted";
$lang["submit_post_by"] = "by";
$lang["submit_post_ago"] = "ago";
$lang["submit_post_buried"] = "This post has been buried";
$lang["submit_post_pending"] = "This post is pending approval by a moderator";

/* Submit RSS Feed */
$lang["submit_rss_latest_from"] = "Latest from";
$lang["submit_rss_top_stories_from"] = "Top Stories from";
$lang["submit_rss_stories_from_user"] = "Stories submitted by";
$lang["submit_rss_stories_tagged"] = "Stories tagged"; 
$lang["submit_rss_stories_in_category"] = "Stories posted in";
$lang["submit_rss_stories_search"] = "Search results for";
$lang["submit_rss_stories_upcoming"] = "Upcoming stories";

?>