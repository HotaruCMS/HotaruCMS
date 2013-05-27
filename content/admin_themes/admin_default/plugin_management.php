<?php 
/**
 * Theme name: admin_default
 * Template name: plugins.php
 * Template author: Nick Ramsay
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

<?php $h->template('admin_sidebar'); ?>

<!--<h2><?php echo $h->lang("admin_theme_plugins"); ?></h2>-->

<?php $h->showMessages(); ?>

<div id="plugin_management">

<?php $h->pluginHook('plugins_top'); ?>

    <ul class="nav nav-tabs" id="Admin_Plugins_Tab">
        <li class="active"><a href="#home" data-toggle="tab">Install</a></li>
        <li><a href="#updates" data-toggle="tab">Updates</a></li>
        <li><a href="#search" data-toggle="tab">Search</a></li>
        <li><a href="#help" data-toggle="tab">Help</a></li>
    </ul>
    
    <div class="tab-content">
        <div class="tab-pane" id="help">
            
            <?php $h->template('plugin_management/help', 'admin'); ?>
            
        </div>
        
        <div class="tab-pane" id="updates">
            
            <?php $h->template('plugin_management/updates'); ?>
            
        </div>
        
        <div class="tab-pane" id="search">
            
            <?php $h->template('plugin_management/search'); ?>
            
        </div>
   
    
        <div class="active tab-pane" id="home">
        
            <?php $h->template('plugin_management/install'); ?>
            
         </div>
    </div>
        
</div>

<div class="clear"></div>
