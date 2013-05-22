<?php

namespace models;

class Comments extends \ActiveRecord\Model
{
        # explicit table name since our table is not "books" 
        static $table_name =  TABLE_COMMENTS;

        # explicit pk since our pk is not "id" 
        static $primary_key = 'comment_id';
        
//        static $belongs_to = array(
//            array('users', 'readonly' => true)
//        );
}

?>