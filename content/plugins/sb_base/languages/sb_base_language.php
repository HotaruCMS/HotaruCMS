<?php
/**
 * SB BASE LANGUAGE
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

/* Index */
$lang["sb_base_site_name"] = SITE_NAME;
$lang["sb_base_top"] = "Top Posts";
$lang["sb_base_latest"] = "Latest";
$lang["sb_base_new"] = "Latest"; // same as above, but necessary
$lang["sb_base_upcoming"] = "Upcoming Posts";
$lang["sb_base_all"] = "All Posts";
$lang["sb_base_tag"] = "Tag";
$lang["sb_base_category"] = "Category";
$lang["sb_base_user"] = "User";
$lang["sb_base_top_24_hours"] = "Top Posts (last 24 Hours)";
$lang["sb_base_top_48_hours"] = "Top Posts (last 48 Hours)";
$lang["sb_base_top_7_days"] = "Top Posts (last 7 Days)";
$lang["sb_base_top_30_days"] = "Top Posts (last 30 Days)";
$lang["sb_base_top_365_days"] = "Top Posts (last 365 Days)";
$lang["sb_base_top_all_time"] = "Top Posts (All-Time)";
$lang["sb_base_no_posts"] = "No posts found.";

/* Post */
$lang["sb_base_post_edit"] = "Edit";
$lang["sb_base_post_read_more"] = "[Read More]";
$lang["sb_base_post_posted_by"] = "Posted by";
$lang["sb_base_post_in"] = "in";
$lang["sb_base_post_ago"] = "ago";
$lang["sb_base_post_buried"] = "This post has been buried";
$lang["sb_base_post_pending"] = "This post is pending approval by a moderator";

/* SB Base Settings */
$lang["sb_base_settings_header"] = "SB Base Configuration";
$lang["sb_base_settings_posts_per_page"] = "Posts per page <small>(default: 10)</small>";
$lang["sb_base_settings_rss_redirect"] = "Automatically redirect RSS links to their original sources";
$lang["sb_base_settings_post_archiving"] = "<b>Post Archiving</b>";
$lang["sb_base_settings_post_archive_desc"] = "On the Maintenance page, there's a link you can use to archive old posts (or remove an existing archive). Archiving speeds up your site by excluding old posts and their comments, votes and tags from most database usage. Old posts can still be accessed via the search box or when loaded directly (e.g. from Google). <i>Note: Archiving is not automatic. You will need to update the archive periodically from the Maintenance page.</i>";
$lang["sb_base_settings_post_archive"] = "Posts older than this will be archived. <small>(Default: No archive)</small>";
$lang["sb_base_settings_post_archive_no_archive"] = "No archive";
$lang["sb_base_settings_post_archive_30"] = "1 month";
$lang["sb_base_settings_post_archive_90"] = "3 months";
$lang["sb_base_settings_post_archive_180"] = "6 months";
$lang["sb_base_settings_post_archive_365"] = "1 year";
$lang["sb_base_settings_post_archive_730"] = "2 years";
$lang["sb_base_settings_post_archive_1095"] = "3 years";

/* Maintenance page */
$lang["sb_base_maintenance_update_archive"] = "Update the post archive";
$lang["sb_base_maintenance_update_archive_remove"] = "Move any archived posts out of the archive. <small>(See SB Base Settings)</small>";
$lang["sb_base_maintenance_update_archive_desc_1"] = "Posts older than ";
$lang["sb_base_maintenance_update_archive_desc_2"] = " will be moved into the archive. <small>(See SB Base Settings)</small>";
$lang["sb_base_maintenance_archive_removed"] = "Done. There are currently no archived posts.";
$lang["sb_base_maintenance_archive_updated"] = "Archive Updated";

/* Admin Stats */
$lang["sb_base_admin_stats_total_posts"] = "Total posts";
$lang["sb_base_admin_stats_approved_posts"] = "Approved posts";
$lang["sb_base_admin_stats_pending_posts"] = "Pending posts";
$lang["sb_base_admin_stats_buried_posts"] = "Buried posts";
$lang["sb_base_admin_stats_archived_posts"] = "Archived posts";

/* User Settings */
$lang['sb_base_users_settings_open_new_tab'] = "Open posts in a new tab?";
$lang['sb_base_users_settings_link_action'] = "List page links open:";
$lang['sb_base_users_settings_source'] = "Original post";
$lang['sb_base_users_settings_post'] = SITE_NAME . " post";

/* Sort & Filter */
$lang["sb_base_sort_by"] = "Sort by:";
$lang["sb_base_sort_recently_popular"] = "Popular";
$lang["sb_base_sort_upcoming"] = "Upcoming";
$lang["sb_base_sort_latest"] = "Latest";
$lang["sb_base_sort_new"] = "Latest"; // same as latest, but necessary
$lang["sb_base_sort_all"] = "All";
$lang["sb_base_sort_best_from"] = "Best from:";
$lang["sb_base_sort_top_1_day"] = "24 Hrs";
$lang["sb_base_sort_top_2_days"] = "48 Hrs";
$lang["sb_base_sort_top_7_days"] = "7 Days";
$lang["sb_base_sort_top_30_days"] = "30 Days";
$lang["sb_base_sort_top_365_days"] = "365 Days";
$lang["sb_base_sort_top_all_time"] = "All Time";

/* Submit RSS Feed */
$lang["sb_base_rss_latest_from"] = "Latest from";
$lang["sb_base_rss_top_stories_from"] = "Top Stories from";
$lang["sb_base_rss_upcoming_stories_from"] = "Upcoming Stories on";
$lang["sb_base_rss_stories_from_user"] = "Stories submitted by";
$lang["sb_base_rss_stories_tagged"] = "Stories tagged"; 
$lang["sb_base_rss_stories_in_category"] = "Stories posted in";
$lang["sb_base_rss_stories_search"] = "Search results for";
$lang["sb_base_rss_stories_upcoming"] = "Upcoming stories";
?>