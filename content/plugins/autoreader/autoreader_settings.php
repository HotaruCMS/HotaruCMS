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

  require_once(PLUGINS . 'autoreader/inc/tools.class.php' );
  require_once(PLUGINS . 'autoreader/helper/form.helper.php' );
  require_once(PLUGINS . 'autoreader/helper/tag.helper.php');

  
class AutoreaderSettings
{
     /**
     * Admin settings for the Tweet This plugin
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
                                <li><a name="autoreader_add" href="#">Add Campaign</a>
                                    <ul class="sub_menu">
                                        <li><a name="autoreader_add" href="#">Add Campaign</a></li>
                                        <li><a name="autoreader_list" href="#">List Campaigns</a></li>
                                     </ul>
                                </li>
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
                $autoreader_settings = $h->getSerializedSettings();
            }
        }
    }

    /**
     * Get Option Settings, update if required
     * 
     * 
     */
    public function getOptionSettings($h, $options = null)
    {
        print "---" . $h->plugin->folder;
        $autoreader_settings = $h->getSerializedSettings();

        // Default settings
        if (!isset($autoreader_settings['log_actions'])) { $autoreader_settings['wpo_log'] = true; }
        if (!isset($autoreader_settings['log_stdout'])) { $autoreader_settings['wpo_log_stdout'] = false; }
        if (!isset($autoreader_settings['log_unixcron'])) { $autoreader_settings['wpo_unixcron'] = false; }
        if (!isset($autoreader_settings['log_croncode'])) { $autoreader_settings['wpo_croncode'] = 0; }
        if (!isset($autoreader_settings['log_cacheimage'])) { $autoreader_settings['wpo_cacheimage'] = 0; }
        if (!isset($autoreader_settings['log_cachepath'])) { $autoreader_settings['wpo_cachepath'] = 'cache'; }

        if ($h->cage->post->testAlpha('action') == "save" ) {
            $array = array('saved' => 'true');
            $autoreader_settings['wpo_log'] = $h->cage->post->testAlnumLines('option_logging');
            $autoreader_settings['wpo_log_stdout'] =  $h->cage->post->testAlnumLines('option_log_stdout');
            $autoreader_settings['wpo_unixcron'] =  $h->cage->post->testAlnumLines('option_unixcron');
            $autoreader_settings['wpo_croncode'] =  $h->cage->post->testAlnumLines('option_croncode');
            $autoreader_settings['wpo_cacheimage'] = $h->cage->post->testAlnumLines('option_cachimage');
            $autoreader_settings['wpo_cachepath'] = $h->cage->post->testAlnumLines('option_cachepath');

            $h->updateSetting('autoreader_settings', serialize($autoreader_settings));
        }
        else { $array = $autoreader_settings; }
        
        return $array;
    }


   /**
   * Retrieves campaigns from database
   *
   *
   */
  public function getCampaigns($h, $args = '')
  {       
    $where ="";
  	if(! empty($search))
  	  $where .= " AND title LIKE '%{$search}%' ";
     $orderby =""; $ordertype=""; $limit="";
  	//if($unparsed)
  	//  $where .= " AND active = 1 AND (frequency + UNIX_TIMESTAMP(lastactive)) < ". (current_time('timestamp', true) - get_option('gmt_offset') * 3600) . " ";

  	$sql = "SELECT * FROM {$this->db['campaign']} WHERE 1 = 1 $where "
         . ""; //ORDER BY $orderby $ordertype $limit";

    //print $sql;  
    return $h->db->get_results($sql);  
  }


    /**
   * Retrieves feeds for a certain campaign
   *
   * @param   integer   $id     Campaign id
   */
  function getCampaignFeeds($h,$id)
  {
    return $h->db->get_results("SELECT * FROM {$this->db['campaign_feed']} WHERE campaign_id = $id");
  }


  /**
   * Retrieves all posts for a certain campaign
   *
   * @param   integer   $id     Campaign id
   */
  function getCampaignPosts($h, $id)
  {
    return $h->db->get_results("SELECT post_id FROM {$this->db['campaign_post']} WHERE campaign_id = $id ");
  }

  /**
   * Adds a feed by url and campaign id
   *
   *
   */
  function addCampaignFeed($h, $id, $feed)
  {

    $simplepie = $this->fetchFeed($feed, true);
    $url = $h->db->escape($simplepie->subscribe_url());

    // If it already exists, ignore it
    if(! $h->db->get_var("SELECT id FROM {$this->db['campaign_feed']} WHERE campaign_id = $id AND url = '$url' "))
    {
      $h->db->query(WPOTools::insertQuery($this->db['campaign_feed'],
        array('url' => $url,
              'title' => $h->db->escape($simplepie->get_title()),
              'description' => $h->db->escape($simplepie->get_description()),
              'logo' =>$h->db->escape($simplepie->get_image_url()),
              'campaign_id' => $id)
      ));  

      return  $h->db->insert_id;
    }

    return false;
  }







     /**
       * List campaigns section
       *
       *
       */
      public function adminList($h)
      {
        if(isset($_REQUEST['q']))
        {
          $q = $_REQUEST['q'];
          $campaigns = $this->getCampaigns('search=' . $q);
        } else
          $campaigns = $this->getCampaigns('orderby=CREATED_ON');
       
      }

