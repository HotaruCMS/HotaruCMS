<?php
/**
 * name: Link Bar
 * description: Bare bones link bar with an iframe
 * version: 0.1
 * folder: link_bar
 * class: LinkBar
 * type: bar
 * requires: sb_base 0.8
 * hooks: sb_base_show_post_pre_title, sb_base_theme_index_top, admin_plugin_settings, admin_sidebar_plugin_settings
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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
 * @author    shibuya246 <blog@shibuya246.com>
 * @copyright Copyright (c) 2010
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://shibuya246.com
 */
class LinkBar
{
    /**
     * Check to see if we should redirect to the link bar page
     */
    public function sb_base_theme_index_top($h)
    {
        $access= false;
    
        // if using rss forwarding, enable use of the link bar
        if ($h->cage->get->keyExists('forward')) {
            $post_id = $h->cage->get->testInt('forward');
            $access = true;
        }
        
        // if coming from a post with a link parameter, enable use of the link bar
        if ($h->cage->get->keyExists('link')) {
            $post_id = $h->cage->get->testInt('link');
            $access = true;
        }
        
        // if neither RSS forwarding or "link" URL, get out of here!
        if (!$access) { return false; }
        
        // read the post into $h and display the link bar
        if ($post_id) {
            $h->readPost($post_id);
            
            // if the source url contains the BASEURL, don't use the bar
            // This is so we don't show the bar on posts submitted without a link
            if (strpos($h->post->origUrl, BASEURL) !== false) { return false; }
            
            // get the settings
            $h->vars['link_bar_settings'] = $h->getSerializedSettings('link_bar');
            
            // plugin hook
            $h->pluginHook('link_bar_pre_template');
            
            // display link bar template
            $h->displayTemplate('link_bar_top');
            die(); exit;
        }
    }


    /**
     * Modify the post URL with an appended "link" parameter
     */
    public function sb_base_show_post_pre_title($h)
    {
        $h->post->origUrl = $h->url(array('page'=>$h->post->id, 'link'=>$h->post->id));
    }
}