<?php

namespace Hotaru\Models;

class Messaging extends BaseModel
{
    protected $table = 'messaging';
    
    public function sender()
    {
        //return $this->hasOne('\Hotaru\Models\User', 'user_category_id', 'user_id');
    }
    
    public function reader()
    {
        //return $this->hasOne('\Hotaru\Models\User', 'user_category_id', 'user_id');
    }
    
    public static function getCount($box, $userId)
    {
        if ($box == 'inbox') { $direction = "to"; } else { $direction = "from"; }
        
        $model = self::where('message_archived','N')
                    ->where('message_' . $direction, $userId)
                    ->where('message_' . $box, 1)
                    ->count();
        
        return $model;
    }
    
    public static function getAll($box, $userId)
    {
        if ($box == 'inbox') { $direction = "to"; } else { $direction = "from"; }

        $model = self::where('message_archived','N')
                    ->where('message_' . $direction, $userId)
                    ->where('message_' . $box, 1)
                    ->orderBy('message_date', 'desc')
                    ->get();
        
        return $model;
    }	
    
    public static function getCountUnread($userId)
    {
        $model = self::where('message_archived','N')
                    ->where('message_to', $userId)
                    ->where('message_read', 0)
                    ->where('message_inbox', 1)
                    ->count();
        
        return $model;
    }
    
}