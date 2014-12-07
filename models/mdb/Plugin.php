<?php

namespace Hotaru\Models2;

class Plugin extends BaseModel
{
        # explicit table name since our table is not "pluginhooks" 
        protected $table =  'plugins';

        # explicit pk since our pk is not "id" 
        protected $primaryKey = 'plugin_id';

        // guarded columns in db on mass assignment like when using firstOrCreate
        protected $guarded = array('plugin_order');

        // change these in db
        const CREATED_AT = 'plugin_updatedts';

        // get
        
        public static function getAllDetails($h)
        {
            $sql = "SELECT * FROM " . TABLE_PLUGINS . " ORDER BY plugin_order ASC";
            $model = $h->db->get_results($sql); 
                         
            //$model = $h->mdb->queryObj($sql);
            
            return $model;
        }
        
        public static function getVersionNumber($h, $folder)
        {
            $sql = "SELECT plugin_version FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s";
            $model = $h->db->get_var($h->db->prepare($sql, $folder));
                         
            //$model = $h->mdb->queryObj($sql);
            
            return $model;
        }
        
        public static function getAllActiveNames($h)
        {
            $sql = "SELECT plugin_name, plugin_folder, plugin_type FROM " . TABLE_PLUGINS
                    . " WHERE plugin_enabled = 1"
                    . " ORDER BY plugin_order";            
            
            $model = $h->db->get_results($sql, ARRAY_A);
            //$model = $h->mdb->query($sql);
            
            return $model;
        }
        
        public static function getAllActiveNamesOrderByName($h)
        {
            $sql = "SELECT plugin_name, plugin_folder, plugin_type FROM " . TABLE_PLUGINS
                    . " WHERE plugin_enabled = 1"
                    . " ORDER BY plugin_name";  
            
            $model = $h->db->get_results($sql); 
            //$model = $h->mdb->query($sql, 1);
            
            return $model;
        }
        
        public static function getAllActiveDetails($h)
        {
            $sql = "SELECT plugin_id, plugin_folder, plugin_name, plugin_class, plugin_enabled FROM " . TABLE_PLUGINS
                    . " WHERE plugin_enabled = 1"
                    . " ORDER BY plugin_order ASC";  
            
            $model = $h->db->get_results($sql); 
            //$model = $h->mdb->query($sql, 1);
            
            return $model;
        }
        
        public static function getAllActiveAndInactiveDetails($h)
        {
            $sql = "SELECT * FROM " . TABLE_PLUGINS;  
            $model = $h->db->get_results($sql); 
            //$model = $h->mdb->query($sql);
            
            return $model;
        }
        
        public static function getFirstFolderForClass($h, $class)
        {
            $sql = "SELECT plugin_folder FROM " . TABLE_PLUGINS
                    . " WHERE plugin_class = %d"
                    . " ORDER BY plugin_order"
                    . " LIMIT 1";
            $model = $h->db->get_row($h->db->prepare($sql, $class));
            //$model = $h->mdb->queryFirstField($sql, $class);
            
            return $model;
        }
        
        public static function getEnabledStatus($h, $folder)
        {
            $model = $h->db->get_row($h->db->prepare("SELECT plugin_folder, plugin_enabled FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $folder));
		
            //$model = self::where('plugin_folder', $folder)->first();  //->remember(30, 'plugin_folder')
//            $sql = "SELECT * FROM " . TABLE_PLUGINS
//                    . " WHERE plugin_folder = %d";                                               
//            $model = $h->mdb->queryFirstRow($sql, $folder);
            
            return $model;
        }
        
        // count
        
        public static function countEnabled($h)
        {
            //$model = self::where('plugin_enabled', 1)->count();   //->remember(5, 'plugin_enabled_count')
            $model = $h->db->get_var("SELECT count(plugin_id) FROM " . TABLE_PLUGINS . " WHERE plugin_enabled = 1");                                                        
            
            return $model;
        }
        
        public static function countByPluginFolder($h,$folder)
        {
            $model = self::where('plugin_folder', $folder)->count();   //->remember(5, 'plugin_folder_count')
            return $model;
        }
        
        public static function countByPluginType($h,$type)
        {
            $model = self::where('plugin_type', $type)->count();   //->remember(5, 'plugin_type_count')
            return $model;
        }
                
        // save
        
        public static function makeUpdate($h, $pluginName, $resource, $userId)
        {
            $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_latestversion = %s, plugin_resourceId = %d, plugin_resourceVersionId = %d, plugin_rating = %d, plugin_updateby = %d"
                    . " WHERE (plugin_name = %s)";
            $h->db->query($h->db->prepare($sql, $resource['version_string'], $resource['id'], $resource['version_id'], $resource['rating_avg'], $userId, $pluginName));
		               
//            $model = self::where('plugin_name', $pluginName)->first();
//
//            if (!$model) { return false; }
//            
//            $model->plugin_latestversion = $resource['version_string'];
//            $model->plugin_resourceId = $resource['id'];
//            $model->plugin_resourceVersionId = $resource['version_id'];
//            $model->plugin_rating = $resource['rating_avg'];
//            $model->plugin_updateby = $userId;
//            
//            $model->save();
            
            return true;
        }
        
        public static function updateEnabled($h, $folder, $newStatus, $userId)
        {
            $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_enabled = %s, plugin_updateby = %d"
                    . " WHERE (plugin_folder = %s)";
            
            $h->db->query($h->db->prepare($sql, $newStatus, $userId, $folder));
	
            
//            $model = self::where('plugin_folder', $folder)->first();
//
//            if (!$model) { return false; }
//            
//            $model->plugin_enabled = $newStatus;
//            $model->plugin_updateby = $userId;
//            
//            return $model->save();
        }
}
