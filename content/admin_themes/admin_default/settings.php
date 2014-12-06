<?php 
/**
 * Theme name: admin_default
 * Template name: plugins.php
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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
$h->pluginHook('plugins_top');

$h->template('admin_sidebar');

$h->showMessages();
?>

<div class="bs-example bs-example-tabs">
    <ul id="myTab" class="nav nav-tabs" role="tablist">
      <li class="active"><a href="#main" role="tab" data-toggle="tab">Main</a></li>
      <li><a href="#performance" role="tab" data-toggle="tab">Performance</a></li>
<!--      <li><a href="#spam" role="tab" data-toggle="tab">Spam</a></li>-->
      <li><a href="#security" role="tab" data-toggle="tab">Security</a></li>
<!--      <li><a href="#notifications" role="tab" data-toggle="tab">Notifications</a></li>-->
      <li class="dropdown">
        <?php echo \Libs\PluginSettings::getSettingsDropdownList($h); ?>
      </li>
    </ul>


<?php 

    $page = 'settings';
    $active = " active";

    $names = array(
        'Main', 'Performance', 'Spam', 'Security', 'Notifications'      
     );

    echo '<div class="tab-content">';
        foreach ($names as $name) {             
           echo '<div class="tab-pane fade-in ' . $active . '" id="' . strtolower($name) . '">';              
               $h->template($page .'/' . strtolower($name), 'admin');
               if ($active == " active") $active = '';
           echo '</div>';        
        }
    echo '</div>';
?>

  </div>  


<?php $h->pluginHook('admin_settings_bottom'); ?>