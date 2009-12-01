<?php
/**
 * name: Google Analytics
 * description: Displays "Google Analyitics Code"
 * version: 0.1
 * folder: google_analytics
 * class: GoogleAnalytics
 * hooks: pre_close_body, admin_plugin_settings, admin_sidebar_plugin_settings 
 *
 * Usage: Add <?php $hotaru->plugins->pluginHook('google_analytics'); ?> to footer.php before </body> tag.
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
 * @author    Carlo Armanni <carlo.armanni@libero.it>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.tr3ndy.com/
 */

class GoogleAnalytics extends PluginFunctions
{

    /**
     * Default settings on install
     */
    public function install_plugin()
    {
        // Default settings 
        if (!$this->getSetting('google_analytics_key')) { $this->updateSetting('google_analytics_key', ''); }
    }
		 
    public function pre_close_body()
    {
	$google_analytics_key = $this->getSetting('google_analytics_key');
    echo "<script type=\"text/javascript\">
		 var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");
		 document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));
		 </script>
		 <script type=\"text/javascript\">
		 try {
		 var pageTracker = _gat._getTracker(\"$google_analytics_key\");
		 pageTracker._trackPageview();
		 } catch(err) {}</script>";
	
	}

}

?>
