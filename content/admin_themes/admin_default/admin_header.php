<?php 
/**
 * Theme name: admin_default
 * Template name: header.php
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

?>
<?php header('Content-type: text/html; charset=utf-8'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head profile="http://gmpg.org/xfn/11">

        <title><?php echo $h->getTitle(); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
        
        <!-- Bootstrap -->
        <?php $h->getFramework(); ?>	
        
        <!-- Theme -->
        <?php $h->getThemeCss(); ?>
         <link rel='stylesheet' href='<?php echo SITEURL; ?>content/admin_themes/admin_default/css/bootstrapSwitch.css' type='text/css'>
        
        <!-- include this CSS last so it gets priority -->
        <!-- Bootstrap Responsive -->
        <?php $h->getFramework('bootstrap-responsive'); ?>
		
        
<!--       
        <!-- Include merged files for all the plugin css and javascript (if any) -->
        <?php $h->doIncludes('css'); ?>		

        <!-- <link rel="shortcut icon" href="<?php echo SITEURL; ?>favicon.ico"> -->
	
	<?php $h->pluginHook('admin_header_include_raw'); ?>

</head>

<body>

    <div id="wrap">        
        <?php if ($h->currentUser->adminAccess) {
            echo $h->template('admin_navigation');
                           
            $announcements = $h->checkAnnouncements();
            if ($announcements && $h->currentUser->adminAccess) { 

                echo '<div id="announcement">';
                        $h->pluginHook('admin_announcement_first');
                        foreach ($announcements as $announcement) { echo $announcement . "<br/>"; }
                        $h->pluginHook('admin_announcement_last');
                echo '</div>';

             } 
         } 
        
        ?>
        <br/>