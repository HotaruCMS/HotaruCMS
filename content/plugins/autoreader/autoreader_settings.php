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
      










}
?>