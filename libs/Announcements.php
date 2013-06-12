<?php
/**
 * Announcement functions
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
class Announcements
{
	/**
	 * Displays an announcement at the top of the screen
	 *
	 * @param string $announcement - optional 
	 * @return array
	 */
	public function checkAnnouncements($h, $announcement = '') 
	{
		$announcements = array();
		
		if (SITE_OPEN == "false") {
			array_push(
				$announcements, 
				$h->lang('main_announcement_site_closed')
			);
		}
		
		// "All plugins are currently disabled."
		if (!$h->numActivePlugins()) {
			array_push(
				$announcements, 
				$h->lang('main_announcement_plugins_disabled')
			);
                        if (SITE_OPEN) { $h->pageName = 'welcome'; $h->pageTitle = 'Welcome'; }
		}
		
		// if using the announcement parameter, then add to non-admin pages only:
		if ($announcement && !$h->adminPage) {
			array_push($announcements, $announcement);
		}
		
		// get the announcement set in the Admin Maintenance page:
		require_once(LIBS . 'Maintenance.php');
		$maintenance = new Maintenance();
		$maintenance->getSiteAnnouncement($h);
		if ($h->vars['admin_announcement_enabled']) {
			array_push($announcements, urldecode($h->vars['admin_announcement']));
		}
		
		// Plugins can add announcements with this:
		$h->vars['hotaru_announcements'] = $announcements;
		$h->pluginHook('hotaru_announcements');
		$announcements = $h->vars['hotaru_announcements'];
		
		if (!is_array($announcements)) {
			return false;
		} else {
			return $announcements;
		}
	}
	
	
	/**
	 * Returns an announcement for display at the top of Admin
	 *
	 * @return array|false - array of announcements
	 */
	public function checkAdminAnnouncements($h)
	{
		// Check if the install file has been deleted:
		
		$announcements = array();

		// Check if install file has been deleted
		$filename = INSTALL . 'install.php';
		if (file_exists($filename)) {
			array_push($announcements, $h->lang('admin_announcement_delete_install'));
		}
		
		// Check if install file has not been run
                $hotaru_version = $h->miscdata('hotaru_version');
		if (version_compare($h->version, $hotaru_version, '>')) {
			array_push($announcements, $h->lang('admin_announcement_run_install'));
		}
		
		// Site is currently undergoing maintenance
		if (SITE_OPEN == "false") {
			array_push($announcements, $h->lang('admin_announcement_site_closed'));
		}
		
		// Please enter a site email address
		if (SITE_EMAIL == "email@example.com") {
			array_push($announcements, $h->lang('admin_announcement_change_site_email'));    
		} 
		
		// "Go to Plugin Management to enable some plugins"
		if (!$h->numActivePlugins()) {
			array_push($announcements, $h->lang('admin_announcement_plugins_disabled'));                        
		}
                
                // "Don't forget - debug mode is on"
		if ($h->isDebug) {
			array_push($announcements, $h->lang('admin_announcement_debug_mode_on'));    
		}
		
		// Plugins can add announcements with this:
		$h->vars['admin_announcements'] = $announcements;
		$h->pluginHook('admin_announcements');
		$announcements = $h->vars['admin_announcements'];
		
		if (!is_array($announcements)) {
			return false;
		} else {
			return $announcements;
		}
	}
}
?>