      /**
       * Add campaign section
       *
       *
       */
      public function adminAdd($h)
      {
        $data = $this->campaign_structure;
        $data_add = $h->cage->post->testAlnumLines('campaign_add');

        if(isset($data_add))
        {         
          if($this->errno)
            $data = $this->campaign_data;
          else
            $addedid = $this->adminProcessAdd();
        }

        $author_usernames = $this->getBlogUsernames();
        $campaign_add = true;
       // include(WPOTPL . 'edit.php');
      }


      /**
   * Edit campaign section
   *
   *
   */
  function adminEdit($h)
  {
     $id = $h->cage->get->testInt('id');
    if(!$id) die("Can't be called directly");

//    if(isset($_REQUEST['campaign_edit']))
//    {
//      check_admin_referer('wpomatic-edit-campaign');
//
//      $data = $this->campaign_data;
//      $submitted = true;
//
//      if(! $this->errno)
//      {
//        $this->adminProcessEdit($h, $id);
//        $edited = true;
//        $data = $this->getCampaignData($h, $id);
//      }
//    } else
      $data = $this->getCampaignData($h, $id);

   // $author_usernames = $this->getBlogUsernames();
    $campaign_edit = true;

   // include(WPOTPL . 'edit.php');
    return $data;
  }

  function adminEditCategories($h, $data, $parent = 0, $level = 0, $categories = 0)
  {
  	if ( !$categories )       
        $args = array("orderby"=>"category_order", "order"=>"ASC");
  		$categories = $h->getCategories($args);
   
  	if ( $categories ) {

        require_once(LIBS . 'Category.php');
        $catObj = new Category();
        $depth = 1;

  		echo "<ul class='categories_widget'>\n";
        foreach ($categories as $cat) {
            $cat_level = 1;    // top level category.           
            if ($cat->category_safe_name != "all") {
                echo '<li class="required pad'.$depth.'">';
                if ($cat->category_parent > 1) {
                    $depth = $catObj->getCatLevel($h, $cat->category_id, $cat_level, $categories);
                    for($i=1; $i<$depth; $i++) {
                        echo "--- ";
                    }
                }
                $category = stripslashes(html_entity_decode(urldecode($cat->category_name), ENT_QUOTES,'UTF-8'));               
                echo radiobutton_tag('campaign_categories[]', $cat->category_id, in_array($cat->category_id, $data['categories']), 'id=category_' .$cat->category_id);
                echo "&nbsp;" . label_for('category_' .  $cat->category_id, $category) .  "</li>\n";
            }
        }
        echo "</ul>\n";
  	} else {
  		return false;
  	}

  }

  /**
   * Resets a campaign (sets post count to 0, forgets last parsed post)
   *
   *
   * @todo Make it ajax-compatible here and add javascript code
   */
  function adminReset($h)
  {
    $id = intval($_REQUEST['id']);

    if(! defined('DOING_AJAX'))
      check_admin_referer('reset-campaign_'.$id);

    // Reset count and lasactive
    $wpdb->query(WPOTools::updateQuery($this->db['campaign'], array(
      'count' => 0,
      'lastactive' => 0
    ), "id = $id"));

    // Reset feeds hashes, count, and lasactive
    foreach($this->getCampaignFeeds($id) as $feed)
    {
      $wpdb->query(WPOTools::updateQuery($this->db['campaign_feed'], array(
        'count' => 0,
        'lastactive' => 0,
        'hash' => ''
      ), "id = {$feed->id}"));
    }

    if(defined('DOING_AJAX'))
      die('1');
    else
      $this->adminList();
  }

  /**
   * Deletes a campaign
   *
   *
   */
  function adminDelete($h)
  {

    $id = intval($_REQUEST['id']);

    // If not called through admin-ajax.php
    if(! defined('DOING_AJAX'))
      check_admin_referer('delete-campaign_'.$id);

    $h->db->query("DELETE FROM {$this->db['campaign']} WHERE id = $id");
    $h->db->query("DELETE FROM {$this->db['campaign_feed']} WHERE campaign_id = $id");
    $h->db->query("DELETE FROM {$this->db['campaign_word']} WHERE campaign_id = $id");
    $h->db->query("DELETE FROM {$this->db['campaign_category']} WHERE campaign_id = $id");

    if(defined('DOING_AJAX'))
      die('1');
    else
      $this->adminList();
  }

  /**
   * Options section
   *
   *
   */
  function adminOptions($h)
  {

    if(isset($_REQUEST['update']))
    {
      update_option('wpo_unixcron',     isset($_REQUEST['option_unixcron']));
      update_option('wpo_log',          isset($_REQUEST['option_logging']));
      update_option('wpo_log_stdout',   isset($_REQUEST['option_logging_stdout']));
      update_option('wpo_cacheimages',  isset($_REQUEST['option_caching']));
      update_option('wpo_cachepath',    rtrim($_REQUEST['option_cachepath'], '/'));

      $updated = 1;
    }

    if(!is_writable($this->cachepath))
      $not_writable = true;

    include(WPOTPL . 'options.php');
  }


