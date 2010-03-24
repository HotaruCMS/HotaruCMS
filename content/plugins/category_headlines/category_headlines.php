<?php
/**
 * name: Category Headlines
 * description: Category Headlines
 * version: 0.1
 * folder: category_headlines
 * class: CategoryHeadlines
 * type: categorywidget
 * hooks: install_plugin, admin_plugin_settings, admin_sidebar_plugin_settings
 * requires: widgets 0.6
 * author: shibuya246
 * authorurl: http://shibuya246.com
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


class CategoryHeadlines
{
    public function install_plugin($h)
{
        // widget
        $h->addWidget('category_headlines', 'category_headlines', '');  // plugin name, function name, optional arguments
}

    public function widget_category_headlines($h) {

       $category_headlines_settings = $h->getSerializedSettings('category_headlines');
      
       $cats_id = array(1,5,6);  // $categoryheadlines_settings['cats']

       foreach ($cats_id as $cat_id) {
          $posts = $this->getCats($h, $cat_id,  '', $category_headlines_settings['limit']);

           if ($posts) {
               foreach($posts as $post) {                  
                   $h->readPost(0,$post);
                   $h->displayTemplate('category_headlines_box','category_headlines', false);

               }
           }
       }

            
    }

    public function getCats($h, $cat_id = 0, $status='', $limit = 0) {
       if (!isset($h->vars['SB'])) { $h->vars['SB'] = new SbBaseFunctions($h); }
       $h->vars['select'] = "*";
       $h->vars['filter']['post_category=%d'] = $cat_id;
       $h->vars['filter']['post_status = %s'] = $limit;
       $h->vars['limit'] = $limit;
       $h->vars['orderby'] = 'post_date DESC';

       return $h->vars['SB']->prepareList($h, $status, 'posts');
    }
}

?>