<?php 
/**
 * Theme name: Keep it Simple
 * Template name: navigation.php
 * Original Template author: Nick Ramsay
 * Original Design: Erwin Aligam
 * Original Author URI : http://www.styleshout.com/ 
 * Template author: Carlo Armanni
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
 * @author    Carlo Armanni <admin@tr3ndy.com>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.tr3ndy.com/
 */
?>

<!-- Navigation Bar -->
    <div id="nav">
	<ul>
    <?php if ($h->pageName == 'top') { $status = "id='navigation_active'"; } else { $status = ""; } ?>
	
	<?php $h->pluginHook('navigation_first'); ?>
	<li <?php echo $status; ?>><a <?php echo $status; ?> href="<?php echo BASEURL; ?>"><?php echo $h->lang["main_theme_navigation_home"]; ?></a></li>
    
	<?php $h->pluginHook('navigation'); ?>
	
	<?php 
        if (!$h->isActive('users')) { 

            if ($h->currentUser->loggedIn == true) { 
            
                if ($h->pageName == 'admin') { $status = "id='navigation_active'"; } else { $status = ""; }
                echo "<li " . $status . "><a " . $status . " href='" . $h->url(array(), 'admin') . "'>" . $h->lang["main_theme_navigation_admin"] . "</a></li>"; 
                
                if ($h->pageName == 'logout') { $status = "id='navigation_active'"; } else { $status = ""; }
                echo "<li " . $status . "><a " . $status . " href='" . $h->url(array('page'=>'admin_logout'), 'admin') . "'>" . $h->lang["main_theme_navigation_logout"] . "</a></li>";
            } else { 
                if ($h->pageName == 'login') { $status = "id='navigation_active'"; } else { $status = ""; }
                echo "<li " . $status . "><a " . $status . " href='" . $h->url(array(), 'admin') . "'>" . $h->lang["main_theme_navigation_login"] . "</a></li>"; 
            }
        } else {
            $h->pluginHook('navigation_users', true, 'users'); // ensures login/logout/register are last.
        }
			
    ?>
	</ul>
	</div>


    <?php     // RSS Link and icon if Submit plugin is active
        if ($h->isActive('submit')) { ?>
		<div id="iconrss">
		<a href="<?php echo $h->url(array('page'=>'rss')); ?>">
                    <img src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/new-rss.png">
        </a>
		</div>
			<div id="ricerca">
				<form name='search_form' id="quick-search" action="<?php echo BASEURL; ?>index.php?page=search" method="get" >
				<p>
				<label for="qsearch">Search:</label>
				<input class="tbox" id="qsearch" type="text" name="search" value="<?php echo $h->lang['search_text']; ?>" title="Start typing and hit ENTER" onfocus="if (this.value == '<?php echo $h->lang['search_text']; ?>') {this.value = '';}" />
				<input class="btn" alt="Search" type="image" name="searchsubmit" title="Search" src="<?php echo BASEURL; ?>content/themes/<?php echo THEME; ?>images/search.gif" />
				</p>
				</form>
			</div>
    <?php } 	?>

