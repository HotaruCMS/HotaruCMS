<?php
/**
 * Functions for Admin pages, e.g. settings, maintenance, blocked list...
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
class AdminPages
{
	 /**
	 * Admin Pages
	 */
	public function pages($h, $page = 'admin_login')
	{
		$h->vars['admin_sidebar_layout'] = 'vertical';
		$h->sidebars = true;
                
		$h->pluginHook('admin_pages');
                                
		switch ($page) {
			case "admin_login":
				$h->sidebars = false;
				$h->adminLoginLogout('login');
				break;
			case "admin_logout":
				$h->adminLoginLogout('logout');
				break;
                        case "admin_news":
				echo $h->adminNews(10, 3, 300);
				die();
			case "admin_account":
				$h->vars['admin_account'] = $this->adminAccount($h);
				break;
			case "settings":
				$h->vars['admin_settings'] = $this->settings($h);
				break;
			case "maintenance":
				$this->maintenanceAction($h);
				$h->vars['admin_plugin_settings'] = $this->listPluginSettings($h);
				$h->vars['admin_plugin_tables'] = $this->listDbTables($h, true);
				break;
			case "blocked":
				$h->vars['admin_blocked_list'] = $this->blocked($h);
				break;
                        case "pages_management":
                                $h->vars['admin_pages_array'] = $this->getPages($h);
				break;
			case "plugin_management":				
				$h->vars['admin_sidebar_layout'] = 'horizontal';
				$this->adminPlugins($h);
				break;
			case "plugin_search":				
				$h->vars['admin_sidebar_layout'] = 'horizontal';
				//$this->adminPluginSearch($h);				
				break;
			case "plugin_settings":
				$h->vars['settings_plugin'] = $h->cage->get->testAlnumLines('plugin'); // get plugin name from url
				if (!$h->vars['settings_plugin']) { 
					$h->vars['settings_plugin'] = $h->cage->post->testAlnumLines('plugin');  // get plugin name from form
				}
				$h->vars['plugin_settings_csrf_error'] = '';
				if ($h->cage->post->testAlpha('submitted') == 'true') { 
					$h->vars['plugin_settings_csrf_error'] = (!$h->csrf()) ? true : false;
				}
				$alt_template = $h->cage->get->testPage('alt_template');
				if ($alt_template) { $h->template($alt_template, $h->vars['settings_plugin']); exit; }
				break;
			case "theme_settings":
				$h->vars['settings_theme'] = $h->cage->get->testAlnumLines('theme'); // get plugin name from url
				if (!$h->vars['settings_theme']) { 
					$h->vars['settings_theme'] = $h->cage->post->testAlnumLines('theme');  // get plugin name from form
				}
				$h->vars['theme_settings_csrf_error'] = '';
				if ($h->cage->post->testAlpha('submitted') == 'true') { 
					$h->vars['theme_settings_csrf_error'] = (!$h->csrf()) ? true : false;
				}
				break;
			default:
				// we need this because it's not specified in the url:
				$h->pageName = 'admin_home';
				break;
		}
		
		// Display the main theme's index.php template
		$h->template('admin_index');
	}
	
	
	/* *************************************************************
	*
	*  ACCOUNT PAGE
	*
	* *********************************************************** */
	
	
	/**
	 * Call the updateAccount method in UserAuth
	 */    
	public function adminAccount($h)
	{
		return $h->currentUser->updateAccount($h);
	}
	
	
	/* *************************************************************
	*
	*  SETTINGS PAGE
	*
	* *********************************************************** */
	
	
	/**
	 * Process the settings form
	 */    
	public function settings($h)
	{
		$loaded_settings = $this->getAllAdminSettings($h->db);    // get all admin settings from the database
		
		$error = 0;
		
		if ($h->cage->post->noTags('settings_update')  == 'true') {
		
			// if either the login or forgot password form is submitted, check the CSRF key
			if (!$h->csrf()) { $error = 1; }
			
			foreach ($loaded_settings as $setting_name) {
				if ($h->cage->post->keyExists($setting_name->settings_name)) {
					$setting_value = $h->cage->post->getRaw($setting_name->settings_name);
					if (!$error && $setting_value && $setting_value != $setting_name->settings_value) {
						$this->adminSettingUpdate($h, $setting_name->settings_name, $setting_value);
					
					} else {
						if (!$setting_value) {
							// empty value 
							$error = 1;
						}
					}
				} else {
					// values that are allowed to be empty:
					$exempt = array('SMTP_USERNAME', 'SMTP_PASSWORD');
					if ($setting_name->settings_show == 'N') { array_push($exempt, $setting_name->settings_name); }
					if (!in_array($setting_name->settings_name, $exempt)) { 
						// otherwise flag as an error:
						$error = 1;
					} 
				}
			}
		
			// cron hook to include SYS_UPDATES job
			if ($h->cage->post->keyExists('SYS_UPDATES') == 'true' ) {
				$timestamp = time();
				$recurrence = "daily";
                                
                                $hooks = array("SystemInfo:hotaru_feedback", "SystemInfo:hotaru_version", "SystemInfo:plugin_version_getAll");
				foreach ( $hooks as $hook ) {
                                    $h->pluginHook('cron_update_job', 'cron', array('timestamp'=>$timestamp, 'recurrence'=>$recurrence, 'hook'=>$hook));
                                }
			}
			else {
                                $hooks = array("SystemInfo:hotaru_feedback", "SystemInfo:hotaru_version", "SystemInfo:plugin_version_getAll");
				foreach ( $hooks as $hook ) {
                                    $h->pluginHook('cron_delete_job', 'cron', array('hook'=>$hook));
                                }
			}
		
			if ($error == 0) {
				$h->message = $h->lang('admin_settings_update_success');
				$h->messageType = 'green alert-success';
			} else {
				$h->message = $h->lang('admin_settings_update_failure');
				$h->messageType = 'red alert-error';
			}
		}
		
		// Activate themes from theme settings pages - called via JavaScript
		if ($h->cage->post->testAlnumLines('admin') == 'theme_settings' )
		{
			$theme = strtolower($h->cage->post->testAlnumLines('theme') . "/" );
			$this->adminSettingUpdate($h, 'THEME', $theme);
			$h->deleteFiles(CACHE . 'css_js_cache'); // clear the CSS/JS cache
			$json_array = array('activate'=>'true', 'message'=>$h->lang("admin_settings_theme_activate_success"), 'color'=>'green alert-success');
			
			// Send back result data
			echo json_encode($json_array);
			exit;
		}

		$loaded_settings = $this->getAllAdminSettings($h->db);
		
		return $loaded_settings;
	}
	
	
	/**
	 * Returns all setting-value pairs
	 *
	 * @return array|false
	 */
	public function getAllAdminSettings($db)
	{
		$sql = "SELECT settings_name, settings_value, settings_default, settings_note, settings_show FROM " . TABLE_SETTINGS;
		$results = $db->get_results($db->prepare($sql));
		if ($results) { return $results; } else { return false; }
	}
	
	
	/**
	 * Update an admin setting
	 *
	 * @param string $setting
	 * @param string $value
	 */
	public function adminSettingUpdate($h, $setting = '', $value = '')
	{
		$exists = $this->adminSettingExists($h->db, $setting);
		
		if (!$exists) {
			$sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_value, settings_updateby) VALUES (%s, %s, %d)";
			$h->db->query($h->db->prepare($sql, $setting, $value, $h->currentUser->id));
		} else {
			$sql = "UPDATE " . TABLE_SETTINGS . " SET settings_name = %s, settings_value = %s, settings_updateby = %d WHERE (settings_name = %s)";
			$h->db->query($h->db->prepare($sql, $setting, $value, $h->currentUser->id, $setting));
		}
	}
	
	
	/**
	 * Determine if a setting already exists
	 *
	 * Note: The actual value is ignored
	 *
	 * @param string $setting
	 * @return mixed|false
	 */
	public function adminSettingExists($db, $setting = '')
	{
		$sql = "SELECT settings_name FROM " . TABLE_SETTINGS . " WHERE (settings_name = %s)";
		$returned_setting = $db->get_var($db->prepare($sql, $setting));
		if ($returned_setting) { return $returned_setting; } else { return false; }
	}
	
	
	/* *************************************************************
	*
	*  MAINTENANCE PAGE
	*
	* *********************************************************** */
	
	
	/**
	 * Check action called in Maintenance template
	 */
	public function maintenanceAction($h)
	{
		require_once(LIBS . 'Maintenance.php');
		$maintenance = new Maintenance();
		$maintenance->getSiteAnnouncement($h);
		
		// check if we're viewing a debug file
		$debug_file = $h->cage->get->noPath('debug');
		if ($debug_file) {
			// skip the opening die() statement and echo debug file
			$debug_contents = file_get_contents(CACHE . 'debug_logs/' . $debug_file, NULL, NULL, 16);
			echo nl2br($debug_contents);
			exit; 
		}
		
		// check if we're performing an action
		$action = $h->cage->get->testAlnumLines('action');
		
		if ($action == 'announcement') { $maintenance->addSiteAnnouncement($h); }
		if ($action == 'open') { $h->openCloseSite('open'); }
		if ($action == 'close') { $h->openCloseSite('close'); }
		if ($action == 'clear_all_cache') {                         
			$h->clearCache('db_cache', false);
			$h->clearCache('css_js_cache', false);
			$h->clearCache('rss_cache', false);
			$h->clearCache('html_cache', false);
			$h->clearCache('lang_cache', false);
			@unlink(BASE. 'cache/smartloader_cache.php');
                        $h->pluginHook('maintenance_clear_all_cache');
			$h->messages[$h->lang('admin_maintenance_clear_all_cache_success')] = 'green';                         
		}
		if ($action == 'clear_db_cache') { $h->clearCache('db_cache'); }
		if ($action == 'clear_css_js_cache') { $h->clearCache('css_js_cache'); }
		if ($action == 'clear_rss_cache') { $h->clearCache('rss_cache'); }
		if ($action == 'clear_html_cache') { $h->clearCache('html_cache'); }
		if ($action == 'clear_lang_cache') { $h->clearCache('lang_cache'); }
		if ($action == 'optimize') { $h->optimizeTables(); }
                if ($action == 'export') { $h->exportDatabase(); }
		if ($action == 'empty') { $h->emptyTable($h->cage->get->testAlnumLines('table')); }
		if ($action == 'drop') { $h->dropTable($h->cage->get->testAlnumLines('table')); }
		if ($action == 'remove_settings') { $h->removeSettings($h->cage->get->testAlnumLines('settings')); }
		if ($action == 'system_report') { $h->generateReport(); } 		
		if ($action == 'delete_debugs') { 
			$h->clearCache('debug_logs');
			$h->vars['debug_files'] = $h->getFiles(CACHE . 'debug_logs');
		}
		
		// get list of debug logs
		$h->vars['debug_files'] = $h->getFiles(CACHE . 'debug_logs');
	}
	
	
	/**
	 * List all plugins with settings
	 *
	 * @return array|false
	 */
	public function listPluginSettings($h)
	{
		$plugin_settings = array();
		$sql = "SELECT DISTINCT plugin_folder FROM " . DB_PREFIX . "pluginsettings";
		$results = $h->db->get_results($h->db->prepare($sql));
		
		if (!$results) { return false; } 
		
		foreach ($results as $item) {
			array_push($plugin_settings, $item->plugin_folder);
		}
		
		return $plugin_settings;
	}
	
	
	/**
	 * List all created tables - used for emtying tables in Maintenance
	 *
	 * @param bool $exclude_tables - true to exclude important tables
	 */
	public function listDbTables($h, $exclude_tables = false)
	{
		$db_tables = array();
		
		if ($exclude_tables) {
			$exclude = array(
				DB_PREFIX . 'settings',
				DB_PREFIX . 'users',
				DB_PREFIX . 'usermeta',
				DB_PREFIX . 'categories',
				DB_PREFIX . 'comments',
				DB_PREFIX . 'commentvotes',
				DB_PREFIX . 'miscdata',
				DB_PREFIX . 'postmeta',
				DB_PREFIX . 'posts',
				DB_PREFIX . 'postvotes',
				DB_PREFIX . 'site',
				DB_PREFIX . 'tags',
				DB_PREFIX . 'useractivity',
				DB_PREFIX . 'widgets',
			);
		}

		$h->db->selectDB(DB_NAME);
		
		if (!$h->db->get_col("SHOW TABLES",0)) { return $db_tables; }
		
		foreach ( $h->db->get_col("SHOW TABLES",0) as $table_name )
		{
			if ($exclude_tables) {
				if (!in_array($table_name, $exclude)) {
					array_push($db_tables, $table_name);
				}
			} else {
				array_push($db_tables, $table_name);
			}
		}
		
		return $db_tables;
	}
	
	
	/* *************************************************************
	*
	*  BLOCKED PAGE
	*
	* *********************************************************** */
	
	
	/**
	 * Determine and respond to actions from the Blocked list
	 */
	public function blocked($h)
	{
		require_once(LIBS . 'Blocked.php');
		$blocked = new Blocked();
		$blocked_items = $blocked->buildBlockedList($h);
		
		return $blocked_items;
	}
	
        
        public function getPages($h)
        {
            
        }
        
	
	/* *************************************************************
	*
	*  PLUGIN MANAGEMENT PAGE
	*
	* *********************************************************** */


	 /**
	 * Call functions based on user actions in Plugin Management
	 */
	public function adminPlugins($h)
	{
		$pfolder = $h->cage->get->testAlnumLines('plugin');
		$h->plugin->folder = $pfolder;   // assign this plugin to Hotaru
		
		$action = $h->cage->get->testAlnumLines('action');
		$order = $h->cage->get->testAlnumLines('order');                
				
		$plugman = new PluginManagement();
		
		switch ($action) {
                        case "orderAjax":
                                $sort = $h->cage->post->testAlnumLines('sort');
                                $plugman->pluginReorder($h, $sort);
                                //echo 1; 
                                die();
			case "activate":
				$plugman->activateDeactivate($h, 1);
				break;
                        case "activateAjax":
				$result = $plugman->activateDeactivate($h, 1, true);
                                echo json_encode($result);
				die();    
			case "deactivate":
				$plugman->activateDeactivate($h, 0);
				break;  
                        case "deactivateAjax":
				$result = $plugman->activateDeactivate($h, 0, true);
                                echo json_encode($result);
				die();     
			case "activate_all":
				$plugman->activateDeactivateAll($h, 1);
				break;
			case "deactivate_all":
				$plugman->activateDeactivateAll($h, 0);
				break;    
			case "uninstall_all":
				$plugman->uninstallAll($h);
				break;    
			case "install":
				$plugman->install($h);
				break;
			case "uninstall":
				$plugman->uninstall($h);
				break;    
			case "orderup":
				$plugman->pluginOrder($h, $order, "up");
				break;    
			case "orderdown":
				$plugman->pluginOrder($h, $order, "down");
				break;
			case "update":
				$plugman->activateDeactivate($h, 0);
				$plugman->update($h);
				$plugman->activateDeactivate($h, 1);
				break;
			case "version_check":
				$plugman->versionCheck($h);
				break;
			default:
				// nothing to do here...
				break;
		}
		
		$plugman->refreshPluginDetails($h);
		
		// get and sort all the plugins ready for display:
		$allplugins = $plugman->getPlugins($h);  // get plugins
		
		$installed_plugins = array_filter($allplugins, array($plugman, 'getInstalledPlugins'));
		$h->vars['installed_plugins'] = sksort($installed_plugins, "order", "int", true);
		
		$uninstalled_plugins = array_filter($allplugins, array($plugman, 'getUninstalledPlugins'));
		$h->vars['uninstalled_plugins'] = sksort($uninstalled_plugins, 'name', 'char', true);
		
		return true;
	}
        
        
        public static function sidebarPluginsList($h, $pluginResult)
        {           
            $pFuncs = new PluginFunctions();
            $base = $pFuncs->getValues($h, $pluginResult);

            try {
                if (is_array($base)) {
                    foreach ($base as $links) {  // loop through each plugins array
                        foreach ($links as $label => $params) {  // loop through each link item
                            // Going to check the arrays first as we dont want this to break
                            $linkLabel = isset($label) ? $label : '---';
                            $linkUrl = isset($params[0]) ? $params[0] : '#';
                            echo "<li><a href='" . BASEURL . $linkUrl . "'>" . $linkLabel . "</a></li>";        
                        }
                    }
                }
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
            
        }
        
 
        public function adminNav($h)
        {
            ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $h->lang("main_theme_navigation_admin"); ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                      <li><a href="<?php echo $h->url(array(), 'admin'); ?>">Home</a></li>
                      <li><a href="<?php echo $h->url(array('page' => 'plugin_management'), 'admin'); ?>">Plugins</a></li>
                      <li><a href="<?php echo $h->url(array('page' => 'maintenance'), 'admin'); ?>">Maintenance</a></li>
                      <li class="divider"></li>
                      <li class="nav-header">Plugins</li>
                      <?php $h->pluginHook('adminNav_plugins'); ?>
                      
                      <?php // TODO
                            // Include the following plugins in list by calling them from the plugin
                            // after plugin has been updated for v.1.5.0
                      ?>
                      <?php if ($h->isActive('user_manager')) { ?>
                        <li><a href="<?php echo $h->url(array('page' => 'plugin_settings', 'plugin' => 'user_manager'), 'admin'); ?>">User Manager</a></li>
                      <?php  } ?>
                        <?php if ($h->isActive('post_manager')) { ?>
                        <li><a href="<?php echo $h->url(array('page' => 'plugin_settings', 'plugin' => 'post_manager'), 'admin'); ?>">Post Manager</a></li>
                      <?php  } ?>
                      <?php if ($h->isActive('category_manager')) { ?>
                        <li><a href="<?php echo $h->url(array('page' => 'plugin_settings', 'plugin' => 'category_manager'), 'admin'); ?>">Category Manager</a></li>
                      <?php  } ?>
                        <?php if ($h->isActive('widgets')) { ?>
                        <li><a href="<?php echo $h->url(array('page' => 'plugin_settings', 'plugin' => 'widgets'), 'admin'); ?>">Widgets</a></li>
                      <?php  } ?>
                    </ul>
                  </li>
            <?php
        }
}
?>