<?php
/**
 * A collection of functions for making friendly urls
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
 
/**
 * Generate either default or friendly urls
 *
 * @param array $parameters an array of pairs, e.g. 'page' => 'about' 
 * @param string $head either 'index' or 'admin'
 * @return string
 */
function url($parameters = array(), $head = 'index')
{
    global $post;
    
    if (FRIENDLY_URLS == "false") {
    
        if ($head == 'index') {
            $url = BASEURL . 'index.php?';
        } elseif ($head == 'admin') {
            $url = BASEURL . 'admin_index.php?';    
        } else {
            // Error. $head must be index or admin
        }
        
        if (empty($parameters)) { 
            $url = rtrim($url, '?'); 
            return $url; 
        } 

        foreach ($parameters as $key => $value) {
            $url .= $key . '=' . $value . '&amp;';
        }
        return rstrtrim($url, '&amp;');    
        
    } 
    
    if (FRIENDLY_URLS == "true") {
    
        if ($head == 'index') { 
            $url = BASEURL;
        } elseif ($head == 'admin') {
            $url = BASEURL . 'admin/';    
        } else {
            $url = BASEURL . $head . '/';
        }
        
        foreach ($parameters as $key => $value) {
        
            if ($key == 'page' && is_numeric($value) ) {
            
                // must be a post title, let's get the post_url...
                $value = $post->getUrl();
                
                if (isset($post->vars['category']) && $post->vars['category'] != 1) {
                    $url .= $post->vars['catSafeName'] . '/';
                }
                
                $url .= $value . '/';
                
            } elseif ($key == 'category' && is_numeric($value) ) {
                
                require_once(PLUGINS . 'categories/libs/Category.php');
                $cat = new Category();
                $url .= $key . '/' . $cat->getCatSafeName($value) . '/';
                    
            } elseif ($key == 'page') {
            
                // don't show "page" in the url, only the value
                $url .= $value . '/';    
                                
            } else {
                $url .= $key . '/' . $value . '/';
            }
        }
        return $url;
    }
    
}

?>
