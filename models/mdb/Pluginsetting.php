<?php

namespace Hotaru\Models2;

class PuginSetting extends BaseModel
{
        protected $table = 'pluginsettings';

        # explicit pk since our pk is not "id" 
        protected $primaryKey = 'phook_id';
        
        // get
        
        public static function getAll()
        {
//            $model = self::get(['plugin_folder', 'plugin_setting', 'plugin_value']);
//            return $model;
        }
        
        public static function getAllWhereEnabled($h)
        {
//            $model = self::whereHas('plugin', function($query) {
//                            $query->where('plugin_enabled', 1);
//                        })
//                        ->get(['plugin_folder', 'plugin_setting', 'plugin_value']);
                        
            $sql = "SELECT S.plugin_folder, S.plugin_setting, S.plugin_value FROM " . TABLE_PLUGINSETTINGS . " AS S"
                    . " LEFT OUTER JOIN " . TABLE_PLUGINS . " AS P ON S.plugin_folder = P.plugin_folder"
                    . " AND P.plugin_enabled = 1";
            
            $model = $h->db->get_results($sql, ARRAY_A);
                    
            //$model = $h->mdb->query($sql);
            
            return $model;
        }
}
    