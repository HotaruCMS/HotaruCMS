<?php
/**
 * name: Disqus
 * description: Enables comments using Disqus
 * version: 0.6
 * folder: disqus
 * class: Disqus
 * type: comments
 * requires: sb_base 0.1
 * hooks: header_include, header_include_raw, install_plugin, hotaru_header, sb_base_show_post_extra_fields, sb_base_post_show_post, pre_close_body, admin_plugin_settings, admin_sidebar_plugin_settings
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

class Disqus
{

    /**
     * Default settings on install
     */
    public function install_plugin($h)
    {
        // Default settings 
        if (!$h->getSetting('disqus_shortname')) { $h->updateSetting('disqus_shortname', 'subconcious'); } // This is the default in Disqus' generic code
    }
    
    
    /**
     * Parameters for Developers
     *
     * @link http://wiki.disqus.net/JSEmbed/
     */
    public function header_include_raw($h)
    {
        if (!$h->pageType == 'post') { return false; }
        echo '
        <script type="text/javascript">
            var disqus_developer = true; 
            var disqus_identifier = ' . $h->post->id . 
        '</script>';
        echo "\n";
    }
    
    
    /**
     * Link to comments
     */
    public function sb_base_show_post_extra_fields($h)
    {
        $url = $h->url(array('page'=>$h->post->id));
        echo '<li><a class="disqus_comments_link" href="' . $url . '#disqus_thread">' . $h->lang['disqus_comments_link'] . '</a></li>' . "\n";
    }
    
    
    /**
     * Display Disqus comments and form
     */
    public function sb_base_post_show_post($h)
    {
        if (!$h->isPage('submit2')) {
            $h->vars['shortname'] = $h->getSetting('disqus_shortname');
            $h->displayTemplate('disqus_comments', 'disqus');
        }
    }
    
    /**
     * Include footer code on list pages
     */
    public function pre_close_body($h)
    {
        if (!$h->pageType == 'list') { return false; }
    
        $h->vars['shortname'] = $h->getSetting('disqus_shortname');
        $h->displayTemplate('disqus_footer', 'disqus');
    }
    
}
?>