 /**
   * Called by cron.php to update the site
   *
   *
   */
  function runCron($h, $log = true)
  {
    $this->log($h, 'Running cron job');
    $this->processAll($h);
  }

  /**
   * Finds a suitable command to run cron
   *
   * @return string command
   **/
  function getCommand()
  {
    $commands = array(
      @WPOTools::getBinaryPath('curl'),
      @WPOTools::getBinaryPath('wget'),
      @WPOTools::getBinaryPath('lynx', ' -dump'),
      @WPOTools::getBinaryPath('ftp')
    );

    return WPOTools::pick($commands[0], $commands[1], $commands[2], $commands[3], '<em>{wget or similar command here}</em>');
  }

  /**
   * Determines what the title has to link to
   *
   * @return string new text
   **/
  function filterPermalink($h, $url)
  {
    // if from admin panel
    if($this->admin)
      return $url;

    if(get_the_ID())
    {
    	$campaignid = (int) get_post_meta(get_the_ID(), 'wpo_campaignid', true);

    	if($campaignid)
    	{
    	  $campaign = $this->getCampaignById($campaignid);
    	  if($campaign->linktosource)
    	    return get_post_meta(get_the_ID(), 'wpo_sourcepermalink', true);
    	}

    	return $url;
    }
  }




    /**
   * Processes all campaigns
   *
   */
  function processAll($h)
  {
    @set_time_limit(0);

    $campaigns = $this->getCampaigns($h, 'unparsed=1');

    foreach($campaigns as $campaign)
    {
      $this->processCampaign($h, $campaign);
    }
  }

  /**
   * Processes a campaign
   *
   * @param   object    $campaign   Campaign database object
   * @return  integer   Number of processed items
   */
  function processCampaign($h, &$campaign)
  {
    @set_time_limit(0);
    ob_implicit_flush();

    // Get campaign
    $campaign = is_numeric($campaign) ? $this->getCampaignById($h,$campaign) : $campaign;

    // Log
    $this->log($h, 'Processing campaign ' . $campaign->title . ' (ID: ' . $campaign->id . ')');

    // Get feeds
    $count = 0;
    $feeds = $this->getCampaignFeeds($h, $campaign->id);

    foreach($feeds as $feed)
      $count += $this->processFeed($h, $campaign, $feed);
    $h->db->query(WPOTools::updateQuery($this->db['campaign'], array(
      'count' => $campaign->count + $count,
      'lastactive' => time() //('mysql', true)
    ), "id = {$campaign->id}"));

    return $count;
  }

  /**
   * Processes a feed
   *
   * @param   $campaign   object    Campaign database object
   * @param   $feed       object    Feed database object
   * @return  The number of items added to database
   */

  function processFeed($h, &$campaign, &$feed)
  {   

    @set_time_limit(0);

    // Log
    $this->log($h, 'Processing feed ' . $feed->title . ' (ID: ' . $feed->id . ')');

    // Access the feed
    $simplepie = $this->fetchFeed($feed->url, false, $campaign->max);

    // Get posts (last is first)
    $items = array();
    $count = 0;

    foreach($simplepie->get_items() as $item)
    {
      if($feed->hash == $this->getItemHash($item))
      {
        if($count == 0) $this->log($h, 'No new posts');
        break;
      }

      if($this->isDuplicate($h, $campaign, $feed, $item))
      {
        $this->log($h, 'Filtering duplicate post');
        break;
      }

      $count++;
      array_unshift($items, $item);

      if($count == $campaign->max)
      {
        $this->log($h, 'Campaign fetch limit reached at ' . $campaign->max);
        break;
      }
    }

    // Processes post stack
    foreach($items as $item)
    {
      $this->processItem($h, $campaign, $feed, $item);
      $lasthash = $this->getItemHash($item);
    }

    // If we have added items, let's update the hash
    if($count)
    {
      $h->db->query(WPOTools::updateQuery($this->db['campaign_feed'], array(
        'count' => $count,
        'lastactive' => time(),//current_time('mysql', true),
        'hash' => $lasthash
      ), "id = {$feed->id}"));

      $this->log($h, $count . ' posts added' );
    }

    return $count;
  }


 /**
   * Processes an item
   *
   * @param   $item       object    SimplePie_Item object
   */
  function getItemHash($item)
  {    
    return sha1($item->get_title() . $item->get_permalink());
  }


