<?php
/**
 * name: Tags
 * description: Show tags, filter tags and RSS for tags
 * version: 1.6
 * folder: tags
 * class: Tags
 * type: tags
 * requires: sb_base 0.1, submit 1.9
 * hooks: sb_base_theme_index_top, header_include, header_include_raw, header_meta, sb_base_show_post_extra_fields, sb_base_show_post_extras, sb_base_functions_preparelist, breadcrumbs
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

class Tags
{
    /**
     * Determine if we are filtering to tags
     */
    public function sb_base_theme_index_top($h)
    {
        if ($h->cage->get->keyExists('tag')) { 
            $h->pageTitle = stripslashes(make_name($h->cage->get->noTags('tag')));
            if (!$h->pageName) { $h->pageName = 'popular'; }
            $h->subPage = 'tags';
            $h->pageType = 'list';
            $h->vars['tag'] = $h->cage->get->noTags('tag');
        } 
    }
    
    
    /**
     * Match meta tag to a post's keywords (description is done in the Submit plugin)
     * Also changes meta when browsing a tag page
     */
    public function header_meta($h)
    {    
        if ($h->pageType == 'post')
        {
            echo '<meta name="keywords" content="' . stripslashes($h->post->tags) . '" />' . "\n";
            return true;
        } 
        elseif ($h->subPage == 'tags')
        { 
            $tag = stripslashes($h->vars['tag']); 
            
            if ($tag) {
                echo '<meta name="description" content="' . $h->lang['tags_meta_description_before'] . $tag . $h->lang['tags_meta_description_after'] . '" />' . "\n";
                echo '<meta name="keywords" content="' . $tag . $h->lang['tags_meta_keywords_more'] . '" />' . "\n";
                return true;
            }
        }
    }
    
    
    /**
     * JavaScript for dropdown tags list
     */
    public function header_include_raw($h)
    {    
        echo "<script type='text/javascript'>\n";
        echo "$(document).ready(function(){\n";
            echo "$('.tags_link').click(function () {\n";
            echo "var target = $(this).parents('div').next('div').children('div.show_tags');\n";
            echo "target.fadeToggle();\n";
            echo "return false;\n";
            echo "});\n";
        echo "});\n";
        echo "</script>\n";
    }

    
    /**
     * Gets a tag from the url and sets the filter for get_posts
     */
    public function sb_base_functions_preparelist($h)
    {
        if ($h->subPage == 'tags') 
        {
            $tag = stripslashes($h->vars['tag']); 
            
            if ($tag) {
                $h->vars['filter']['post_tags LIKE %s'] = '%' . urlencode($tag) . '%'; 
                $h->vars['filter']['post_archived = %s'] = 'N'; // don't include archived posts
            }
            return true;    
        }
        
        return false;    
    }
    
    
    /**
     * Add RSS link to breadcrumbs
     */
    public function breadcrumbs($h)
    {
        if ($h->subPage != 'tags') { return false; }
        
        $crumbs = "<a href='" . $h->url(array('tag'=>$h->vars['tag'])) . "'>\n";
        $crumb_title = stripslashes(make_name($h->cage->get->noTags('tag')));
        $crumbs .= $crumb_title . "</a>\n ";
        
        return $crumbs . $h->rssBreadcrumbsLink('', array('tag'=>$h->vars['tag']));
    }
    
    
    /**
     * Shows tags in each post
     */
    public function sb_base_show_post_extra_fields($h)
    { 
        if ($h->post->tags)
        { 
            echo "<li><a class='tags_link' href='#'>" . $h->lang['tags']  . "</a></li>\n";
        }
    }
    
    
     /**
     * List of tags
     */
    public function sb_base_show_post_extras($h, $vars = array())
    {
        if (!$h->post->tags) { return false; }
        
        $tags = explode(',', $h->post->tags);
        
        // lots of nice issets for php 5.3 compatibility
        if (isset($vars[0]) && isset($vars[1]) && ($vars[0] == "tags") && ($vars[1] == "raw")) {
            $raw = true;
        } else {
            $raw = false;
        }
        
        if (!$raw) {
            echo "<div class='show_tags' style='display: none;'>\n";
            echo "<ul><li>" . $h->lang["tags_list"] . "</li>";
        }
        
        foreach ($tags as $tag) {
            echo "<li><a href='" . $h->url(array('tag' => str_replace(' ', '_', trim($tag)))) . "'>" . trim($tag) . "</a></li>";
        }
        
        if (!$raw) {
            echo "</ul>\n";
            echo "</div>\n";
            echo "<div class='clear'>&nbsp;</div>\n";
        }
    }
}
?>