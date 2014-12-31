<?php

namespace Hotaru\Models2;

class SpamLog extends BaseModel
{
    protected $table = 'spamlog';
    
    // i think the primary keys should be changed to just id
    protected $primaryKey = 'spamlog_id';
    
    // change these in db
    const CREATED_AT = 'spamlog_updatedts';
    
    
    public static function getAll($h)
    {
        $sql = "SELECT * FROM " . TABLE_SPAMLOG;
        $model = $h->db->get_results($h->db->prepare($sql));
        
        return $model;
    }
    
    public static function get($h, $pluginFolder)
    {
        $sql = "SELECT * FROM " . TABLE_SPAMLOG . " WHERE spamlog_pluginfolder = %s";
        $model = $h->db->get_result($h->db->prepare($sql, $pluginFolder));
        
        return $model;
    }
    
    public static function count($h, $pluginFolder)
    {
        $sql = "SELECT count(*) FROM " . TABLE_SPAMLOG . " WHERE spamlog_pluginfolder = %s";
        $model = $h->db->get_var($h->db->prepare($sql, $pluginFolder));
        
        return $model;
    }
    
    
    public static function add($h, $pluginFolder, $type, $email)
    {
        $sql = "INSERT INTO " . TABLE_SPAMLOG . " (spamlog_pluginfolder, spamlog_type, spamlog_email) VALUES (%s, %d, %s)";
        $result = $h->db->query($h->db->prepare($sql, $pluginFolder, $type, $email));

        return $result;
    }
    
}
