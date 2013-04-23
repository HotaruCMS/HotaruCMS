<?php

namespace models;

class Pluginsettings extends \ActiveRecord\Model
{
         # explicit table name since our table is not "books" 
        static $table_name =  'hotaru_pluginsettings';

        # explicit pk since our pk is not "id" 
        static $primary_key = 'psetting_id';
}

?>