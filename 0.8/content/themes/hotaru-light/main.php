<?php 
/**
 * Theme name: hotaru-light
 * Template name: main.php
 * Template author: carlo75
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

<div id="breadcrumbs">
    <a href="<?php echo BASEURL; ?>"><?php echo SITE_NAME; ?></a> 
    <?php $hotaru->plugins->pluginHook('breadcrumbs'); ?> 
    &raquo; <?php if ($hotaru->title == 'top') { echo $hotaru->lang['main_theme_home']; } else { echo $hotaru->title; } ?>
</div>

<?php $hotaru->plugins->pluginHook('main_top'); ?>
    
<?php $hotaru->plugins->pluginHook('main'); ?>

<?php $hotaru->plugins->pluginHook('main_bottom'); ?>
