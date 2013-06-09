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

<div class="container-fluid">
    <div class="row-fluid">
        <!-- BREADCRUMBS -->
        <div class='breadcrumb'>
                <?php echo $h->breadcrumbs("/"); ?>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row-fluid">
                    
        <?php if ($h->sidebars) { ?>
            <div class="span3">
                <!-- SIDEBAR -->
                <?php                
                        // plugin hook
                        $result = $h->pluginHook('admin_theme_index_sidebar');
                        if (!$result) {
                                $h->template('admin_sidebar');                                
                        }
                ?>
            </div>           
        <?php } ?>	
        
            <!-- MAIN -->
            <div class="span9">
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
            }
            ?>
            </div> 	

    </div>
</div>

</div> <!--/wrap-->
<!-- FOOTER -->
<?php
	// plugin hook
	$result = $h->pluginHook('admin_theme_index_footer');
	if (!$result) {
		$h->template('admin_footer');
	}
?>
</body>
</html>
<?php } ?>


