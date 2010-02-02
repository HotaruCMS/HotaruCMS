<?php
/**
 * name: Autoreader
 * description: Enables reading of RSS feeds and populating database
 * version: 0.1
 * folder: autoreader
 * class: Autoreader
 * type: autoreader
 * requires:
 * hooks: install_plugin, theme_index_top, header_include, admin_plugin_settings, admin_sidebar_plugin_settings
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

class Autoreader
{
     var $version = '0.2';
     var $newsetup = false;  // set to true only if this version requires db changes from last version

    /**
     * Install or Upgrade
     */
    public function install_plugin($h)
    {
        // ************
        // PERMISSIONS
        // ************




        // ************
        // SETTINGS
        // ************

        // Get settings from database if they exist...
        $autoreader_settings = $h->getSerializedSettings();

        // Default settings
        //if (!isset($comments_settings['comment_all_forms'])) { $comments_settings['comment_all_forms'] = "checked"; }


        $h->updateSetting('autoreader_settings', serialize($autoreader_settings));
    }


    /**
     * Define table name, include language file and creat global Comments object
     */
    public function theme_index_top($h)
    {
        // Create a new global object called "autoreader".
        //require_once(LIBS . 'Comment.php');
        $h->comment = new Autoreader();

        // Get settings from database if they exist...
        $autoreader_settings = $h->getSerializedSettings();

        // Assign settings to class member
        //$h->comment->avatars = $comments_settings['comment_avatars'];

        return false;
    }


    /**
     * Include css and JavaScript
     */
    public function header_include($h)
    {
        $h->includeCss('autoreader', 'autoreader');
        $h->includeJs('autoreader', 'autoreader');
    }


     /**
     * Constructor for autoreader
     */
    public function autoreader()
    {


     // Table names init
     $this->db = array(
      'campaign'            => $wpdb->prefix . 'autoreader_campaign',
      'campaign_category'   => $wpdb->prefix . 'autoreader_campaign_category',
      'campaign_feed'       => $wpdb->prefix . 'autoreader_campaign_feed',
      'campaign_word'       => $wpdb->prefix . 'autoreader_campaign_word',
      'campaign_post'       => $wpdb->prefix . 'autoreader_campaign_post',
      'log'                 => $wpdb->prefix . 'autoreader_log'
    );

    // Is installed ?
    $this->installed = get_option('autoreader_version');
    $this->setup = get_option('autoreader_setup');

    }


    /**
     * Called when autoreader plugin is first activated
     *
     *
     */
    public function activate($force_install = false)
    {

    // write options to hotaru db if required
    //

    // only re-install if there is new version or plugin has been uninstalled
    if($force_install || ! $this->installed || $this->installed != $this->version)
    {
        # autoreader_campaign
        dbDelta( "CREATE TABLE {$this->db['campaign']} (
                            id int(11) unsigned NOT NULL auto_increment,
                            title varchar(255) NOT NULL default '',
                            active tinyint(1) default '1',
                            slug varchar(250) default '',
                            template MEDIUMTEXT default '',
                          frequency int(5) default '180',
                            feeddate tinyint(1) default '0',
                            cacheimages tinyint(1) default '1',
                            posttype enum('publish','draft','private') NOT NULL default 'publish',
                            authorid int(11) default NULL,
                            comment_status enum('open','closed','registered_only') NOT NULL default 'open',
                            allowpings tinyint(1) default '1',
                            dopingbacks tinyint(1) default '1',
                            max smallint(3) default '10',
                            linktosource tinyint(1) default '0',
                            count int(11) default '0',
                            lastactive datetime NOT NULL default '0000-00-00 00:00:00',
                            created_on datetime NOT NULL default '0000-00-00 00:00:00',
                            PRIMARY KEY (id)
                       );" );

		# autoreader_campaign_category
        dbDelta(  "CREATE TABLE {$this->db['campaign_category']} (
  						    id int(11) unsigned NOT NULL auto_increment,
  							  category_id int(11) NOT NULL,
  							  campaign_id int(11) NOT NULL,
  							  PRIMARY KEY  (id)
  						 );" );

         # autoreader_campaign_feed
         dbDelta(  "CREATE TABLE {$this->db['campaign_feed']} (
                                id int(11) unsigned NOT NULL auto_increment,
                                  campaign_id int(11) NOT NULL default '0',
                                  url varchar(255) NOT NULL default '',
                                  type varchar(255) NOT NULL default '',
                                  title varchar(255) NOT NULL default '',
                                  description varchar(255) NOT NULL default '',
                                  logo varchar(255) default '',
                                  count int(11) default '0',
                                  hash varchar(255) default '',
                                  lastactive datetime NOT NULL default '0000-00-00 00:00:00',
                                  PRIMARY KEY  (id)
                             );" );

        # autoreader_campaign_post
        dbDelta(  "CREATE TABLE {$this->db['campaign_post']} (
                            id int(11) unsigned NOT NULL auto_increment,
                              campaign_id int(11) NOT NULL,
                              feed_id int(11) NOT NULL,
                              post_id int(11) NOT NULL,
                                hash varchar(255) default '',
                              PRIMARY KEY  (id)
                         );" );

         # autoreader_campaign_word
         dbDelta(  "CREATE TABLE {$this->db['campaign_word']} (
                                id int(11) unsigned NOT NULL auto_increment,
                                  campaign_id int(11) NOT NULL,
                                  word varchar(255) NOT NULL default '',
                                    regex tinyint(1) default '0',
                                  rewrite tinyint(1) default '1',
                                  rewrite_to varchar(255) default '',
                                  relink varchar(255) default '',
                                  PRIMARY KEY  (id)
                             );" );

             # autoreader_log
         dbDelta(  "CREATE TABLE {$this->db['log']} (
                                id int(11) unsigned NOT NULL auto_increment,
                                  message mediumtext NOT NULL default '',
                                  created_on datetime NOT NULL default '0000-00-00 00:00:00',
                                  PRIMARY KEY  (id)
                             );" );


          add_option('autoreader_version', $this->version, 'Installed version log');

          $this->installed = true;
        }






    }

    /**
    * Called when plugin is deactivated
    *
    *
    */
   function deactivate()
   {
   }


   /**
   * Uninstalls
   *
   *
   */
  function uninstall()
  {

    foreach($this->db as $table)
      $h->query("DROP TABLE {$table} ");

    // Delete options
    // if any options set in main hotaru tables delete them as well
  }

  /**
   * Checks that autoreader tables exist
   *
   *
   */
  function tablesExist()
  {

    foreach($this->db as $table)
    {
      if(! $h->query("SELECT * FROM {$table}"))
        return false;
    }

    return true;
  }















}

$autoreader = & new Autoreader();
?>