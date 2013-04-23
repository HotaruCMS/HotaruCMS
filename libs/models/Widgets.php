<?php

namespace models;

class Widgets extends \ActiveRecord\Model
{
        # explicit table name since our table is not "books" 
        static $table_name =  'hotaru_widgets';

        # explicit pk since our pk is not "id" 
        static $primary_key = 'widget_id';
}

?>