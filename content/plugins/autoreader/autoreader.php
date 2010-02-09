<?php
/**
 * name: Autoreader
 * description: Enables reading of RSS feeds and populating database
 * version: 0.1
 * folder: autoreader
 * class: Autoreader
 * type: autoreader
 * hooks: install_plugin, admin_header_include, admin_plugin_settings, admin_sidebar_plugin_settings, admin_plugin_dropdown_menu
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

require_once(PLUGINS . 'autoreader/autoreader_settings.php');

class Autoreader extends AutoreaderSettings
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
        if (!isset($autoreader_settings['log_actions'])) { $autoreader_settings['log_actions'] = true; }
        if (!isset($autoreader_settings['log_stdout'])) { $autoreader_settings['log_stdout'] = false; }
        if (!isset($autoreader_settings['log_unixcron'])) { $autoreader_settings['log_unixcron'] = false; }
        if (!isset($autoreader_settings['log_croncode'])) { $autoreader_settings['log_croncode'] = 0; }
        if (!isset($autoreader_settings['log_cacheimage'])) { $autoreader_settings['log_cacheimage'] = 0; }
        if (!isset($autoreader_settings['log_cachepath'])) { $autoreader_settings['log_cachepath'] = 'cache'; }

        $h->updateSetting('autoreader_settings', serialize($autoreader_settings));

        $this->activate($h);
    }



     /**
     * Constructor for autoreader
     */
    public function autoreader()
    {
         // Table names init
         $this->db = array(
          'campaign'            =>  DB_PREFIX . 'autoreader_campaign',
          'campaign_category'   =>  DB_PREFIX .  'autoreader_campaign_category',
          'campaign_feed'       =>  DB_PREFIX .  'autoreader_campaign_feed',
          'campaign_word'       =>  DB_PREFIX .  'autoreader_campaign_word',
          'campaign_post'       =>  DB_PREFIX .  'autoreader_campaign_post',
          'log'                 =>  DB_PREFIX .  'autoreader_log'
        );
    }


    /**
     * Called when autoreader plugin is first activated
     *
     *
     */
    public function activate($h, $force_install = false)
    {

    // write options to hotaru db if required
    //

    // only re-install if there is new version or plugin has been uninstalled
    if($force_install || ! $h->getPluginVersion() || $h->getPluginVersion() != $this->version)   
    {        
        # autoreader_campaign
        $exists = $h->db->table_exists($this->db['campaign']);
        if (!$exists) {
            $h->db->query ( "CREATE TABLE" . $this->db['campaign'] . " (
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
                           ) ENGINE=" . DB_ENGINE . " DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . " COMMENT='autoreader campaign'; "
            );
        }

		# autoreader_campaign_category
        $exists = $h->db->table_exists($this->db['campaign_category']);
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
        $exists = $h->db->table_exists($this->db['campaign_feed']);
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
        $exists = $h->db->table_exists($this->db['campaign_word']);
        if (!$exists) {
            $h->db->query ( "CREATE TABLE " . $this->db['campaign_word'] . " (
                            id int(11) unsigned NOT NULL auto_increment,
                              campaign_id int(11) NOT NULL,
                              feed_id int(11) NOT NULL,
                              post_id int(11) NOT NULL,
                                hash varchar(255) default '',
                              PRIMARY KEY  (id)
                          ) ENGINE=" . DB_ENGINE . " DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . " COMMENT='autoreader campaign feed'; "
                );
        }

         # autoreader_campaign_word
         $exists = $h->db->table_exists($this->db['campaign_post']);
         if (!$exists) {
            $h->db->query ( "CREATE TABLE " . $this->db['campaign_post'] . " (
                                id int(11) unsigned NOT NULL auto_increment,
                                  campaign_id int(11) NOT NULL,
                                  word varchar(255) NOT NULL default '',
                                    regex tinyint(1) default '0',
                                  rewrite tinyint(1) default '1',
                                  rewrite_to varchar(255) default '',
                                  relink varchar(255) default '',
                                  PRIMARY KEY  (id)
                              ) ENGINE=" . DB_ENGINE . " DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . " COMMENT='autoreader campaign feed'; "
                 );
         }

          # autoreader_log
          $exists = $h->db->table_exists($this->db['log']);
          if (!$exists) {
            $h->db->query ( "CREATE TABLE " . $this->db['log'] . " (
                                id int(11) unsigned NOT NULL auto_increment,
                                  message mediumtext NOT NULL default '',
                                  created_on datetime NOT NULL default '0000-00-00 00:00:00',
                                  PRIMARY KEY  (id)
                              ) ENGINE=" . DB_ENGINE . " DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE . " COMMENT='autoreader campaign feed'; "
                 );
          }


          //add_option('autoreader_version', $this->version, 'Installed version log');

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


    /**
    * Saves a log message to database
    *
    *
    * @param string  $message  Message to save
    */
      function log($h, $message)
      {
        $autoreader_settings = $h->getSerializedSettings();
        if ($autoreader_settings['log_actions'])

        if ($autoreader_settings['log_stdout'])
        {
          //$message = $h->$db->escape($message);
          //$time = current_time('mysql', true);
          //$h->$db->query("INSERT INTO {$this->db['log']} (message, created_on) VALUES ('{$message}', '{$time}') ");
        }
      }


      


 }
?>