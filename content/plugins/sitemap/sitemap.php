<?php
/**
 * name: Sitemap
 * description: Produces a Sitemap for your site.
 * version: 0.6
 * folder:sitemap
 * hooks: install_plugin, admin_sidebar_plugin_settings, admin_plugin_settings, theme_index_top, sitemap_runcron
 * class: Sitemap
 * author: Justin Tiearney
 * authorurl: http://obzerver.com
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
 * @author    Justin Tiearney <admin@obzerver.com>
 * @copyright Copyright (c) 2010
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://obzerver.com
 */  

class Sitemap
{
	/*
	 * Setup the default settings
	 * */
	public function install_plugin($h)
	{
		// Get plugin settings if they exist
		$sitemap_settings = $h->getSerializedSettings();
		
		if (!isset($sitemap_settings['sitemap_location'])) { $sitemap_settings['sitemap_location'] = BASEURL; }
		if (!isset($sitemap_settings['sitemap_last_run'])) { $sitemap_settings['sitemap_last_run'] = 'Never'; }
		if (!isset($sitemap_settings['sitemap_last_pinged'])) { $sitemap_settings['sitemap_last_pinged'] = 'Never'; }
		if (!isset($sitemap_settings['sitemap_compress'])) { $sitemap_settings['sitemap_compress'] = ''; }
		if (!isset($sitemap_settings['sitemap_use_cron'])) { $sitemap_settings['sitemap_use_cron'] = ''; }
		if (!isset($sitemap_settings['sitemap_frequency'])) { $sitemap_settings['sitemap_frequency'] = 'weekly'; }
		if (!isset($sitemap_settings['sitemap_priority_baseurl'])) { $sitemap_settings['sitemap_priority_baseurl'] = '1.0'; }
		if (!isset($sitemap_settings['sitemap_priority_categories'])) { $sitemap_settings['sitemap_priority_categories'] = '0.8'; }
		if (!isset($sitemap_settings['sitemap_priority_posts'])) { $sitemap_settings['sitemap_priority_posts'] = '0.5'; }
		if (!isset($sitemap_settings['sitemap_password'])) { $sitemap_settings['sitemap_password'] = md5(rand()); }
		if (!isset($sitemap_settings['sitemap_ping_google'])) { $sitemap_settings['sitemap_ping_google'] = false; }
		if (!isset($sitemap_settings['sitemap_ping_bing'])) { $sitemap_settings['sitemap_ping_bing'] = false; }

		
		// Update plugin settings
		$h->updateSetting('sitemap_settings', serialize($sitemap_settings));
	}
	
	public function theme_index_top($h)
	{
		if($h->isPage('sitemap')){
			$this->sitemapSecurity($h);
			return true;
		}
	}
	
	public function sitemapSecurity($h)
	{
		// get settings from database
		$sitemap_settings = $h->getSerializedSettings();
		
		if(strcmp($sitemap_settings['sitemap_password'],$h->cage->get->getAlnum('passkey')) == 0) {
			$this->newSitemap($h);
		}
	}
	
