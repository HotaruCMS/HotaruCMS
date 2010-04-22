<?php
/**
 * name: Media Select
 * description: Choose and filter to media type, i.e. news, videos or images 
 * version: 0.3
 * folder: media_select
 * class: MediaSelect
 * hooks: install_plugin, sb_base_theme_index_top, post_read_post, post_add_post, post_update_post, submit_2_fields, submit_functions_process_submitted, sb_base_functions_preparelist, category_bar_end, breadcrumbs
 * requires: submit 1.9
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

class MediaSelect
{
    /**
     * Add a post_media field to posts table if it doesn't already exist
     */
    public function install_plugin($h)
    {
        // Create a new table column called "post_media" if it doesn't already exist
        $exists = $h->db->column_exists('posts', 'post_media');
        if (!$exists) {
            $h->db->query("ALTER TABLE " . TABLE_POSTS . " ADD post_media VARCHAR(20) NOT NULL DEFAULT 'text' AFTER post_comments");
        } 
    }
    
    
    /**
     * Determine if we are filtering to a media type
     */
    public function sb_base_theme_index_top($h)
    {
        if ($h->cage->get->keyExists('media')) { 
            $h->vars['media'] = $h->cage->get->testAlpha('media');
            
            switch ($h->vars['media']) {
                case 'video':
                    $h->pageTitle = $h->lang['media_select_videos'];
                    break;
                case 'image':
                    $h->pageTitle = $h->lang['media_select_images'];
                    break;
                default:
                    $h->pageTitle = $h->lang['media_select_text'];
                    break;
            }
            
            $h->subPage = 'media';
            $h->pageType = 'list';
        } 
    }
    
    
    /**
     * Read post media if post_id exists.
     */
    public function post_read_post($h)
    {
        if (!isset($h->post->vars['post_row']->post_media)) { return false; }
        
        $h->post->vars['media'] = $h->post->vars['post_row']->post_media;
    }
    
    
    /**
     * Add media the posts table
     */
    public function post_add_post($h)
    {
        $h->post->vars['media'] = $h->vars['submitted_data']['submit_media'];
        
        $sql = "UPDATE " . TABLE_POSTS . " SET post_media = %s WHERE post_id = %d";
        $h->db->query($h->db->prepare($sql, $h->post->vars['media'] , $h->post->vars['last_insert_id']));
    }
    
    
    /**
     * Update media the posts table
     */
    public function post_update_post($h)
    {
    	if (!isset($h->vars['submitted_data']['submit_media'])) { return false; }
    	
        $h->post->vars['media'] = $h->vars['submitted_data']['submit_media'];
        
        $sql = "UPDATE " . TABLE_POSTS . " SET post_media = %s WHERE post_id = %d";
        $h->db->query($h->db->prepare($sql, urlencode(trim($h->post->vars['media'] )), $h->post->id));
    }
    
    
    /**
     * Add a media field to submit form 2 and edit post page
     */
    public function submit_2_fields($h)
    {
        if (!isset($h->post->vars['media'])) { 
            if (isset($h->vars['submitted_data']['submit_media'])) { 
                $h->post->vars['media'] = $h->vars['submitted_data']['submit_media'];
            } else {
                $h->post->vars['media'] = 'text';
            }
        }
        
        switch ($h->post->vars['media']) {
            case 'video':
                $video = "checked"; $image = ""; $text = "";
                break;
            case 'image':
                $video = ""; $image = "checked"; $text = "";
                break;
            default:
                $video = ""; $image = ""; $text = "checked";
        }

        // radio buttons
        
        echo "<tr>\n";
        
            echo "<td>" . $h->lang["submit_form_media"] . "&nbsp; </td>\n";
            echo "<td colspan=2>\n";
        
            // news
            echo "<input type='radio' name='post_media' value='text' " . $text . " >";
            echo "&nbsp;&nbsp;" . $h->lang['media_select_text'] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n"; 
            
            // video
            echo "<input type='radio' name='post_media' value='video' " . $video . " >";
            echo "&nbsp;&nbsp;" . $h->lang['media_select_video'] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n"; 
            
            // image
            echo "<input type='radio' name='post_media' value='image' " . $image . " >";
            echo "&nbsp;&nbsp;" . $h->lang['media_select_image'] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n"; 
            
        echo "</tr>\n";
    }
    
    
    /**
     * Check and update post_submit in Submit step 2 and Post Edit pages
     */
    public function submit_functions_process_submitted($h)
    {
        if (($h->pageName != 'submit2') && ($h->pageName != 'edit_post')) { return false; }

        if ($h->cage->post->keyExists('post_media')) {
            $h->post->vars['media'] = $h->cage->post->getAlpha('post_media');
        } else {
            // use existing setting unless blank, in which case set default:
            if (!$h->post->vars['media']) { $h->post->vars['media'] = 'text'; } // default
        }

        $h->vars['submitted_data']['submit_media'] = $h->post->vars['media'];
    }
    
    
    /**
     * Filter posts to a media type
     */
    public function sb_base_functions_preparelist($h)
    {
        if ($h->cage->get->keyExists('media')) 
        {
            $media = $h->cage->get->testAlnumLines('media'); 
            
            if ($media) {
                $h->vars['filter']['post_media = %s'] = $media; 
                $h->vars['filter']['post_archived = %s'] = 'N'; // don't include archived posts
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Add media options to category bar
     */
    public function category_bar_end($h)
    {
		$h->displayTemplate('media_menu');
    }
    
    
    /**
     * Add RSS link to breadcrumbs
     */
    public function breadcrumbs($h)
    {
        if ($h->subPage != 'media') { return false; }
        
        $crumbs = "<a href='" . $h->url(array('media'=>$h->vars['media'])) . "'>\n";
        $crumbs .= $h->pageTitle . "</a>\n ";
        
        return $crumbs . $h->rssBreadcrumbsLink('', array('media'=>$h->vars['media']));
    }
     
}
?>