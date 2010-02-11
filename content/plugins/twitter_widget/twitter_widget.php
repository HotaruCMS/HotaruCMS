<?php 
/**
 * name: Twitter Widget
 * description: Sidebar widget that shows your sites Twitter follower count and tweets
 * version: 0.1
 * folder: twitter_widget
 * class: TwitterWidget
 * requires: widgets 0.7
 * hooks: install_plugin, header_include, hotaru_header, admin_plugin_settings
 * author: Jon Harvey
 * authorurl: http://hotarucms.org/
 * thanks to: http://github.com/jdp/twitterlibphp and http://woork.blogspot.com/2009/06/super-simple-way-to-work-with-twitter.html
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
 *
*/

class TwitterWidget
{
    /**
     * Default settings on install
     */
    public function install_plugin($h)
    {
        // Default settings 
       if (!$h->getSetting('twitter_widget_username')) { $h->updateSetting('twitter_widget_username', ''); }
       if (!$h->getSetting('twitter_widget_password')) { $h->updateSetting('twitter_widget_password', ''); }
       
        // widget
        $h->addWidget('twitter_widget', 'twitter_widget', '');  // plugin name, function name, optional arguments
    }


    public function header_include($h)
    {
        $h->includeCss('twitter_widget');
    }

    public function widget_twitter_widget($h)
    {
        // your twitter username and password
        $twitter_widget_username = $h->getSetting('twitter_widget_username', 'twitter_widget'); 
        $twitter_widget_password = $h->getSetting('twitter_widget_password', 'twitter_widget');
        
        // include Twitterlibphp
        require_once(PLUGINS . 'twitter_widget/libs/twitter_lib.php');
        
        // initialize the twitter class
        $twitter = new Twitter($twitter_widget_username, $twitter_widget_password);
    
        // fetch your profile in xml format
        $xml = $twitter->getFriendsTimeline();
    
        // fetch your session xml format    
        $twitter_status = new SimpleXMLElement($xml);
        
        // show twitter widget template

        echo '<div class="twitter_container">';

        foreach($twitter_status->status as $status){
            echo '<div class="twitter_status">';
            foreach($status->user as $user){
                echo '<img src="'.$user->profile_image_url.'" class="twitter_image">';
                echo '<a href="http://www.twitter.com/'.$user->screen_name.'">'.$user->name.'</a>: ';
            }
            echo $status->text;
            echo '<br/>';
            echo '<div class="twitter_posted_at"><strong>Posted at:</strong> '.$status->created_at.'</div>';
            echo '</div>';
            echo '</div>';
        }

    }

}
?>