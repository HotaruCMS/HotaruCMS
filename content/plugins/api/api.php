<?php
/**
 * name: API
 * description: API server
 * version: 0.1
 * folder: api
 * class: Api
 * type: api
 * hooks: theme_index_top
 * author: shibuya246
 * authorurl: http://shibuya246.com
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

//require_once 'XML/Serializer.php';

class Api
{
    public function theme_index_top($h)
    {       
        if ($h->pageName == 'api') {                      
            $this->hotaruAPI($h);
            die(); exit;
        }        
    }
    
    
    public function hotaruAPI($h)
    {
        // set to get for testing
        $api_key = $h->cage->post->testAlnumLines('api_key');
        $method = $h->cage->post->getHtmLawed('method');
        $format = $h->cage->post->testAlnumLines('format');
        $args = $h->cage->post->getHtmLawed('args');
 
        $data = array("result"=>"empty");

//        print $method;
//        print $format;

        switch ($method) {
            case "hotaru.version.get":
                $data = $this->getHotaruVersion($h);
                break;
            case "hotaru.posts.getLatest":
                break;
            case "hotaru.activity.getLatest":
                $data = $this->getLatestActivity($h);
                break;
            case "hotaru.systemFeedback.add":
                $this->addSystemFeedback($h, $args);
                break;
            default:
                break;
        }

        switch ($format) {
            case "xml" :
                // An array of serializer options
//                $serializer_options = array (
//                   'addDecl' => TRUE,
//                   'encoding' => 'ISO-8859-1',
//                   'indent' => '  ',
//                   'rootName' => 'hotaru',
//                );
//                $Serializer = new XML_Serializer($serializer_options);
//                // Serialize the data structure
//                $status = $Serializer->serialize($data);
//                // Check whether serialization worked
//                if (PEAR::isError($status)) {
//                   die($status->getMessage());
//                }
//                // Display the XML document
//                header('Content-type: text/xml');
//                echo $Serializer->getSerializedData();
                break;
            case "php" :
                echo  serialize($data);                
                break;
            default:
                echo json_encode($data);
                break;
        }
    }



    public function getHotaruVersion($h) {     
        return array("version"=>$h->version);
    }

    public function getLatestActivity($h) {
        require_once(PLUGINS . 'activity/activity.php');
        $activity = new activity();
        $result = $activity->getLatestActivity($h);
        return $result;
    }

    public function addSystemFeedback($h, $args) {
        $data = unserialize($args);

        $origUrl = $data['hotaru_baseurl'];

        $result = $h->urlExists($origUrl);

        if ($result) {           
            $h->post = $result[0];
            $h->post->content = $h->debug->logSystemReport($h, $data);
            $return = $h->updatePost();
        }
        else {            
            $h->post = new Post();

            $h->post->author = 1;
            $h->post->status = 'new';
            $h->post->title = $data['hotaru_site_name'];
            $h->post->origUrl = $data['hotaru_baseurl'];
            $h->post->content = $h->debug->logSystemReport($h, $data);
            $return = $h->addPost();
        }
    }
}

?>