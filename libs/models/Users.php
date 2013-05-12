<?php

namespace models;
    
    class Users extends \ActiveRecord\Model
    {
        # explicit table name since our table is not "users" 
        static $table_name = TABLE_USERS;

        # explicit pk since our pk is not "id" 
        static $primary_key = 'user_id';
    }


?>