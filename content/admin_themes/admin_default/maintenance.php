<?php 
/**
 * Theme name: admin_default
 * Template name: maintenance.php
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

$plugin_settings = $h->vars['admin_plugin_settings'];
$db_tables = $h->vars['admin_plugin_tables'];
?>

<?php $h->showMessage(); ?>

<?php $h->pluginHook('admin_maintenance_top'); ?>

<?php 
    $tabs = array('General', 'Cache', 'Debug', 'Database', 'Other');
    $tabs = array('General', 'Cache', 'Debug', array('Database', array('db_tables' => $db_tables, 'some' => 'ds')), 'Other');
    
    buildtabs($h, 'maintenance', $tabs);
?>

<?php $h->pluginHook('admin_maintenance_bottom'); ?>


<?php 

// TODO
// Would like to put this function in the core somewhere
function buildTabs($h, $page = '', $tabs = array())
{
    if (!$tabs || !$page) return false;
    
    // first extract the data and populate the name array fields
    foreach ($tabs as $tab) {
        if (is_array($tab)) {                                   
            $names[] = $tab[0];
            $dataItems = $tab[1];
            
            if (is_array($dataItems)) { 
                foreach ($dataItems as $key => $dataItem) {
                    $$key = $dataItem;
                }
            }
        } else {
            $names[] = $tab;
        }
    }    
    
    // tab structure
    $active = " class = 'active'";
        
    echo '<ul class="nav nav-tabs" id="Tabs">';
    foreach ($names as $name) {        
        echo '<li' . $active . '><a href="#' . strtolower($name) . '" data-toggle="tab">' . ucfirst($name) . '</a></li>';
        if ($active == " class = 'active'") $active = '';
    }
    echo '</ul>';
    
    // page content
    $active = " active";
    
    echo '<div class="tab-content">';
        foreach ($names as $name) {    
           echo '<div class="tab-pane' . $active . '" id="' . strtolower($name) . '">';           
               $h->template($page .'/' . strtolower($name), 'admin');
               if ($active == " active") $active = '';
           echo '</div>';        
        }
    echo '</div>';
}

?>
