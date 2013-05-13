<?php 
/**
 * Theme name: default
 * Template name: sidebar.php
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
 * @author    Shibuya246 <admin@hotarucms.org>
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://hotarucms.org/
 */

?>
<?php if ($h->isActive('submit')) { ?>
<div class="well sidebar-nav">    
        <center><a href="<?php echo $h->url(array('page'=>'submit'));?>"><div class="btn btn-success"><?php echo $h->lang['submit_submit_a_story']; ?></div></a></center>
</div>
<?php } ?>

<div class="well sidebar-nav">
    <div id="sidebar">               
        <?php $h->pluginHook('widget_block', '', array(1)); ?>
    </div>
</div>
