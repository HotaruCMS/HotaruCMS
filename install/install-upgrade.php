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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */


$h = new \Libs\Hotaru('start'); // must come before language inclusion

global $lang;

$cage = init_inspekt_cage();
$step = $cage->get->getInt('step');        // Installation steps.
$show_next = true;    

$db = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
$db->show_errors = false;
$database_exists = $db->quick_connect(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);	

if (!$database_exists) {
    $h->messages[$lang['install_step1_no_db_exists_failure']] = 'red';
    $old_version = '';
    $show_next = false;
    $step = 1;  // rest to step if db not available
} else {        
    $table_exists = $db->table_exists('miscdata');
    if (!$table_exists) {
        $h->messages[$lang['install_step1_no_table_exists_failure']] = 'red';
        $old_version = '';
        $show_next = false;
        $step = 1;  // rest to step if db not available
    } else {
        $h = new \Libs\Hotaru();  // replace start one with full one
        $sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
        $old_version = $h->db->get_var($h->db->prepare($sql, "hotaru_version"));
    }
}

switch ($step) {
	case 0:		
		break;
	case 1:
		upgrade_check($h, $old_version, $show_next);
		break;
	case 2:
		do_upgrade($h, $old_version);
		upgrade_database($h);    // Delete "install" folder. Visit your site"
		break;
	case 3:
		upgrade_step_3($h);
		break;
        case 4:
		upgrade_finish($h);
		break;
	default:		
		upgrade_welcome();
		break;
}

exit;



/**
 * Step 1 of upgrade - checks existing version available and confirms details
 */
function upgrade_check($h, $old_version, $show_next)         
{               
        // delete existing cache
        $h->deleteFiles(CACHE . 'db_cache');
        $h->deleteFiles(CACHE . 'css_js_cache');
        $h->deleteFiles(CACHE . 'rss_cache');
        $h->deleteFiles(CACHE . 'lang_cache');
        $h->deleteFiles(CACHE . 'html_cache');

        template($h, 'upgrade/upgrade_step_1.php', array(
            'old_version' => $old_version,
            'show_next' => $show_next
            ));	
}

    
/**
 * Step 2 of upgrade
 */
function upgrade_database($h)
{
        template($h, 'upgrade/upgrade_step_2.php');          
}



/**
 * Do Upgrade
 */
