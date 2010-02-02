<?php
/**
 * name: Categories Widget
 * description: Category list in a widget
 * version: 0.1
 * folder: categories_widget
 * class: CategoriesWidget
 * requires: category_manager 0.7
 * hooks: install_plugin
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

class CategoriesWidget
{
    public function install_plugin($h)
    {
        // widget
        $h->addWidget('categories_widget', 'categories');  // plugin name, function name, optional arguments
    } 
    
    
    /**
     * Displays categories as a tree
     *
     * @param mixed $args
     *
     * This isn't a plugin hook, but a public function call created in the Sidebar plugin.
     */
    public function widget_categories($h)
    {
        $sql = "SELECT * FROM " . TABLE_CATEGORIES . " ORDER BY category_order ASC";
        $the_cats = $h->db->get_results($h->db->prepare($sql));
        
        require_once(LIBS . 'Category.php');
        $catObj = new Category();
        
        echo "<h2 class='widget_head'>" . $h->lang["categories"] . "</h2>";
        echo "<div class='widget_body'>\n";
        echo "<ul class='categories_widget'>\n";
        foreach ($the_cats as $cat) {
            $cat_level = 1;    // top level category.
            if ($cat->category_safe_name != "all") {
                echo "<li>";
                if ($cat->category_parent > 1) {
                    $depth = $catObj->getCatLevel($h, $cat->category_id, $cat_level, $the_cats);
                    for($i=1; $i<$depth; $i++) {
                        echo "--- ";
                    }
                } 
                $category = stripslashes(html_entity_decode(urldecode($cat->category_name), ENT_QUOTES,'UTF-8'));
                echo "<a href='" . $h->url(array('category'=>$cat->category_id)) . "'>";
                echo $category . "</a></li>\n";
            }
        }
        echo "</ul></div>\n";
    }
}

?>