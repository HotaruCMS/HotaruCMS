<?php

namespace models;
  
    class Settings extends \ActiveRecord\Model
    {
        # explicit table name since our table is not "books" 
        static $table_name =  'hotaru_settings';

        # explicit pk since our pk is not "id" 
        static $primary_key = 'settings_id';

    }


?>