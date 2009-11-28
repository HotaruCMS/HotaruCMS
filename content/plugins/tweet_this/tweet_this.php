<?php
/**
 * name: Tweet This
 * description: Send posts to Twitter
 * version: 0.1
 * folder: tweet_this
 * class: TweetThis
 * hooks: install_plugin, admin_sidebar_plugin_settings, admin_plugin_settings, submit_show_post_extra_fields, theme_index_replace, header_include
 * requires: submit 1.6
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


class TweetThis extends PluginFunctions
{
    /**
     * Install Tweet This
     */
    public function install_plugin()
    {
        // Plugin settings
        $tweet_this_settings = $this->getSerializedSettings();
        if (!isset($tweet_this_settings['tt_shortener'])) { $tweet_this_settings['tt_shortener'] = "isgd"; }
        if (!isset($tweet_this_settings['tt_bitly_login'])) { $tweet_this_settings['tt_bitly_login'] = ""; }
        if (!isset($tweet_this_settings['tt_bitly_api_key'])) { $tweet_this_settings['tt_bitly_api_key'] = ""; }
        $this->updateSetting('tweet_this_settings', serialize($tweet_this_settings));
    }
    
    /**
     * Display Twitter link
     */
    public function submit_show_post_extra_fields()
    {
        $this->includeLanguage();
        echo "<li><a class='tweet_this_link' href='" . $this->hotaru->url(array('page'=>'tweet_this', 'id'=>$this->hotaru->post->id)) . "' target='_blank'>";
        echo $this->lang['tweet_this'] . "</a></li>\n";
    }
    
    /**
     * Determine if the user has clicked Tweet This
     */
    public function theme_index_replace()
    {
        if ($this->hotaru->isPage('tweet_this')) {
            $this->tweetThisPost();
        }
    }
    
    /**
     * Build the link 
     */
    public function tweetThisPost()
    {
        // get the post's id from the url
        $post_id = $this->cage->get->testInt('id');
        
        // get the compressed url for this link
        $shortened_url = $this->getShortUrl($post_id);
        
        // add the compressed link to the Twitter status update url
        $twitter_url = $this->getTwitterUrl($post_id, $shortened_url); 

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
    public function getShortUrl($post_id)
    {
        // Check the database to see if there's already a short link there.
        $query = "SELECT postmeta_value FROM " . TABLE_POSTMETA . " where postmeta_postid = %d AND postmeta_key = %s";
        $sql = $this->db->prepare($query, $post_id, 'compressed_url');
        $stored_short_link = $this->db->get_var($sql);
        
        if(!$stored_short_link) {
            // no short link in db. We need to create one:
            
            // get the post's url and encode it:
            $post_url = urlencode($this->hotaru->url(array('page'=>$post_id)));
            
            // get settings so we know which shortener to use:
            $tweet_this_settings = $this->getSerializedSettings();
            
            switch($tweet_this_settings['tt_shortener']) {
                case 'tinyurl':
                    $url = file_get_contents('http://tinyurl.com/api-create.php?url=' . $post_url);
                    break;
                case 'bitly':
                    $url = $this->getBitlyLink($post_url, $tweet_this_settings);
                    break;
                default:
                    $url = file_get_contents('http://is.gd/api.php?longurl=' . $post_url);
            }

            // then store it in the database:
            $query = "INSERT INTO " . TABLE_POSTMETA . " (postmeta_postid, postmeta_key, postmeta_value, postmeta_updateby) VALUES(%d, %s, %s, %d)";
            $sql = $this->db->prepare($query, $post_id, 'compressed_url', urlencode(trim($url)), $this->current_user->id);
            $this->db->query($sql);

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
    public function getBitlyLink($post_url, $tweet_this_settings)
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
            return $bitlyinfo['results'][urldecode($url)]['shortUrl'];
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
    public function getTwitterUrl($post_id, $shortened_url)
    {
        // build an object of Post class so we can get the post's title
        require_once(PLUGINS . 'submit/libs/Post.php');
        $post = new Post($this->hotaru);
        $post->readPost($post_id);
        $title = $post->title;
        
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