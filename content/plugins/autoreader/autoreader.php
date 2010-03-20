<?php
/**
 * name: Autoreader
 * description: Enables reading of RSS feeds and populating database
 * version: 0.1
 * folder: autoreader
 * class: Autoreader
 * type: autoreader
 * hooks: install_plugin, admin_plugin_settings, admin_sidebar_plugin_settings, autoreader_runcron, admin_theme_index_top
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
 *
 * Ported from original WP Plugin WP-O-Matic
 * Description: Enables administrators to create posts automatically from RSS/Atom feeds.
 * Author: Guillermo Rauch
 * Plugin URI: http://devthought.com/wp-o-matic-the-wordpress-rss-agreggator/
 * Version: 1.0RC4-6
 *
 * Code modification for hotaru by shibuya246
 */

require_once(PLUGINS . 'autoreader/autoreader_settings.php');

class Autoreader extends AutoreaderSettings
{

     var $version = '0.2';
     var $newsetup = false;  // set to true only if this version requires db changes from last version

     var $campaign_structure = array('main' => array(), 'rewrites' => array(),
                                  'categories' => array(), 'feeds' => array());


     public function admin_theme_index_top($h) {
         print "top";
         if ($h->cage->get->testAlnumLines('plugin')  == 'autoreader') {
             print "here";
            $h->vars['autoreader_settings'] = $h->getSerializedSettings('autoreader');
            print_r( $h->vars['autoreader_settings'] );

                     // Table names init
         $this->db = array(
          'campaign'            =>  DB_PREFIX . 'autoreader_campaign',
          'campaign_category'   =>  DB_PREFIX .  'autoreader_campaign_category',
          'campaign_feed'       =>  DB_PREFIX .  'autoreader_campaign_feed',
          'campaign_word'       =>  DB_PREFIX .  'autoreader_campaign_word',
          'campaign_post'       =>  DB_PREFIX .  'autoreader_campaign_post',
          'log'                 =>  DB_PREFIX .  'autoreader_log'
        );

        $this->pluginpath = PLUGINS . '/autoreader/';
        $this->cachepath = $this->pluginpath . $h->vars['autoreader_settings']['wpo_cachepath'];

        # Cron command / url
        $this->cron_url = $this->pluginpath . '/cron.php?code=' .   $h->vars['autoreader_settings']['wpo_croncode'];
        $this->cron_command = '*/20 * * * * '. $this->getCommand() . ' ' . $this->cron_url;



         }
     }

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
        if (!isset($autoreader_settings['wpo_log'])) { $autoreader_settings['wpo_log'] = true; }
        if (!isset($autoreader_settings['wpo_log_stdout'])) { $autoreader_settings['wpo_log_stdout'] = false; }
        if (!isset($autoreader_settings['wpo_unixcron'])) { $autoreader_settings['wpo_unixcron'] = false; }       
        if (!isset($autoreader_settings['wpo_cacheimages'])) { $autoreader_settings['wpo_cacheimages'] = 0; }
        if (!isset($autoreader_settings['wpo_croncode'])) { $autoreader_settings['wpo_croncode'] = substr(md5(time()), 0, 8); }
        if (!isset($autoreader_settings['wpo_cachepath'])) { $autoreader_settings['wpo_cachepath'] = 'cache'; }
        if (!isset($autoreader_settings['wpo_help'])) { $autoreader_settings['wpo_help'] = false; }
        if (!isset($autoreader_settings['wpo_premium'])) { $autoreader_settings['wpo_premium'] = false; }

        $h->updateSetting('autoreader_settings', serialize($autoreader_settings));

