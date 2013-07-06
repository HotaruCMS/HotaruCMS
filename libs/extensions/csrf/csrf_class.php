<?php

/* Customized version of http://www.phpclasses.org/browse/package/5313.html */
// have added an index to token_action
// removed index from token_key, 
// changed key to varchar and changed timestamp field
class csrf
{
    public  $action = '';   // action page the script is good for
    public  $life   = 0;    // minutes for which key is good
    private $table  = '';
    private $sid;           // session id of user


    public function csrfInit($h, $type = 'check', $action = 'unspecified', $life = 60)
    {
        $this->sid  = preg_replace('/[^a-z0-9]+/i', '', session_id());
        $this->action = (!$action ) ? $this->action = $h->getPagename() : $this->action = $action;
        $this->table = DB_PREFIX . 'tokens';
        $this->life = is_int($life) ? $life : 60;

        if ($type == 'set') { 
            $h->csrfToken = $this->csrfkey($h);     // set a new token
        } else {
            $result = $this->checkcsrf($h);         // check existing token, then clear it
            //$h->csrfToken = $this->csrfkey($h);     // set a new token
            return $result;                         // return result of check
        }
    }


    public function csrfkey($h) {
        $key = md5(microtime() . $this->sid . rand());
        $sql = "INSERT INTO " . $this->table . " (token_sid, token_key, token_stamp, token_action) VALUES (%s, %s, TIMESTAMPADD(MINUTE, %d, CURRENT_TIMESTAMP()), %s)";
        $h->db->query($h->db->prepare($sql, $this->sid, $key, $this->life, $this->action));
        //print $h->db->prepare($sql, $this->sid, $key, $this->life, $this->action) . '<br/>';
        return $key;
    }


    public function checkcsrf($h)
    {
        $this->cleanOld($h);
        
        $key = $h->cage->post->testAlnum('csrf');                  // try to get csrf token from POST
        
        if (!$key) { $key = $h->cage->get->testAlnum('csrf'); }    // try to get csrf token from GET
        if (!$key) { return false; }

        $cleanKey = preg_replace('/[^a-z0-9]+/','',$key);
        if (strcmp($key,$cleanKey) != 0) 
            return false;        
        
        $sql = "SELECT token_sid FROM " . $this->table . " WHERE token_sid = %s AND token_key = %s AND token_action = %s LIMIT 1";
        $valid = $h->db->get_var($h->db->prepare($sql, $this->sid, $cleanKey, $this->action));
        
        if (!$valid) return false;        
    
//        foreach ($results as $row) {
//            $valid = $row->token_sid;
//        }
        
//        if (isset($valid)) {
            $sql = "DELETE FROM " . $this->table . " WHERE token_sid = %s AND token_key = %s LIMIT 500";
            $h->db->query($h->db->prepare($sql, $valid, $cleanKey));                
//        }
        
        return true;
    }


    private function cleanOld($h)
    {
        // remove expired keys
        $sql = "DELETE FROM " . $this->table . " WHERE token_stamp < CURRENT_TIMESTAMP() LIMIT 500";
        $h->db->query($h->db->prepare($sql));
        return true;
    }


    public function logout($h)
    {
        $sql = "DELETE FROM " . $this->table . " WHERE token_sid = %s LIMIT 500";
        $h->db->query($h->db->prepare($sql, $this->sid));
        return true;
    }
}
?>