	/*
	 * Builds and saves the sitemap to the server
	 * */
	public function newSitemap($h)
	{
		//Retrieve the links and last update time from the database
		$sql = "SELECT post_id, post_url, post_category, post_updatedts FROM ". TABLE_POSTS;
		$maps = $h->db->get_results($sql);
		
		//Retrieve categories from the database
		$sql_cat = "SELECT category_id, category_updatedts FROM ". TABLE_CATEGORIES;
		$maps_cat = $h->db->get_results($sql_cat);
		
		//Get settings from database
		$sitemap_settings = $h->getSerializedSettings();
		
		//In case the site has more than 50000 links or is larger than 10MB prepare an index file
		$sitemapIndex = '<?xml version="1.0" encoding="UTF-8"?>';
		$sitemapIndex .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		
		//Tracks the number of sitemap files
		$sitemapNum = 0;
		
		//Build the sitemap header
		$sitemap[$sitemapNum] = '<?xml version="1.0" encoding="UTF-8"?>';
		$sitemap[$sitemapNum] .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$sitemap[$sitemapNum] .='<url><loc>'. htmlentities(BASEURL) . '</loc><changefreq>daily</changefreq><priority>'.$sitemap_settings['sitemap_priority_baseurl'].'</priority></url>';
		
		/*
		 * If records were returned we have links to add. We must also check for sitemap limitations.
		 * A sitemap cannot handle more than 50000 links or be larger than 10MB.
		 * 
		 * */
		if( $maps && $maps_cat )
		{
			$count = 0;
			foreach($maps as $map)
			{
				$h->post->id = $map->post_id;
				$h->post->category = $map->post_category;
				$h->post->url = $map->post_url;
				//Format the date to ISO standards
				$datetime = date("c", strtotime($map->post_updatedts));
				//Stop it a bit early to be safe
				if($count < 49998 && strlen($sitemap[$sitemapNum]) < 10484000){
					$sitemap[$sitemapNum] .='<url>';
					$sitemap[$sitemapNum] .='<loc>'. htmlentities($h->url(array('page'=>$h->post->id))) . '</loc>';
					$sitemap[$sitemapNum] .='<lastmod>'.htmlentities($datetime).'</lastmod>';
					$sitemap[$sitemapNum] .='<changefreq>'.$sitemap_settings['sitemap_frequency'].'</changefreq>';
					$sitemap[$sitemapNum] .='<priority>'.$sitemap_settings['sitemap_priority_posts'].'</priority>';
					$sitemap[$sitemapNum] .='</url>';
				}else{
					$sitemap[$sitemapNum]  .= '</urlset>';
					$sitemapNum++;
					$count = 0;
					$sitemap[$sitemapNum] = '<?xml version="1.0" encoding="UTF-8"?>';
					$sitemap[$sitemapNum] .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
					$sitemap[$sitemapNum] .='<url>';
					$sitemap[$sitemapNum] .='<loc>'. htmlentities($h->url(array('page'=>$h->post->id))) . '</loc>';
					$sitemap[$sitemapNum] .='<lastmod>'.htmlentities($datetime).'</lastmod>';
					$sitemap[$sitemapNum] .='<changefreq>'.$sitemap_settings['sitemap_frequency'].'</changefreq>';
					$sitemap[$sitemapNum] .='<priority>'.$sitemap_settings['sitemap_priority_posts'].'</priority>';
					$sitemap[$sitemapNum] .='</url>';
				}
				$count++;
			}
			
			foreach($maps_cat as $cat)
			{
				$datetime_cat = date("c", strtotime($cat->category_updatedts));
				$sitemap[$sitemapNum] .='<url>';
				$sitemap[$sitemapNum] .='<loc>'. htmlentities( $h->url(array('category'=>$cat->category_id)) ) . '</loc>';
				$sitemap[$sitemapNum] .='<lastmod>'.htmlentities($datetime_cat).'</lastmod>';
				$sitemap[$sitemapNum] .='<changefreq>'.$sitemap_settings['sitemap_frequency'].'</changefreq>';
				$sitemap[$sitemapNum] .='<priority>'.$sitemap_settings['sitemap_priority_categories'].'</priority>';
				$sitemap[$sitemapNum] .='</url>';
			}
			
			$sitemap_settings['sitemap_last_run'] = date("F j, Y, g:i:s a");
		
			//Save the last run time
			$h->updateSetting('sitemap_settings', serialize($sitemap_settings));
		}
		
		$sitemap[$sitemapNum] .= '</urlset>';
		
		//Counts the sitemap files
		$subcount = 0;
		
		//Check for compression and save the sitemap(s) to file
		if(strcmp($sitemap_settings['sitemap_compress'],'checked') == 0)
		{
			foreach($sitemap as $submap){
				if($sitemapNum > 0){
					$sitemapName = BASEURL.'sitemap'.$subcount.'.gz';
					$sitemapIndex .= '<sitemap><loc>'.htmlentities($sitemapName).'</loc></sitemap>';
					$gz = gzopen(BASE.'sitemap'.$subcount.'.gz','w9') or die("Can't write file");
					gzwrite($gz, $submap);
					gzclose($gz);
					$subcount++;
				}else{
					$gz = gzopen(BASE.'sitemap.gz','w9') or die("Can't write file");
					gzwrite($gz, $submap);
					gzclose($gz);
				}
				
			}
			
			if($sitemapNum > 0){
				$sitemapIndex .= '</sitemapindex>';
				$gz = gzopen(BASE.'sitemap.gz','w9') or die("Can't write file");
				gzwrite($gz, $sitemapIndex);
				gzclose($gz);
			}

		}else
		{
			foreach($sitemap as $submap){
				if($sitemapNum > 0){
					$sitemapName = BASEURL.'sitemap'.$subcount.'.xml';
					$sitemapIndex .= '<sitemap><loc>'.htmlentities($sitemapName).'</loc></sitemap>';
					$fh = fopen(BASE.'sitemap'.$subcount.'.xml', 'w') or die("Can't write file");
					fwrite($fh, $submap);
					fclose($fh);
					$subcount++;
				}else{
					$fh = fopen(BASE.'sitemap.xml', 'w') or die("Can't write file");
					fwrite($fh, $submap);
					fclose($fh);
				}
				
			}
			
			if($sitemapNum > 0){
				$sitemapIndex .= '</sitemapindex>';
				$fh = fopen(BASE.'sitemap.xml', 'w') or die("Can't write file");
				fwrite($fh, $sitemapIndex);
				fclose($fh);
			}
		}

	}
	
