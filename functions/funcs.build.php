<?php

/**
 * A collection of functions to deal with building html blocks
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

/**
 * 
 * Build tabs anc their content for twitter bootstrap style tabbing
 * Particulalry being used in admin section where we can have a folder/views structure
 * Content is called using the $h->template function where file has same name as #tab
 * 
 * @param type $h
 * @param type $page
 * @param type $tabs
 * @return boolean
 */
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
