<?php
/**
 * name: Media Select
 * description: Choose and filter to media type, i.e. news, videos or images 
 * version: 0.1
 * folder: media_select
 * class: MediaSelect
 * hooks: install_plugin, post_read_post_2, post_add_post, post_update_post, submit_form_2_assign, submit_form_2_fields, submit_form_2_process_submission, post_list_filter, category_bar_end
 * requires: submit 1.8
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

class MediaSelect extends PluginFunctions
{
    /**
     * Add a post_type field to posts table if it doesn't already exist
     */
    public function install_plugin()
    {
        // Create a new table column called "post_type" if it doesn't already exist
        $exists = $this->db->column_exists('posts', 'post_type');
        if (!$exists) {
            $this->db->query("ALTER TABLE " . TABLE_POSTS . " ADD post_type VARCHAR(20) NOT NULL DEFAULT 'news' AFTER post_updatedts");
        } 
    }
    
    
    /**
     * Read post type if post_id exists.
     */
    public function post_read_post_2()
    {
        $this->hotaru->post->vars['type'] = $this->hotaru->post->vars['post_row']->post_type;
    }
    
    
    /**
     * Add type the posts table
     */
    public function post_add_post()
    {
        $sql = "UPDATE " . TABLE_POSTS . " SET post_type = %s WHERE post_id = %d";
        $this->db->query($this->db->prepare($sql, $this->hotaru->post->vars['type'], $this->hotaru->post->vars['last_insert_id']));
    }
    
    
    /**
     * Update type the posts table
     */
    public function post_update_post()
    {
        $sql = "UPDATE " . TABLE_POSTS . " SET post_type = %s WHERE post_id = %d";
        $this->db->query($this->db->prepare($sql, urlencode(trim($this->hotaru->post->vars['type'])), $this->hotaru->post->id));
    }
    
    
    /**
     * Set $type_check to the value submitted through the form
     */
    public function submit_form_2_assign()
    {
        if ($this->cage->post->getAlpha('submit2') == 'true') {
            // Submitted this form...
            $this->hotaru->post->vars['type_check'] = $this->cage->post->keyExists('post_type');
            
        } elseif ($this->cage->post->getAlpha('submit3') == 'edit') {
            // Come back from step 3 to make changes...
            $this->hotaru->post->vars['type_check'] = $this->hotaru->post->vars['type'];
            
        } elseif ($this->hotaru->isPage('edit_post')) {
            // Editing a previously submitted post
            if ($this->cage->post->getAlpha('edit_post') == 'true') {
                $this->hotaru->post->vars['type_check'] = $this->cage->post->keyExists('post_type');
            } else {
                $this->hotaru->post->vars['type_check'] = $this->hotaru->post->vars['type'];
            }
            
        } else {
            // First time here...
            $this->hotaru->post->vars['type_check'] = 'news';
        }
    
    }
    
    /**
     * Add a tags field to submit form 2
     */
    public function submit_form_2_fields()
    {
        $this->includeLanguage();
        
        switch ($this->hotaru->post->vars['type_check']) {
            case 'video':
                $video = "checked"; $image = ""; $news = "";
                break;
            case 'image':
                $video = ""; $image = "checked"; $news = "";
                break;
            default:
                $video = ""; $image = ""; $news = "checked";
        }

        // radio buttons
        
        echo "<tr>\n";
        
            echo "<td>" . $this->lang["submit_form_type"] . "&nbsp; </td>\n";
            echo "<td colspan=2>\n";
        
            // news
            echo "<input type='radio' name='post_type' value='news' " . $news . " >";
            echo "&nbsp;&nbsp;" . $this->lang['media_select_news'] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n"; 
            
            // video
            echo "<input type='radio' name='post_type' value='video' " . $video . " >";
            echo "&nbsp;&nbsp;" . $this->lang['media_select_video'] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n"; 
            
            // image
            echo "<input type='radio' name='post_type' value='image' " . $image . " >";
            echo "&nbsp;&nbsp;" . $this->lang['media_select_image'] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n"; 
            
        echo "</tr>\n";
    }
    
    
    /**
     * Set $this->hotaru->post->post_tags to submitted string of tags
     */
    public function submit_form_2_process_submission()
    {
        $this->hotaru->post->vars['type'] = $this->cage->post->keyExists('post_type');
    }
    
    
    /**
     * Filter posts to a media type
     */
    public function post_list_filter()
    {
        if ($this->cage->get->keyExists('type')) 
        {
            $type = $this->cage->get->testAlnumLines('type'); 
            
            if ($type) {
                $this->hotaru->vars['filter']['post_type = %s'] = $type; 
                $this->hotaru->vars['filter']['post_archived = %s'] = 'N'; // don't include archived posts
                if ($this->hotaru->title == 'top') {
                    $rss = " <a href='" . $this->hotaru->url(array('page'=>'rss', 'type'=>$type, 'status'=>'top')) . "'>";
                } elseif ($this->hotaru->title == 'latest') {
                    $rss = " <a href='" . $this->hotaru->url(array('page'=>'rss', 'type'=>$type, 'status'=>'new')) . "'>";
                } else {
                    $rss = " <a href='" . $this->hotaru->url(array('page'=>'rss', 'type'=>$type)) . "'>";
                }
            }
            
            $this->includeLanguage();
            $media_word = "media_select_" . $type; // used below in $lang
            $media_word_link = "<a href='" . $this->hotaru->url(array('type'=>$type)) . "'>" . $this->lang[$media_word] . "</a>";
            
            /* The breadcrumb for top, latest and all, gets overwritten with our new breadcrumb, but we still need the 
               language for those filters so lets include it: */
            $this->includeLanguage('submit', 'submit');
            switch ($this->hotaru->title) {
                case 'latest':
                    $filter_word = $this->lang["post_breadcrumbs_latest"];
                    break;
                case 'upcoming':
                    $filter_word = $this->lang["post_breadcrumbs_upcoming"];
                    break;
                case 'all':
                    $filter_word = $this->lang["post_breadcrumbs_all"];
                    break;
                default:
                    $filter_word = $this->lang["post_breadcrumbs_top"];
            }
            
            $rss .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png'></a>";       
            $this->hotaru->vars['page_title'] = $media_word_link . " &raquo; " . $filter_word . $rss;
            
            return true;    
        }
        
        return false;
    }
    
    /**
     * Add media options to category bar
     */
    public function category_bar_end()
    {
        $this->includeLanguage();
        echo "<li><a href='#'>" . $this->lang['media_select'] . "\n";
        echo "<ul>";
        echo "<li><a href='" . $this->hotaru->url(array('type'=>'news')) . "'>" . $this->lang['media_select_news'] . "</a>\n";
        echo "<li><a href='" . $this->hotaru->url(array('type'=>'video')) . "'>" . $this->lang['media_select_videos'] . "</a>\n";
        echo "<li><a href='" . $this->hotaru->url(array('type'=>'image')) . "'>" . $this->lang['media_select_images'] . "</a>\n";
        echo "</ul></li>";
    }
     
}
?>