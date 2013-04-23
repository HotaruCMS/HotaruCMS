<?php

namespace models;

class Plugins extends \ActiveRecord\Model
{
         # explicit table name since our table is not "books" 
        static $table_name =  'hotaru_plugins';

        # explicit pk since our pk is not "id" 
        static $primary_key = 'plugin_id';
}

?>