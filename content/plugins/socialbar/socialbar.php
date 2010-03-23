<?php
/**
 * name: SocialBar
 * description: Provides a Social link bar with iFrame
 * version: 0.1
 * folder: socialbar
 * class: SocialBar
 * type: socialbar
 * hooks: install_plugin, theme_index_top, sb_base_show_post_pre_title
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
 * @copyright Copyright (c) 2010
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://shibuya246.com
 */
class SocialBar
{
	/*
	 * Setup the default settings
	 * */
    public function install_plugin($h)
    {
        
    }  

    public function theme_index_top($h) {

         $access= false;

         if ($h->cage->get->keyExists('forward')) {
             $post_id = $h->cage->get->testInt('forward');
             $access = true;
         }

          if ($h->cage->get->keyExists('link')) {
             $post_id = $h->cage->get->testInt('link');
             $access = true;
         }

     if ( $access) {
            if ($post_id) {
                $h->vars['socialbar']['voters'] = $this->getWhoVoted($h, 5);

//                if ($h->isActive('gravatar')) {
//                    require_once(PLUGINS . 'gravatar/gravatar.php');
//                    $gravatar = new Gravatar();
//                 }

                $h->readPost($post_id);
                $this->getBar($h);
                die(); exit;
            }
        }
    }


    public function admin_theme_index_top($h) {       
        //$h->vars['cron_settings'] = $h->getSerializedSettings();             
        //$this->run_cron($h);
    }

    public function getBar($h){
            $h->displayTemplate('socialbar_top');
            return true;
    }

    public function sb_base_show_post_pre_title($h) {
       
        $data = $h->cage->post->testUri('url');
        $post_id = $h->post->id; 

        $h->post->origUrl = $h->url(array('page'=>$h->post->id, 'link'=>$h->post->id));
    }


     /**
     * Get related results from the database
     *
     * return array|false
     */
    public function getWhoVoted($h, $limit)
    {
        if ($limit) { $limit_text = " LIMIT " . $limit; } else { $limit_text = ''; }

        $sql = "SELECT " . TABLE_USERS . ".user_id, " . TABLE_USERS . ".user_username, " . TABLE_POSTVOTES . ".vote_user_id FROM " . TABLE_USERS . ", " . TABLE_POSTVOTES . " WHERE (" . TABLE_USERS . ".user_id = " . TABLE_POSTVOTES . ".vote_user_id) AND (" . TABLE_POSTVOTES . ".vote_rating > %d) AND (" . TABLE_POSTVOTES . ".vote_post_id = %d) ORDER BY " . TABLE_POSTVOTES . ".vote_date ASC" . $limit_text;
        $results = $h->db->get_results($h->db->prepare($sql, 0, $h->post->id));

        return $results;
    }

    
}