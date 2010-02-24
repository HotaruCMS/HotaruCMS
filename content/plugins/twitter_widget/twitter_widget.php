<?php 
/**
 * name: Twitter Widget
 * description: Sidebar widget that shows your sites Twitter follower count and friends tweets
 * version: 0.1
 * folder: twitter_widget
 * class: TwitterWidget
 * requires: widgets 0.7, sb_base 0.1
 * hooks: install_plugin, header_include, admin_plugin_settings, admin_sidebar_plugin_settings
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
        $h->includeCss();
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
        

		///// testing purposes (doesn't count against your hit limit to call up remaining hits)
        if ($h->isDebug) {
		    // grabs status for API rate limit for testing cache
			$calls = $twitter->rateLimitStatus();
			
			$hits = new SimpleXMLElement($calls);
			
            echo '<br/>';
            echo 'remaining hits for this hour = ' . $hits->{'remaining-hits'};
        }
        
        $need_cache = false;

        // check for a cached version and use it if no recent update:
        $cached_output = $h->smartCache('html', 'posts', 10, '', 'twitter_widget');
        if ($cached_output) {
            echo $cached_output; // cached HTML
            return true;
        } else {
            $need_cache = true;
        }

        // fetch your profile in xml format
        $user = $twitter->showUser();
        
        $my_info = new SimpleXMLElement($user);
        
        // fetch your friends (people you follow) in xml format or use getUserTimeline() to show your own 
        $xml = $twitter->getFriendsTimeline();
        
        // fetch your session xml format    
        $twitter_status = new SimpleXMLElement($xml);
        
        // trim characters to show for each tweet        
        function ShortenText($text) {
          // Change here. default 77
          $chars = 77;

          $text = $text." ";
          $text = substr($text,0,$chars);
          $text = substr($text,0,strrpos($text,' '));
          $text = $text."...";

          return $text;
          }    
          
        
    // show twitter widget template
        $output = "<div class='twitter_container'>\n";        
        $output .= "<div class='twitter_header'>\n";
        $output .= "<img src='".$my_info->profile_image_url."' alt=".$my_info->screen_name." title=".$my_info->screen_name." >\n";
        $output .= "<h3><a href='http://www.twitter.com/".$my_info->screen_name."'>".$my_info->followers_count ."&nbsp;". $h->lang['twitter_widget_followers']."</a></h3>\n";
        //$output .= "<br/>\n";
        $output .= "<a href='http://www.twitter.com/".$my_info->screen_name."'>".$h->lang['twitter_widget_follow_us']."</a>\n";
        $output .= "</div>\n";
        
            $i = 1;
     foreach($twitter_status->status as $status){
            if($i < 6){ //show up to 20 latest tweets default is 5
            $output .= "<div class='twitter_status'>\n";
            foreach($status->user as $user){
                $output .= "<img src='".$user->profile_image_url."' alt=".$user->screen_name." title=".$user->screen_name." class='twitter_image'>\n";
                $output .= "<a href='http://www.twitter.com/".$user->screen_name."'>".$user->name."</a>: \n";
            }
            $output .= ShortenText($status->text);
            $output .= "<br/>\n";
            // uncommment below to show posted time - might have to adjust CSS!
            //$output .= "<div class='twitter_posted_at'><strong>Posted at:</strong> ".$status->created_at."</div>";
            $output .= "</div>\n";
            }
             $i++;
        }
        $output .= "</div>";

        
        if ($need_cache) {
            $h->smartCache('html', 'posts', 10, $output, 'twitter_widget'); 
        }
        
        echo $output;

    }

}
?>