<?php
/**
 * Sorting Options (Vote Simple plugin)
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

// check if we're looking at a category
if ($hotaru->cage->get->keyExists('category')) { 
    $category = $hotaru->cage->get->noTags('category');
    if (!is_numeric($category)) { 
        require_once(PLUGINS . 'categories/libs/Category.php');
        $cat = new Category($this->db);
        $category = $cat->getCatId($category);
    }
} 

// check if we're looking at a tag
if ($hotaru->cage->get->keyExists('tag')) { 
    $tag = $hotaru->cage->get->noTags('tag');
} 

// check if we're looking at a user
if ($hotaru->cage->get->keyExists('user')) { 
    $user = $hotaru->cage->get->testUsername('user');
} 

// check if we're looking at a sorted page
if ($hotaru->cage->get->keyExists('sort')) { 
    $sort = $hotaru->cage->get->testAlnumLines('sort');
} 

?>

<!-- SORT -->
<div id="sort_box">
    <ul class="sort_menu">
    
        <?php // POPULAR
            if ($category) { $url = $hotaru->url(array('category'=>$category));
             } elseif ($tag) { $url = $hotaru->url(array('tag'=>$tag));
             } elseif ($user) { $url = $hotaru->url(array('user'=>$user));
             } else { $url = $hotaru->url(array()); } 
        ?>
        <li <?php if ($hotaru->getPageName() == 'main' && !$sort) { echo "class='active'"; } ?>>
        <a href="<?php echo $url; ?>"><?php echo $hotaru->lang["vote_sort_recently_popular"]; ?></a></li>
        
        <?php // UPCOMING
            if ($category) { $url = $hotaru->url(array('page'=>'upcoming', 'category'=>$category));
             } elseif ($tag) { $url = $hotaru->url(array('page'=>'upcoming', 'tag'=>$tag));
             } elseif ($user) { $url = $hotaru->url(array('page'=>'upcoming', 'user'=>$user));
             } else { $url = $hotaru->url(array('page'=>'upcoming')); } ?>
        <li <?php if ($hotaru->getPageName() == 'upcoming' && !$sort) { echo "class='active'"; } ?>>
        <a href="<?php echo $url; ?>"><?php echo $hotaru->lang["vote_sort_upcoming"]; ?></a></li>
        
        <?php // NEWEST
            if ($category) { $url = $hotaru->url(array('page'=>'latest', 'category'=>$category));
             } elseif ($tag) { $url = $hotaru->url(array('page'=>'latest', 'tag'=>$tag));
             } elseif ($user) { $url = $hotaru->url(array('page'=>'latest', 'user'=>$user));
             } else { $url = $hotaru->url(array('page'=>'latest')); } ?>
        <li <?php if ($hotaru->getPageName() == 'latest' && !$sort) { echo "class='active'"; } ?>>
        <a href="<?php echo $url; ?>"><?php echo $hotaru->lang["vote_sort_latest"]; ?></a></li>
        
        &nbsp;&nbsp;&nbsp;&nbsp; <?php echo $hotaru->lang["vote_sort_best_from"]; ?>
        
        <?php // 24 HOURS
            if ($category) { $url = $hotaru->url(array('sort'=>'top-24-hours', 'category'=>$category));
             } elseif ($tag) { $url = $hotaru->url(array('sort'=>'top-24-hours', 'tag'=>$tag));
             } elseif ($user) { $url = $hotaru->url(array('sort'=>'top-24-hours', 'user'=>$user));
             } else { $url = $hotaru->url(array('sort'=>'top-24-hours')); } ?>
        <li <?php if ($sort == 'top-24-hours') { echo "class='active'"; } ?>>
        <a href="<?php echo $url; ?>"><?php echo $hotaru->lang["vote_sort_top_1_day"]; ?></a></li>
        
        <?php // 48 HOURS
            if ($category) { $url = $hotaru->url(array('sort'=>'top-48-hours', 'category'=>$category));
             } elseif ($tag) { $url = $hotaru->url(array('sort'=>'top-48-hours', 'tag'=>$tag));
             } elseif ($user) { $url = $hotaru->url(array('sort'=>'top-48-hours', 'user'=>$user));
             } else { $url = $hotaru->url(array('sort'=>'top-48-hours')); } ?>
        <li <?php if ($sort == 'top-48-hours') { echo "class='active'"; } ?>>
        <a href="<?php echo $url; ?>"><?php echo $hotaru->lang["vote_sort_top_2_days"]; ?></a></li>
        
        <?php // 7 DAYS
            if ($category) { $url = $hotaru->url(array('sort'=>'top-7-days', 'category'=>$category));
             } elseif ($tag) { $url = $hotaru->url(array('sort'=>'top-7-days', 'tag'=>$tag));
             } elseif ($user) { $url = $hotaru->url(array('sort'=>'top-7-days', 'user'=>$user));
             } else { $url = $hotaru->url(array('sort'=>'top-7-days')); } ?>
        <li <?php if ($sort == 'top-7-days') { echo "class='active'"; } ?>>
        <a href="<?php echo $url; ?>"><?php echo $hotaru->lang["vote_sort_top_7_days"]; ?></a></li>
        
        <?php // 30 DAYS
            if ($category) { $url = $hotaru->url(array('sort'=>'top-30-days', 'category'=>$category));
             } elseif ($tag) { $url = $hotaru->url(array('sort'=>'top-30-days', 'tag'=>$tag));
             } elseif ($user) { $url = $hotaru->url(array('sort'=>'top-30-days', 'user'=>$user));
             } else { $url = $hotaru->url(array('sort'=>'top-30-days')); } ?>
        <li <?php if ($sort == 'top-30-days') { echo "class='active'"; } ?>>
        <a href="<?php echo $url; ?>"><?php echo $hotaru->lang["vote_sort_top_30_days"]; ?></a></li>
        
        <?php // 365 DAYS
            if ($category) { $url = $hotaru->url(array('sort'=>'top-365-days', 'category'=>$category));
             } elseif ($tag) { $url = $hotaru->url(array('sort'=>'top-365-days', 'tag'=>$tag));
             } elseif ($user) { $url = $hotaru->url(array('sort'=>'top-365-days', 'user'=>$user));
             } else { $url = $hotaru->url(array('sort'=>'top-365-days')); } ?>
        <li <?php if ($sort == 'top-365-days') { echo "class='active'"; } ?>>
        <a href="<?php echo $url; ?>"><?php echo $hotaru->lang["vote_sort_top_365_days"]; ?></a></li>
        
        <?php // ALL TIME
            if ($category) { $url = $hotaru->url(array('sort'=>'top-all-time', 'category'=>$category));
             } elseif ($tag) { $url = $hotaru->url(array('sort'=>'top-all-time', 'tag'=>$tag));
             } elseif ($user) { $url = $hotaru->url(array('sort'=>'top-all-time', 'user'=>$user));
             } else { $url = $hotaru->url(array('sort'=>'top-all-time')); } ?>
        <li <?php if ($sort == 'top-all-time') { echo "class='active'"; } ?>>
        <a href="<?php echo $url; ?>"><?php echo $hotaru->lang["vote_sort_top_all_time"]; ?></a></li>
        
    </ul>
</div>
