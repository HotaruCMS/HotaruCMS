<?php

namespace HotaruModels;

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
        
        public static function getAllActiveNames()
        {
            $model = self::where('plugin_enabled', 1)->orderBy('plugin_order')->get(['plugin_name', 'plugin_folder', 'plugin_type']);   //->remember(30, 'plugin_names')
            return $model;
        }
        
        public static function getAllActiveNamesOrderByName()
        {
            $model = self::where('plugin_enabled', 1)->orderBy('plugin_name')->get(['plugin_name', 'plugin_folder', 'plugin_type']);   //->remember(30, 'plugin_names')
            return $model;
        }
        
        public static function getAllActiveDetails()
        {
            $model = self::where('plugin_enabled', 1)
                    ->orderBy('plugin_order', 'asc')
                    ->get(['plugin_id','plugin_folder','plugin_name', 'plugin_class', 'plugin_enabled']);   //remember(30, 'plugin_details')->
            return $model;
        }
        
        public static function getAllActiveAndInactiveDetails()
        {
            $model = self::get();   //remember(30, 'plugin_details')->
            return $model;
        }
        
        public static function getFirstFolderForClass($class)
        {
            $model = self::where('plugin_class', $class)->orderBy('plugin_order')->pluck('plugin_folder');  //->remember(30, 'plugin_folder')
            return $model;
        }
        
        public static function getEnabledStatus($folder)
        {
            $model = self::where('plugin_folder', $folder)->first();  //->remember(30, 'plugin_folder')
            return $model;
        }
        
        // count
        
        public static function countEnabled()
        {
            $model = self::where('plugin_enabled', 1)->count();   //->remember(5, 'plugin_enabled_count')
            return $model;
        }
        
        public static function countByPluginFolder($folder)
        {
            $model = self::where('plugin_folder', $folder)->count();   //->remember(5, 'plugin_folder_count')
            return $model;
        }
        
        public static function countByPluginType($type)
        {
            $model = self::where('plugin_type', $type)->count();   //->remember(5, 'plugin_type_count')
            return $model;
        }
        
        // scope
        
        public function scopeEnabled($query)
        {
            return $query->where('plugin_enabled', '=', 1);
        }
                 
        
        // save
        
        public static function makeUpdate($pluginName, $resource, $userId)
        {
            $model = self::where('plugin_name', $pluginName)->first();

            if (!$model) { return false; }
            
            $model->plugin_latestversion = $resource['version_string'];
            $model->plugin_resourceId = $resource['id'];
            $model->plugin_resourceVersionId = $resource['version_id'];
            $model->plugin_rating = $resource['rating_avg'];
            $model->plugin_updateby = $userId;
            
            $model->save();
            
            return true;
        }
        
        public static function updateEnabled($folder, $newStatus, $userId)
        {
            $model = self::where('plugin_folder', $folder)->first();

            if (!$model) { return false; }
            
            $model->plugin_enabled = $newStatus;
            $model->plugin_updateby = $userId;
            
            return $model->save();
        }
}

?>