<?php
/**
 * Post functions
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
class Post
{
    // individual posts
    protected $id = 0;
    protected $origUrl          = '';           // original url for the submitted post
    protected $domain           = '';           // the domain of the submitted url
    protected $title            = '';           // post title
    protected $content          = '';           // post description
    protected $contentLength    = 50;           // default min characters for content
    protected $summary          = '';           // truncated post description
    protected $summaryLength    = 200;          // default max characters for summary
    protected $status           = 'unsaved';    // initial status before database entry
    protected $author           = 0;            // post author
    protected $url              = '';           // post slug (needs BASEURL and category attached)
    protected $date             = '';           // post submission date
    protected $subscribe        = 0;            // is the post author subscribed to comments?
    
    // general
    protected $postsPerPage     = 10;           // Number of posts to show on list pages
    protected $allowableTags    = '';           // allowable HTML tags
    protected $useSummary       = true;         // truncate the post description on list pages


    /**
     * Access modifier to set protected properties
     */
    public function __set($var, $val)
    {
        $this->$var = $val;  
    }
    
    
    /**
     * Access modifier to get protected properties
     */
    public function &__get($var)
    {
        return $this->$var;
    }
}
?>