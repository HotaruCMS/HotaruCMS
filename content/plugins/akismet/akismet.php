<?php
/**
 * name: Akismet
 * description: Anti-spam service
 * version: 0.3
 * folder: akismet
 * class: HotaruAkismet
 * requires: submit 1.4, comments 1.0
 * hooks: admin_plugin_settings, admin_sidebar_plugin_settings, install_plugin, comment_pre_add_comment, submit_step_3_pre_trackback, com_man_approve_comment, com_man_delete_comment, comments_delete_comment, post_man_status_new, post_man_status_top, post_man_status_buried, post_man_delete, submit_edit_post_change_status, submit_edit_post_delete, vote_post_status_buried
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

class HotaruAkismet extends PluginFunctions
{

    /**
     * Default settings on install
     */
    public function install_plugin()
    {
        // Default settings 
        $akismet_settings = $this->getSerializedSettings();
        if (!isset($akismet_settings['akismet_use_posts'])) { $akismet_settings['akismet_use_posts'] = ""; }
        if (!isset($akismet_settings['akismet_use_comments'])) { $akismet_settings['akismet_use_comments'] = ""; }
        if (!isset($akismet_settings['akismet_key'])) { $akismet_settings['akismet_key'] = ""; }
        
        $this->updateSetting('akismet_settings', serialize($akismet_settings));
        
        // Include language file. Also included in hotaru_header, but needed here so 
        // that the link in the Admin sidebar shows immediately after installation.
        $this->includeLanguage();
    }
    
    
    /**
     * Set up Akismet
     *
     * @return object
     */
    public function prepareAkismet()
    {
        $akismet_settings = $this->getSerializedSettings();
        
        $WordPressAPIKey    = $akismet_settings['akismet_key'];
        $MySiteURL          = BASEURL;
        
        require_once(PLUGINS . 'akismet/libs/Akismet.class.php');
        $akismet = new Akismet($this->cage, $MySiteURL ,$WordPressAPIKey);
        
        return $akismet;
    }
    
    
    /**
     * Displays "Akismet!" wherever the plugin hook is.
     */
    public function akismet($username, $email, $website, $comment, $permalink, $type)
    {
        $akismet = $this->prepareAkismet();

        $akismet->setCommentAuthor($username);
        $akismet->setCommentAuthorEmail($email);
        $akismet->setCommentAuthorURL($website);
        $akismet->setCommentContent($this->db->escape($comment));
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
                if ($type == 'comment') { $this->hotaru->comment->status = 'pending'; }
                if ($type == 'post') { $this->hotaru->post->status = 'pending'; }
            } else {
                if ($type == 'comment') { $this->hotaru->comment->status = 'approved'; }
                if ($type == 'post') { $this->hotaru->post->status = 'new'; }
            }
        }
    }
    

    /**
     * Call to Akimset before adding a comment
     */
    public function comment_pre_add_comment()
    {
        $akismet_settings = $this->getSerializedSettings();
        if (!$akismet_settings['akismet_use_comments']) { return false; }
        
        $user = new UserBase($this->hotaru);
        $username = $user->getUserNameFromId($this->hotaru->comment->author);
        $email = $user->getEmailFromId($this->hotaru->comment->author);
        $website = '';
        $comment = $this->hotaru->comment->content;
        $permalink = $this->hotaru->url(array('page'=>$this->hotaru->post->id));
        
        $this->akismet($username, $email, $website, $comment, $permalink, 'comment');
    }
    
    
    /**
     * Call to Akimset before adding a post
     */
    public function submit_step_3_pre_trackback()
    {
        $akismet_settings = $this->getSerializedSettings();
        if (!$akismet_settings['akismet_use_posts']) { return false; }
        
        $user = new UserBase($this->hotaru);
        $username = $user->getUserNameFromId($this->hotaru->post->author);
        $email = $user->getEmailFromId($this->hotaru->post->author);
        $website = $this->hotaru->post->origUrl;   // the url being submitted
        $comment = $this->hotaru->post->content;
        $permalink = '';    // There is no permalink since this post hasn't been submitted yet.
        
        $this->akismet($username, $email, $website, $comment, $permalink, 'post');
    }


    /**
     * Tell Akismet this comment is HAM
     *
     * @param object $comment
     *
     * This hook is in Comment Manager Settings, just before individual comments are approved.
     */
    public function com_man_approve_comment($comment)
    {
        $akismet_settings = $this->getSerializedSettings();
        if (!$akismet_settings['akismet_use_comments']) { return false; }
        
        $c = $comment[0]; 
        
        $user = new UserBase($this->hotaru);
        $username = $user->getUserNameFromId($this->hotaru->post->author);
        $email = $user->getEmailFromId($this->hotaru->post->author);
        $website = '';
        $comment = $c->content;
        $permalink = $this->hotaru->url(array('page'=>$this->hotaru->post->id));
        $this->akismet($username, $email, $website, $comment, $permalink, 'ham');
    }
    
    
    /**
     * Tell Akismet this comment is SPAM
     *
     * @param object $comment
     *
     * This hook is in Comment Manager Settings, just before individual comments are deleted.
     */
    public function com_man_delete_comment($comment)
    {
        $akismet_settings = $this->getSerializedSettings();
        if (!$akismet_settings['akismet_use_comments']) { return false; }
        
        $c = $comment[0]; 
        
        $user = new UserBase($this->hotaru);
        $username = $user->getUserNameFromId($this->hotaru->post->author);
        $email = $user->getEmailFromId($this->hotaru->post->author);
        $website = '';
        $comment = $c->content;
        $permalink = $this->hotaru->url(array('page'=>$this->hotaru->post->id));
        $this->akismet($username, $email, $website, $comment, $permalink, 'spam');
    }
    
    
    /**
     * Tell Akismet this comment is SPAM
     *
     * This hook is in Comments, just before individual comments are deleted.
     */
    public function comments_delete_comment()
    {
        $akismet_settings = $this->getSerializedSettings();
        if (!$akismet_settings['akismet_use_comments']) { return false; }
        
        $user = new UserBase($this->hotaru);
        $username = $user->getUserNameFromId($this->hotaru->post->author);
        $email = $user->getEmailFromId($this->hotaru->post->author);
        $website = '';
        $comment = $this->hotaru->comment->content;
        $permalink = $this->hotaru->url(array('page'=>$this->hotaru->post->id));
        $this->akismet($username, $email, $website, $comment, $permalink, 'spam');
    }


    /**
     * Tell Akismet this post is HAM or SPAM
     */
    public function reportPostHamSpam($type = 'ham')
    {
        $akismet_settings = $this->getSerializedSettings();
        if (!$akismet_settings['akismet_use_posts']) { return false; }
        
        $user = new UserBase($this->hotaru);
        $username = $user->getUserNameFromId($this->hotaru->post->author);
        $email = $user->getEmailFromId($this->hotaru->post->author);
        $website = $this->hotaru->post->origUrl;
        $post_content = $this->hotaru->post->content;
        $permalink = $this->hotaru->url(array('page'=>$this->hotaru->post->id));
        
        /* for testing:
        echo "username: " . $username . "<br />";
        echo "email: " . $email . "<br />";
        echo "website: " . $website . "<br />";
        echo "content: " . $post_content . "<br />";
        echo "permalink: " . $permalink . "<br />";
        echo "type: " . $type . "<br />";
        exit;
        */
        
        $this->akismet($username, $email, $website, $post_content, $permalink, $type);
    }

    public function post_man_status_new() { $this->reportPostHamSpam('ham'); } // HAM
    public function post_man_status_top() { $this->reportPostHamSpam('ham'); } // HAM
    public function post_man_status_buried() { $this->reportPostHamSpam('spam'); } // SPAM
    public function post_man_delete() { $this->reportPostHamSpam('spam'); } // SPAM
    public function submit_edit_post_delete() { $this->reportPostHamSpam('spam'); } // SPAM
    public function vote_post_status_buried() { $this->reportPostHamSpam('spam'); } // SPAM
    
    
    /**
     * Tell Akismet this post is HAM or SPAM when editing a post
     */
    public function submit_edit_post_change_status()
    { 
        switch($this->hotaru->post->status) {
            case 'top':
                $this->reportPostHamSpam('ham');
                break;
            case 'new':
                $this->reportPostHamSpam('ham');
                break;
            case 'buried':
                $this->reportPostHamSpam('spam');
                break;
            default:
                // do nothing
        }
    } 
}

?>
