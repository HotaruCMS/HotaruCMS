<?php
/**
 * Breadcrumb functions
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
class Breadcrumbs
{
    /**
     * Build breadcrumbs
     */
    public function buildBreadcrumbs($hotaru)
    {
        $output = '';
        $output .= "<a href='" . BASEURL . "'>" . SITE_NAME . "</a>\n"; 
        
        // Admin only:
        if ($hotaru->isAdmin) {
            $output .= " &raquo; <a href='" . $hotaru->url(array(), 'admin') . "'>";
            $output .= $hotaru->lang['admin_theme_main_admin_cp'] . "</a>\n";
        }
        
        // plugin hook:
        $crumbs = $hotaru->pluginHook('breadcrumbs');
        if ($crumbs) {
            $output .= $crumbs['breadcrumbs']; // I KNOW THIS WON'T WORK.
            return $output;
        } 
        
        // in case of no plugins:
        $output .= " &raquo; " . $hotaru->pageTitle;

        return $output;
    }
}
?>