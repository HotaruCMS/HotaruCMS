<?php
/**
 * name: Facebook Share
 * description: Show Facebook Share button
 * version: 0.2
 * folder: facebook_share
 * class: FacebookShare
 * hooks: install_plugin, admin_sidebar_plugin_settings, admin_plugin_settings, sb_base_show_post_extra_fields, theme_index_top, header_include
 * author: William Dahlheim
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

class FacebookShare
{
	/**
     * Install Facebook Share
     */
    public function install_plugin($h)
    {
        // Plugin settings
        $facebook_share_settings = $h->getSerializedSettings();
        if (!isset($facebook_share_settings['icon_or_button'])) { $facebook_share_settings['icon_or_button'] = "icon_link"; }
		if (!isset($facebook_share_settings['button_counter'])) { $facebook_share_settings['button_counter'] = "false"; }
        if (!isset($facebook_share_settings['fb_shortener'])) { $facebook_share_settings['fb_shortener'] = "isgd"; }
        if (!isset($facebook_share_settings['fb_bitly_login'])) { $facebook_share_settings['fb_bitly_login'] = ""; }
        if (!isset($facebook_share_settings['fb_bitly_api_key'])) { $facebook_share_settings['fb_bitly_api_key'] = ""; }
        if (!isset($facebook_share_settings['fb_use_GA_tracking'])) { $facebook_share_settings['fb_use_GA_tracking'] = "checked"; }		
        $h->updateSetting('facebook_share_settings', serialize($facebook_share_settings));
    }
	
	/**
     * Include jQuery for hiding and showing email options in plugin settings
     */
    public function admin_header_include_raw($h)
    {
        if ($h->isSettingsPage('facebook_share')) {
            echo "<script type='text/javascript'>\n";
            echo "$(document).ready(function(){\n";
			echo " if ( $('input#facebook_share_icon_link').attr('checked') ) { $('input#button_counter').attr('disabled' ,'disabled'); }\n";
            echo "	$('ul#facebook_share_container input[type=radio]').live('click', function(){\n";
			echo "		if ( $(this).attr('id') == 'facebook_share_button') {\n";
			echo "			$('input#button_counter').removeAttr('disabled');\n";
			echo "		} else {\n";
			echo "			$('input#button_counter').attr('disabled' ,'disabled');\n";
			echo "		}\n";
			echo "	});\n";
            echo "});\n";
            echo "</script>\n";
        }
    }

    /**
     * Shows Facebook Share button in each post
     */
    public function sb_base_show_post_extra_fields($h)
    {
		$facebook_share_settings = $h->getSerializedSettings();
		$facebook_share_type = $facebook_share_settings['icon_or_button'];
		$facebook_share_counter = $facebook_share_settings['button_counter'];
		$facebook_share_url = $h->url(array('page'=>'facebook_share', 'id'=>$h->post->id));
		
		if ( $facebook_share_type == 'button' && $facebook_share_counter == 'true' ) { 
			$facebook_share_type = 'button_count'; 
		}
		
		// share this
		echo '<li><a class="facebook_share_link" name="fb_share" type="' . $facebook_share_type . '" share_url="' . $facebook_share_url . '" href="http://www.facebook.com/sharer.php?u=' . $facebook_share_url .' &t=' . SITE_NAME. '&nbsp;&raquo;&nbsp;' . $h->post->title . '">'.$h->lang['facebook_share_label'].'</a><script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script></li>';

	}

    /**
     * Determine if the user has clicked Facebook Share
     */
    public function theme_index_top($h)
    {
        if ($h->isPage('facebook_share')) {
            $this->facebookThisPost($h);
        }
    }
    
    /**
     * Build the link 
     */
    public function facebookThisPost($h)
    {
        // get the post's id from the url
        $post_id = $h->cage->get->testInt('id');
        
        // get the compressed url for this link
        $shortened_url = $this->getShortUrl($h, $post_id);
        
        // add the compressed link to the Facebook status update url
        $Facebook_url = $this->getFacebookUrl($h, $post_id, $shortened_url); 

        // redirect to Facebook
        header("Location: " . $Facebook_url);
        exit;
    }

    /**
     * Build the shortened link 
     *
     * @param int $post_id
     * @return string $url
     */
    public function getShortUrl($h, $post_id)
    {
        // Check the database to see if there's already a short link there.
        $query = "SELECT postmeta_value FROM " . TABLE_POSTMETA . " where postmeta_postid = %d AND postmeta_key = %s";
        $sql = $h->db->prepare($query, $post_id, 'compressed_url');
        $stored_short_link = $h->db->get_var($sql);
                
        if(!$stored_short_link) {
            // no short link in db. We need to create one:

            // get settings so we know which shortener to use:
            $facebook_share_settings = $h->getSerializedSettings();

            // get the post's url and encode it:
           if ($facebook_share_settings['fb_use_GA_tracking'] == "checked") {  // do we want GA tracking tags?
				if (FRIENDLY_URLS == "false")	{
	            	// friendly URLs are not enabled (add more query string parameters)
	      			$post_url = urlencode($h->url(array('page'=>$post_id)) . '&utm_source=fb-share&utm_medium=Facebook&utm_campaign=story-promotion' );
	        	}
	        	else { // friendly URLs are enabled (start with query string parameters)
	      			$post_url = urlencode($h->url(array('page'=>$post_id)) . '?utm_source=fb-share&utm_medium=Facebook&utm_campaign=story-promotion' );
	  			} 
	  		}
			else { // just send the URL without tracking tags
				$post_url = urlencode($h->url(array('page'=>$post_id)));
        	}	
            
        	// which shortener do we use?
            switch($facebook_share_settings['fb_shortener']) {
                case 'tinyurl':
                    $url = file_get_contents('http://tinyurl.com/api-create.php?url=' . $post_url);
                    break;
                case 'bitly':
                    $url = $this->getBitlyLink($h, $post_url, $facebook_share_settings);
                    break;
                default:
                    $url = file_get_contents('http://is.gd/api.php?longurl=' . $post_url);
            }

            // then store it in the database:
            $query = "INSERT INTO " . TABLE_POSTMETA . " (postmeta_postid, postmeta_key, postmeta_value, postmeta_updateby) VALUES(%d, %s, %s, %d)";
            $sql = $h->db->prepare($query, $post_id, 'compressed_url', urlencode(trim($url)), $h->currentUser->id);
            $h->db->query($sql);

        } else {
            // we can use the existing one.
            $url = $stored_short_link;
        }
        return trim($url);
    }

    /**
     * Shorten url with bit.ly
     *
     * @param string $post_url
     * @param array $facebook_share_settings
     * @return string $url
     */
    public function getBitlyLink($h, $post_url, $facebook_share_settings)
    {
        // get our login and api key from the saved settings
        $bitly_login = $facebook_share_settings['fb_bitly_login'];
        $bitly_apikey = $facebook_share_settings['fb_bitly_api_key'];
        
        // build the api call:
        $api_call = file_get_contents("http://api.bit.ly/shorten?version=2.0.1&longUrl=" . $post_url . "&login=" . $bitly_login . "&apiKey=" . $bitly_apikey);
        
        // get the result of the api call
        $bitlyinfo = json_decode(utf8_encode($api_call),true);

        // return the shortened url if no error
        if ($bitlyinfo['errorCode'] == 0) {
            return $bitlyinfo['results'][urldecode($post_url)]['shortUrl'];
        } else {
            return false;
        }
    }
    
    /**
     * Build the Facebook status update link
     *
     * @param int $post_id
     * @param string $shortened_url
     * @return string $url
     */
    public function getFacebookUrl($h, $post_id, $shortened_url)
    {
        $h->readPost($post_id);
        $title = html_entity_decode($h->post->title, ENT_QUOTES, "UTF-8");

        $title = urlencode($title);
        
        // The final Facebook URL:
        //$url = 'http://Facebook.com/home/?status=' . $title . '+' . $shortened_url;
		$url = 'http://www.facebook.com/sharer.php?u=' . $title . '+' . $shortened_url;
		
        return $url;
    }
}    

?>