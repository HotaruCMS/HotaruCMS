<?php
/**
 * name: Disqus
 * description: Enables comments using Disqus
 * version: 0.1
 * folder: disqus
 * prefix: disq
 * requires: submit 0.1
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

/**
 * Default settings on install
 */
function disq_install_plugin()
{
    global $plugin, $lang;
        
    // Default settings 
    $plugin->plugin_settings_update('disqus', 'disqus_shortname', 'subconcious'); // This is the default in Disqus' generic code
    
    // Include language file. Also included in hotaru_header, but needed here so 
    // that the link in the Admin sidebar shows immediately after installation.
    $plugin->include_language('disqus');
}


/**
 * Parameters for Developers
 *
 * @link http://wiki.disqus.net/JSEmbed/
 */
function disq_header_include_raw()
{
    global $lang, $plugin, $post;

    echo '
    <script type="text/javascript">
        var disqus_developer = true; 
        var disqus_identifier = ' . $post->post_id . 
    '</script>';
    echo "\n";
}


/**
 * Include language file
 */
function disq_hotaru_header()
{
    global $lang, $plugin;

    $plugin->include_language('disqus');
}


/**
 * Display Admin sidebar link
 */
function disq_admin_sidebar_plugin_settings()
{
    global $lang;
    
    echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>'disqus'), 'admin') . "'>" . $lang['disqus_admin_sidebar'] . "</a></li>";
}

/**
 * Display Admin settings page
 *
 * @return true
 */
function disq_admin_plugin_settings()
{
    require_once(plugins . 'disqus/disqus_settings.php');
    disq_settings();
    return true;
}

/**
 * Link to comments
 */
function disq_submit_show_post_extra_fields()
{
    global $post, $lang;
    
    $url = url(array('page'=>$post->post_id));
    echo '<li><a class="disqus_comments_link" href="' . $url . '#disqus_thread">' . $lang['disqus_comments_link'] . '</a></li>' . "\n";
}


/**
 * Display Disqus comments and form
 */
function disq_submit_post_show_post()
{
    global $hotaru;
    
    $hotaru->display_template('disqus_comments', 'disqus');
}


/**
 * Include CSS
 */
function disq_pre_close_body()
{
    global $hotaru;
    
    $hotaru->display_template('disqus_footer', 'disqus');
}



?>