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

?>

<!-- SORT -->
<div id="sort_box">
    <ul class="sort_menu">
    
        <li <?php echo $hotaru->vars['popular_active']; ?>>
            <a href="<?php echo $hotaru->vars['popular_link']; ?>">
            <?php echo $hotaru->lang["vote_sort_recently_popular"]; ?></a>
        </li>

        <li <?php echo $hotaru->vars['upcoming_active']; ?>>
            <a href="<?php echo $hotaru->vars['upcoming_link']; ?>">
            <?php echo $hotaru->lang["vote_sort_upcoming"]; ?></a>
        </li>
        
        <li <?php echo $hotaru->vars['latest_active']; ?>>
            <a href="<?php echo $hotaru->vars['latest_link']; ?>">
            <?php echo $hotaru->lang["vote_sort_latest"]; ?></a>
        </li>
        
        <li <?php echo $hotaru->vars['all_active']; ?>>
            <a href="<?php echo $hotaru->vars['all_link']; ?>">
            <?php echo $hotaru->lang["vote_sort_all"]; ?></a>
        </li>
        
        
        <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $hotaru->lang["vote_sort_best_from"]; ?></li>
        
        <li <?php echo $hotaru->vars['top_24_hours_active']; ?>>
            <a href="<?php echo $hotaru->vars['24_hours_link']; ?>">
            <?php echo $hotaru->lang["vote_sort_top_1_day"]; ?></a>
        </li>
        
        <!-- Doesn't fit in the default theme
        <li <?php echo $hotaru->vars['top_48_hours_active']; ?>>
            <a href="<?php echo $hotaru->vars['48_hours_link']; ?>">
            <?php echo $hotaru->lang["vote_sort_top_2_days"]; ?></a>
        </li>
        -->
        
        <li <?php echo $hotaru->vars['top_7_days_active']; ?>>
            <a href="<?php echo $hotaru->vars['7_days_link']; ?>">
            <?php echo $hotaru->lang["vote_sort_top_7_days"]; ?></a>
        </li>
        
        <li <?php echo $hotaru->vars['top_30_days_active']; ?>>
            <a href="<?php echo $hotaru->vars['30_days_link']; ?>">
            <?php echo $hotaru->lang["vote_sort_top_30_days"]; ?></a>
        </li>
        
        <li <?php echo $hotaru->vars['top_365_days_active']; ?>>
            <a href="<?php echo $hotaru->vars['365_days_link']; ?>">
            <?php echo $hotaru->lang["vote_sort_top_365_days"]; ?></a>
        </li>
        
        <li <?php echo $hotaru->vars['top_all_time_active']; ?>>
            <a href="<?php echo $hotaru->vars['all_time_link']; ?>">
            <?php echo $hotaru->lang["vote_sort_top_all_time"]; ?></a>
        </li>
        
    </ul>
</div>