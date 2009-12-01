<?php
/**
 * name: Video Inc
 * description: Embeds submitted video urls into post descriptions 
 * version: 0.1
 * folder: video_inc
 * class: VideoInc
 * hooks: header_include, theme_index_replace, submit_show_post_content_list, submit_show_post_content_post
 * requires: submit 1.8, media_select 0.1
 *
 * Uses http://autoembed.com/
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

class VideoInc extends PluginFunctions
{
    public function theme_index_replace()
    {
        if (!$this->hotaru->isPage('video_inc')) { return false; }

        $url = $this->cage->get->testUri('url');
        
        $embed = $this->getCode($url);

        if (!$embed) { return false; }
        echo "\n<div style='width: 100%; background-color: #000; text-align: center;'>\n" . $embed . "</div>\n";
        exit;
    }
    
    
    public function submit_show_post_content_list()
    {
        if ($this->hotaru->post->vars['type'] != 'video') { return false; }
        
        include_once(PLUGINS . "video_inc/libs/AutoEmbed.class.php");
        $AE = new AutoEmbed();
        
        // If this url doesn't parse as a valid video link...
        if (!$AE->parseUrl($this->hotaru->post->origUrl) ) { return false; }
        
        // get an associated static image (if there is one)
        $imageURL = $AE->getImageURL();

        $video_inc_url = BASEURL . "index.php?page=video_inc&amp;url=" . urlencode($this->hotaru->post->origUrl);
        
        // echo the image
        if ($imageURL) {
            echo "<div class='video_inc_list'>\n";
            if ($this->isActive('thickbox')) {
                echo "<a href='" . $video_inc_url . "&height=320&width=560' class='thickbox'>\n"; 
                echo "<img src='" . $imageURL . "'></a>\n";
            } else {
                echo "<img src='" . $imageURL . "'>\n";
            }
            echo "</div>\n";
        }

    }
    
    public function submit_show_post_content_post()
    {
        if ($this->hotaru->post->vars['type'] != 'video') { return false; }
        
        $embed = $this->getCode($this->hotaru->post->origUrl);

        if (!$embed) { return false; }
        
        // embed the video
        echo "\n<div class='video_inc_post'>\n" . $embed . "</div>\n";
    }
    
    public function getCode($url)
    {
        include_once(PLUGINS . "video_inc/libs/AutoEmbed.class.php");
        $AE = new AutoEmbed();
        
        // If this url doesn't parse as a valid video link...
        if (!$AE->parseUrl($url) ) { return false; }
        
        $AE->setWidth(533);
        $AE->setHeight(300);
        return $AE->getEmbedCode();
    }
}
?>