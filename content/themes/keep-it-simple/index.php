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

// plugin hook
$result = $hotaru->plugins->pluginHook('theme_index_replace');
if (!isset($result) || !is_array($result)) {
?>
        <!-- HEADER-->

        <?php
            // plugin hook
            $result = $hotaru->plugins->pluginHook('theme_index_header');
            if (!isset($result) || !is_array($result)) {
                $hotaru->displayTemplate('header');
            }
        ?>
<!-- div content starts -->
<div id="content-outer">
<div id="content-wrapper" class="container_16">
    
                            <!-- MAIN -->
							
<?php if ($hotaru->title == "submit") {  "<div id=\"submission\">"; }
else {
echo "<div id=\"main\" class=\"grid_8\">"; 
} ?>
                            <?php     
                                // plugin hook
                            $result = $hotaru->plugins->pluginHook('theme_index_main');
                            if (!isset($result) || !is_array($result)) {
                                $page = $hotaru->getPageName();
                                $hotaru->displayTemplate($page); 
                            }
                        ?>	
		</div>

                    <?php if ($hotaru->sidebars) { ?>

<?php if ($hotaru->templateName == "submit_step1" || $hotaru->templateName == "submit_step2")
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
							<div id=\"left-columns\" class=\"grid_8\">
							<div class=\"grid_4 alpha\"><!-- left-sidebar starts -->
							<div class=\"sidemenu\">";	
                            
                                // plugin hook
                                $result = $hotaru->plugins->pluginHook('theme_index_sidebar');
                                if (!isset($result) || !is_array($result)) {
                                    $hotaru->displayTemplate('sidebar_left');
                                }                                
                            
					  echo "</div>
							</div>
							<div class=\"grid_4 omega\"><!-- right-sidebar starts -->
							<div class=\"sidemenu\">";
                            
                                // plugin hook
                                $result = $hotaru->plugins->pluginHook('theme_index_sidebar_2');
                                if (!isset($result) || !is_array($result)) {
                                    $hotaru->displayTemplate('sidebar_right');
                                }                                
                            
					  echo "</div>
							</div>
							</div>";
							} ?>

                    <?php } ?>

        <!-- FOOTER -->
        <?php
            // plugin hook
            $result = $hotaru->plugins->pluginHook('theme_index_footer');
            if (!isset($result) || !is_array($result)) {
                $hotaru->displayTemplate('footer');
            }
        ?>
<?php    } ?>
