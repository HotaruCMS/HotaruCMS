<?php

namespace HotaruModels2;

class Pluginhook extends BaseModel
{
        # explicit table name since our table is not "pluginhooks" 
        protected $table =  'pluginhooks';

        # explicit pk since our pk is not "id" 
        protected $primaryKey = 'phook_id';

        public static function getAllEnabled($h)
        {
//            $model = self::whereHas('plugin', function($query) {
//                            $query->where('plugin_enabled', 1);
//                        })
//                    ->get(['plugin_folder', 'plugin_hook']); //remember(30, 'pluginhook_all')
            
            $sql = "SELECT H.plugin_folder, H.plugin_hook FROM " . TABLE_PLUGINHOOKS . " AS H"
                    . " LEFT OUTER JOIN " . TABLE_PLUGINS . " AS P ON H.plugin_folder = P.plugin_folder"
                    . " AND P.plugin_enabled = 1";
            //$model = $h->mdb->query($sql, 1);
            
            $model = $h->db->get_results($sql);
            return $model;
        }
        
        public static function getHooks($h, $hook)
        {
//            $model = self::where('plugin_hook', $hook)->get(['plugin_folder', 'plugin_hook']);
//            return $model;
        }
        
        public static function removeHook($h, $folder)
        {
            $sql = "DELETE FROM " . TABLE_PLUGINHOOKS . " WHERE plugin_folder = %s";
            $h->db->query($h->db->prepare($sql, $folder));
        }
}
