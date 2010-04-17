<?php
/**
 * Upgrade Hotaru CMS
 * 
 * Steps through the set-up process, creating database tables and registering 
 * the Admin user. Note: You must delete this file after installation as it 
 * poses a serious security risk if left.
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

require_once('../hotaru_settings.php');
require_once(BASE . 'Hotaru.php');
$h = new Hotaru(); // must come before language inclusion
$sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
$old_version = $h->db->get_var($h->db->prepare($sql, "hotaru_version"));
require_once(INSTALL . 'install_language.php');    // language file for install

// delete existing cache
$h->deleteFiles(CACHE . 'db_cache');
$h->deleteFiles(CACHE . 'css_js_cache');
$h->deleteFiles(CACHE . 'rss_cache');

$step = $h->cage->get->getInt('step');        // Installation steps.

switch ($step) {
	case 1:
		upgrade_welcome();     // "Welcome to Hotaru CMS. 
		break;
	case 2:
		do_upgrade($old_version);
		upgrade_complete();    // Delete "install" folder. Visit your site"
		break;
	default:
		// Anything other than step=2
		upgrade_welcome();
		break;
}

exit;


/**
 * HTML header
 *
 * @return string returns the html output for the page header
 */
function html_header()
{
	global $lang;
	
	$header = "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 3.2//EN'>\n";
	$header .= "<HTML><HEAD>\n";
	$header .= "<meta http-equiv=Content-Type content='text/html; charset=UTF-8'>\n";
	
	// Title
	$header .= "<TITLE>" . $lang['upgrade_title'] . "</TITLE>\n";
	$header .= "<META HTTP-EQUIV='Content-Type' CONTENT='text'>\n";
	$header .= "<link rel='stylesheet' type='text/css' href='" . BASEURL . "install/reset-fonts-grids.css' type='text/css'>\n";
	$header .= "<link rel='stylesheet' type='text/css' href='" . BASEURL . "install/install_style.css'>\n";
	$header .= "</HEAD>\n";
	
	// Body start
	$header .= "<BODY>\n";
	$header .= "<div id='doc' class='yui-t7 install'>\n";
	$header .= "<div id='hd' role='banner'>";
	$header .= "<img align='left' src='" . BASEURL . "content/admin_themes/admin_default/images/hotaru.png' style='height:60px; width:60px;'>";
	$header .= "<h1>" . $lang['upgrade_title'] . "</h1></div>\n"; 
	$header .= "<div id='bd' role='main'>\n";
	$header .= "<div class='yui-g'>\n";
	
	return $header;
}


/**
 * HTML footer
 *
 * @return string returns the html output for the page footer
 */
function html_footer()
{
	global $lang;
	
	$footer = "<div class='clear'></div>\n"; // clear floats
	
	// Footer content (a link to the forums)
	$footer .= "<div id='ft' role='contentinfo'>";
	$footer .= "<p>" . $lang['install_trouble'] . "</p>";
	$footer .= "</div>\n"; // close "ft" div
	
	$footer .= "</div>\n"; // close "yui-g" div
	$footer .= "</div>\n"; // close "main" div
	$footer .= "</div>\n"; // close "yui-t7 install" div
	
	$footer .= "</BODY>\n";
	$footer .= "</HTML>\n";
	
	return $footer;
}


/**
 * Step 1 of installation - Welcome message
 */
function upgrade_welcome()
{
	global $lang;
	
	echo html_header();
	
	// Step title
	echo "<h2>" . $lang['upgrade_step1'] . "</h2>\n";
	
	// Step content
	echo "<div class='install_content'>" . $lang['upgrade_step1_details'] . "</div>\n";
	
	// Next button
	echo "<div class='next'><a href='upgrade.php?step=2'>" . $lang['install_next'] . "</a></div>\n";
	
	echo html_footer();
}

    
/**
 * Step 2 of upgrade - shows completion.
 */
function upgrade_complete()
{
	global $lang;
	
	echo html_header();
	
	// Step title
	echo "<h2>" . $lang['upgrade_step2'] . "</h2>\n";
	
	// Step content
	echo "<div class='install_content'>" . $lang['upgrade_step2_details'] . "</div>\n";
	
	// Next button
	echo "<div class='next'><a href='" . BASEURL . "'>" . $lang['upgrade_home'] . "</a></div>\n";
	
	echo html_footer();    
}

/**
 * Do Upgrade
 */
