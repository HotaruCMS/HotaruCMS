<?php
/**
 * Functions for handling pages, e.g. titles and templates
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
class PageHandling
{
    /**
     * Includes a template to display
     *
     * @param string $page page name
     * @param string $plugin optional plugin name
     * @param bool $include_once true or false
     */
    public function displayTemplate($hotaru, $page = '', $plugin = '', $include_once = true)
    {
        // if no plugin folder, provide it:
        if (!$plugin) { $plugin = $hotaru->folder; }
        
        if ($hotaru->isAdmin) { 
            $themes = ADMIN_THEMES; $theme = ADMIN_THEME; 
        } else {
            $themes = THEMES; $theme = THEME; 
        } 
        
        $page = $page . '.php';

        /* 
            1. Check the custom theme
            2. Check the default theme
            3. Check the plugin folder
            4. Show the 404 Not Found page from the theme
            5. Show the 404 Not Found page from "themes" folder
        */
        if (file_exists($themes . $theme . $page))
        {
            if (!$include_once) {
                // Special case, do not restrict to include once.
                include($themes . $theme . $page);
            } else {
                include_once($themes . $theme . $page);
            }
        } 
        elseif (file_exists($themes . 'default/' . $page))
        {
            if (!$include_once) {
                // Special case, do not restrict to include once.
                include($themes . 'default/' . $page);
            } else {
                include_once($themes . 'default/' . $page);
            }
        }
        elseif ($plugin != '' && file_exists(PLUGINS .  $plugin . '/templates/' . $page))
        {
                if (!$include_once) {
                    // Special case, do not restrict to include once.
                    include(PLUGINS . $plugin . '/templates/' . $page);
                } else {
                    include_once(PLUGINS . $plugin . '/templates/' . $page);
                }
                return true;
                die();
        }
        elseif (file_exists($themes . $theme . '404error.php')) 
        {
            include_once($themes . $theme . '404error.php');
        }
        else
        {
            include_once($themes . '404error.php');
        }
    }
    
    
    /**
     * Gets the current page name
     *
     * @return string|false $page
     */
    public function getPageName($cage)
    {
        // Try GET...
        $page = $cage->get->testPage('page');
        if (!$page) {
            /*  Possibly a post with multi-byte characters? 
                Try getMixedString2... */
            $page = $cage->get->getMixedString2('page');
        }
        
        // Try POST...
        if (!$page) { $page = $cage->post->testPage('page'); }

        if ($page) {
            $page = rtrim($page, '/');
            return $page;
        } else {
            return 'main';
        }
    }
    
    
    /**
     * Generate either default or friendly urls
     *
     * @param array $parameters an array of pairs, e.g. 'page' => 'about' 
     * @param string $head either 'index' or 'admin'
     * @return string
     */
    public function url($hotaru, $parameters = array(), $head = 'index')
    {
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
                    $value = $this->post->url;
                    
                    // if we're using categories and the category is not "all"...
                    if (isset($this->post->vars['useCategories']) && $this->post->vars['category'] != 1) {
                        $url .= $this->post->vars['catSafeName'] . '/';
                    }
                    
                    $url .= $value . '/';
                    
                } elseif ($key == 'category' && is_numeric($value) ) {
                    
                    require_once(PLUGINS . 'categories/libs/Category.php');
                    $cat = new Category($this->db);
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
}
?>