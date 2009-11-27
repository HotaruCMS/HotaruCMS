<?php
/**
 * name: EVB
 * description: External Vote Button
 * version: 0.2
 * folder: evb
 * class: Evb
 * hooks: hotaru_header
 * requires: submit 1.4
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

class Evb extends PluginFunctions
{
    public function hotaru_header()
    {
        if ($this->hotaru->isPage('evb') && !$this->cage->get->keyExists('url'))
        {
            header("Location:" . BASEURL . "content/plugins/evb/get_url.php");
            die(); exit;
        }
        elseif ($this->hotaru->isPage('evb') && $this->cage->get->keyExists('url')) 
        {
            $this->hotaruEVB();
            die(); exit;
        }
    }
    
    
    public function hotaruEVB()
    {
        $this->includeLanguage();
        
        $url = $this->cage->get->testuri('url');
        $server = BASEURL; //enter the url to your Hotaru CMS installation (with trailing slash)
        
        $url = htmlspecialchars(strip_tags($url));
        
        $url1 = ""; $url2 = "";
        $slash_check = substr($url, -1);
        if ($slash_check == '/') {
            $url1 = substr($url, 0, -1);
            $url2 = $url;
        } else {
            $url1 = $url;
            $url2 = $url."/";
        }
        
        $query = "SELECT post_id, post_votes_up FROM " . TABLE_POSTS . " WHERE (post_orig_url = %s OR post_orig_url = %s) AND (post_status = %s OR post_status = %s) LIMIT 1";
        $sql = $this->db->prepare($query, urlencode($url1), urlencode($url2), 'top', 'new');
        $result = $this->db->get_row($sql);
        
        include_once(PLUGINS . 'submit/libs/Post.php');
        $this->hotaru->post = new Post($this->hotaru); // used to get post information

        $this->hotaru->vars['url'] = $url;
        
        if ($result) {
            $this->hotaru->vars['id'] = $result->post_id;
            $this->hotaru->vars['votes'] = $result->post_votes_up;
        }

        $output = "<link rel='stylesheet' type='text/css' href='" . BASEURL . "content/plugins/evb/css/evb.css' />";
        
        $output = "
            <style>
            .evb_wrap {background:url(" . BASEURL . "content/plugins/evb/images/vote.gif) no-repeat 0 0;position:absolute;top:0px;left:0px;width:54px;height:71px;text-align:center;font-size:85%;margin:0;padding:0;list-style:none;font-family:Arial, Helvetica, sans-serif; font-weight: bold;}
            .evb_top a {font-size:26px;letter-spacing:-1px;text-decoration:none;line-height:48px;padding:15px 0 6px 0}
            .evb_top a small {font-size:12px;letter-spacing:0px;text-decoration:none} 
            .evb_top a:link, .evb_top a:visited {color: #0D4F84;}
            .evb_top a:hover, .evb_top a:active {text-decoration:none;color: #0B8BEA;}
            
            .evb_bottom a:link,.evb_bottom a:visited, .evb_bottom span {display:block;padding:4px 0 5px 0;color: #0D4F84;font-size:12px; text-decoration:none}
            .evb_bottom a:hover {color: #0B8BEA}
            .evb_bottom span {color:#ccc;font-size:11px}
            </style>
        ";
        
        $output .= "<div>";
        $output .= "<ul class='evb_wrap'>";
        
        if ($result) { 
            $this->hotaru->post->readPost($this->hotaru->vars['id']); // need to read the post to get the proper url
            $output .= "<li class='evb_top'>";
            $output .= "<a href='" . $this->hotaru->url(array('page'=>$this->hotaru->vars['id'])) . "' title='Vote for this story on " . SITE_NAME . "' target='_blank'>";
            $output .= "<b>" . $this->hotaru->vars['votes'] . "</b></a>";
            $output .= "</li>";
            
            $output .= "<li class='evb_bottom'>";
            $output .= "<a href='" . $this->hotaru->url(array('page'=>$this->hotaru->vars['id'])) . "' target='_blank'>";
            $output .= $this->lang['evb_vote'] . "</a>";
            $output .= "</li>";
        } else {
            $output .= "<li class='evb_top'>";
            $output .= "<a href='" . BASEURL . "index.php?page=submit&url=" . $this->hotaru->vars['url']  . "' title='Submit this story to " . SITE_NAME . "' target='_blank'>";
            $output .= $this->lang['evb_go'] . "</a>";
            $output .= "</li>";
            
            $output .= "<li class='evb_bottom'>"; 
            $output .= "<a href='" . BASEURL . "index.php?page=submit&url=" . $this->hotaru->vars['url'] . "' title='Submit this story to " . SITE_NAME . "' target='_blank'>";
            $output .= $this->lang['evb_submit'] . "</a>";
            $output .= "</li>";
        }
            
        $output .= "</ul>";
        $output .= "</div>";
        
        echo $output;
    }

}

?>