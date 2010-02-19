<?php
/**
 * name: Comments Widget
 * description: Adds links in the sidebar to the latest comments on the site.
 * version: 0.2
 * folder: comments_widget
 * class: CommentsWidget
 * requires: widgets 0.6, comments 1.2
 * hooks: install_plugin, header_include, admin_sidebar_plugin_settings, admin_plugin_settings
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
 
class CommentsWidget
{
    /**
     *  Add default settings for Comments Widget plugin on installation
     */
    public function install_plugin($h)
    {
        // Default settings
        $comments_widget_settings = $h->getSerializedSettings();
        
        if ($h->isActive('avatar')) {
            if (!isset($comments_widget_settings['avatar'])) { $comments_widget_settings['avatar'] = "checked"; }
        } else {
            if (!isset($comments_widget_settings['avatar'])) { $comments_widget_settings['avatar'] = ""; }
        }
        if (!isset($comments_widget_settings['avatar_size'])) { $comments_widget_settings['avatar_size'] = 16; }
        if (!isset($comments_widget_settings['author'])) { $comments_widget_settings['author'] = ''; }
        if (!isset($comments_widget_settings['length'])) { $comments_widget_settings['length'] = 100; }
        if (!isset($comments_widget_settings['number'])) { $comments_widget_settings['number'] = 10; }
        
        $h->updateSetting('comments_widget_settings', serialize($comments_widget_settings));
        
        // widget
        $h->addWidget('comments_widget', 'comments', '');  // plugin name, function name, optional arguments
    }
    
    
    /**
     * Display comments in the sidebar
     */
    public function widget_comments($h)
    {
        // Get settings from database if they exist...
        $comments_widget_settings = $h->getSerializedSettings('comments_widget');
        
        $comments = $this->getCommentsWidget($h, $comments_widget_settings['number']);
        
        // build link that will link the widget title to all comments...
        
        $anchor_title = htmlentities($h->lang["comments_widget_title_anchor_title"], ENT_QUOTES, 'UTF-8');
        $title = "<a href='" . $h->url(array('page'=>'comments')) . "' title='" . $anchor_title . "'>";
        $title .= $h->lang['comments_widget_title'] . "</a>";
        
        if (isset($comments) && !empty($comments)) {
            
            $output = "<h2 class='widget_head comments_widget_title'>\n";

            $link = BASEURL;
            $output .= $title;
            $output .= "<a href='" . $h->url(array('page'=>'rss_comments')) . "' title='" . $anchor_title . "'>\n";
            $output .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_16.png' alt='rss' />\n</a>"; // RSS icon
            $output .= "</h2>\n"; 
                
            $output .= "<ul class='widget_body comments_widget_items'>\n";
            $output .= $this->getCommentsWidgetItems($h, $comments, $comments_widget_settings);
            $output .= "</ul>\n\n";
        }
        
        // Display the whole thing:
        if (isset($output) && $output != '') { echo $output; }
    }


    /**
     * Get Comments Widget
     *
     * return array $comments
     */
    public function getCommentsWidget($h, $limit)
    {
        $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_archived = %s AND comment_status = %s ORDER BY comment_date DESC LIMIT " . $limit;
        $comments = $h->db->get_results($h->db->prepare($sql, 'N', 'approved'));
        
        if ($comments) { return $comments; } else { return false; }
    }
    
    
    /**
     * Get sidebar comment items
     *
     * @param array $comments 
     * return string $output
     */
    public function getCommentsWidgetItems($h, $comments = array(), $comments_widget_settings)
    {
        $need_cache = false;
        
        // check for a cached version and use it if no recent update:
        $output = $h->smartCache('html', 'comments', 10);
        if ($output) {
            return $output;
        } else {
            $need_cache = true;
        }
        
        $author = new UserBase();
                
        if (!$comments) { return false; }
        
        foreach ($comments as $item)
        {
            // Post used in Hotaru's url function
            $h->readPost($item->comment_post_id);
            
            // Hide comments from widget if their post has been buried or set to pending:
            if ($h->post->status == 'pending' || $h->post->status == 'buried') { continue; }

            // get author details
            $author->getUserBasic($h, $item->comment_user_id);

            $h->post->vars['catSafeName'] =  $h->getCatSafeName($h->post->category);

            // OUTPUT COMMENT
            $output .= "<li class='comments_widget_item'>\n";
            
            if($h->isActive('avatar') && $comments_widget_settings['avatar']) {
                $h->setAvatar($author->id, $comments_widget_settings['avatar_size']);
                $output .= "<div class='comments_widget_avatar'>\n";
                $output .= $h->linkAvatar();
                $output .= "</div> \n";
            }
            
            if ($comments_widget_settings['author']) {
                $output .= "<a class='comments_widget_author' href='" . $h->url(array('user' => $author->name)) . "'>" . $author->name . "</a>: \n";
            }
            
            $output .= "<div class='comments_widget_content'>\n";
            $item_content = stripslashes(html_entity_decode(urldecode($item->comment_content), ENT_QUOTES,'UTF-8'));
            $item_content = truncate($item_content, $comments_widget_settings['length'], true);
            
            $h->comment->content = $item_content ; // make it available to other plugins
            $h->pluginHook('comments_widget_comment_content'); // hook for other plugins to edit the comment
            $item_content = $h->comment->content; // assign edited or unedited comment back to $content.
            
            $comment_link = $h->url(array('page'=>$item->comment_post_id)) . "#c" . $item->comment_id;
            $comment_tooltip = $h->lang["comments_widget_title_tooltip"] . $h->post->title;
            $anchor_title = htmlentities($comment_tooltip, ENT_QUOTES, 'UTF-8');
            $output .= "<a href='" . $comment_link . "' title='" . $comment_tooltip . "'>" . $item_content . "</a>\n</div>\n";
            $output .= "</li>\n\n";
        }
        
        if ($need_cache) {
            $h->smartCache('html', 'comments', 10, $output); // make or rewrite the cache file
        }
        
        return $output;
    }

}
?>