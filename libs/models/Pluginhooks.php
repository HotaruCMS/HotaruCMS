<?php

namespace models;

class Pluginhooks extends \ActiveRecord\Model
{
         # explicit table name since our table is not "books" 
        static $table_name =  'hotaru_pluginhooks';

        # explicit pk since our pk is not "id" 
        static $primary_key = 'phook_id';
}

?>