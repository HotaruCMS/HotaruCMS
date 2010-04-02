<?php
/**
 * name: Link Bar Vote
 * description: Adds a vote button into the Link Bar
 * version: 0.1
 * folder: link_bar_vote
 * class: LinkBarVote
 * requires: link_bar 0.1, vote 1.4
 * hooks: link_bar_css_js, link_bar_post
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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
 * @copyright Copyright (c) 2010
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://hotarucms.com
 */
class LinkBarVote
{
    /**
     * Include javascript from Vote plugin
     */
    public function link_bar_css_js($h)
    {
        // this plugin requires the Vote plugin
        if (!$h->isActive('vote')) { return false; }
        
        // we also need to get the constant like BASEURL for use in javascript
        if (file_exists(CACHE . 'css_js_cache/JavascriptConstants.js')) {
            echo "<script type='text/javascript' src='" . BASEURL . "cache/css_js_cache/JavascriptConstants.js'></script>";
        }
        
        // and we need the javascript for the vote button itself
        if (file_exists(PLUGINS . 'vote/javascript/vote.js')) {
            echo "<script type='text/javascript' src='" . BASEURL . "content/plugins/vote/javascript/vote.js'></script>";
        }
    }
    
    
    /**
     * Override the default sort filters for the front page
     */
    public function link_bar_post($h)
    {
        // this plugin requires the Vote plugin
        if (!$h->isActive('vote')) { return false; }
        
        // determine where to return the user to after logging in:
        if (!$h->cage->get->keyExists('return')) {
            $return = urlencode($h->url(array('page'=>$h->post->id)));
        } else {
            $return = $h->cage->get->testUri('return'); // use existing return parameter
        }
        $h->vars['vote_login_url'] = BASEURL . "index.php?page=login&amp;return=" . $return;
        
        // check to see if the current user has voted for this post
        if ($h->currentUser->loggedIn) {
            $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_rating != %d";
            $h->vars['voted'] = $h->db->get_var($h->db->prepare($sql, $h->post->id, $h->currentUser->id, -999));
        } 
        
        // display the button
        $h->displayTemplate('lb_vote_button');
    }
}