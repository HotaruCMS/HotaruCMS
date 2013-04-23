<?php

namespace models;

class Postvotes extends \ActiveRecord\Model
{
        # explicit table name since our table is not "books" 
        static $table_name =  'hotaru_postvotes';

        # explicit pk since our pk is not "id" 
        static $primary_key = 'vote_post_id, vote_user_id';
}

?>