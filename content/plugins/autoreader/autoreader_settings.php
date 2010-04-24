<?php
/**
 * File: plugins/autoreader/autoreader_settings.php
 * Purpose: The functions for autoreader.
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
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 * *
 * Ported from original WP Plugin WP-O-Matic
 * Description: Enables administrators to create posts automatically from RSS/Atom feeds.
 * Author: Guillermo Rauch
 * Plugin URI: http://devthought.com/wp-o-matic-the-wordpress-rss-agreggator/
 * Version: 1.0RC4-6
 * 
 * Additions for Image cache, original url link, category search, tags, thumbnail, short excertps, code change for hotaru by shibuya246
 */

  
  
class AutoreaderSettings extends Autoreader
{
     /**
     * Admin settings
     */
    public function settings($h)
    {
        // include language file
        $h->includeLanguage();

        $template_call = $h->cage->post->testAlnumLines('autoreader_template');     

        switch($template_call) {
            case 'autoreader_list': {
                $h->displayTemplate($template_call);
            }

            default : {
                // show header
                echo "<h1>" . $h->lang["autoreader_settings_header"] . "</h1>\n";
                ?>

                <div id="admin_plugin_menu">
                    <ul class="dropdown">
                        <li><a name="autoreader_dashboard" href="#">Dashboard</a></li>
                        <li><a name="autoreader_list" href="#">Campaigns</a>
                            <ul class="sub_menu">
                                <li><a name="autoreader_add" href="#">Add Campaign</a></li>
                                <li><a name="autoreader_list" href="#">List Campaigns</a></li>
                            </ul>
                        </li>
                        <li><a name="autoreader_options" href="#">Options</a></li>
                    </ul>
                </div>
               
                <div id="admin_plugin_content">

                </div>

                <?php
                // Get settings from database if they exist...
                //$autoreader_settings = $h->getSerializedSettings();
            }
        }
    }

    


}

?>