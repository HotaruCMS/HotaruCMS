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
 */
 
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
                        <li><a name="autoreader_empty" href="#">Options</a></li>
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

        if(isset($_REQUEST['campaign_add']))
        {
          check_admin_referer('wpomatic-edit-campaign');

          if($this->errno)
            $data = $this->campaign_data;
          else
            $addedid = $this->adminProcessAdd();
        }

        $author_usernames = $this->getBlogUsernames();
        $campaign_add = true;
        include(WPOTPL . 'edit.php');
      }


      /**
   * Edit campaign section
   *
   *
   */
  function adminEdit($h)
  {
    $id = intval($_REQUEST['id']);
    if(!$id) die("Can't be called directly");

    if(isset($_REQUEST['campaign_edit']))
    {
      check_admin_referer('wpomatic-edit-campaign');

      $data = $this->campaign_data;
      $submitted = true;

      if(! $this->errno)
      {
        $this->adminProcessEdit($id);
        $edited = true;
        $data = $this->getCampaignData($id);
      }
    } else
      $data = $this->getCampaignData($id);

    $author_usernames = $this->getBlogUsernames();
    $campaign_edit = true;

    include(WPOTPL . 'edit.php');
  }

  function adminEditCategories($h, &$data, $parent = 0, $level = 0, $categories = 0)
  {
  	if ( !$categories )
  		$categories = get_categories(array('hide_empty' => 0));

    if(function_exists('_get_category_hierarchy'))
      $children = _get_category_hierarchy();
    elseif(function_exists('_get_term_hierarchy'))
      $children = _get_term_hierarchy('category');
    else
      $children = array();

  	if ( $categories ) {
  		ob_start();
  		foreach ( $categories as $category ) {
  			if ( $category->parent == $parent) {
  				echo "\t" . _wpo_edit_cat_row($category, $level, $data);
  				if ( isset($children[$category->term_id]) )
  					$this->adminEditCategories($data, $category->term_id, $level + 1, $categories );
  			}
  		}
  		$output = ob_get_contents();
  		ob_end_clean();

  		echo $output;
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

    $wpdb->query("DELETE FROM {$this->db['campaign']} WHERE id = $id");
    $wpdb->query("DELETE FROM {$this->db['campaign_feed']} WHERE campaign_id = $id");
    $wpdb->query("DELETE FROM {$this->db['campaign_word']} WHERE campaign_id = $id");
    $wpdb->query("DELETE FROM {$this->db['campaign_category']} WHERE campaign_id = $id");

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
   * Checks submitted campaign edit form for errors
   *
   *
   * @return array  errors
   */
  function adminCampaignRequest($h, $data)
  {

    # Main data
    $this->campaign_data = $this->campaign_structure;
    $this->campaign_data['main'] = array(
        'title'         => $data['campaign_title'],
        'active'        => isset($data['campaign_active']),
        'slug'          => $data['campaign_slug'],
        'template'      => (isset($data['campaign_templatechk']))
                            ? $data['campaign_template'] : null,
        'frequency'     => intval($data['campaign_frequency_d']) * 86400
                          + intval($data['campaign_frequency_h']) * 3600
                          + intval($data['campaign_frequency_m']) * 60,
        'cacheimages'   => (int) isset($data['campaign_cacheimages']),
        'feeddate'      => (int) isset($data['campaign_feeddate']),
        'posttype'      => $data['campaign_posttype'],
        'author'        => sanitize($data['campaign_author']),
        'comment_status' => $data['campaign_commentstatus'],
        'allowpings'    => (int) isset($data['campaign_allowpings']),
        'dopingbacks'   => (int) isset($data['campaign_dopingbacks']),
        'max'           => intval($data['campaign_max']),
        'linktosource'  => (int) isset($data['campaign_linktosource'])
    );

    // New feeds
    foreach($data['campaign_feed']['new'] as $i => $feed)
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

//    // Existing feeds.
    if(isset($data['id']))
    {
      $this->campaign_data['feeds']['edit'] = array();
      foreach($this->getCampaignFeeds(intval($data['id'])) as $feed)
        $this->campaign_data['feeds']['edit'][$feed->id] = $feed->url;
    }

//    // Categories
    if(isset($data['campaign_categories']))
    {
      foreach($data['campaign_categories'] as $category)
      {
        $id = intval($category);
        $this->campaign_data['categories'][] = $category;
      }
    }

//    # New categories
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
 //     $errors['basic'][] = __('You have to enter a campaign title', 'wpomatic');
      $this->errno++;
    }

    # Feeds
    $feedscount = 0;

    if(isset($this->campaign_data['feeds']['new'])) $feedscount += count($this->campaign_data['feeds']['new']);
    if(isset($this->campaign_data['feeds']['edit'])) $feedscount += count($this->campaign_data['feeds']['edit']);
    if(isset($this->campaign_data['feeds']['delete'])) $feedscount -= count($this->campaign_data['feeds']['delete']);

    if(!$feedscount)
    {
//      $errors['feeds'][] = __('You have to enter at least one feed', 'wpomatic');
      $this->errno++;
    } else {
      if(isset($this->campaign_data['feeds']['new']))
      {
        foreach($this->campaign_data['feeds']['new'] as $feed)
        {
          $simplepie = $this->fetchFeed($feed, true);
          if($simplepie->error())
          {
//            $errors['feeds'][] = sprintf(__('Feed <strong>%s</strong> could not be parsed (SimplePie said: %s)', 'wpomatic'), $feed, $simplepie->error());
            $this->errno++;
          }
        }
      }
    }

    # Categories
    if(! sizeof($this->campaign_data['categories']))
    {
//      $errors['categories'][] = __('Select at least one category', 'wpomatic');
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
//            $errors['rewrites'][] = __('There\'s an error with the supplied RegEx expression', 'wpomatic');
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
//      $errors['options'][] = __('Selected frequency is not valid', 'wpomatic');
      $this->errno++;
    }

    if(! ($this->campaign_data['main']['max'] === 0 || $this->campaign_data['main']['max'] > 0))
    {
//      $errors['options'][] = __('Max items should be a valid number (greater than zero)', 'wpomatic');
      $this->errno++;
    }

    if($this->campaign_data['main']['cacheimages'] && !is_writable($this->cachepath))
    {
//      $errors['options'][] = sprintf(__('Cache path (in <a href="%s">Options</a>) must be writable before enabling image caching.', 'wpomatic'), $this->adminurl . '&s=options' );
      $this->errno++;
    }

//    $this->errors = $errors;
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
    $this->adminProcessEdit($cid);
    return $cid;
  }

   /**
   * Cleans everything for the given id, then redoes everything
   *
   * @param integer $id           The id to edit
   */
  function adminProcessEdit($h,$id)
  {

    // If we need to execute a tool action we stop here
    if($this->adminProcessTools()) return;

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
        $this->addCampaignFeed($id, $feed);
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
    $author = get_userdatabylogin($this->campaign_data['main']['author']);
    $main['authorid'] = $author->ID;
    unset($main['author']);

    // Query
    $query = WPOTools::updateQuery($this->db['campaign'], $main, 'id = ' . intval($id));
    $wpdb->query($query);
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
      $this->adminUpdateCampaignPosts($id, array(
        'post_status' => $wpdb->escape($_REQUEST['campaign_tool_changetype'])
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
        $this->adminUpdateCampaignPosts($id, array('post_author' => $authorid));
      } else {
        $this->errno = 1;
        $this->errors = array('tools' => array(sprintf(__('Author %s not found', 'wpomatic'), attribute_escape($_REQUEST['campaign_tool_changeauthor']))));
      }

      $this->tool_success = __('Posts status updated', 'wpomatic');
      return true;
    }

    return false;
  }

  function adminUpdateCampaignPosts($h,$id, $properties)
  {

    $posts = $this->getCampaignPosts($id);

    foreach($posts as $post)
       $h->db->query(WPOTools::updateQuery($wpdb->posts, $properties, "ID = {$post->id}"));
  }

/**
   * Parses a feed with SimplePie
   *
   * @param   boolean     $stupidly_fast    Set fast mode. Best for checks
   * @param   integer     $max              Limit of items to fetch
   * @return  SimplePie_Item    Feed object
   **/
  function fetchFeed($h,$url, $stupidly_fast = false, $max = 0)
  {
    # SimplePie
    if(! class_exists('SimplePie'))
      require_once( BASEURL . 'libs/extensions/simplepie/simplepie.inc' );

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
   * Tests a feed
   *
   *
   */
  function adminTestfeed()
  {
    if(!isset($_REQUEST['url'])) return false;

    $url = $_REQUEST['url'];
    $feed = $this->fetchFeed($url, true);
    $works = ! $feed->error(); // if no error returned


         if($works): ?>
        <?php printf(__('The feed %s has been parsed successfully.', 'wpomatic'), $url) ?>
        <?php else: ?>
        <?php printf(__('The feed %s cannot be parsed. Simplepie said: %s', 'wpomatic'), $url, $works) ?>
        <?php endif ;

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













}

?>