   /**
   * Processes an item
   *
   * @param   $campaign   object    Campaign database object
   * @param   $feed       object    Feed database object
   * @param   $item       object    SimplePie_Item object
   */
  function processItem($h, &$campaign, &$feed, &$item)
  {
    $this->log($h, 'Processing item');

    // Item content
    $content = $this->parseItemContent($h, $campaign, $feed, $item);

    // Item date
   /* if($campaign->feeddate && ($item->get_date('U') > (current_time('timestamp', 1) - $campaign->frequency) && $item->get_date('U') < current_time('timestamp', 1)))
      $date = $item->get_date('U');
    else
      $date = null;*/

	   if($campaign->feeddate)
     	 $date = $item->get_date('U');
    else
      $date = null;

    // Categories
    $categories = $this->getCampaignData($h, $campaign->id, 'categories');

    // Meta
    $permalink=$item->get_permalink();
    $root=$_SERVER['HTTP_HOST'];

//		$posturl=file_get_contents("http://$root/wp-content/plugins/wp-o-matic/original_url.php?blog=$permalink");

//    $posturl = $this->get_sourceurl($permalink);
$posturl =  $permalink;


    $meta = array(
      'wpo_campaignid' => $campaign->id,
      'wpo_feedid' => $feed->id,
      'wpo_sourcepermalink' =>$posturl,
//	  'wpo_website' => $wpo_website
    );
  
  
	//tags
    $post_tags=$item->get_categories();

	$tag_list="";
	if($post_tags)
	 {
		foreach($post_tags as $post_tag)
		 $tag_list.=$post_tag->term.",";
         $tag_list = trim($tag_list,',');
	 }
     
    // Create post
    $postid = $this->insertPost($h, $h->db->escape($item->get_title()), $h->db->escape($content), $date, $categories, $tag_list, $campaign->posttype, $campaign->authorid, $campaign->allowpings, $campaign->comment_status, $meta);

    
    /*
    // If pingback/trackbacks
    if($campaign->dopingbacks)
    {
      $this->log('Processing item pingbacks');

      require_once(ABSPATH . WPINC . '/comment.php');
    	pingback($content, $postid);
    }
*/

    // Save post to log database
    $h->db->query(WPOTools::insertQuery($this->db['campaign_post'], array(
      'campaign_id' => $campaign->id,
      'feed_id' => $feed->id,
      'post_id' => $postid,
      'hash' => $this->getItemHash($item)
    )));
  }



 /**
   * Processes an item
   *
   * @param   $campaign   object    Campaign database object
   * @param   $feed       object    Feed database object
   * @param   $item       object    SimplePie_Item object
   */
  function isDuplicate($h, &$campaign, &$feed, &$item)
  {
    $hash = $this->getItemHash($item);
    $row = $h->db->get_row("SELECT * FROM {$this->db['campaign_post']} "
                          . "WHERE campaign_id = {$campaign->id} AND feed_id = {$feed->id} AND hash = '$hash' ");
    return !! $row;
  }


 /**
   * Writes a post to blog
   *
   *
   * @param   string    $title            Post title
   * @param   string    $content          Post content
   * @param   integer   $timestamp        Post timestamp
   * @param   array     $category         Array of categories
   * @param   string    $status           'draft', 'published' or 'private'
   * @param   integer   $authorid         ID of author.
   * @param   boolean   $allowpings       Allow pings
   * @param   boolean   $comment_status   'open', 'closed', 'registered_only'
   * @param   array     $meta             Meta key / values
   * @return  integer   Created post id
   */
  function insertPost($h, $title, $content, $timestamp = null, $category = null, $tags= null, $status = 'pending', $authorid = null, $allowpings = true, $comment_status = 'open', $meta = array())
  {
    $date = ($timestamp) ? gmdate('Y-m-d H:i:s', $timestamp + (get_option('gmt_offset') * 3600)) : null;

    $h->post = new Post();

    $h->post->title =  $title;
    $h->post->url = make_url_friendly($title);
    $h->post->content = $content;
    $h->post->type = 'news';
    $h->post->category = "";
    $h->post->tags = $tags;
    $h->post->author = $h->currentUser->id;
    $h->post->status = $status;

    $h->addPost();


   /* $postid = wp_insert_post(array(
    	'post_title' 	            => $title,
  		'post_content'  	        => $content,
  		'post_content_filtered'  	=> $content,
  		'post_category'           => $category,
  		'post_status' 	          => $status,
  		'post_author'             => $authorid,
  		'post_date'               => $date,
  		'comment_status'          => $comment_status,
  		'ping_status'             => $allowpings
    ));

		foreach($meta as $key => $value)
			$this->insertPostMeta($postid, $key, $value);
*/
        $postid = $h->post->vars['last_insert_id'];

		return $postid;
  }

