<?php

namespace models;

class Pluginhooks extends \ActiveRecord\Model
{
         # explicit table name since our table is not "pluginhooks" 
        static $table_name =  TABLE_PLUGINHOOKS;

        # explicit pk since our pk is not "id" 
        static $primary_key = 'phook_id';
}

?>