<?php

namespace Hotaru\Models2;

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
    
    public static function getCount($h, $box, $userId)
    {
        if ($box == 'inbox') { $direction = "to"; } else { $direction = "from"; }
        
        $sql = "SELECT count(*) FROM " . TABLE_MESSAGING . " WHERE message_archived = %s AND message_" . $direction . " = %d AND message_" . $box . " = %d";

        $model = $h->db->get_var($h->db->prepare($sql, 'N', $h->currentUser->id, 1));
        
//        $model = self::where('message_archived','N')
//                    ->where('message_' . $direction, $userId)
//                    ->where('message_' . $box, 1)
//                    ->count();
        
        return $model;
    }
    
    public static function getAll($h, $box, $userId)
    {
        if ($box == 'inbox') { $direction = "to"; } else { $direction = "from"; }

        $sql = "SELECT * FROM " . TABLE_MESSAGING . " WHERE message_archived = %s AND message_" . $direction . " = %d AND message_" . $box . " = %d";
        $sql .= " ORDER BY message_date DESC";
        
        $model = $h->db->get_results($h->db->prepare($sql, 'N', $h->currentUser->id, 1));
        
//        $model = self::where('message_archived','N')
//                    ->where('message_' . $direction, $userId)
//                    ->where('message_' . $box, 1)
//                    ->orderBy('message_date', 'desc')
//                    ->get();
        
        return $model;
    }	
    
    public static function getCountUnread($h, $userId)
    {
        $sql = "SELECT count(*) FROM " . DB_PREFIX . "messaging WHERE message_archived = %s AND message_to = %d AND message_read = %d AND message_inbox = %d";
        $query = $h->db->prepare($sql, 'N', $userId, 0, 1);

        $h->smartCache('on', 'messaging', 10, $query); // start using cache
        $model = $h->db->get_var($query);
        $h->smartCache('off'); // stop using cache  
            
//        $model = self::where('message_archived','N')
//                    ->where('message_to', $userId)
//                    ->where('message_read', 0)
//                    ->where('message_inbox', 1)
//                    ->count();
        
        return $model;
    }
    
}