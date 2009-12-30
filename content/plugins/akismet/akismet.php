<?php
/**
 * name: Akismet
 * description: Anti-spam service
 * version: 0.4
 * folder: akismet
 * class: HotaruAkismet
 * type: antispam
 * requires: submit 1.9, comments 1.2
 * hooks: admin_plugin_settings, admin_sidebar_plugin_settings, install_plugin, comment_pre_add_comment, submit_step_3_pre_trackback, com_man_approve_comment, com_man_delete_comment, comments_delete_comment, post_man_status_new, post_man_status_top, post_man_status_buried, post_man_delete, submit_edit_post_change_status, submit_edit_post_delete, vote_post_status_buried
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
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

class HotaruAkismet
{
    /**
     * Default settings on install
     */
    public function install_plugin($h)
    {
        // Default settings 
        $akismet_settings = $h->getSerializedSettings();
        if (!isset($akismet_settings['akismet_use_posts'])) { $akismet_settings['akismet_use_posts'] = ""; }
        if (!isset($akismet_settings['akismet_use_comments'])) { $akismet_settings['akismet_use_comments'] = ""; }
        if (!isset($akismet_settings['akismet_key'])) { $akismet_settings['akismet_key'] = ""; }
        
        $h->updateSetting('akismet_settings', serialize($akismet_settings));
    }
    
    
    /**
     * Set up Akismet
     *
     * @return object
     */
    public function prepareAkismet($h)
    {
        $akismet_settings = $h->getSerializedSettings();
        
        $WordPressAPIKey    = $akismet_settings['akismet_key'];
        $MySiteURL          = BASEURL;
        
        require_once(PLUGINS . 'akismet/libs/Akismet.class.php');
        $akismet = new Akismet($h->cage, $MySiteURL ,$WordPressAPIKey);
        
        return $akismet;
    }
    
    
    /**
     * Displays "Akismet!" wherever the plugin hook is.
     */
    public function akismet($h, $username, $email, $website, $comment, $permalink, $type)
    {
        $akismet = $this->prepareAkismet($h);

        $akismet->setCommentAuthor($username);
        $akismet->setCommentAuthorEmail($email);
        $akismet->setCommentAuthorURL($website);
        $akismet->setCommentContent($h->db->escape($comment));
        $akismet->setPermalink($permalink);
        
        if ($type == 'ham')
        {
            $akismet->submitHam();  // falsely flagged as spam, but actually ham
        }
        elseif ($type == 'spam') 
        {
            $akismet->submitSpam();  // falsely flagged as ham, but actually spam
        } 
        else 
        {
            if($akismet->isCommentSpam()) {
                if ($type == 'comment') { $h->comment->status = 'pending'; }
                if ($type == 'post') { $h->post->status = 'pending'; }
            } else {
                if ($type == 'comment') { $h->comment->status = 'approved'; }
                if ($type == 'post') { $h->post->status = 'new'; }
            }
        }
    }
    

    /**
     * Call to Akimset before adding a comment
     */
    public function comment_pre_add_comment($h)
    {
        $akismet_settings = $h->getSerializedSettings();
        if (!$akismet_settings['akismet_use_comments']) { return false; }
        
        $username = $h->getUserNameFromId($h->comment->author);
        $email = $h->getEmailFromId($h->comment->author);
        $website = '';
        $comment = $h->comment->content;
        $permalink = $h->url(array('page'=>$h->post->id));
        
        $this->akismet($h, $username, $email, $website, $comment, $permalink, 'comment');
    }
    
    
    /**
     * Call to Akimset before adding a post
     */
    public function submit_step_3_pre_trackback($h)
    {
        $akismet_settings = $h->getSerializedSettings();
        if (!$akismet_settings['akismet_use_posts']) { return false; }
        
        $username = $h->getUserNameFromId($h->post->author);
        $email = $h->getEmailFromId($h->post->author);
        $website = $h->post->origUrl;   // the url being submitted
        $comment = $h->post->content;
        $permalink = '';    // There is no permalink since this post hasn't been submitted yet.
        
        $this->akismet($h, $username, $email, $website, $comment, $permalink, 'post');
    }


    /**
     * Tell Akismet this comment is HAM
     *
     * @param object $comment
     *
     * This hook is in Comment Manager Settings, just before individual comments are approved.
     */
    public function com_man_approve_comment($h, $comment)
    {
        $akismet_settings = $h->getSerializedSettings();
        if (!$akismet_settings['akismet_use_comments']) { return false; }
        
        $c = $comment[0]; 
        
        $username = $h->getUserNameFromId($h->post->author);
        $email = $h->getEmailFromId($h->post->author);
        $website = '';
        $comment = $c->content;
        $permalink = $h->url(array('page'=>$h->post->id));
        $this->akismet($h, $username, $email, $website, $comment, $permalink, 'ham');
    }
    
    
    /**
     * Tell Akismet this comment is SPAM
     *
     * @param object $comment
     *
     * This hook is in Comment Manager Settings, just before individual comments are deleted.
     */
    public function com_man_delete_comment($h, $comment)
    {
        $akismet_settings = $h->getSerializedSettings();
        if (!$akismet_settings['akismet_use_comments']) { return false; }
        
        $c = $comment[0]; 
        
        $username = $h->getUserNameFromId($h->post->author);
        $email = $h->getEmailFromId($h->post->author);
        $website = '';
        $comment = $c->content;
        $permalink = $h->url(array('page'=>$h->post->id));
        $this->akismet($h, $username, $email, $website, $comment, $permalink, 'spam');
    }
    
    
    /**
     * Tell Akismet this comment is SPAM
     *
     * This hook is in Comments, just before individual comments are deleted.
     */
    public function comments_delete_comment($h)
    {
        $akismet_settings = $h->getSerializedSettings();
        if (!$akismet_settings['akismet_use_comments']) { return false; }
        
        $username = $h->getUserNameFromId($h->post->author);
        $email = $h->getEmailFromId($h->post->author);
        $website = '';
        $comment = $h->comment->content;
        $permalink = $h->url(array('page'=>$h->post->id));
        $this->akismet($h, $username, $email, $website, $comment, $permalink, 'spam');
    }


    /**
     * Tell Akismet this post is HAM or SPAM
     */
    public function reportPostHamSpam($h, $type = 'ham')
    {
        $akismet_settings = $h->getSerializedSettings();
        if (!$akismet_settings['akismet_use_posts']) { return false; }
        
        $username = $h->getUserNameFromId($h->post->author);
        $email = $h->getEmailFromId($h->post->author);
        $website = $h->post->origUrl;
        $post_content = $h->post->content;
        $permalink = $h->url(array('page'=>$h->post->id));
        
        /* for testing:
        echo "username: " . $username . "<br />";
        echo "email: " . $email . "<br />";
        echo "website: " . $website . "<br />";
        echo "content: " . $post_content . "<br />";
        echo "permalink: " . $permalink . "<br />";
        echo "type: " . $type . "<br />";
        exit;
        */
        
        $this->akismet($h, $username, $email, $website, $post_content, $permalink, $type);
    }

    public function post_man_status_new($h) { $this->reportPostHamSpam($h, 'ham'); } // HAM
    public function post_man_status_top($h) { $this->reportPostHamSpam($h, 'ham'); } // HAM
    public function post_man_status_buried($h) { $this->reportPostHamSpam($h, 'spam'); } // SPAM
    public function post_man_delete($h) { $this->reportPostHamSpam($h, 'spam'); } // SPAM
    public function submit_edit_post_delete($h) { $this->reportPostHamSpam($h, 'spam'); } // SPAM
    public function vote_post_status_buried($h) { $this->reportPostHamSpam($h, 'spam'); } // SPAM
    
    
    /**
     * Tell Akismet this post is HAM or SPAM when editing a post
     */
    public function submit_edit_post_change_status($h)
    { 
        switch($h->post->status) {
            case 'top':
                $this->reportPostHamSpam($h, 'ham');
                break;
            case 'new':
                $this->reportPostHamSpam($h, 'ham');
                break;
            case 'buried':
                $this->reportPostHamSpam($h, 'spam');
                break;
            default:
                // do nothing
        }
    } 
}

?>
