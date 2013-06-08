<?php

/* Customized version of http://www.phpclasses.org/browse/package/5313.html */

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
        $this->life = $life;

        if ($type == 'set') { 
            $h->csrfToken = $this->csrfkey($h);   // set a new token
        } else {
            $result = $this->checkcsrf($h);            // check existing token, then clear it
            $h->csrfToken = $this->csrfkey($h);   // set a new token
            return $result;                                 // return result of check
        }
    }


    public function csrfkey($h) {
        $key = md5(microtime() . $this->sid . rand());
        $stamp = time() + (60 * $this->life);
        $sql = "INSERT INTO " . $this->table . " (token_sid, token_key, token_stamp, token_action) VALUES (%s, %s, %d, %s)";
        $h->db->query($h->db->prepare($sql, $this->sid, $key, $stamp, $this->action));
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
        
        $sql = "SELECT token_sid FROM " . $this->table . " WHERE token_sid = %s AND token_key = %s AND token_action = %s";
        $results = $h->db->get_results($h->db->prepare($sql, $this->sid, $cleanKey, $this->action));
        
        if (!$results) return false;        
    
        foreach ($results as $row) {
            $valid = $row->token_sid;
        }
        
        if (isset($valid)) {
            $sql = "DELETE FROM " . $this->table . " WHERE token_sid = %s AND token_key = %s";
            $h->db->query($h->db->prepare($sql, $valid, $cleanKey));                
        }
        
        return true;
    }


    private function cleanOld($h)
    {
        // remove expired keys
        $exp = time();
        $sql = "DELETE FROM " . $this->table . " WHERE token_stamp < %d";
        $h->db->query($h->db->prepare($sql, $exp));
        return true;
    }


    public function logout($h)
    {
        $sql = "DELETE FROM " . $this->table . " WHERE token_sid = %s";
        $h->db->query($h->db->prepare($sql, $this->sid));
        return true;
    }
}
?>
