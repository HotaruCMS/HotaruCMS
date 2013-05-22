<?php

namespace models;

class Categories extends \ActiveRecord\Model
{
        # explicit table name since our table is not "books" 
        static $table_name =  TABLE_CATEGORIES;

        # explicit pk since our pk is not "id" 
        static $primary_key = 'category_id';
        
//        static $belongs_to = array(
//            array('users', 'readonly' => true)
//        );
}

?>