function do_upgrade($old_version)
{
	global $h;
	
	// can't upgrade from pre-1.0 versions of Hotaru.
	
	// 1.0 to 1.0.1
	if ($old_version == "1.0") {

		// Change "positive" to 10
		$sql = "UPDATE " . TABLE_POSTVOTES . " SET vote_rating = %d WHERE vote_rating = %s";
		$h->db->query($h->db->prepare($sql, 10, 'positive'));
		
		// Change "negative" to -10
		$sql = "UPDATE " . TABLE_POSTVOTES . " SET vote_rating = %d WHERE vote_rating = %s";
		$h->db->query($h->db->prepare($sql, -10, 'negative'));
		
		// Change "alert" to -999
		$sql = "UPDATE " . TABLE_POSTVOTES . " SET vote_rating = %d WHERE vote_rating = %s";
		$h->db->query($h->db->prepare($sql, -999, 'alert'));
		
		// Alter the PostVotes table so the vote rating is an INT
		$sql = "ALTER TABLE " . TABLE_POSTVOTES . " CHANGE vote_rating vote_rating smallint(11) NOT NULL DEFAULT %d";
		$h->db->query($h->db->prepare($sql, 0));
		
		// check there are default permissions present and add if necessary
		$sql = "SELECT miscdata_id FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
		$result = $h->db->get_var($h->db->prepare($sql, 'permissions'));
		if (!$result) {
			// Default permissions
			$perms['options']['can_access_admin'] = array('yes', 'no');
			$perms['can_access_admin']['admin'] = 'yes';
			$perms['can_access_admin']['supermod'] = 'yes';
			$perms['can_access_admin']['default'] = 'no';
			$perms = serialize($perms);
			
			$sql = "INSERT INTO " . TABLE_MISCDATA . " (miscdata_key, miscdata_value, miscdata_default) VALUES (%s, %s, %s)";
			$h->db->query($h->db->prepare($sql, 'permissions', $perms, $perms));
		}
		
		// check there are default user_settings present and add if necessary
		$sql = "SELECT miscdata_id FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
		$result = $h->db->get_var($h->db->prepare($sql, 'user_settings'));
		if (!$result) {
			// default settings
			$sql = "INSERT INTO " . TABLE_MISCDATA . " (miscdata_key, miscdata_value, miscdata_default) VALUES (%s, %s, %s)";
			$h->db->query($h->db->prepare($sql, 'user_settings', '', ''));
		}
		
		// update "old version" for next set of upgrades
		$old_version = "1.0.1";
	}


	// 1.0.1 to 1.0.2
	if ($old_version == "1.0.1") {
		
		// Add new user_lastactivity field
		$exists = $h->db->column_exists('users', 'user_lastvisit');
		if (!$exists) {
			// Alter the Users table to include user_lastvisit
			$sql = "ALTER TABLE " . TABLE_USERS . " ADD user_lastvisit TIMESTAMP NULL AFTER user_lastlogin";
			$h->db->query($h->db->prepare($sql));
		}
		
		// Add site announcement record
		$sql = "SELECT miscdata_id FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
		$result = $h->db->get_var($h->db->prepare($sql, 'site_announcement'));
		if (!$result) {
			// site announcement
			$sql = "INSERT INTO " . DB_PREFIX . $table_name . " (miscdata_key, miscdata_value, miscdata_default) VALUES (%s, %s, %s)";
			$h->db->query($h->db->prepare($sql, 'site_announcement', '', ''));
		}
		
		// update "old version" for next set of upgrades
		$old_version = "1.0.2";
	}


	// 1.0.2 to 1.0.3
	if ($old_version == "1.0.2") {
		// nothing to do...
		
		// update "old version" for next set of upgrades
		$old_version = "1.0.3";
	}


	// 1.0.3 to 1.0.4
	if ($old_version == "1.0.3") {
		
		// remove language pack option from settings
		$sql = "DELETE FROM " . TABLE_SETTINGS . " WHERE settings_name = %s";
		$h->db->query($h->db->prepare($sql, 'LANGUAGE_PACK'));
		
		// Drop temporary cvotes_temp table if it already exists
		$sql = 'DROP TABLE IF EXISTS `' . DB_PREFIX . 'cvotes_temp`;';
		$h->db->query($sql);
		
		// create a temp table to store old comment votes
		$sql = "CREATE TABLE `" . DB_PREFIX . "cvotes_temp` (
			`cvote_archived` enum('Y','N') NOT NULL DEFAULT 'N',
			`cvote_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
			`cvote_post_id` int(11) NOT NULL DEFAULT '0',
			`cvote_comment_id` int(11) NOT NULL DEFAULT '0',
			`cvote_user_id` int(11) NOT NULL DEFAULT '0',
			`cvote_user_ip` varchar(32) NOT NULL DEFAULT '0',
			`cvote_date` timestamp NOT NULL,
			`cvote_rating` smallint(11) NOT NULL DEFAULT '0',
			`cvote_reason` tinyint(3) NOT NULL DEFAULT 0,
			`cvote_updateby` int(20) NOT NULL DEFAULT 0
		) ENGINE=" . DB_ENGINE . " DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . " COMMENT='Comment Votes';";
		$h->db->query($sql);

		// get old cvote data
		$sql = "SELECT * FROM " . TABLE_COMMENTVOTES;
		$old_cvotes = $h->db->get_results($h->db->prepare($sql));
		if ($old_cvotes) {
			$columns    = "cvote_post_id, cvote_comment_id, cvote_user_id, cvote_user_ip, cvote_date, cvote_rating, cvote_reason, cvote_updateby";
			foreach ($old_cvotes as $cvote) {
				if ($cvote->cvote_rating == 'negative') { $rating = -10; } else { $rating = 10; }
				$sql = "INSERT INTO " . DB_PREFIX . "cvotes_temp (" . $columns . ") VALUES(%d, %d, %d, %s, %s, %d, %d, %d)";
				$h->db->query($h->db->prepare($sql, $cvote->cvote_post_id, $cvote->cvote_comment_id, $cvote->cvote_user_id, $cvote->cvote_user_ip, $cvote->cvote_date, $rating, $cvote->cvote_reason, $cvote->cvote_updateby));
			}
		}
		
		// drop old commentvotes table
		$h->db->query("DROP TABLE " . TABLE_COMMENTVOTES);
		
		// rename new table
		$h->db->query("RENAME TABLE " . DB_PREFIX . "cvotes_temp TO " . DB_PREFIX . "commentvotes");
		
		// add new comment_votes_down column to comments table
		$exists = $h->db->column_exists('comments', 'comment_votes_down');
		if (!$exists) {
			// Alter the Users table to include user_lastvisit
			$sql = "ALTER TABLE " . TABLE_COMMENTS . " ADD comment_votes_down smallint(11) NOT NULL DEFAULT '0' AFTER comment_votes";
			$h->db->query($h->db->prepare($sql));
		}
		
		// rename comment_votes column to comments_votes_up
		$exists = $h->db->column_exists('comments', 'comment_votes_up');
		if (!$exists) {
			// Alter the Users table to include user_lastvisit
			$sql = "ALTER TABLE " . TABLE_COMMENTS . " CHANGE comment_votes comment_votes_up smallint(11) NOT NULL DEFAULT '0'";
			$h->db->query($h->db->prepare($sql));
		}
		
		// move any negative comment vote counts to the down column
		$sql = "SELECT comment_id, comment_votes_up FROM " . TABLE_COMMENTS . " WHERE comment_votes_up < %d";
		$negatives = $h->db->get_results($h->db->prepare($sql, 0));
		if ($negatives) {
			foreach ($negatives as $neg) {
				$sql = "UPDATE " . TABLE_COMMENTS . " SET comment_votes_up = %d, comment_votes_down = %d WHERE comment_id = %d";
				$h->db->query($h->db->prepare($sql, 0, abs($neg->comment_votes_up), $neg->comment_id));
			}
		}

		// update "old version" for next set of upgrades
		$old_version = "1.0.4";
	}

	// 1.0.4 to 1.0.5
	if ($old_version == "1.0.4") {

		// remove true/false "Notes" from admin settings
		$sql = "UPDATE " . TABLE_SETTINGS . " SET settings_note = %s WHERE settings_note = %s";
		$h->db->query($h->db->prepare($sql, '', 'true/false'));
		
		// update "old version" for next set of upgrades
		$old_version = "1.0.5";
	}

	// 1.0.5 to 1.1
	if ($old_version == "1.0.5") { $old_version = "1.1"; } // update "old version" for next set of upgrades
	
	// 1.1 to 1.1.1
	if ($old_version == "1.1") { $old_version = "1.1.1"; } // update "old version" for next set of upgrades
	
	// 1.1 to 1.1.2
	if ($old_version == "1.1.1") { 

		// SMTP on
		$sql = "SELECT settings_name FROM " . TABLE_SETTINGS . " WHERE settings_name = %s";
		$result = $h->db->get_var($h->db->prepare($sql, 'SMTP_ON'));
		if(!$result) {
			$sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
			$h->db->query($h->db->prepare($sql, 'SMTP_ON', 'false', 'false', 'Email auth'));
		}

		// SMTP host
		$sql = "SELECT settings_name FROM " . TABLE_SETTINGS . " WHERE settings_name = %s";
		$result = $h->db->get_var($h->db->prepare($sql, 'SMTP_HOST'));
		if(!$result) {
			$sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
			$h->db->query($h->db->prepare($sql, 'SMTP_HOST', 'mail.example.com', 'mail.example.com', ''));
		}

		// SMTP port
		$sql = "SELECT settings_name FROM " . TABLE_SETTINGS . " WHERE settings_name = %s";
		$result = $h->db->get_var($h->db->prepare($sql, 'SMTP_PORT'));
		if(!$result) {
			$sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
			$h->db->query($h->db->prepare($sql, 'SMTP_PORT', '25', '25', ''));
		}

		// SMTP username
		$sql = "SELECT settings_name FROM " . TABLE_SETTINGS . " WHERE settings_name = %s";
		$result = $h->db->get_var($h->db->prepare($sql, 'SMTP_USERNAME'));
		if(!$result) {
			$sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
			$h->db->query($h->db->prepare($sql, 'SMTP_USERNAME', '', '', ''));
		}

		// SMTP password
		$sql = "SELECT settings_name FROM " . TABLE_SETTINGS . " WHERE settings_name = %s";
		$result = $h->db->get_var($h->db->prepare($sql, 'SMTP_PASSWORD'));
		if(!$result) {
			$sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
			$h->db->query($h->db->prepare($sql, 'SMTP_PASSWORD', '', '', ''));
		}

		// update "old version" for next set of upgrades
		$old_version = "1.1.2"; 
	}

	 // 1.1.2 to 1.1.3
	if ($old_version == "1.1.2") {

		// System Feedback
		$sql = "SELECT settings_name FROM " . TABLE_SETTINGS . " WHERE settings_name = %s";
		$result = $h->db->get_var($h->db->prepare($sql, 'SYS_FEEDBACK'));
		if(!$result) {
			$sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
			$h->db->query($h->db->prepare($sql, 'SYS_FEEDBACK', 'true', 'true', 'Send system report'));
		}

		// Remove ON from constant names
		$sql = "UPDATE " . TABLE_SETTINGS . " SET settings_name = %s WHERE settings_name = %s";
		$h->db->query($h->db->prepare($sql, 'DB_CACHE', 'DB_CACHE_ON'));
		
		$sql = "UPDATE " . TABLE_SETTINGS . " SET settings_name = %s WHERE settings_name = %s";
		$h->db->query($h->db->prepare($sql, 'RSS_CACHE', 'RSS_CACHE_ON'));
		
		$sql = "UPDATE " . TABLE_SETTINGS . " SET settings_name = %s WHERE settings_name = %s";
		$h->db->query($h->db->prepare($sql, 'CSS_JS_CACHE', 'CSS_JS_CACHE_ON'));
		
		$sql = "UPDATE " . TABLE_SETTINGS . " SET settings_name = %s WHERE settings_name = %s";
		$h->db->query($h->db->prepare($sql, 'HTML_CACHE', 'HTML_CACHE_ON'));
		
		$sql = "UPDATE " . TABLE_SETTINGS . " SET settings_name = %s WHERE settings_name = %s";
		$h->db->query($h->db->prepare($sql, 'SMTP', 'SMTP_ON'));
		
		// update "old version" for next set of upgrades
		$old_version = "1.1.3"; 
	}
	
	 // 1.1.3 to 1.1.4
	if ($old_version == "1.1.3") {
	
	// copy post_date column to post_pub_date
		$exists = $h->db->column_exists('posts', 'post_pub_date');
		if (!$exists) {
			// Create a post_pub_date column
			$sql = "ALTER TABLE " . TABLE_POSTS . " ADD post_pub_date timestamp NOT NULL AFTER post_date";
			$h->db->query($h->db->prepare($sql));
			
			// Copy post_date to post_pub_date
			$sql = "UPDATE " . TABLE_POSTS . " SET post_pub_date = post_date";
			$h->db->query($h->db->prepare($sql));
		}
		
		// update "old version" for next set of upgrades
		$old_version = "1.1.4"; 
	}

	 // 1.1.4 to 1.2
	if ($old_version == "1.1.4") {

		// check whether table exists first

		// create a Friends table
		$sql = "CREATE TABLE `" . DB_PREFIX . $table_name . "` (
				`follower_user_id` int(20) NOT NULL default '0',
				`following_user_id` int(20) NOT NULL default '0',
                                `friends_date` datetime NOT NULL default '0000-00-00 00:00:00',
                                `friends_updateby` datetime NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY (follower_user_id, following_user_id)
	       ) ENGINE=" . DB_ENGINE . " DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . " COMMENT='Friends';";
		echo $lang['install_step3_creating_table'] . ": '" . $table_name . "'...<br />\n";
		$db->query($sql);

		// update "old version" for next set of upgrades
		$old_version = "1.2.0";
	}

	// Update Hotaru version number to the database (referred to when upgrading)
	$sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_key = %s, miscdata_value = %s, miscdata_default = %s WHERE miscdata_key = %s";
	$h->db->query($h->db->prepare($sql, 'hotaru_version', $h->version, $h->version, 'hotaru_version'));
}

?>