<?php
/**
 * name: DB Optimize
 * description: Show the tables in the database and their level of optimization
 * version: 0.1
 * folder: db_optimize
 * type: db_optimize
 * class: DBOptimize
 * hooks: install_plugin,admin_sidebar_plugin_settings, admin_plugin_settings
 * author: shibuya246
 * authorurl: http://shibuya246.com
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
 * @category  Content Management System
 * @package   HotaruCMS
 * @author    shibuya246
 * @copyright Copyright (c) 2010, shbuya246
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

class DBOptimize
{

    /**
     * Add db-optimize settings fields to the db.
     */
    public function install_plugin($h)
    {
        // Default settings
        $db_optimize_settings = $h->getSerializedSettings();

	/**
	*
	* Type in your settings to be saved / retrieved from the db table
	* e.g. for a simple checked box  if (!isset($db_optimize_settings['setting_var_to_save'])) { $db_optimize_settings['setting_var_to_save'] = "checked"; }
	*
	*/

	$h->updateSetting('db_optimize_settings', serialize($db_optimize_settings));
    }


}
?>
