<?php
/**
 * name: Tweet This
 * description: Send posts to Twitter
 * version: 0.4
 * folder: tweet_this
 * class: TweetThis
 * hooks: install_plugin, admin_sidebar_plugin_settings, admin_plugin_settings, sb_base_show_post_extra_fields, theme_index_top, header_include
 * requires: sb_base 0.1
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
 *
 * Based on the original Tweet This plugin for Wordpress by Richard X. Thripp - http://richardxthripp.thripp.com/tweet-this
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


class TweetThis
{
    /**
     * Install Tweet This
     */
    public function install_plugin($h)
    {
        // Plugin settings
        $tweet_this_settings = $h->getSerializedSettings();
        if (!isset($tweet_this_settings['tt_shortener'])) { $tweet_this_settings['tt_shortener'] = "isgd"; }
        if (!isset($tweet_this_settings['tt_bitly_login'])) { $tweet_this_settings['tt_bitly_login'] = ""; }
        if (!isset($tweet_this_settings['tt_bitly_api_key'])) { $tweet_this_settings['tt_bitly_api_key'] = ""; }
        if (!isset($tweet_this_settings['tt_use_GA_tracking'])) { $tweet_this_settings['tt_use_GA_tracking'] = "checked"; }
        $h->updateSetting('tweet_this_settings', serialize($tweet_this_settings));
    }
    
    /**
     * Display Twitter link
     */
    public function sb_base_show_post_extra_fields($h)
    {
        echo "<li><a class='tweet_this_link' href='" . $h->url(array('page'=>'tweet_this', 'id'=>$h->post->id)) . "' target='_blank'>";
        echo $h->lang['tweet_this'] . "</a></li>\n";
    }
    
    /**
     * Determine if the user has clicked Tweet This
     */
    public function theme_index_top($h)
    {
        if ($h->isPage('tweet_this')) {
            $this->tweetThisPost($h);
        }
    }
    
    /**
     * Build the link 
     */
    public function tweetThisPost($h)
    {
        // get the post's id from the url
        $post_id = $h->cage->get->testInt('id');
        
        // get the compressed url for this link
        $shortened_url = $this->getShortUrl($h, $post_id);
        
        // add the compressed link to the Twitter status update url
        $twitter_url = $this->getTwitterUrl($h, $post_id, $shortened_url); 

        // redirect to Twitter
        header("Location: " . $twitter_url);
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
            
            // get the post's url and encode it:
            $post_url = urlencode($h->url(array('page'=>$post_id)));
            
            // get settings so we know which shortener to use:
            $tweet_this_settings = $h->getSerializedSettings();

            // get the post's url and encode it:
           if ($tweet_this_settings['tt_use_GA_tracking']) // do we want GA tracking tags?
           {
                if (FRIENDLY_URLS == "false")    {
                    // friendly URLs are not enabled (add more query string parameters)
                    $post_url = urlencode($h->url(array('page'=>$post_id)) . '&utm_source=tweet-this&utm_medium=Twitter&utm_campaign=story-promotion' );
                }
                else 
                {   // friendly URLs are enabled (start with query string parameters)
                    $post_url = urlencode($h->url(array('page'=>$post_id)) . '?utm_source=tweet-this&utm_medium=Twitter&utm_campaign=story-promotion' );
                } 
            }
            else 
            {   
                // just send the URL without tracking tags
                $post_url = urlencode($h->url(array('page'=>$post_id)));
            }
            
            // which shortener do we use?
            switch($tweet_this_settings['tt_shortener']) {
                case 'tinyurl':
                    $url = file_get_contents('http://tinyurl.com/api-create.php?url=' . $post_url);
                    break;
                case 'bitly':
                    $url = $this->getBitlyLink($h, $post_url, $tweet_this_settings);
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
     * @param array $tweet_this_settings
     * @return string $url
     */
    public function getBitlyLink($h, $post_url, $tweet_this_settings)
    {
        // get our login and api key from the saved settings
        $bitly_login = $tweet_this_settings['tt_bitly_login'];
        $bitly_apikey = $tweet_this_settings['tt_bitly_api_key'];
        
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
     * Build the Twitter status update link
     *
     * @param int $post_id
     * @param string $shortened_url
     * @return string $url
     */
    public function getTwitterUrl($h, $post_id, $shortened_url)
    {
        $h->readPost($post_id);
        $title = html_entity_decode($h->post->title, ENT_QUOTES, "UTF-8");
        
        $orig_length = strlen($title); // get original title length
        if ($orig_length > 110) {
            $title = substr($title, 0, 100); // keep only the first 100 characters
            $title = substr($title, 0, strrpos($title,' ')); // keep everything up to the last space
            $title .= "..."; // adds some dots to show we've truncated it
        }
        $title = $title . " "; // 100 chars + "..." + " " = 104 chars, leaving 36 chars for the url
        $title = urlencode($title);
        
        // The final Twitter URL:
        $url = 'http://twitter.com/home/?status=' . $title . '+' . $shortened_url ;
        return $url;
    }
}