  /**
   * insertPostMeta
   *
   *
   */
	function insertPostMeta($h, $postid, $key, $value) {

		$result = $h->db->query( "INSERT INTO $h->db->postmeta (post_id,meta_key,meta_value ) "
					                . " VALUES ('$postid','$key','$value') ");
					
		return $h->db->insert_id;
	}













/**
   * Checks submitted campaign edit form for errors
   *
   *
   * @return array  errors
   */
  function adminCampaignRequest($h)
  {
    $data_active = $h->cage->post->testAlnumLines('campaign_active');
    $data_template = $h->cage->post->testAlnumLines('campaign_templatechk');
    $data_cacheimages = $h->cage->post->keyExists('campaign_cacheimages');
    $data_feeddate = $h->cage->post->keyExists('campaign_feeddate');
    $data_allowpings = $h->cage->post->keyExists('campaign_allowpings');
    $data_dopingbacks = $h->cage->post->keyExists('campaign_dopingbacks');
    $data_linktosource =  $h->cage->post->testInt('campaign_linktosource');

    # Main data
    $this->campaign_data = $this->campaign_structure;
    $this->campaign_data['main'] = array(
        'title'         => $h->cage->post->testAlnumLines('campaign_title'),
        'active'        => (isset( $data_active)),
        'slug'          => $h->cage->post->testAlnumLines('campaign_slug'),
        'template'      => (isset($data_template))
                            ? $data = $h->cage->post->testAlnumLines('campaign_template') : null,
        'frequency'     => intval($h->cage->post->testInt('campaign_frequency_d')) * 86400
                          + intval($h->cage->post->testInt('campaign_frequency_h')) * 3600
                          + intval($h->cage->post->testInt('campaign_frequency_m')) * 60,
        'cacheimages'   => (int) isset( $data_cacheimages),
        'feeddate'      => (int) isset( $data_feeddate),
        'posttype'      => $h->cage->post->testAlpha('campaign_posttype'),
        'author'        => $h->cage->post->testAlpha('campaign_author'),
        'comment_status' => $h->cage->post->testAlpha('campaign_commentstatus'),
        'allowpings'    => (int) isset($data_allowpings),
        'dopingbacks'   => (int) isset( $data_dopingbacks),
        'max'           => intval($h->cage->post->testInt('campaign_max')),
        'linktosource'  => (int) isset($data_linktosource)
    );

    // New feeds
    
    $results=($h->cage->post->getRaw('campaign_feed/new'));
   
    foreach( $results as $i => $feed)
    {
      $feed = trim($feed);

      if(!empty($feed))
      {
        if(!isset($this->campaign_data['feeds']['new']))
          $this->campaign_data['feeds']['new'] = array();

        $this->campaign_data['feeds']['new'][$i] = $feed;
      }
    }

    // Existing feeds to delete
    if(isset($data['campaign_feed']['delete']))
    {
      $this->campaign_data['feeds']['delete'] = array();

      foreach($data['campaign_feed']['delete'] as $feedid => $yes)
        $this->campaign_data['feeds']['delete'][] = intval($feedid);
    }

    // Existing feeds.
    if(isset($data['id']))
    {
      $this->campaign_data['feeds']['edit'] = array();
      foreach($this->getCampaignFeeds(intval($data['id'])) as $feed)
        $this->campaign_data['feeds']['edit'][$feed->id] = $feed->url;
    }

    // Categories
    if(isset($data['campaign_categories']))
    {
      foreach($data['campaign_categories'] as $category)
      {
        $id = intval($category);
        $this->campaign_data['categories'][] = $category;
      }
    }

    # New categories
    if(isset($data['campaign_newcat']))
    {
      foreach($data['campaign_newcat'] as $k => $on)
      {
        $catname = $data['campaign_newcatname'][$k];
        if(!empty($catname))
        {
          if(!isset($this->campaign_data['categories']['new']))
            $this->campaign_data['categories']['new'] = array();

          $this->campaign_data['categories']['new'][] = $catname;
        }
      }
    }

   // Rewrites
    if(isset($data['campaign_word_origin']))
    {
      foreach($data['campaign_word_origin'] as $id => $origin_data)
      {
        $rewrite = isset($data['campaign_word_option_rewrite'])
                && isset($data['campaign_word_option_rewrite'][$id]);
        $relink = isset($data['campaign_word_option_relink'])
                && isset($data['campaign_word_option_relink'][$id]);

        if($rewrite || $relink)
        {
          $rewrite_data = trim($data['campaign_word_rewrite'][$id]);
          $relink_data = trim($data['campaign_word_relink'][$id]);

          // Relink data field can't be empty
          if(($relink && !empty($relink_data)) || !$relink)
          {
            $regex = isset($data['campaign_word_option_regex'])
                  && isset($data['campaign_word_option_regex'][$id]);

            $data = array();
            $data['origin'] = array('search' => $origin_data, 'regex' => $regex);

            if($rewrite)
              $data['rewrite'] = $rewrite_data;

            if($relink)
              $data['relink'] = $relink_data;

            $this->campaign_data['rewrites'][] = $data;
          }
        }
      }
    }

    $errors = array('basic' => array(), 'feeds' => array(), 'categories' => array(),
                    'rewrite' => array(), 'options' => array());

    # Main
    if(empty($this->campaign_data['main']['title']))
    {
      $errors['basic'][] = 'You have to enter a campaign title';
      $this->errno++;
    }

    # Feeds
    $feedscount = 0;

    if(isset($this->campaign_data['feeds']['new'])) $feedscount += count($this->campaign_data['feeds']['new']);
    if(isset($this->campaign_data['feeds']['edit'])) $feedscount += count($this->campaign_data['feeds']['edit']);
    if(isset($this->campaign_data['feeds']['delete'])) $feedscount -= count($this->campaign_data['feeds']['delete']);

    if(!$feedscount)
    {
      $errors['feeds'][] ='You have to enter at least one feed';
      $this->errno++;
    } else {
      if(isset($this->campaign_data['feeds']['new']))
      {
        foreach($this->campaign_data['feeds']['new'] as $feed)
        {
          $simplepie = $this->fetchFeed($feed, true);
          if($simplepie->error())
          {
            $errors['feeds'][] = 'Feed <strong>' . $feed . '</strong> could not be parsed (SimplePie said: ' . $simplepie->error() . ')';
            $this->errno++;
          }
        }
      }
    }

    # Categories
    if(! sizeof($this->campaign_data['categories']))
    {
      $errors['categories'][] ='Select at least one category';
      $this->errno++;
    }

    # Rewrite
    if(sizeof($this->campaign_data['rewrites']))
    {
      foreach($this->campaign_data['rewrites'] as $rewrite)
      {
        if($rewrite['origin']['regex'])
        {
          if(false === @preg_match($rewrite['origin']['search'], ''))
          {
            $errors['rewrites'][] = 'There\'s an error with the supplied RegEx expression';
            $this->errno++;
          }
        }
      }
    }

    # Options
//    if(! get_userdatabylogin($this->campaign_data['main']['author']))
//    {
//      $errors['options'][] = __('Author username not found', 'wpomatic');
//      $this->errno++;
//    }

    if(! $this->campaign_data['main']['frequency'])
    {
      $errors['options'][] ='Selected frequency is not valid';
      $this->errno++;
    }

    if(! ($this->campaign_data['main']['max'] === 0 || $this->campaign_data['main']['max'] > 0))
    {
      $errors['options'][] ='Max items should be a valid number (greater than zero)';
      $this->errno++;
    }

    if($this->campaign_data['main']['cacheimages'] && !is_writable($this->cachepath))
    {
      $errors['options'][] = 'Cache path (in <a href="' . $this->adminurl . '&s=options">Options</a>) must be writable before enabling image caching.';
      $this->errno++;
    }

    $this->errors = $errors;
    return $errors;
    //print_r ( $this->errors);
    //exit;
  }

