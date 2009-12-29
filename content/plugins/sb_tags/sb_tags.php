<?php
/**
 * name: SB Tags
 * description: Show tags, filter tags and RSS for tags
 * version: 0.1
 * folder: sb_tags
 * class: SbTags
 * type: tags
 * requires: sb_base 0.1, sb_submit 0.1
 * hooks: sb_base_theme_index_top, header_include, header_include_raw, header_meta, sb_base_show_post_extra_fields, sb_base_show_post_extras, sb_base_functions_preparelist, breadcrumbs
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

class SbTags
{
    /**
     * Determine if we are filtering to tags
     */
    public function sb_base_theme_index_top($h)
    {
        // friendly URLs: FALSE
        if ($h->cage->get->keyExists('tag')) { 
            $h->pageTitle = stripslashes(make_name($h->cage->get->noTags('tag')));
            $h->pageName = 'tags';
            $h->pageType = 'list';
        } 
        
        // friendly URLs: TRUE
        if (!$h->pageTitle && $h->cage->get->keyExists('pos2')) { 
            $h->pageTitle = stripslashes(make_name($h->cage->get->noTags('pos2')));
            $h->pageName = 'tags';
            $h->pageType = 'list';
        }
    }
    
    
    /**
     * Match meta tag to a post's keywords (description is done in the Submit plugin)
     */
    public function header_meta($h)
    {    
        if ($h->pageType == 'post') {
            echo '<meta name="keywords" content="' . stripslashes($h->post->tags) . '">' . "\n";
            return true;
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
        if ($h->pageName == 'tags') 
        {
            // friendly URLs: FALSE
            $tag = stripslashes($h->cage->get->noTags('tag')); 
            
            // friendly URLs: TRUE
            if (!$tag) { $tag = $h->cage->get->noTags('pos2'); } 
            
            if ($tag) {
                $h->vars['filter']['post_tags LIKE %s'] = '%' . urlencode($tag) . '%'; 
                $h->vars['filter']['post_archived = %s'] = 'N'; // don't include archived posts
                //$rss = " <a href='" . $h->url(array('page'=>'rss', 'tag'=>$tag)) . "'>";
            }
            
            //$rss .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png'></a>";       
            //$h->vars['page_title'] = $h->lang["post_breadcrumbs_tag"] . " &raquo; " . stripslashes($h->title) . $rss;
            
            return true;    
        }
        
        return false;    
    }
    
    
    /**
     * Add RSS link to breadcrumbs
     */
    public function breadcrumbs($h)
    {
        if ($h->pageName == 'tags') {
                $h->pageTitle .= $h->rssBreadcrumbsLink('all');
        }
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
            echo "<ul>" . $h->lang["tags_list"] . " \n";
        }
        
        foreach ($tags as $tag) {
            echo "<a href='" . $h->url(array('tag' => str_replace(' ', '_', trim($tag)))) . "'>" . trim($tag) . "</a>&nbsp;\n";
        }
        
        if (!$raw) {
            echo "</ul>\n";
            echo "</div>\n";
        }
    }
}
?>