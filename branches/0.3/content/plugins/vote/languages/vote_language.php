<?php

/* **************************************************************************************************** 
 *  File: plugins/vote/languages/vote_language.php
 *  Purpose: A language file for Vote. 
 *  Notes: ---
 *  License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */

/* Sidebar */
$lang["vote_admin_sidebar"] = "Vote";

/* Navigation */
$lang["vote_navigation_top_posts"] = "Top Posts";
$lang["vote_navigation_latest"] = "Latest";

/* Vote Button */
$lang["vote_button_vote"] = "Vote!";
$lang["vote_button_unvote"] = "Un-vote";
$lang["vote_button_voted"] = "Voted";
$lang["vote_button_up_link"] = "Up!";
$lang["vote_button_up"] = "Up";
$lang["vote_button_down_link"] = "Down!";
$lang["vote_button_down"] = "Down";
$lang["vote_button_yes_link"] = "Yes!";
$lang["vote_button_yes"] = "Yes";
$lang["vote_button_no_link"] = "No!";
$lang["vote_button_no"] = "No";
$lang['vote_already_voted'] = "Sorry, you have already voted for this story.";

/* Show Post - ALert links and flagged message */
$lang["vote_alert"] = "Flag it";
$lang["vote_alert_reason_title"] = "Reason for flagging this post:";
$lang["vote_alert_reason_1"] = "Spam";
$lang["vote_alert_reason_2"] = "Inappropriate";
$lang["vote_alert_reason_3"] = "Broken Link";
$lang["vote_alert_reason_4"] = "Duplicate";
$lang["vote_alert_reason_5"] = "Wrong Category";
$lang["vote_alert_reason_6"] = "Really lame";
$lang["vote_alert_already_flagged"] = "You've already flagged this post";
$lang["vote_alert_flagged_message_1"] = "This post has been flagged by";
$lang["vote_alert_flagged_message_2"] = "as";
$lang["vote_alert_flagged_message_user"] = "user";
$lang["vote_alert_flagged_message_users"] = "users";
$lang["vote_alert_flagged_message_reason"] = "reason";
$lang["vote_alert_flagged_message_reasons"] = "reasons";
$lang["vote_alert_post_buried"] = "This post has been buried";

/* Vote Settings */
$lang["vote_settings_header"] = "Vote Settings";
$lang["vote_settings_vote_type"] = "Choose the vote type you want to use:";
$lang["vote_settings_vote_unvote"] = "Vote & Un-vote - Standard voting method";
$lang["vote_settings_up_down"] = "Up & Down - Vote posts above or below zero ";
$lang["vote_settings_yes_no"] = "Yes/No Poll - Show votes for <i>and</i> against a post";
$lang["vote_settings_vote_auto"] = "Auto-vote on submission:";
$lang["vote_settings_submit_vote"] = "Automatically vote for a story you submit";
$lang["vote_settings_vote_anonymous"] = "Allow anonymous votes:";
$lang["vote_settings_anonymous_votes"] = "Let non-members vote for stories";
$lang["vote_settings_submit_vote_value"] = "Give that automatic vote a value of";
$lang["vote_settings_submit_vote_value_invalid"] = "The automatic vote must be a positive integer";
$lang["vote_settings_vote_promote_bury"] = "Promoting and burying posts:";
$lang["vote_settings_votes_to_promote"] = "Number of votes needed to get on the front page:";
$lang["vote_settings_votes_to_promote_invalid"] = "The number of votes needed must be a positive integer";
$lang["vote_settings_use_alerts"] = "Enable Alerts (i.e. flag / bury). Alerts are only used on \"new\" posts";
$lang["vote_settings_alerts_to_bury"] = "Number of alerts to automatically bury a post:";
$lang["vote_settings_alerts_to_bury_invalid"] = "The number of alerts needed must be a positive integer";
$lang["vote_settings_physical_delete"] = "Physically delete a post from the database when buried";
$lang["vote_settings_save"] = "Save";
$lang["vote_settings_saved"] = "Settings Saved.";

?>