  /**
   * Creates a campaign, and runs processEdit. If processEdit fails, campaign is removed
   *
   * @return campaign id if created successfully, errors if not
   */
  function adminProcessAdd($h)
  {   
    // Insert a campaign with dumb data
    $h->db->query(WPOTools::insertQuery($this->db['campaign'], array('lastactive' => 0, 'count' => 0)));
    $cid = $h->db->insert_id;

    // Process the edit
    $this->campaign_data['main']['lastactive'] = 0;
    $this->adminProcessEdit($h,$cid);
    return $cid;
  }

   /**
   * Cleans everything for the given id, then redoes everything
   *
   * @param integer $id           The id to edit
   */
  function adminProcessEdit($h,$id)
  {
//print $id;
//print_r ($this->campaign_data);
    // If we need to execute a tool action we stop here
    if($this->adminProcessTools($h)) return;

    // Delete all to recreate
    $h->db->query("DELETE FROM {$this->db['campaign_word']} WHERE campaign_id = $id");
    $h->db->query("DELETE FROM {$this->db['campaign_category']} WHERE campaign_id = $id");

    // Process categories
    # New
    if(isset($this->campaign_data['categories']['new']))
    {
      foreach($this->campaign_data['categories']['new'] as $category)
        $this->campaign_data['categories'][] = wp_insert_category(array('cat_name' => $category));

      unset($this->campaign_data['categories']['new']);

     // print "new campaign";
    }

    # All
    foreach($this->campaign_data['categories'] as $category)
    {
      // Insert
      $wpdb->query(WPOTools::insertQuery($this->db['campaign_category'],
        array('category_id' => $category,
              'campaign_id' => $id)
      ));
    }

    // Process feeds
    # New
    if(isset($this->campaign_data['feeds']['new']))
    {
      foreach($this->campaign_data['feeds']['new'] as $feed)
        $this->addCampaignFeed($h, $id, $feed);
    }

    # Delete
    if(isset($this->campaign_data['feeds']['delete']))
    {
      foreach($this->campaign_data['feeds']['delete'] as $feed)
        $wpdb->query("DELETE FROM {$this->db['campaign_feed']} WHERE id = $feed ");
    }

    // Process words
    foreach($this->campaign_data['rewrites'] as $rewrite)
    {
      $wpdb->query(WPOTools::insertQuery($this->db['campaign_word'],
        array('word' => $rewrite['origin']['search'],
              'regex' => $rewrite['origin']['regex'],
              'rewrite' => isset($rewrite['rewrite']),
              'rewrite_to' => isset($rewrite['rewrite']) ? $rewrite['rewrite'] : '',
              'relink' => isset($rewrite['relink']) ? $rewrite['relink'] : null,
              'campaign_id' => $id)
      ));
    }

    // Main
    $main = $this->campaign_data['main'];

    // Fetch author id
    $main['authorid'] = $h->getUserIdFromName($this->campaign_data['main']['author']);
    unset($main['author']);

    // Query
    $query = WPOTools::updateQuery($this->db['campaign'], $main, 'id = ' . intval($id));
    $h->db->query($query);
  }

