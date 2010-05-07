<?php
/**
 * The StopSpam class is based on this: http://www.stopforumspam.com/forum/t598-Basic-file-read
 *
 * PHP version 5
 *
 */
    
class StopSpamFunctions
{    
    /**
     * Check if the user is a spammer
     */
    public function checkSpammers($type = 'ip', $value)
    {
        //Load the file, and implode the array.
        $xml = implode('',file("http://www.stopforumspam.com/api?" . $type . "=" . $value));
        
        //Start new xml parser.
        $p = xml_parser_create();
        
        //Get the xml into an array
        xml_parse_into_struct($p, $xml, $vals, $index);
        
        //Free some memory by clearing the xml parser
        xml_parser_free($p);
        
        //We don't need $index or the $xml any more
        unset($index,$xml);
        
        
        //Prepare the return array
        $return = array();
        
        //Now we are going to make the aray useable
        foreach ($vals as $array) {
            //If it's the opening array we can do it slightly differnetly
            if($array['type'] == 'open'){
                //Just get weather it was sucess or not.
                $return[$array['tag']] = $array['attributes']['SUCCESS'];
            } elseif($array['type'] == 'complete') {
                //Else just get the value
                $return[$array['tag']] = $array['value'];
            }
        }
        
        //Save a bit more memory by clearing the vals array
        unset($vals);
        
        //Now make time into a unix timestamp
        if($return['LASTSEEN']){
            //Sepparate the timestamp into the time and the date
            $time = explode(' ',$return['LASTSEEN']);
            //Sepparate the date
            $date = explode("-",$time[0]);
            //Sepparate the time
            $time = explode("-",$time[0]);
            
            //Now make the time, note we times by 1 to remove leading zeros, if we don't then php can sometimes use the octal system instead of decimal.
            $return['UNIXLASTSEEN'] = gmmktime($time[0]*1,$time[1]*1,$time[2]*1,$date[1]*1,$date[2]*1,$date[0]*1);
        }
        
        //RESPONSE would be better as booleen, not a string
        if($return['RESPONSE'] == 'true'){ $return['RESPONSE'] = true; } else { $return['RESPONSE'] = false; }
        
        //Now return our array.
        return $return;
    }
    

    /**
     * Use the above function
     */
    public function isSpammer($type, $value)
    {
        //Get the xml results as an array from teh function above
        $result = $this->checkSpammers($type, $value);
        //Is he reported?
        if ($result['FREQUENCY'] > 0) {
            //He is a spammer
            return true;
        } else {
            //He is not reported as a spammer
            return false;
        }
    }
    
    
    /**
     * Add spammer to the StopForumSpam.com database
     */
    public function addSpammer($ip = '', $username = '', $email = '', $apikey = '')
    {
        if (!$ip || !$username || !$email || !$apikey || $ip == '127.0.0.1') { return false; }
        
        $url = "http://www.stopforumspam.com/add.php?";
        $url .= "username=" . urlencode($username);
        $url .= "&ip_addr=" . $ip;
        $url .= "&email=" . urlencode($email);
        $url .= "&api_key=" . $apikey;
        
        require_once(EXTENSIONS . 'SWCMS/HotaruHttpRequest.php');
        $r = new HotaruHttpRequest($url);
        $error = $r->DownloadToString();
        //if (!$error) { echo "Success"; } else { echo $error; }
    }
}

?>