function do_upgrade($h, $old_version)
{
	// can't upgrade from pre-1.0 versions of Hotaru.
	
        // 1.0.0 to 1.3.0 updates all removed (check github for old code)

	 // 1.3.0 to 1.4.0
	if ($old_version == "1.3.0") {

	    // Drop token_id column from the tokens table
	    if ($h->db->column_exists('tokens', 'token_id')) {
		    $h->db->query("ALTER TABLE " . DB_PREFIX . "tokens DROP token_id");
	    }

		// update "old version" for next set of upgrades
		$old_version = "1.4.0";
	}

	 // 1.4.0 to 1.4.1
	if ($old_version == "1.4.0") {
		// update "old version" for next set of upgrades
		$old_version = "1.4.1";
	}

	 // 1.4.1 to 1.4.2
	if ($old_version == "1.4.1") {

		// Change post_title column from `post_title` varchar(255) NULL, to `post_title` text NULL,
		$exists = $h->db->column_exists('posts', 'post_title');
		if ($exists) {
			$sql = "ALTER TABLE " . TABLE_POSTS . " MODIFY post_title text NULL";
			$h->db->query($h->db->prepare($sql));
		}

		// remove multi-site option from settings 
		$sql = "DELETE FROM " . TABLE_SETTINGS . " WHERE settings_name = %s"; 
		$h->db->query($h->db->prepare($sql, 'MULTI_SITE'));

		//tables to remove site_id from:
		$tables = array(
			'blocked'=>'blocked','categories'=>'category',
			'comments'=>'comment', 'plugins'=>'plugin',
			'miscdata'=>'miscdata','pluginsettings'=>'pluginsetting',
			'posts'=>'post','settings'=>'settings',
			'tags'=>'tag', 'users'=>'user', 'widgets'=>'widget',
		);

		// Remove site_id columns
		foreach ($tables as $table => $column)
		{
			if ($exists = $h->db->column_exists($table, $column . '_siteid')) {
				// Remove column
				$sql = "ALTER TABLE " . DB_PREFIX . $table . " DROP " . $column . "_siteid";
				$h->db->query($sql);
			}

			// Remove site_id indices
			$sql = "SHOW INDEX FROM `" . DB_PREFIX . $table . "` WHERE KEY_NAME = '" . $column . "_siteid'";
			$result = $h->db->query($sql);
			if ($result) {
				$sql = "DROP INDEX " . $column . "_siteid ON " . DB_PREFIX . $table;
				$h->db->query($sql);
			}
		}

		// Drop unique site_id keys
		$sql = "ALTER TABLE `" . TABLE_CATEGORIES . "` DROP INDEX `key`";
		$h->db->query($sql);
		$sql = "ALTER TABLE `" . TABLE_CATEGORIES . "` ADD UNIQUE KEY `key` (`category_name`)";
		$h->db->query($sql);
		
		$sql = "ALTER TABLE `" . TABLE_PLUGINS . "` DROP INDEX `key`";
		$h->db->query($sql);
		$sql = "ALTER TABLE `" . TABLE_PLUGINS . "` ADD UNIQUE KEY `key` (`plugin_folder`)";
		$h->db->query($sql);
		
		$sql = "ALTER TABLE `" . TABLE_SETTINGS . "` DROP INDEX `key`";
		$h->db->query($sql);
		$sql = "ALTER TABLE `" . TABLE_SETTINGS . "` ADD UNIQUE KEY `key` (`settings_name`)";
		$h->db->query($sql);
		
		$sql = "ALTER TABLE `" . TABLE_TAGS . "` DROP INDEX `key`";
		$h->db->query($sql);
		$sql = "ALTER TABLE `" . TABLE_TAGS . "` ADD UNIQUE KEY `key` (`tags_post_id`, `tags_word`)";
		$h->db->query($sql);
		
		$sql = "ALTER TABLE `" . TABLE_USERS . "` DROP INDEX `key`";
		$h->db->query($sql);
		$sql = "ALTER TABLE `" . TABLE_USERS . "` ADD UNIQUE KEY `key` (`user_username`)";
		$h->db->query($sql);

		// Drop Site table
		$sql = 'DROP TABLE IF EXISTS `' . DB_PREFIX . 'site`;';
		$h->db->query($sql);

		// Drop Relates table
		$sql = 'DROP TABLE IF EXISTS `' . DB_PREFIX . 'relates`;';
		$h->db->query($sql);

		// remove the "1" cache folder if permissions allow, otherwise error messages supressed.
		if (is_dir(CACHE . '1'))
		{
			if (is_dir(CACHE . '1/db_cache')) {
				$h->deleteFiles(CACHE . '1/db_cache');
				@rmdir(CACHE . '1/db_cache');
			}

			if (is_dir(CACHE . '1/css_js_cache')) {
				$h->deleteFiles(CACHE . '1/css_js_cache');
				@rmdir(CACHE . '1/css_js_cache');
			}

			if (is_dir(CACHE . '1/rss_cache')) {
				$h->deleteFiles(CACHE . '1/rss_cache');
				@rmdir(CACHE . '1/rss_cache');
			}

			if (is_dir(CACHE . '1/lang_cache')) {
				$h->deleteFiles(CACHE . '1/lang_cache');
				@rmdir(CACHE . '1/lang_cache');
			}

			if (is_dir(CACHE . '1/html_cache')) {
				$h->deleteFiles(CACHE . '1/html_cache');
				@rmdir(CACHE . '1/html_cache');
			}

			if (is_dir(CACHE . '1/debug_logs')) {
				$h->deleteFiles(CACHE . '1/debug_logs');
				@rmdir(CACHE . '1/debug_logs');
			}

			if (file_exists(CACHE . '1/smartloader_cache.php')) {
				@unlink(CACHE . '1/smartloader_cache.php');
			}

			@rmdir(CACHE . '1');
		}

		// update "old version" for next set of upgrades
		$old_version = "1.4.2";

	}
        
        // 1.4.2 to 1.5.0
	if ($old_version == "1.4.2") {                            

                $h->messages['Updated from 1.4.2'] = 'green';

                // update "old version" for next set of upgrades
                $old_version = "1.5.0";
        }
        
        // 1.5.0 to 1.5.1
	if (version_compare($old_version, "1.4.2") < '<=') { // should set an upper limit here later
                         
                // Need to cover all of the 1.5.0.RCx verson as well
                // Add a few new settings
		$exists = $h->db->column_exists('settings', 'settings_id');
		if ($exists) {
                    $newSettings = array('FTP_SITE', 'FTP_USERNAME', 'FTP_PASSWORD');
                    foreach($newSettings as $setting) {                        
                        $sql = "SELECT settings_name FROM " . TABLE_SETTINGS . " WHERE settings_name = %s";
                        $result = $h->db->get_var($h->db->prepare($sql, $setting));
                        
                        if(!$result) {
                            $sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_value, settings_default, settings_note, settings_show) VALUES(%s, %s, %s, %s, %s)";                        
                            $h->db->query($h->db->prepare($sql, $setting, ' ', ' ', ' ', 1));
                        }
                        
                    }						
		}
                
                // drop joint primary key to postvotes table if exists
                // should not be there
                $sql = "SHOW INDEX FROM " . TABLE_POSTVOTES . " WHERE KEY_NAME = %s";
		$result = $h->db->get_row($h->db->prepare($sql, 'PRIMARY'));
                if ($result) {                   
			$sql = "ALTER TABLE " . TABLE_POSTVOTES . " DROP PRIMARY KEY";
			$h->db->query($h->db->prepare($sql));
		}                
                
                $h->messages['Updated from 1.5.0, 1.5.1'] = 'green';
                // update "old version" for next set of upgrades
		$old_version = "1.5.1";
        }
        
        // 1.5.1 to 1.5.2
        if (version_compare($old_version, "1.5.2", '<=') > 0) { // this will also cover 1.5.2.b1 etc but need an upper limit
                        
                $sql = "SHOW INDEX FROM `" . TABLE_POSTS . "` WHERE KEY_NAME = %s";
		$result = $h->db->get_row($h->db->prepare($sql, 'post_author'));                
                if (!$result) {
                    $sql = "ALTER TABLE `" . TABLE_POSTS . "` ADD INDEX (`post_author`)";
                    $h->db->query($sql);
                }
                
                $sql = "SHOW INDEX FROM " . TABLE_COMMENTS . " WHERE KEY_NAME = %s";
		$result = $h->db->get_row($h->db->prepare($sql, 'comment_user_id'));
                if (!$result) {
                    $sql = "ALTER TABLE `" . TABLE_COMMENTS . "` ADD INDEX (`comment_user_id`)";
                    $h->db->query($sql);
                }
                
                $sql = "SHOW INDEX FROM " . TABLE_COMMENTS . " WHERE KEY_NAME = %s";
		$result = $h->db->get_row($h->db->prepare($sql, 'comment_parent'));
                if (!$result) {
                    $sql = "ALTER TABLE `" . TABLE_COMMENTS . "` ADD INDEX (`comment_parent`)";
                    $h->db->query($sql);
                }   
                
                $sql = "SHOW INDEX FROM " . TABLE_MESSAGING . " WHERE KEY_NAME = %s";
		$result = $h->db->get_row($h->db->prepare($sql, 'message_to'));
                if (!$result) {
                    $sql = "ALTER TABLE `" . TABLE_MESSAGING . "` ADD INDEX (`message_to`)";
                    $h->db->query($sql);
                }   
                
                // change NULL value setings in settings table
                $sql = "ALTER TABLE " . TABLE_SETTINGS . " MODIFY `settings_name` varchar(64) NOT NULL";
                $h->db->query($sql);
                
                $sql = "ALTER TABLE " . TABLE_SETTINGS . " MODIFY `settings_value` text NULL";
                $h->db->query($sql);
                
                $sql = "ALTER TABLE " . TABLE_SETTINGS . " MODIFY `settings_default` text NULL";
                $h->db->query($sql);
                
                $sql = "ALTER TABLE " . TABLE_SETTINGS . " MODIFY `settings_note` text NULL";
                $h->db->query($sql);
                
                // should we hash the settings table on this version
                
                // should we urldecode the cats and save them back
                
                $h->messages['Updated from 1.5.2.b/rc'] = 'green';
                // update "old version" for next set of upgrades
		$old_version = "1.5.2";
        }

        
        // 1.5.2 to 1.6.0
        if (version_compare($old_version, "1.5.2", '<=') > 0) { // this will also cover 1.5.2.b1 etc but need an upper limit
        
                // Add a few new settings
		$exists = $h->db->column_exists('settings', 'settings_id');
		if ($exists) {
                    $newSettings = array('REST_API');   // add more to array as requird
                    foreach($newSettings as $setting) {                        
                        $sql = "SELECT settings_name FROM " . TABLE_SETTINGS . " WHERE settings_name = %s";
                        $result = $h->db->get_var($h->db->prepare($sql, $setting));
                        
                        if(!$result) {
                            $sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_value, settings_default, settings_note, settings_show) VALUES(%s, %s, %s, %s, %s)";                        
                            $h->db->query($h->db->prepare($sql, $setting, 'false', 'false', ' ', 1));                            
                        }
                        
                    }						
		}
                
                $h->messages['Updated from 1.5.2'] = 'green';
                // update "old version" for next set of upgrades
		$old_version = "1.6.0";
            
        }
        
        // 1.6.0 to 1.6.6
        if (version_compare($old_version, "1.6.0", '<=') > 0)  // this will also cover other versions in between but need an upper limit
        {
                // Add columns to plugins table for resources from forum to plugin into
		$exists = $h->db->column_exists('plugins', 'plugin_resourceId');
		if (!$exists) {
                    $sql = "ALTER TABLE " . TABLE_PLUGINS . " ADD Column `plugin_resourceId` int(11) NOT NULL DEFAULT 0";
                    $h->db->query($sql);
                }
                
                $exists = $h->db->column_exists('plugins', 'plugin_resourceVersionId');
		if (!$exists) {
                    $sql = "ALTER TABLE " . TABLE_PLUGINS . " ADD Column `plugin_resourceVersionId` int(11) NOT NULL DEFAULT 0";
                    $h->db->query($sql);
                }
                
                $exists = $h->db->column_exists('plugins', 'plugin_rating');
		if (!$exists) {
                    $sql = "ALTER TABLE " . TABLE_PLUGINS . " ADD Column `plugin_rating` varchar(8) NOT NULL DEFAULT '0.0'";
                    $h->db->query($sql);
                }
                
                // Add a few new settings
		$exists = $h->db->column_exists('settings', 'settings_id');
		if ($exists) {
                    $newSettings = array('FORUM_USERNAME', 'FORUM_PASSWORD');   // add more to array as requird
                    foreach($newSettings as $setting) {                        
                        $sql = "SELECT settings_name FROM " . TABLE_SETTINGS . " WHERE settings_name = %s";
                        $result = $h->db->get_var($h->db->prepare($sql, $setting));
                        
                        if(!$result) {
                            $sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_value, settings_default, settings_note, settings_show) VALUES(%s, %s, %s, %s, %s)";                        
                            $h->db->query($h->db->prepare($sql, $setting, '', '', 'Need for auto updates', 1));                            
                        }
                        
                    }						
		}
                
                $h->messages['Updated from 1.6.*'] = 'green';
                // update "old version" for next set of upgrades
        }
        
        // 1.6.6 to 1.7.0
        if (version_compare($old_version, "1.7.0.beta1", '<=') > 0)  // this will also cover other versions in between but need an upper limit
        {
                $sql = "SHOW INDEX FROM " . TABLE_POSTS . " WHERE Key_name = %s";
                $result = $h->db->get_row($h->db->prepare($sql, 'post_category'));
                if (!$result) {
                    $sql = "ALTER TABLE `" . TABLE_POSTS . "` ADD INDEX (`post_category`)";
                    $h->db->query($sql);
                }
                
                // delete out any surplus indices from Post table
                $sql = "SHOW INDEX FROM " . TABLE_POSTS . " WHERE KEY_NAME like %s";
		$result = $h->db->get_results($h->db->prepare($sql, 'post_category_%'));
                if ($result) {
                    foreach ($result as $item) {
                        $sql = "DROP INDEX `" . $item->Key_name . "` ON " . TABLE_POSTS;
                        $h->db->query($sql);
                    }
                }
                
                // delete out any surplus indices from Messaging table
                $sql = "SHOW INDEX FROM " . TABLE_MESSAGING . " WHERE KEY_NAME like %s";
		$result = $h->db->get_results($h->db->prepare($sql, 'message_to_%'));
                if ($result) {
                    foreach ($result as $item) {
                        $sql = "DROP INDEX `" . $item->Key_name . "` ON " . TABLE_MESSAGING;
                        $h->db->query($sql);
                    }
                } 
                
                // delete out any surplus indices from Comments table
                $sql = "SHOW INDEX FROM " . TABLE_COMMENTS . " WHERE KEY_NAME like %s";
                $result = $h->db->get_results($h->db->prepare($sql, 'comment_user_id_%'));
                if ($result) {
                    foreach ($result as $item) {
                        $sql = "DROP INDEX `" . $item->Key_name . "` ON " . TABLE_COMMENTS;
                        $h->db->query($sql);
                    }
                }
                
                // delete out any surplus indices from Comments table
                $sql = "SHOW INDEX FROM " . TABLE_COMMENTS . " WHERE KEY_NAME like %s";
                $result = $h->db->get_results($h->db->prepare($sql, 'comment_parent_%'));
                if ($result) {
                    foreach ($result as $item) {
                        $sql = "DROP INDEX `" . $item->Key_name . "` ON " . TABLE_COMMENTS;
                        $h->db->query($sql);
                    }
                }
                
                
                // Add userlogin table
                $table_name = "userlogin";
                $exists = $h->db->table_exists($table_name);
                if (!$exists) {
                    $sql = "CREATE TABLE `" . DB_PREFIX . $table_name . "` (
                            `user_id` int(20) NOT NULL,
                            `login_provider` varchar(128) NULL,
                            `provider_key` varchar(128) NULL,
                            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            INDEX  (`user_id`)
                    ) ENGINE=" . DB_ENGINE_INNODB . " DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . " COMMENT='3rd Party UserLogin Providers';";
                    $h->db->query($sql); 
                }
                
                // Add user_claim table
                $table_name = "userclaim";
                $exists = $h->db->table_exists($table_name);
                if (!$exists) {
                    $sql = "CREATE TABLE `" . DB_PREFIX . $table_name . "` (
                            `claim_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                            `user_id` int(20) NOT NULL,
                            `claim_type` TEXT NULL,
                            `claim_value` TEXT NULL,
                            INDEX  (`user_id`)
                    ) ENGINE=" . DB_ENGINE_INNODB . " DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . " COMMENT='UserClaim for login';";
                    $h->db->query($sql); 
                }
                
                // Add or change type in Posts table for post_img
                $exists = $h->db->column_exists('posts', 'post_img');
		if (!$exists) {
                    $sql = "ALTER TABLE " . TABLE_POSTS . " ADD Column `post_img` varchar(255) NULL";
                    $h->db->query($sql);
                } else {                    
                    // make sure it is a varchar not a text field
                    $sql = "ALTER TABLE " . TABLE_POSTS . " MODIFY `post_img` varchar(255) NULL";
                    $h->db->query($sql);
                }
                
                // Add column to POSTS table for comment_count
                $exists = $h->db->column_exists('posts', 'post_comments_count');
		if (!$exists) {
                    $sql = "ALTER TABLE " . TABLE_POSTS . " ADD Column `post_comments_count` smallint(11) NOT NULL DEFAULT '0'";
                    $h->db->query($sql);
                } 
                
                // Add column to SETTINGS table for setting_type
                $exists = $h->db->column_exists('settings', 'settings_type');
		if (!$exists) {
                    $sql = "ALTER TABLE " . TABLE_SETTINGS . " ADD Column `settings_type` varchar(32) NULL";
                    $h->db->query($sql);
                }   
                
                // Add column to SETTINGS table for setting_subType
                $exists = $h->db->column_exists('settings', 'settings_subType');
		if (!$exists) {
                    $sql = "ALTER TABLE " . TABLE_SETTINGS . " ADD Column `settings_subType` varchar(32) NULL";
                    $h->db->query($sql);
                } 
                
                // Add column to USERS table for `user_is_locked_out`
                $exists = $h->db->column_exists('users', 'user_is_locked_out');
		if (!$exists) {
                    $sql = "ALTER TABLE " . TABLE_USERS . " ADD Column `user_is_locked_out` tinyint(1) NOT NULL DEFAULT 0 AFTER `user_email_conf`";
                    $h->db->query($sql);
                }
                
                // Add column to USERS table for `user_access_failed_count`
                $exists = $h->db->column_exists('users', 'user_access_failed_count');
		if (!$exists) {
                    $sql = "ALTER TABLE " . TABLE_USERS . " ADD Column `user_access_failed_count` tinyint(1) NOT NULL DEFAULT 0";
                    $h->db->query($sql);
                }
                
                // Add column to USERS table for `user_last_password_changed_date`
                $exists = $h->db->column_exists('users', 'user_last_password_changed_date');
		if (!$exists) {
                    $sql = "ALTER TABLE " . TABLE_USERS . " ADD Column `user_last_password_changed_date` timestamp NULL";
                    $h->db->query($sql);
                }
                
                // Add column to USERS table for `user_lockout_date`
                $exists = $h->db->column_exists('users', 'user_lockout_date');
		if (!$exists) {
                    $sql = "ALTER TABLE " . TABLE_USERS . " ADD Column `user_lockout_date` timestamp NULL";
                    $h->db->query($sql);
                }
                
                // Add column to USERS table for `password_version`. set default as 1 to populate all current data as version 1
                $exists = $h->db->column_exists('users', 'password_version');
		if (!$exists) {
                    $sql = "ALTER TABLE " . TABLE_USERS . " ADD Column `password_version` tinyint(1) NOT NULL DEFAULT 1  AFTER `user_email_conf`";
                    $h->db->query($sql);
                }
                
                // modify default value to 2 for new paswords from now on
                $exists = $h->db->column_exists('users', 'password_version');
		if ($exists) {
                    $sql = "ALTER TABLE " . TABLE_USERS . " MODIFY Column `password_version` tinyint(1) NOT NULL DEFAULT 2";
                    $h->db->query($sql);
                }
                
                // Add a few new settings
		$exists = $h->db->column_exists('settings', 'settings_id');
		if ($exists) {
                    $newSettings = array('JQUERY_PATH', 'BOOTSTRAP_PATH');   // add more to array as requird
                    foreach($newSettings as $setting) {                        
                        $sql = "SELECT settings_name FROM " . TABLE_SETTINGS . " WHERE settings_name = %s";
                        $result = $h->db->get_var($h->db->prepare($sql, $setting));
                        
                        if(!$result) {
                            $sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_type, settings_subType, settings_value, settings_default, settings_note, settings_show) VALUES(%s, %s, %s, %s, %s, %s, %s)";                        
                            $h->db->query($h->db->prepare($sql, $setting, 'Perf', 'Files', 'local', 'local', 'Local/CDN', 1));                            
                        }
                    }
                    
                    $newSettings = array('MINIFY_CSS', 'MINIFY_JS');   // add more to array as requird
                    foreach($newSettings as $setting) {                        
                        $sql = "SELECT settings_name FROM " . TABLE_SETTINGS . " WHERE settings_name = %s";
                        $result = $h->db->get_var($h->db->prepare($sql, $setting));
                        
                        if(!$result) {
                            $sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_type, settings_subType, settings_value, settings_default, settings_note, settings_show) VALUES(%s, %s, %s, %s, %s, %s, %s)";                        
                            $h->db->query($h->db->prepare($sql, $setting, 'Perf', 'Scripts', 'false', 'false', '', 1));                            
                        }
                    }	
		}
                
                $sql = "SHOW INDEX FROM " . TABLE_COMMENTS . " WHERE KEY_NAME = %s";
		$result = $h->db->get_row($h->db->prepare($sql, 'comment_post_id'));
                if (!$result) {
                    $sql = "ALTER TABLE `" . TABLE_COMMENTS . "` ADD INDEX (`comment_post_id`)";
                    $h->db->query($sql);
                }  
                
                // Change all tabels to have their updatedts named as just updated_at
                $tablesChangeNameUpdatedCol = array('blocked' => 'blocked', 'categories' => 'category', 'comments' => 'comment', 'commentvotes' => 'cvote', 'friends' => 'friends', 'posts' => 'post', 'messaging' => 'message', 'miscdata' => 'miscdata', 'plugins' => 'plugin', 'pluginhooks' => 'plugin', 'pluginsettings' => 'plugin', 'postmeta' => 'postmeta', 'postvotes' => 'vote', 'settings' => 'settings', 'tags' => 'tags', 'tempdata' => 'tempdata', 'users' => 'user', 'usermeta' => 'usermeta', 'useractivity' => 'useract', 'widgets' => 'widget');
                foreach ($tablesChangeNameUpdatedCol as $table => $col) {
                    $newCol = $col . '_updatedts';
                    $exists = $h->db->column_exists($table, 'updated_at');
                    if ($exists) {                                           
                        $sql = "ALTER TABLE " . DB_PREFIX . $table . " CHANGE `updated_at` `" . $newCol . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
                        $h->db->query($sql);
                    }
                }
                
                $sql = "UPDATE " . TABLE_SETTINGS . " SET settings_type = 'Mail'  WHERE settings_name like 'SMTP%'";
                $h->db->query($sql);
                
                $sql = "UPDATE " . TABLE_SETTINGS . " SET settings_type = 'Security'  WHERE settings_name like 'FTP%' OR settings_name like 'FORUM%'";
                $h->db->query($sql);
                
                $sql = "UPDATE " . TABLE_SETTINGS . " SET settings_type = 'Perf'  WHERE settings_name like '%CACHE%'";
                $h->db->query($sql);
                
                // REMOVE TOKENS TABLE
                $exists = $h->db->table_exists('tokens');
		if ($exists) {
                    $sql = "DROP TABLE " . DB_PREFIX . 'tokens';
                    $h->db->query($sql);
                }

                $h->messages['Updated from 1.6.6'] = 'green';
            }
        
        /*
         * 
         * Update Hotaru version number to the database (referred to when upgrading)
         * This is always the final step of the upgrade
         * 
         */ 
	$sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_key = %s, miscdata_value = %s, miscdata_default = %s WHERE miscdata_key = %s";
	$h->db->query($h->db->prepare($sql, 'hotaru_version', $h->version, $h->version, 'hotaru_version'));
}
