<?php
/**
 * name: Sidebar Comments
 * description: Adds links in the sidebar to the latest comments on the site.
 * version: 0.1
 * folder: sidebar_comments
 * class: SidebarComments
 * requires: sidebar_widgets 0.5, comments 1.0
 * hooks: install_plugin, hotaru_header, header_include, admin_sidebar_plugin_settings, admin_plugin_settings
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
 
return false; die(); // We don't want to just drop into the file.

class SidebarComments extends PluginFunctions
{
    /**
     *  Add default settings for Sidebar Comments plugin on installation
     */
    public function install_plugin()
    {
        // Default settings
        $sb_comments_settings = $this->getSerializedSettings();
        
        if ($this->isActive('gravatar')) {
            if (!isset($sb_comments_settings['sidebar_comments_avatar'])) { $sb_comments_settings['sidebar_comments_avatar'] = "checked"; }
        } else {
            if (!isset($sb_comments_settings['sidebar_comments_avatar'])) { $sb_comments_settings['sidebar_comments_avatar'] = ""; }
        }
        if (!isset($sb_comments_settings['sidebar_comments_avatar_size'])) { $sb_comments_settings['sidebar_comments_avatar_size'] = 16; }
        if (!isset($sb_comments_settings['sidebar_comments_author'])) { $sb_comments_settings['sidebar_comments_author'] = ''; }
        if (!isset($sb_comments_settings['sidebar_comments_length'])) { $sb_comments_settings['sidebar_comments_length'] = 100; }
        if (!isset($sb_comments_settings['sidebar_comments_number'])) { $sb_comments_settings['sidebar_comments_number'] = 10; }
        
        $this->updateSetting('sidebar_comments_settings', serialize($sb_comments_settings));
        
        // Default settings
        require_once(PLUGINS . 'sidebar_widgets/libs/Sidebar.php');
        $sidebar = new Sidebar($this->hotaru);
        // plugin name, function name, optional arguments
        $sidebar->addWidget('sidebar_comments', 'sidebar_comments', '');
    }
    
    
    /**
     * Display the top or latest posts in the sidebar
     *
     * @param $type either 'top' or 'new', matching the post_status in the db.
     */
    public function sidebar_widget_sidebar_comments()
    {
        $this->includeLanguage();
        
        // Get settings from database if they exist...
        $sb_comments_settings = $this->getSerializedSettings('sidebar_comments');
        
        $comments = $this->getSidebarComments($sb_comments_settings);
        
        // build link that will link the widget title to all comments...
        
        $anchor_title = htmlentities($this->lang["sidebar_comments_title_anchor_title"], ENT_QUOTES, 'UTF-8');
        $title = "<a href='" . $this->hotaru->url(array('page'=>'comments')) . "' title='" . $anchor_title . "'>";
        $title .= $this->lang['sidebar_comments_title'] . "</a>";
        
        if (isset($comments) && !empty($comments)) {
            
            $output = "<h2 class='sidebar_widget_head sidebar_comments_title'>\n";
            $output .= "<a href='" . $this->hotaru->url(array('page'=>'rss_comments')) . "' title='" . $anchor_title . "'>\n";
            $output .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_16.png'>\n</a>&nbsp;"; // RSS icon
            $link = BASEURL;
            $output .= $title . "</h2>\n"; 
                
            $output .= "<ul class='sidebar_widget_body sidebar_comments_items'>\n";
            
            $output .= $this->getSidebarCommentsItems($comments, $sb_comments_settings);
            $output .= "</ul>\n\n";
        }
        
        // Display the whole thing:
        if (isset($output) && $output != '') { echo $output; }
    }


    /**
     * Get sidebar posts
     *
     * return array $comments
     */
    public function getSidebarComments($sb_comments_settings)
    {
        $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_status = %s ORDER BY comment_date DESC LIMIT " . $sb_comments_settings['sidebar_comments_number'];
        $comments = $this->db->get_results($this->db->prepare($sql, 'approved'));
        
        if ($comments) { return $comments; } else { return false; }
    }
    
    
    /**
     * Get sidebar post items
     *
     * @param array $comments 
     * return string $ouput
     */
    public function getSidebarCommentsItems($comments = array(), $sb_comments_settings)
    {
        // we need categories for the url
        if ($this->hotaru->post->vars['useCategories']) {
            require_once(PLUGINS . 'categories/libs/Category.php');
            $cat = new Category($this->db);
        }
        
        $this->hotaru->post = new Post($this->hotaru); // used to get post information
        $author = new UserBase($this->hotaru);
                
        if (!$comments) { return false; }
        
        foreach ($comments as $item)
        {
            // Post used in Hotaru's url function
            $this->hotaru->post->readPost($item->comment_post_id);
            
            // get author details
            $author->getUserBasic($item->comment_user_id);
            
            if ($this->hotaru->post->vars['useCategories'] && ($this->hotaru->post->vars['category'] != 1)) {
                $this->hotaru->post->vars['category'] = $this->hotaru->post->vars['category'];
                $this->hotaru->post->vars['catSafeName'] =  $cat->getCatSafeName($this->hotaru->post->vars['category']);
            }

            // OUTPUT COMMENT
            $output .= "<li class='sidebar_comments_item'>\n";
            
            if ($sb_comments_settings['sidebar_comments_avatar'] && $this->isActive('gravatar')) {
                $this->hotaru->vars['gravatar_size'] = $sb_comments_settings['sidebar_comments_avatar_size'];
                $grav = new Gravatar('', $this->hotaru);
                $output .= "<div class='sidebar_comments_avatar'>\n" . $grav->showGravatarLink($author->name, $author->email, true) . "</div> \n";
            }
            
            if ($sb_comments_settings['sidebar_comments_author']) {
                $output .= "<a class='sidebar_comments_author' href='" . $this->hotaru->url(array('user' => $author->name)) . "'>" . $author->name . "</a>: \n";
            }
            
            $output .= "<div class='sidebar_comments_content'>\n";
            $item_content = stripslashes(html_entity_decode(urldecode($item->comment_content), ENT_QUOTES,'UTF-8'));
            $item_content = truncate($item_content, $sb_comments_settings['sidebar_comments_length'], true);
            $comment_link = $this->hotaru->url(array('page'=>$item->comment_post_id)) . "#c" . $item->comment_id;
            $comment_tooltip = $this->hotaru->lang["sidebar_comments_title_tooltip"] . $this->hotaru->post->title;
            $anchor_title = htmlentities($comment_tooltip, ENT_QUOTES, 'UTF-8');
            $output .= "<a href='" . $comment_link . "' title='" . $comment_tooltip . "'>" . $item_content . "</a>\n</div>\n";
            $output .= "</li>\n\n";
        }
        
        return $output;
    }

}
?>