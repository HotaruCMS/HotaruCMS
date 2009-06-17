<?php

// includes
require_once('../hotaru_settings.php');
global $db;
	
$result = create_tables();

$db->select(DB_NAME);
foreach ( $db->get_col("SHOW TABLES",0) as $table_name ) {
	$db->debug();
	$db->get_results("DESC $table_name");
}
$db->debug();

echo "<p>Installation complete.</p>";
echo "<p style='font-weight: bold; font-size: 1.2em;'><a href='" . baseurl . "'>Start using Hotaru!</a></p>";

function create_tables() {
	global $db;	
	$sql = 'DROP TABLE IF EXISTS `' . table_plugins . '`;';
	$db->query($sql);
		
	$sql = "CREATE TABLE `" . table_plugins . "` (
	  `plugin_id` int(11) NOT NULL auto_increment,
	  `plugin_enabled` tinyint(1) NOT NULL default '0',
	  `plugin_name` varchar(50) NOT NULL default '',
	  `plugin_desc` varchar(255) NOT NULL default '',
	  `plugin_folder` varchar(50) NOT NULL default '',
	  `plugin_version` varchar(20) NOT NULL default '0.0',
	  PRIMARY KEY  (`plugin_id`),
	  UNIQUE KEY `key` (`plugin_folder`)
	) TYPE = MyISAM;";
	echo 'Creating table: \'plugins\'...<br />';
	$db->query($sql);
}

?>