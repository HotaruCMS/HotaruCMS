<?php 
/**
 * Theme name: admin_default
 * Template name: index.php
 * Template author: shibuya246
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
 * @author    shibuya246 <admin@hotarucms.org>
 * @copyright Copyright (c) 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

// merge custom admin_language.php if exists in admin theme's languages folder
// can be overridden by an admin_languages.php in a user theme's languages folder
$h->includeThemeLanguage('admin');

// plugin hook
$result = $h->pluginHook('admin_theme_index_top');
if (!$result) {

	// plugin hook
	$result = $h->pluginHook('admin_theme_index_header');
	if (!$result) {
		$h->template('admin_header');
	}
?>

<div id="main-wrapper">
        <?php if ($h->sidebars) { ?>
            <!-- SIDEBAR -->
            <div id="main-menu-sidebar-bgd">
            <?php                
                    // plugin hook
                    $result = $h->pluginHook('admin_theme_index_sidebar');
                    if (!$result) {
                            $h->template('admin_sidebar');                                
                    }
            ?>
            </div> 
        <?php } ?>	
                
        <!-- BREADCRUMBS -->
        <?php if ($h->currentUser->isAdmin) { ?>
                <div id="admin-breadcrumb" class='breadcrumb'>
                    <i class="fa fa-th-large" style="color:gray;"></i>&nbsp;
                    <?php echo $h->breadcrumbs("/"); ?>
                    <i class="pull-right navbar-icon fa fa-bars"></i>
                    <ul class="pull-right" style="margin-top:2px;">
                        <?php
                        if (function_exists('sys_getloadavg')) {
                            $load = sys_getloadavg();
                        }
                        if (isset($load)) {
                            foreach ($load as $l) { 
                                echo "<li class='inline-block label label-default' style='margin:4px;' data-toggle='tooltip' data-placement='top' title='System Load Average'>";
                                echo $l;
                                echo "</li>";
                            }
                         } ?>                   
                    </ul>
                    
                </div>
        <?php } ?>
            <!-- MAIN -->
            <div id="main-content" class="container">
                <div>
                    <?php
                    // plugin hook
                    $result = $h->pluginHook('admin_theme_index_main');
                    if (!$result) {
                            if ($h->pageName == 'admin_login') {
                                    if ($h->currentUser->loggedIn && $h->currentUser->adminAccess) {
                                            $h->template('admin_home');
                                    } else {                                
                                            $h->adminLoginForm();
                                    }
                            } else {
                                    if ($h->pageName == 'plugin_settings') {  

                                    } 

                                    $h->template($h->pageName);
                            } 
                    } ?>
                </div>
            </div>

                <!-- FOOTER -->
                <?php
                // plugin hook
                $result = $h->pluginHook('admin_theme_index_footer');
                if (!$result) {
                        $h->template('admin_footer');
                } ?>
            </div>
        </div> <!--/wrap-->
    </body>
</html>
<?php } 
