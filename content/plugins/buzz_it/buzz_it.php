<?php
/**
 * name: Buzz It
 * description: Send posts to Google Reader/Buzz
 * version: 0.2
 * folder: buzz_it
 * class: BuzzIt
 * hooks: install_plugin, admin_sidebar_plugin_settings, admin_plugin_settings, sb_base_show_post_extra_fields, theme_index_top, header_include
 * requires: sb_base 0.1
 * author: Kyle Carlson
 * authorurl: 
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


class BuzzIt
{
    /**
     * Install Buzz It
     */
    public function install_plugin($h)
    {
        // Plugin settings
        $buzz_it_settings = $h->getSerializedSettings();
        if (!isset($buzz_it_settings['bi_use_GA_tracking'])) { $buzz_it_settings['bi_use_GA_tracking'] = "checked"; }
        $h->updateSetting('buzz_it_settings', serialize($buzz_it_settings));
    }
    
    /**
     * Display Google Buzz link
     */
    public function sb_base_show_post_extra_fields($h)
    {
	    echo "<li><a class='buzz_it_link' href='" . $h->url(array('page'=>'buzz_it',    'id'=>$h->post->id)) . "' target='_blank'>";
	    echo $h->lang['buzz_it'] . "</a></li>\n";
    }
    
    /**
     * Determine if the user has clicked Buzz It
     */
    public function theme_index_top($h)
    {
        if ($h->isPage('buzz_it')) {
            $this->BuzzThisPost($h);
        }
    }
    
    /**
     * Build the link 
     */
    public function BuzzThisPost($h)
    {
        // get the post's id from the url
		$post_id = $h->cage->get->testInt('id');

		// read the post so we can easily pull the title & content into variables
		$h->readPost($post_id);
		        
		// get the post title & body so we can build the Buzz url
//		$post_title = str_replace(' ','+',$h->post->title);
		$post_title = $h->post->title;
        $post_content = $h->post->content;
		
        // get settings so we know whether to add tracking tags:
        $buzz_it_settings = $h->getSerializedSettings();

        // get the post's url and encode it:
       if ($buzz_it_settings['bi_use_GA_tracking'] == "checked") {  // do we want GA tracking tags?
			if (FRIENDLY_URLS == "false")	{
            	// friendly URLs are not enabled (add more query string parameters)
      			$post_url = urlencode($h->url(array('page'=>$post_id)) . '&utm_source=buzz-it&utm_medium=GoogleBuzz&utm_campaign=story-promotion' );
        	}
        	else { // friendly URLs are enabled (start with query string parameters)
      			$post_url = urlencode($h->url(array('page'=>$post_id)) . '?utm_source=buzz-it&utm_medium=GoogleBuzz&utm_campaign=story-promotion' );
  			} 
  		}
		else { // just send the URL without tracking tags
			$post_url = urlencode($h->url(array('page'=>$post_id)));
    	}	

        // build the link
        $buzz_url = "http://www.google.com/reader/link?url=" . $post_url . "&title=" . $post_title . "&snippet=" . urlencode($post_content) . "&srcURL=" . BASEURL . "&srcTitle=" . SITE_NAME; 
                
        // redirect to Google Reader
        header("Location: " . $buzz_url);
        exit;
    }

}