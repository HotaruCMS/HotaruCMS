<?php

// includes
require_once('../hotaru_header.php');
require_once(languages . 'installation/' . strtolower(sitelanguage) . '.php');

$step = $cage->get->getInt('step');		// Installation steps.

switch ($step) {
	case 1:
		installation_welcome();		// "Welcome to Hotaru CMS. Click "Next" to begin installation."
		break;
	case 2:
		database_setup();		// DB name, user, password, prefix...
		break;
	case 3:
		database_creation();	// Creates the database tables
		break;
	case 4:
		hotaru_settings();		// Names, paths, etc.
		break;
	case 5:
		register_admin();		// Username and password for Admin user...
		break;
	case 6:
		installation_complete();	// "Installation complete. Delete the "install" folder. Visit your site"
		break;
	default:
		installation_welcome();		// Anything other than step=2, 3, 4 or 5 will return user to the welcome page.
		break;		
}

exit;

function html_header() {
	global $lang;
	$header = "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 3.2//EN'>\n";
	$header .= "<HTML><HEAD>\n";
	$header .= "<TITLE>" . $lang['install_title'] . "</TITLE>\n";
	$header .= "<META HTTP-EQUIV='Content-Type' CONTENT='text'>\n";
	$header .= "<link rel='stylesheet' href='" . baseurl . "includes/YUI-CSS/reset-fonts-grids.css' type='text/css'>\n";
	$header .= "<link rel='stylesheet' type='text/css' href='" . baseurl . "install/install_style.css'>\n";
	$header .= "</HEAD>\n";
	$header .= "<BODY>\n";
	$header .= "<div id='doc' class='yui-t7 install'>\n";
	$header .= "<div id='hd' role='banner'><img align='left' src='" . baseurl . "images/hotaru.png' style='height:60px; width:69px;'><h1>" . $lang['install_title'] . "</h1></div>\n"; 
	$header .= "<div id='bd' role='main'>\n";
	$header .= "<div class='yui-g'>\n";
	return $header;
}

function html_footer() {
	global $lang;
	$footer = "<div class='clear'></div>\n"; // clear floats
	$footer .= "<div id='ft' role='contentinfo'><p>" . $lang['install_trouble'] . "</p></div>\n"; 
	$footer .= "</div>\n"; // close "yui-g" div
	$footer .= "</div>\n"; // close "main" div
	$footer .= "</div>\n"; // close "yui-t7 install" div
	$footer .= "</BODY>\n";
	$footer .= "</HTML>\n";
	return $footer;
}

function installation_welcome() {
	global $lang;
	echo html_header();
	echo "<h2>" . $lang['install_step1'] . "</h2>\n";
	echo "<div class='install_content'>" . $lang['install_step1_welcome'] . "</div>\n";
	echo "<div class='next'><a href='install.php?step=2'>" . $lang['install_next'] . "</a></div>\n";
	echo html_footer();
}

function database_setup() {
	global $lang;
	echo html_header();
	echo "<h2>" . $lang['install_step2'] . "</h2>\n";
	echo "<div class='install_content'>" . $lang['install_step2_instructions'] . ":</div>\n";
	echo "<ol class='install_content'>\n";
	echo "<li>" . $lang['install_step2_instructions1'] . "</li>\n";
	echo "<li>" . $lang['install_step2_instructions2'] . "</li>\n";
	echo "<li>" . $lang['install_step2_instructions3'] . "</li>\n";
	echo "</ol>\n";
	echo "<div class='install_content'><span style='color: red;'>" . $lang['install_step2_warning'] . "</span>: " . $lang['install_step2_warning_note'] . "</div>\n";
	echo "<div class='back'><a href='install.php?step=1'>" . $lang['install_back'] . "</a></div><div class='next'><a href='install.php?step=3'>" . $lang['install_next'] . "</a></div>\n";
	echo html_footer();
}

