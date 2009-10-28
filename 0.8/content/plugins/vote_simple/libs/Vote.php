<?php
/**
 * The Vote class contains some useful methods for voting
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

class Vote
{
    public $db;                         // database object
    
    
    /**
     * Build a $plugins object containing $db and $cage
     */
    public function __construct($db)
    {
        $this->db   = $db;
    }
    
    
    public function databaseVoteTable() 
    {
        // Create a new table column called "post_votes_up" if it doesn't already exist
        $exists = $this->db->column_exists('posts', 'post_votes_up');
        if (!$exists) {
            $this->db->query("ALTER TABLE " . TABLE_POSTS . " ADD post_votes_up smallint(11) NOT NULL DEFAULT '0' AFTER post_content");
        } 
        
        // Create a new table column called "post_votes_down" if it doesn't already exist
        $exists = $this->db->column_exists('posts', 'post_votes_down');
        if (!$exists) {
            $this->db->query("ALTER TABLE " . TABLE_POSTS . " ADD post_votes_down smallint(11) NOT NULL DEFAULT '0' AFTER post_votes_up");
        } 
        
        // Create a new empty table called "votes" if it doesn't already exist
        $exists = $this->db->table_exists('postvotes');
        if (!$exists) {
            //echo "table doesn't exist. Stopping before creation."; exit;
            $sql = "CREATE TABLE `" . DB_PREFIX . "postvotes` (
              `vote_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
              `vote_post_id` int(11) NOT NULL DEFAULT '0',
              `vote_user_id` int(11) NOT NULL DEFAULT '0',
              `vote_user_ip` varchar(32) NOT NULL DEFAULT '0',
              `vote_date` timestamp NOT NULL,
              `vote_type` varchar(32) NULL,
              `vote_rating` enum('positive','negative','alert') NULL,
              `vote_reason` tinyint(3) NOT NULL DEFAULT 0,
              `vote_updateby` int(20) NOT NULL DEFAULT 0,
               INDEX  (`vote_post_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Post Votes';";
            $this->db->query($sql); 
        }   
    }
}

?>