  /**
   * Processes edit campaign tools actions
   *
   *
   */
  function adminProcessTools($h)
  {
    $id = $h->cage->post->testAlnum('id');

    if($h->cage->post->testAlnumLines('tool_removeall'))
    {
      $posts = $this->getCampaignPosts($id);

      foreach($posts as $post)
      {
        $h->db->query("DELETE FROM {$wpdb->posts} WHERE ID = {$post->post_id} ");
      }

      // Delete log
      $h->db->query("DELETE FROM {$this->db['campaign_post']} WHERE campaign_id = {$id} ");

      // Update feed and campaign posts count
       $h->db->query(WPOTools::updateQuery($this->db['campaign'], array('count' => 0), "id = {$id}"));
       $h->db->query(WPOTools::updateQuery($this->db['campaign_feed'], array('hash' => 0, 'count' => 0), "campaign_id = {$id}"));

      $this->tool_success = __('All posts removed', 'wpomatic');
      return true;
    }

    if(isset($_REQUEST['tool_changetype']))
    {
      $this->adminUpdateCampaignPosts($h, $id, array(
        'post_status' => $h->db->escape($_REQUEST['campaign_tool_changetype'])
      ));

      $this->tool_success = __('Posts status updated', 'wpomatic');
      return true;
    }

    if(isset($_REQUEST['tool_changeauthor']))
    {
//      $author = get_userdatabylogin($_REQUEST['campaign_tool_changeauthor']);
        //if ($h->currentUser->

      if($author)
      {
        $authorid = $author->ID;
        $this->adminUpdateCampaignPosts($h, $id, array('post_author' => $authorid));
      } else {
        $this->errno = 1;
        $this->errors = array('tools' => array(print('Author' . attribute_escape($_REQUEST['campaign_tool_changeauthor'])).  ' not found' ));
      }

      $this->tool_success = 'Posts status updated';
      return true;
    }

    return false;
  }

  function adminUpdateCampaignPosts($h,$id, $properties)
  {
    $posts = $this->getCampaignPosts($h, $id);

    foreach($posts as $post)
       $h->db->query(WPOTools::updateQuery($h->db->posts, $properties, "ID = {$post->id}"));
  }

  /**
   * Parses an item content
   *
   * @param   $campaign       object    Campaign database object
   * @param   $feed           object    Feed database object
   * @param   $item           object    SimplePie_Item object
   */
  function parseItemContent($h, &$campaign, &$feed, &$item)
  {
    $content = $item->get_content();

    // Caching
    if ($campaign->cacheimages)   // set override here for all campaigns  get_option('wpo_cacheimages')
    {
      $images = WPOTools::parseImages($content);
      $urls = $images[2];

      if(sizeof($urls))
      {
        $this->log($h, 'Caching images');

        foreach($urls as $url)
        {
          $newurl = $this->cacheRemoteImage($url);
          if($newurl)
            $content = str_replace($url, $newurl, $content);
        }
      }
    }

    // cut images here
    preg_replace("/<img[^>]+\>/i", "", $content);

    

    // Template parse
    $vars = array(
      '{content}',
      '{title}',
      '{permalink}',
      '{feedurl}',
      '{feedtitle}',
      '{feedlogo}',
      '{campaigntitle}',
      '{campaignid}',
      '{campaignslug}'
    );

    $replace = array(
      $content,
      $item->get_title(),
      $item->get_link(),
      $feed->url,
      $feed->title,
      $feed->logo,
      $campaign->title,
      $campaign->id,
      $campaign->slug
    );

    $content = str_ireplace($vars, $replace, ($campaign->template) ? $campaign->template : '{content}');

    // Rewrite
    $rewrites = $this->getCampaignData($h, $campaign->id, 'rewrites');
    foreach($rewrites as $rewrite)
    {
      $origin = $rewrite['origin']['search'];

      if(isset($rewrite['rewrite']))
      {
        $reword = isset($rewrite['relink'])
                    ? '<a href="'. $rewrite['relink'] .'">' . $rewrite['rewrite'] . '</a>'
                    : $rewrite['rewrite'];

        if($rewrite['origin']['regex'])
        {
          $content = preg_replace($origin, $reword, $content);
        } else
          $content = str_ireplace($origin, $reword, $content);
      } else if(isset($rewrite['relink']))
        $content = str_ireplace($origin, '<a href="'. $rewrite['relink'] .'">' . $origin . '</a>', $content);
    }

    return $content;
  }

  /**
   * Cache remote image
   *
   * @return string New url
   */
  function cacheRemoteImage($h, $url)
  {
    $contents = @file_get_contents($url);

    $url=explode("?", $url );
	$url=$url[0];
    $filename = substr(md5(time()), 0, 5) . '_' . basename($url);

    $cachepath = $this->cachepath;

    if(is_writable($cachepath) && $contents)
    {
      file_put_contents($cachepath . '/' . $filename, $contents);
      return $this->pluginpath . '/' . get_option('wpo_cachepath') . '/' . $filename;
    }

    return false;
  }

/**
   * Parses a feed with SimplePie
   *
   * @param   boolean     $stupidly_fast    Set fast mode. Best for checks
   * @param   integer     $max              Limit of items to fetch
   * @return  SimplePie_Item    Feed object
   **/
  function fetchFeed($url, $stupidly_fast = false, $max = 0)
  {
    # SimplePie     
    if(! class_exists('SimplePie'))
      require_once( LIBS . 'extensions/SimplePie/simplepie.inc' );
 
    $feed = new SimplePie();
    $feed->enable_order_by_date(false); // thanks Julian Popov
    $feed->set_feed_url($url);
    $feed->set_item_limit($max);
    $feed->set_stupidly_fast($stupidly_fast);
    $feed->enable_cache(false);
    $feed->init();
    $feed->handle_content_type();

    return $feed;
  }

