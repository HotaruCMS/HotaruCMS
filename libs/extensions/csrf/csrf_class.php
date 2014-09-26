<?php

class csrf
{
    public  $action = '';   // action page the script is good for
    public  $life   = 0;    // minutes for which key is good
    //private $table  = '';
    private $sid;           // session id of user


    public function csrfInit($h, $type = 'check', $action = 'unspecified', $life = 60)
    {
        $this->sid  = preg_replace('/[^a-z0-9]+/i', '', session_id());
        $this->action = (!$action ) ? $this->action = $h->getPagename() : $this->action = $action;
        //$this->table = DB_PREFIX . 'tokens';
        $this->life = is_int($life) ? $life : 60;

        if ($type == 'set') { 
            $h->csrfToken = $this->csrfkey($h);     // set a new token
        } else {
            $result = $this->checkcsrf($h);         // check existing token, then clear it
            return $result;                         // return result of check
        }
    }


    public function csrfkey($h) {
        
        //send the old token to csrf-form
        $token=$this->get_from_session($h, "csrf");
        $this->store_in_session($h, "csrf-form",$token);
        
        // set new token
        $token=$this->csrfguard_generate_token($h, "csrf");
        return $token;
        
        //$key = md5(microtime() . $this->sid . rand());
        //$sql = "INSERT INTO " . $this->table . " (token_sid, token_key, token_stamp, token_action) VALUES (%s, %s, TIMESTAMPADD(MINUTE, %d, CURRENT_TIMESTAMP()), %s)";
        //$h->db->query($h->db->prepare($sql, $this->sid, $key, $this->life, $this->action));
        //print $h->db->prepare($sql, $this->sid, $key, $this->life, $this->action) . '<br/>';
        //return $key;
    }


    public function checkcsrf($h)
    {
        $key = $h->cage->post->testAlnum('csrf');                  // try to get csrf token from POST
        
        if (!$key) { $key = $h->cage->get->testAlnum('csrf'); }    // try to get csrf token from GET
        if (!$key) { $h->messages[$h->lang['error_csrf']] = 'red'; return false; }

        $cleanKey = preg_replace('/[^a-z0-9]+/','',$key);
        $checkToken = $this->csrfguard_validate_token($h, "csrf-form", $cleanKey);
        
        //$h->messages["key: " . $cleanKey] = 'red';
        
        if (strcmp($key,$cleanKey) != 0 || !$checkToken) 
        {
            $h->messages[$h->lang['error_csrf']] = 'red';
            return false;        
        }
        
        $this->cleanOld($h);
        
        //$sql = "SELECT token_sid FROM " . $this->table . " WHERE token_sid = %s AND token_key = %s AND token_action = %s LIMIT 1";
        //$valid = $h->db->get_var($h->db->prepare($sql, $this->sid, $cleanKey, $this->action));
        
        //if (!$valid) return false;        
    
//        foreach ($results as $row) {
//            $valid = $row->token_sid;
//        }
        
//        if (isset($valid)) {
            //$sql = "DELETE FROM " . $this->table . " WHERE token_sid = %s AND token_key = %s LIMIT 500";
            //$h->db->query($h->db->prepare($sql, $valid, $cleanKey));                
//        }
        
        if ($h->isDebug) $h->messages[$h->lang['debug_success_csrf']] = 'blue';
        
        return true;
    }


    private function cleanOld($h)
    {
        $this->unset_session("csrf-form");
        //$h->messages['clean csrf-form'] = 'blue';
        
        // remove expired keys
        //$sql = "DELETE FROM " . $this->table . " WHERE token_stamp < CURRENT_TIMESTAMP() LIMIT 500";
        //$h->db->query($h->db->prepare($sql));
        return true;
    }


    public function logout($h)
    {
        unset_session("csrf-form");
        //unset_session("csrf");
        
        //$sql = "DELETE FROM " . $this->table . " WHERE token_sid = %s LIMIT 500";
        //$h->db->query($h->db->prepare($sql, $this->sid));
        return true;
    }
    
    
    
    
    
    function store_in_session($h, $key,$value)
    {
            if (isset($_SESSION))
            {
                    //$h->messages["set session: " . $key . ' - ' . $value] = 'green';
                    $_SESSION[$key]=$value;
            }
    }
    public function unset_session($key)
    {
            $_SESSION[$key]=' ';
            unset($_SESSION[$key]);
    }
    function get_from_session($h, $key)
    {
            if (isset($_SESSION[$key]))
            {
                    //$h->messages["validate session: " . $key . ' - ' . $_SESSION[$key]] = 'blue';
                    return $_SESSION[$key];
            }
            else {  return false; }
    }
    
    
    function csrfguard_generate_token($h, $unique_form_name)
    {
            if (function_exists("hash_algos") and in_array("sha512",hash_algos()))
            {
                    $token=hash("sha512",mt_rand(0,mt_getrandmax()));
            }
            else
            {
                    $token=' ';
                    for ($i=0;$i<128;++$i)
                    {
                            $r=mt_rand(0,35);
                            if ($r<26)
                            {
                                    $c=chr(ord('a')+$r);
                            }
                            else
                            { 
                                    $c=chr(ord('0')+$r-26);
                            } 
                            $token.=$c;
                    }
            }
            //$h->messages["create token: " . $unique_form_name . ' - ' . $token] = 'green';
            $this->store_in_session($h, $unique_form_name,$token);
            return $token;
    }
    function csrfguard_validate_token($h, $unique_form_name,$token_value)
    {
            $token=$this->get_from_session($h, $unique_form_name); 
            
            if ($token===false)
            {
                    return false;
            }
            elseif ($token===$token_value)
            {
                    $result=true;
            }
            else
            { 
                    $result=false;
            } 
            $this->unset_session($unique_form_name);
            return $result;
    }
    
    
    
}
?>
