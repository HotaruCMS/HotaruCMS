<?php

namespace models;

class Posts extends \ActiveRecord\Model
{
        # explicit table name since our table is not "books" 
        static $table_name =  TABLE_POSTS;

        # explicit pk since our pk is not "id" 
        static $primary_key = 'post_id';
        
//        static $belongs_to = array(
//            array('users', 'readonly' => true)
//        );
}

?>