function database_creation() {
	global $db, $lang;
	echo html_header();
	echo "<h2>" . $lang['install_step3'] . "</h2>\n";
	
	$skip = 0;
	$tables = array('settings', 'plugins');

	foreach($tables as $table_name) {
		create_table($table_name);
	} 
	
	echo "<div class='install_content'>" . $lang['install_step3_success'] . "</div>\n";
	echo "<div class='back'><a href='install.php?step=2'>" . $lang['install_back'] . "</a></div><div class='next'><a href='install.php?step=4'>" . $lang['install_next'] . "</a></div>\n";
	echo html_footer();	
}

function create_table($table_name) {
	global $db, $lang;	

	$sql = 'DROP TABLE IF EXISTS `' . db_prefix . $table_name . '`;';
	$db->query($sql);

	if($table_name == "settings") {
		$sql = "CREATE TABLE `" . db_prefix . $table_name . "` (
		  `settings_id` int(20) NOT NULL auto_increment,
		  `settings_name` varchar(64) NOT NULL default '',
		  `settings_value` text NOT NULL default '',
		  PRIMARY KEY  (`settings_id`),
		  UNIQUE KEY `key` (`settings_name`)
		) TYPE = MyISAM;";
		echo $lang['install_step3_creating_table'] . ": '" . $table_name . "'...<br />\n";
		$db->query($sql);
	}
		
	if($table_name == "plugins") {
		$sql = "CREATE TABLE `" . db_prefix . $table_name . "` (
		  `plugin_id` int(11) NOT NULL auto_increment,
		  `plugin_enabled` tinyint(1) NOT NULL default '0',
		  `plugin_name` varchar(64) NOT NULL default '',
		  `plugin_desc` varchar(255) NOT NULL default '',
		  `plugin_folder` varchar(64) NOT NULL default '',
		  `plugin_version` varchar(32) NOT NULL default '0.0',
		  PRIMARY KEY  (`plugin_id`),
		  UNIQUE KEY `key` (`plugin_folder`)
		) TYPE = MyISAM;";
		echo $lang['install_step3_creating_table'] . ": '" . $table_name . "'...<br />\n";
		$db->query($sql);
	}
}

function hotaru_settings() {
	global $lang;
	echo html_header();
	echo "<h2>" . $lang['install_step4'] . "</h2>\n";
	echo "<div class='install_content'>" . $lang['install_step4_instructions'] . ":</div>\n";
	echo "<ol class='install_content'>\n";
	echo "<li>" . $lang['install_step4_instructions1'] . "</li>\n";
	echo "<li>" . $lang['install_step4_instructions2'] . "</li>\n";
	echo "<li>" . $lang['install_step4_instructions3'] . "</li>\n";
	echo "<li>" . $lang['install_step4_instructions4'] . "</li>\n";
	echo "</ol>\n";
	echo "<div class='back'><a href='install.php?step=3'>" . $lang['install_back'] . "</a></div><div class='next'><a href='install.php?step=5'>" . $lang['install_next'] . "</a></div>\n";
	echo html_footer();
}

function register_admin() {
	global $lang;
	echo html_header();
	echo "<h2>" . $lang['install_step5'] . "</h2>\n";
	echo "<div class='install_content'>" . $lang['install_step5_instructions'] . ":</div>\n";
	echo "<div class='back'><a href='install.php?step=4'>" . $lang['install_back'] . "</a></div><div class='next'><a href='install.php?step=6'>" . $lang['install_next'] . "</a></div>\n";
	echo html_footer();
}
	
function installation_complete() {
	global $lang;
	echo html_header();	
	echo "<h2>" . $lang['install_step6'] . "</h2>\n";
	echo "<div class='install_content'>" . $lang['install_step6_installation_complete'] . "</div>\n";
	echo "<div class='back'><a href='install.php?step=5'>" . $lang['install_back'] . "</a></div><div class='next'><a href='" . baseurl . "'>" . $lang['install_home'] . " " . sitename . "</a></div>\n";
	echo html_footer();	
}

?>