	/**
	 * Generate a sitemap
	 */
	public function sitemap_runcron($h)
	{
		$sitemap_settings = $h->getSerializedSettings();
		$this->createSitemap($h);		
		$this->pingSites($h);
	}
	
	
	/*
	 * Creates the new sitemap
	 * */
	public function createSitemap($h)
	{
		//Get settings from database
		$sitemap_settings = $h->getSerializedSettings();
		$this->newSitemap($h);		
		$sitemap_settings['sitemap_last_run'] = date("F j, Y, g:i:s a");		
		//Save the last run time
		$h->updateSetting('sitemap_settings', serialize($sitemap_settings));
		
	}

	public function pingSites($h) {
	    $sitemap_settings = $h->getSerializedSettings();
	    $ext = $sitemap_settings['sitemap_compress'] == 'checked' ? 'gz' : xml;
	    $sitemap = 'sitemap.' . $ext;
	    $this->pingGoogle($h, $sitemap_settings, $sitemap);
	    $this->pingBing($h, $sitemap_settings, $sitemap);
	}

	//Ping Google
	public function pingGoogle($h, $sitemap_settings = array(), $sitemap = 'sitemap.xml') {
	    if ($sitemap_settings['sitemap_ping_google']) {		
		$pingUrl = "http://www.google.com/webmasters/sitemaps/ping?sitemap=" . urlencode($sitemap_settings['sitemap_location'] . $sitemap);
		
		$pingres = $this->getWebPage($pingUrl);
		
		if ($pingres['content'] == NULL || $pingres['content'] === false) {
		    $h->messages["Failed to ping Google: " . htmlspecialchars(strip_tags($pingres['content']))] = 'red';
		} else {		
		    $result = "success";
		    $h->messages['Google ' . $h->lang["sitemap_ping_success"]] = 'green';
		    $sitemap_settings['sitemap_last_pinged'] = date("F j, Y, g:i:s a");
		}
	    }
	    $h->updateSetting('sitemap_settings', serialize($sitemap_settings));
	}

	//Ping Bing
	public function pingBing($h, $sitemap_settings = array(), $sitemap = 'sitemap.xml') {
	    if ($sitemap_settings['sitemap_ping_bing']) {		
		$pingUrl = "http://www.bing.com/webmaster/ping.aspx?siteMap=" . urlencode($sitemap_settings['sitemap_location'] . $sitemap);

		$pingres = $this->getWebPage($pingUrl);

		if ($pingres['content'] == NULL || $pingres['content'] === false || strpos($pingres['content'], "Thanks for submitting your sitemap") === false) {
		    $h->messages["Failed to ping Bing: " . htmlspecialchars(strip_tags($pingres['content']))] = 'red';
		} else {		
		    $result = "success";
		    $h->messages['Bing ' . $h->lang["sitemap_ping_success"]] = 'green';
		    $sitemap_settings['sitemap_last_pinged'] = date("F j, Y, g:i:s a");
		}
	    }
	    $h->updateSetting('sitemap_settings', serialize($sitemap_settings));	    
	}
	
	/**
	 * Get a web file (HTML, XHTML, XML, image, etc.) from a URL.  Return an
	 * array containing the HTTP server response header fields and content.
	 * code from http://nadeausoftware.com/articles/2007/06/php_tip_how_get_web_page_using_curl
	 */
	public function getWebPage( $url, $timeout = 120 )
	{
	    $options = array(
		CURLOPT_RETURNTRANSFER => true,     // return web page
		CURLOPT_HEADER         => false,    // don't return headers
		CURLOPT_FOLLOWLOCATION => true,     // follow redirects
		CURLOPT_ENCODING       => "",       // handle all encodings
		CURLOPT_USERAGENT      => "sitemap", // who am i
		CURLOPT_AUTOREFERER    => true,     // set referer on redirect
		CURLOPT_CONNECTTIMEOUT => $timeout,      // timeout on connect
		CURLOPT_TIMEOUT        => $timeout,      // timeout on response
		CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	    );

	    $ch      = curl_init( $url );
	    curl_setopt_array( $ch, $options );
	    $content = curl_exec( $ch );
	    $err     = curl_errno( $ch );
	    $errmsg  = curl_error( $ch );
	    $header  = curl_getinfo( $ch );
	    curl_close( $ch );

	    $header['errno']   = $err;
	    $header['errmsg']  = $errmsg;
	    $header['content'] = $content;
	    return $header;
	}
}
?> 
