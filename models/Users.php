<?php

class Users extends ActiveRecord\Model
{
    # explicit table name since our table is not "users" 
    static $table_name = 'hotaru_users';    // DB_PREFIX . '_users';
  
    # explicit pk since our pk is not "id" 
    static $primary_key = 'user_id';
}

?>