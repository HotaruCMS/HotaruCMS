<?php

namespace models;

class Plugins extends \ActiveRecord\Model
{
         # explicit table name since our table is not "plugins" 
        static $table_name =  TABLE_PLUGINS;

        # explicit pk since our pk is not "id" 
        static $primary_key = 'plugin_id';
}

?>