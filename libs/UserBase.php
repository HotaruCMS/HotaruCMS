<?php
/**
 * Base User functions for basic info, settings and permissions
 *
 * PHP version 5
 *
 * LICENSE: Hotaru CMS is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version. 
 *
 * Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE. 
 *
 * You should have received a copy of the GNU General Public License along 
 * with Hotaru CMS. If not, see http://www.gnu.org/licenses/.
 * 
 * @category  Content Management System
 * @package   HotaruCMS
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
class UserBase
{
    protected $id           = 0;
    protected $name         = '';
    protected $role         = 'member';
    protected $password     = 'password';
    protected $email        = '';
    protected $emailValid   = 0;
    protected $loggedIn     = false;
    protected $perms        = array();  // permissions
    protected $ip           = 0;
    

    /**
     * Access modifier to set protected properties
     */
    public function __set($var, $val)
    {
        $this->$var = $val;  
    }
    
    
    /**
     * Access modifier to get protected properties
     */
    public function __get($var)
    {
        return $this->$var;
    }
    
    
    /**
     * Set permission 
     *
     * @param string $perm_name
     * @param mixed $setting
     */
    public function setPermission($perm_name, $setting)
    {
        // don't need options for each user:
        if ($perm_name == 'options') { return false; }
        
        $this->perms[$perm_name] = $setting;
    }
    
    
    /**
     * Set ALL permissions 
     *
     * @param string $setting
     */
    public function setAllPermissions($perms)
    {
        foreach ($perms as $key => $value) {
            $this->setPermission($key, $value);
        }
    }
    
    
    /**
     * Get permission 
     *
     * @param string $perm_name
     * @return mixed 
     */
    public function getPermission($perm_name)
    {
        if (!isset($this->perms[$perm_name])) { 
            return false; 
        } else {
            return $this->perms[$perm_name];
        }
    }
    
    
    /**
     * Get ALL permissions (serialized)
     *
     * @return string 
     */
    public function getAllPermissions()
    {
        return $this->perms;
    }
    
    
    /**
     * Get basic user details
     *
     * @param int $userid 
     * @param string $username
     * @return array|false
     *
     * Note: Needs either userid or username, not both
     */    
    public function getUserBasic($hotaru, $userid = 0, $username = '', $no_cache = false)
    {
        // Prepare SQL
        if ($userid != 0){              // use userid
            $where = "user_id = %d";
            $param = $userid;
        } elseif ($username != '') {    // use username
            $where = "user_username = %s";
            $param = $username;
        } else {
            return false;
        }
        
        // Build SQL
        $query = "SELECT * FROM " . TABLE_USERS . " WHERE " . $where;
        $sql = $hotaru->db->prepare($query, $param);
        
        if (!isset($hotaru->vars['tempUserCache'])) { $hotaru->vars['tempUserCache'] = array(); }

        // If this query has already been read once this page load, we should have it in memory...
        if (!$no_cache && array_key_exists($sql, $hotaru->vars['tempUserCache'])) {
            // Fetch from memory
            $user_info = $hotaru->vars['tempUserCache'][$sql];
        } else {
            // Fetch from database
            $user_info = $hotaru->db->get_row($sql);
            $hotaru->vars['tempUserCache'][$sql] = $user_info;
        }

        if (!$user_info) { return false; }
        
        $this->id = $user_info->user_id;
        $this->name = $user_info->user_username;
        $this->password = $user_info->user_password;
        $this->role = $user_info->user_role;
        $this->email = $user_info->user_email;
        $this->emailValid = $user_info->user_email_valid;
        $this->ip = $user_info->user_ip;
        
        // If a new plugin is installed, we need a way of adding any new default permissions
        // that plugin provides. So, we get all defaults, then overwrite with existing perms.
        
        // get default permissions for the site
        $default_perms = $this->getDefaultPermissions($hotaru, $this->role);
        
        // get existing permissions for the user
        $existing_perms = unserialize($user_info->user_permissions);
        
        // merge permissions
        $updated_perms = array_merge($default_perms, $existing_perms);
        
        $this->setAllPermissions($updated_perms);
        $user_info->user_permissions = serialize($updated_perms);   // update user_info
        
        return $user_info;
    }
    
    
    /**
     * Update a user
     */
    public function updateUserBasic($hotaru, $userid = 0)
    {
        //determine if the current user is the same as this object's user
        if($userid != $this->id) {
            $updatedby = $userid;
        } else {
            $updatedby = $this->id;
        }
        
        if ($this->id != 0) {
            $sql = "UPDATE " . TABLE_USERS . " SET user_username = %s, user_role = %s, user_password = %s, user_email = %s, user_permissions = %s, user_ip = %s, user_updateby = %d WHERE user_id = %d";
            $hotaru->db->query($hotaru->db->prepare($sql, $this->name, $this->role, $this->password, $this->email, serialize($this->getAllPermissions()), $this->ip, $updatedby, $this->id));
            return true;
        } else {
            return false;
        }
    }
    
    
    /**
     * Default permissions
     *
     * @param string $role or 'all'
     * @param string $field 'site' for site defaults and 'base' for base defaults
     * @param book $options_only returns just the options if true
     * @return array $perms
     */
    public function getDefaultPermissions($hotaru, $role = '', $defaults = 'site', $options_only = false) 
    {
        $perms = array(); // to be filled with default permissions for this user
        
        if ($defaults == 'site') { 
            $field = 'miscdata_value';  // get site permissions
        } else {
            $field = 'miscdata_default'; // get base permissions (i.e. the originals)
        }
        
        // get default permissions from the database:
        $query = "SELECT " . $field . " FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
        $sql = $hotaru->db->prepare($query, 'permissions');
        
        // Create temp cache array
        if (!isset($hotaru->vars['tempPermissionsCache'])) { $hotaru->vars['tempPermissionsCache'] = array(); }
        
        // If this query has already been read once this page load, we should have it in memory...
        if (array_key_exists($sql, $hotaru->vars['tempPermissionsCache'])) {
            // Fetch from memory
            $db_perms = $hotaru->vars['tempPermissionsCache'][$sql];
        } else {
            // Fetch from database
            $db_perms = $hotaru->db->get_var($sql);
            $hotaru->vars['tempPermissionsCache'][$sql] = $db_perms;
        }

        $permissions = unserialize($db_perms);
        
        if (!$permissions) { return false; }
        
        if ($options_only) {
            return $permissions['options']; // the editPermissions function in the Users plugin needs these 
        }
        
        if ($role == 'all') { return $permissions; } // plugins need all permissions and options when installed
                
        unset($permissions['options']); // don't need the options anymore
        
        foreach ($permissions as $perm => $roles) { 
            if (isset($roles[$role])) {
                $perms[$perm] = $roles[$role];  // perm for this role
            } else {
                $perms[$perm] = $roles['default']; // default perm because nothing specified for this role
            }
        }
        
        return $perms;
    }
    
    
    /**
     * Update Default permissions
     *
     * @param array $new_perms from a plugin's install function
     * @param string $defaults - either "site", "base" or "both" 
     */
    public function updateDefaultPermissions($hotaru, $new_perms = array(), $defaults = 'both') 
    {
        if (!$new_perms) { return false; }
        
        // get and merge permissions
        if ($defaults == 'site')
        {
            $site_perms = $this->getDefaultPermissions($hotaru,'all', 'site'); //get site defaults
            $site_perms = array_merge_recursive($site_perms, $new_perms); // merge
            $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s WHERE miscdata_key = %s";
            $hotaru->db->query($this->db->prepare($sql, serialize($site_perms), 'permissions'));
        } 
        elseif ($defaults == 'base')
        {
            $base_perms = $this->getDefaultPermissions($hotaru,'all', 'base'); // get base defaults
            $base_perms = array_merge_recursive($site_perms, $new_perms); // merge
            $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_default = %s WHERE miscdata_key = %s";
            $hotaru->db->query($this->db->prepare($sql, serialize($base_perms), 'permissions'));
        }
        else 
        {
            $site_perms = $this->getDefaultPermissions($hotaru,'all', 'site'); //get site defaults
            $site_perms = array_merge_recursive($site_perms, $new_perms); // merge
            $base_perms = $this->getDefaultPermissions($hotaru,'all', 'base'); // get base defaults
            $base_perms = array_merge_recursive($site_perms, $new_perms); // merge
            $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s, miscdata_default = %s WHERE miscdata_key = %s";
            $hotaru->db->query($this->db->prepare($sql, serialize($site_perms), serialize($base_perms), 'permissions'));
        }
    }

    
    /**
     * update permissions in the database
     *
     * @param int $userid
     */
    public function updatePermissions($hotaru)
    {
        $sql = "UPDATE " . TABLE_USERS . " SET user_permissions = %s WHERE user_id = %d";
        $hotaru->db->get_var($hotaru->db->prepare($sql, serialize($this->getAllPermissions()), $this->id));
    }
}