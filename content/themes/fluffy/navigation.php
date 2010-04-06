<?php 
/**
 * Theme name: default
 * Template name: navigation.php
 * Template author: Carlo Armanni
 * Template author website: http://www.tr3ndy.com
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
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
?>

<!-- Navigation Bar -->
<ul class="navigation">
    
    <?php if ($h->pageName == 'index') { $status = "id='navigation_active'"; } else { $status = ""; } ?>
    <li><a <?php echo $status; ?> href="<?php echo BASEURL; ?>"><?php echo $h->lang["main_theme_navigation_home"]; ?></a></li>
    <?php $h->pluginHook('navigation'); ?>
    <?php 
        if (!$h->isActive('signin')) { 

            if ($h->currentUser->loggedIn == true) { 
            
                if ($h->isAdmin) { $status = "id='navigation_active'"; } else { $status = ""; }
                echo "<li><a " . $status . " href='" . $h->url(array(), 'admin') . "'>" . $h->lang["main_theme_navigation_admin"] . "</a></li>"; 
                
                if ($h->pageName == 'logout') { $status = "id='navigation_active'"; } else { $status = ""; }
                echo "<li><a " . $status . " href='" . $h->url(array('page'=>'admin_logout'), 'admin') . "'>" . $h->lang["main_theme_navigation_logout"] . "</a></li>";
            } else { 
                if ($h->pageName == 'login') { $status = "id='navigation_active'"; } else { $status = ""; }
                echo "<li><a " . $status . " href='" . $h->url(array(), 'admin') . "'>" . $h->lang["main_theme_navigation_login"] . "</a></li>"; 
            }
        } else {
            $h->pluginHook('navigation_users'); // ensures login/logout/register are last.
        }
    ?>



    <?php     // RSS Link and icon if a type "base" plugin is active
        if ($h->isActive('base')) { ?>
        <li>
        <a href="<?php echo $h->url(array('page'=>'rss')); ?>">RSS             
        </a>
		</li>
    
	<li>
		<?php
			if ($h->currentUser->getPermission('can_access_admin') == 'no') { // controllo utente. Se non è admin l'avatar rimane 
		?> 
		
		<?php   
			if($h->isActive('avatar')) {
				$h->setAvatar($h->currentUser->id, 32);
				echo $h->linkAvatar();
			}
		?>
		
		<?php 
			if ($h->currentUser->loggedIn == false) { // se è loggato non fare nulla altrimenti...
			echo '<img src="' . BASEURL . 'content/plugins/gravatar/images/default_80.png" alt="Gravatar Default" width="32" height="32" />'; // è un visitatore: metti l'avatar di default o qualcos'altro
			} 
			}
		?>
	</li>
	<?php } ?>
</ul>