  /**
   * Returns all blog usernames (in form [user_login => display_name (user_login)] )
   *
   * @return array $usernames
   **/
  function getBlogUsernames()
  {
    $return = array();
    $users = get_users_of_blog();

    foreach($users as $user)
    {
      if($user->display_name == $user->user_login)
        $return[$user->user_login] = "{$user->display_name}";
      else
        $return[$user->user_login] = "{$user->display_name} ({$user->user_login})";
    }

    return $return;
  }


  /**
   * Returns all data for a campaign
   *
   *
   */
  function getCampaignData($h, $id, $section = null)
  {   
    $campaign = (array) $this->getCampaignById($h, $id);
//print_r ($campaign);
    if($campaign)
    {
      $campaign_data = $this->campaign_structure;

      // Main
      if(!$section || $section == 'main')
      {
        $campaign_data['main'] = array_merge($campaign_data['main'], $campaign);
//        $userdata = get_userdata($campaign_data['main']['authorid']);
//        $campaign_data['main']['author'] = $userdata->user_login;
      }

      // Categories
      if(!$section || $section == 'categories')
      {
        $categories = $h->db->get_results("SELECT * FROM {$this->db['campaign_category']} WHERE campaign_id = $id");
        if ($categories) {
        foreach($categories as $category)
          $campaign_data['categories'][] = $category->category_id;
        } else {
            }
      }

      // Feeds
      if(!$section || $section == 'feeds')
      {
        $campaign_data['feeds']['edit'] = array();

        $feeds = $this->getCampaignFeeds($h, $id);
        foreach($feeds as $feed)
          $campaign_data['feeds']['edit'][$feed->id] = $feed->url;
      }

      // Rewrites
      if(!$section || $section == 'rewrites')
      {
        $rewrites = $h->db->get_results("SELECT * FROM {$this->db['campaign_word']} WHERE campaign_id = $id");
        if ($rewrites) {
            foreach($rewrites as $rewrite)
            {
              $word = array('origin' => array('search' => $rewrite->word, 'regex' => $rewrite->regex), 'rewrite' => $rewrite->rewrite_to, 'relink' => $rewrite->relink);

              if(! $rewrite->rewrite) unset($word['rewrite']);
              if(empty($rewrite->relink)) unset($word['relink']);

              $campaign_data['rewrites'][] = $word;
            }
        }  
      }

      if($section)
        return $campaign_data[$section];

      return $campaign_data;
    }

    return false;
  }

  /**
   * Retrieves logs from database
   *
   *
   */
  function getLogs($h, $args = '')
  {   
    extract(WPOTools::getQueryArgs($args, array('orderby' => 'created_on',
                                                'ordertype' => 'DESC',
                                                'limit' => null,
                                                'page' => null,
                                                'perpage' => null)));
    if(!is_null($page))
    {
      if($page == 0) $page = 1;
      $page--;

      $start = $page * $perpage;
      $end = $start + $perpage;
      $limit = "LIMIT {$start}, {$end}";
    }

  	return $h->db->get_results("SELECT * FROM {$this->db['log']} ORDER BY $orderby $ordertype $limit");
  }

  /**
   * Retrieves a campaign by its id
   *
   *
   */
  function getCampaignById($h, $id)
  {
    $id = intval($id);
    return $h->db->get_row("SELECT * FROM {$this->db['campaign']} WHERE id = $id");
  }

  /**
   * Retrieves a feed by its id
   *
   *
   */
  function getFeedById($h, $id)
  {
    $id = intval($id);
    return $h->db->get_row("SELECT * FROM {$this->db['campaign_feed']} WHERE id = $id");
  }





  





   /**
   * Tests a feed
   *
   *
   */
  function adminTestfeed($url)
  {
     
    //if(!isset($_REQUEST['url'])) return false;

   // $url = $_REQUEST['url'];
    $feed = $this->fetchFeed($url, true);
    $works = ! $feed->error(); // if no error returned

     if($works):
        $json_array = array('result'=>'ok', 'url'=>$feed->feed_url );
     else:
        $json_array = array('result'=>'fail', 'error'=>$works );
     endif ;

     echo json_encode($json_array);

  }


  /**
   * Forcedfully processes a campaign
   *
   *
   */
  function adminForcefetch($h)
  {
    $cid = $h->cage->post->testInt('id');

    $this->forcefetched = $this->processCampaign($h,$cid);
    
    return $this->forcefetched;
  }






 /**
    * Saves a log message to database
    *
    *
    * @param string  $message  Message to save
    */
      function log($h, $message)
      {       
        $autoreader_settings = $h->getSerializedSettings('autoreader');
      
        if ($autoreader_settings['wpo_log_stdout'])
             echo $message;

        if ($autoreader_settings['wpo_log'])
        {
          $message = $h->db->escape($message);
          $time = time(); // current_time('mysql', true);
          $h->db->query("INSERT INTO {$this->db['log']} (message, created_on) VALUES ('{$message}', '{$time}') ");
        }
      }



}

?>