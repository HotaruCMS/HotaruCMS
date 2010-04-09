<?php 
/**
 * Theme name: Keep it Simple
 * Template name: index.php
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

// set a custom home page:
$h->setHome();

// get language
$h->includeThemeLanguage();
 
// plugin hook
$result = $h->pluginHook('theme_index_top');
if (!$result) {
?>
        <!-- HEADER-->

        <?php
            // plugin hook
            $result = $h->pluginHook('theme_index_header');
            if (!$result) {
                $h->displayTemplate('header');
            }
        ?>
<!-- div content starts -->
<div id="content-outer">
<div id="content-wrapper" class="container_16">
    <!-- BREADCRUMBS -->
		<div id='breadcrumbs'>
		<?php echo $h->breadcrumbs(); ?>
		</div>
		
                            <!-- USER TABS -->
                            <?php 
                                // plugin hook
                                $result = $h->pluginHook('theme_index_post_breadcrumbs');
                            ?>
                            
                            <!-- FILTER TABS -->
                            <?php 
                                // plugin hook
                                $result = $h->pluginHook('theme_index_pre_main');
                            ?>
                            
                            <!-- MAIN -->
							
<?php if ($h->pageName == "submit1" || $h->pageName == "submit2" || $h->pageName == "edit_post") {  "<div id=\"submission\">"; }
else {
echo "<div id=\"main\" class=\"grid_8\">"; 
} ?>
                            <?php     
                                // plugin hook
                            $result = $h->pluginHook('theme_index_main');
                            if (!$result) {
                                $h->displayTemplate($h->pageName); 
                            }
                        ?>	
		</div>

                    <?php if ($h->sidebars) { ?>

<?php if ($h->pageName == "submit1" || $h->pageName == "submit2" || $h->pageName == "edit_post")
{ // DO NOTHING Submission in progress 
	echo "<style type=\"text/css\">
	<!-- #main form {
	width: 870px;
	}
	--></style>";
}
else {
                      echo "<!-- SIDEBAR -->
							<!-- left-columns starts -->
							<div id=\"left-columns\" class=\"grid_8\" style=\"margin-top: -65px;\">
							<div class=\"grid_4 alpha\"><!-- left-sidebar starts -->
							<div class=\"sidemenu\">";	
                            
                                // plugin hook
                                $result = $h->pluginHook('theme_index_sidebar');
                                if (!$result) {
                                    $h->displayTemplate('sidebar_left');
                                }                                
                            
					  echo "</div>
							</div>
							<div class=\"grid_4 omega\"><!-- right-sidebar starts -->
							<div class=\"sidemenu\">";
                            
                                // plugin hook
                                $result = $h->pluginHook('theme_index_sidebar_2');
                                if (!$result) {
                                    $h->displayTemplate('sidebar_right');
                                }                                
                            
					  echo "</div>
							</div>
							</div>";
							} ?>

                    <?php } ?>

        <!-- FOOTER -->
        <?php
            // plugin hook
            $result = $h->pluginHook('theme_index_footer');
            if (!$result) {
                $h->displayTemplate('footer');
            }
        ?>
<?php    } ?>
