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
    public function buildBreadcrumbs($h)
    {
        $output = '';
        $output .= "<a href='" . BASEURL . "'>" . $h->lang['main_theme_breadcrumbs_home'] . "</a>\n"; 
        
        // Admin only:
        if ($h->isAdmin) {
            $output .= " &raquo; <a href='" . $h->url(array(), 'admin') . "'>";
            $output .= $h->lang['admin_theme_main_admin_cp'] . "</a>\n";
        }
        
        // plugin hook:
        $crumbs = $h->pluginHook('breadcrumbs');
        if ($crumbs) {
            $output .= $crumbs['breadcrumbs']; // I KNOW THIS WON'T WORK.
            return $output;
        } 
        
        // in case of no plugins:
        $output .= " &raquo; " . $h->pageTitle;
        return $output;
    }
    
    
    /**
     * prepares the RSS breadcrumbs link
     *
     * @param string $type - post status, e.g. new, top, etc.
     * @return string
     */    
    public function rssBreadcrumbsLink($h, $type = 'all')
    {
        $rss = "<a href='" . $h->url(array('page'=>'rss', 'status'=>$type)) . "'>";
        $rss .= " <img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png' alt='" . $h->pageTitle . " RSS' /></a>";
        return $rss;
    }
}
?>
