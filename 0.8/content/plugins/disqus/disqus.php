<?php
/**
 * name: Disqus
 * description: Enables comments using Disqus
 * version: 0.4
 * folder: disqus
 * class: Disqus
 * requires: submit 0.7
 * hooks: header_include, header_include_raw, install_plugin, hotaru_header, submit_show_post_extra_fields, submit_post_show_post, pre_close_body, admin_plugin_settings, admin_sidebar_plugin_settings
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

class Disqus extends PluginFunctions
{

    /**
     * Default settings on install
     */
    public function install_plugin()
    {
        // Default settings 
        $this->updateSetting('disqus_shortname', 'subconcious');    // This is the default in Disqus' generic code
    }
    
    
    /**
     * Parameters for Developers
     *
     * @link http://wiki.disqus.net/JSEmbed/
     */
    public function header_include_raw()
    {
        echo '
        <script type="text/javascript">
            var disqus_developer = true; 
            var disqus_identifier = ' . $this->hotaru->post->id . 
        '</script>';
        echo "\n";
    }
    
    
    /**
     * Link to comments
     */
    public function submit_show_post_extra_fields()
    {
        $url = $this->hotaru->url(array('page'=>$this->hotaru->post->id));
        echo '<li><a class="disqus_comments_link" href="' . $url . '#disqus_thread">' . $this->lang['disqus_comments_link'] . '</a></li>' . "\n";
    }
    
    
    /**
     * Display Disqus comments and form
     */
    public function submit_post_show_post()
    {
        if (!$this->hotaru->isPage('submit2')) {
            $this->hotaru->vars['shortname'] = $this->getSetting('disqus_shortname');
            $this->hotaru->displayTemplate('disqus_comments', 'disqus');
        }
    }
    
    
    /**
     * Display Admin settings page
     *
     * @return true
     */
    public function admin_plugin_settings()
    {
        require_once(PLUGINS . 'disqus/disqus_settings.php');
        $disqSettings = new DisqusSettings($this->folder, $this->hotaru);
        $disqSettings->settings();
        return true;
    }

}
?>