        $this->activate($h);
    }



    /**
     * Called when autoreader plugin is first activated
     *
     *
     */
    public function activate($h, $force_install = false)
    {

    // only re-install if there is new version or plugin has been uninstalled
    if($force_install || ! $h->getPluginVersion() || $h->getPluginVersion() != $this->version)   
    {        
        # autoreader_campaign
        $exists = $h->db->table_exists(str_replace(DB_PREFIX, "", $this->db['campaign']));
        if (!$exists) {
            $h->db->query ( "CREATE TABLE " . $this->db['campaign'] . " (
                                id int(11) unsigned NOT NULL auto_increment,
                                title varchar(255) NOT NULL default '',
                                active tinyint(1) default '1',
                                slug varchar(250) default '',
                                template MEDIUMTEXT default '',
                                frequency int(5) default '180',
                                feeddate tinyint(1) default '0',
                                cacheimages tinyint(1) default '1',
                                posttype enum('new','pending','latest') NOT NULL default 'pending',
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
                           ) ENGINE=" . DB_ENGINE . " DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . " COMMENT='autoreader campaign'; "
            );
        }

		# autoreader_campaign_category
        $exists = $h->db->table_exists( str_replace(DB_PREFIX, "", $this->db['campaign_category']));
        if (!$exists) {
            $h->db->query ( "CREATE TABLE " . $this->db['campaign_category'] . " (
  						    id int(11) unsigned NOT NULL auto_increment,
  							  category_id int(11) NOT NULL,
  							  campaign_id int(11) NOT NULL,
  							  PRIMARY KEY  (id)
  						 ) ENGINE=" . DB_ENGINE . " DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . " COMMENT='autoreader campaign category'; "
                );
        }

        # autoreader_campaign_feed
        $exists = $h->db->table_exists(str_replace(DB_PREFIX, "", $this->db['campaign_feed']));
        if (!$exists) {
            $h->db->query ( "CREATE TABLE " . $this->db['campaign_feed'] . " (
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
                             ) ENGINE=" . DB_ENGINE . " DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . " COMMENT='autoreader campaign feed'; "
            );
        }

        # autoreader_campaign_post
        $exists = $h->db->table_exists(str_replace(DB_PREFIX, "", $this->db['campaign_post']));
        if (!$exists) {
            $h->db->query ( "CREATE TABLE " . $this->db['campaign_post'] . " (
                            id int(11) unsigned NOT NULL auto_increment,
                              campaign_id int(11) NOT NULL,
                              feed_id int(11) NOT NULL,
                              post_id int(11) NOT NULL,
                                hash varchar(255) default '',
                              PRIMARY KEY  (id)
                          ) ENGINE=" . DB_ENGINE . " DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . " COMMENT='autoreader campaign post'; "
                );
        }

         # autoreader_campaign_word
         $exists = $h->db->table_exists(str_replace(DB_PREFIX, "", $this->db['campaign_word']));
         if (!$exists) {
            $h->db->query ( "CREATE TABLE " . $this->db['campaign_word'] . " (
                                id int(11) unsigned NOT NULL auto_increment,
                                  campaign_id int(11) NOT NULL,
                                  word varchar(255) NOT NULL default '',
                                  regex tinyint(1) default '0',
                                  rewrite tinyint(1) default '1',
                                  rewrite_to varchar(255) default '',
                                  relink varchar(255) default '',
                                  PRIMARY KEY  (id)
                              ) ENGINE=" . DB_ENGINE . " DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . " COMMENT='autoreader campaign word'; "
                 );
         }

          # autoreader_log
          $exists = $h->db->table_exists(str_replace(DB_PREFIX, "", $this->db['log']));
          if (!$exists) {
            $h->db->query ( "CREATE TABLE " . $this->db['log'] . " (
                                id int(11) unsigned NOT NULL auto_increment,
                                  message mediumtext NOT NULL default '',
                                  created_on datetime NOT NULL default '0000-00-00 00:00:00',
                                  PRIMARY KEY  (id)
                              ) ENGINE=" . DB_ENGINE . " DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . " COMMENT='autoreader log'; "
                 );
          }


            # Options
            WPOTools::addMissingOptions(array(
             'wpo_log'          => array(1, 'Log WP-o-Matic actions'),
             'wpo_log_stdout'   => array(0, 'Output logs to browser while a campaign is being processed'),
             'wpo_unixcron'     => array(WPOTools::isUnix(), 'Use unix-style cron'),
             'wpo_croncode'     => array(substr(md5(time()), 0, 8), 'Cron job password.'),
             'wpo_cacheimages'  => array(0, 'Cache all images. Overrides campaign options'),
             'wpo_cachepath'    => array('cache', 'Cache path relative to wpomatic directory')
            ));

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
  function uninstall_plugin($h)
  {
    //might be best to prompt here before deleted tables

    //foreach($this->db as $table)
    //   $h->db->query("DROP TABLE {$table} ");

    // Delete options
    // delete data in plugins table related to this plugin
    //$autoreader_settings = $h->getSerializedSettings();
  }

    /**
    * Checks that autoreader tables exist
    * 
    *
    */
    function tablesExist($h)
    {
        foreach($this->db as $table)
        {
          if(!  $h->db->query("SELECT * FROM {$table}"))
            return false;
        }
        return true;
    }


   public function autoreader_runcron($h, $args) {
    $cid = $args['id'];

    $this->forcefetched = $this->processCampaign($h,$cid);
}
     

 }
?>