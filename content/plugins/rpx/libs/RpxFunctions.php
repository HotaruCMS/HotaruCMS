<?php
/**
 * The RpxFunctions class contains some more useful methods for using RPX
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
    
class RpxFunctions
{
    /**
     * Make api call to RPX to get profile data
     *
     * @param string $token
     * @param string $api_key - RPX api key
     * @return array $rpx_profile
     */
    public function getProfile($token = '', $api_key = '')
    {
        //$post_data = array('token' => $token, 'token_url' => $this->tokenUrl, 'apiKey' => $this->apiKey, 'format' => 'json');  ARRAYS DON'T WORK!!!!
        $post_data = 'token='. $token . '&apiKey=' . $api_key . '&format=json'; 
        
        // make the api call using libcurl
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, 'https://rpxnow.com/api/v2/auth_info');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $raw_json = curl_exec($curl);
        curl_close($curl);

        // parse the json response into an associative array
        $auth_info = json_decode($raw_json, true);

        // process the auth_info response
        if ($auth_info['stat'] == 'ok') {
            $rpx_profile = $auth_info['profile'];
            return $rpx_profile;
        } else {
            // error
            $error = '<b>Error:</b> ' . $auth_info['err']['msg'] . '<br/>';
            die($error); exit;
        }
    }
    

    /**
     * Map a user on RPX
     *
     * @param int $pkey - user id
     * @param string $ident - unique identifying url from Twitter etc.
     * @return string $auth_info['stat'] - status, e.g. "ok"
     */
    public function map($pkey, $ident, $api_key = '')
    {
        // Map the new user's primary key (user_id) to their OpenId:
        //$post_data = array('primaryKey' => $pkey, 'apiKey' => apiKey, 'identifier' => $ident, 'format' => 'json'); ARRAYS DON'T WORK!!!!
        $post_data = 'primaryKey='. $pkey . '&apiKey=' . $api_key . '&identifier=' . $ident . '&format=json'; 
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, 'https://rpxnow.com/api/v2/map');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $raw_json = curl_exec($curl);
        curl_close($curl);

        // parse the json response into an associative array
        $auth_info = json_decode($raw_json, true);
        
        // process the auth_info response

        return $auth_info['stat'];
    }


    /**
     * Unmap a user on RPX
     *
     * @param int $pkey - user id
     * @param string $ident - unique identifying url from Twitter etc.
     * @return string $auth_infor['status'] - hopefully "ok"
     */
    public function unmap($pkey, $ident, $api_key = '')
    {
        // Map the new user's primary key (user_id) to their OpenId:
        //$post_data = array('primaryKey' => $pkey, 'apiKey' => apiKey, 'identifier' => $ident, 'format' => 'json'); 
        $post_data = 'primaryKey='. $pkey . '&apiKey=' . $api_key . '&identifier=' . $ident . '&format=json'; 
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, 'https://rpxnow.com/api/v2/unmap');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $raw_json = curl_exec($curl);
        curl_close($curl);

        // parse the json response into an associative array
        $auth_info = json_decode($raw_json, true);

        // return auth_info response
        return $auth_info['stat'];
    }

    /**
     * Get a user's account mappings
     *
     * @param int $pkey - user id
     * @return array $auth_info
     */
    public function get_user_mappings($pkey = 0, $api_key = '')
    {

        // Map the new user's primary key (user_id) to their OpenId:
        // $post_data = array('primaryKey' => $pkey, 'apiKey' => apiKey, 'format' => 'json'); // arrays don't work!
        $post_data = 'primaryKey='. $pkey . '&apiKey=' . $api_key . '&format=json'; 
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, 'https://rpxnow.com/api/v2/mappings?');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $raw_json = curl_exec($curl);
        curl_close($curl);

        // parse the json response into an associative array
        $auth_info = json_decode($raw_json, true);

        // process the auth_info response
        /*
        if ($auth_info['stat'] == 'ok') {
            echo "succesfully got mappings<br />";
        } else {
            echo "couldn't get mappings<br />";
        }
        */
        
        return $auth_info;
    }
}