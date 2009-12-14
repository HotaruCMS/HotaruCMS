<?php

/* Customized version of http://www.phpclasses.org/browse/package/5313.html */

class csrf
{
    public  $action = '';   // action page the script is good for
    public  $life   = 0;    // minutes for which key is good
    private $table  = '';
    private $sid;           // session id of user


    public function csrfInit($hotaru, $type = 'check', $action = 'unspecified', $life = 10)
    {
        $this->sid  = preg_replace('/[^a-z0-9]+/i', '', session_id());
        $this->action = (!$action ) ? $this->action = $hotaru->getPagename() : $this->action = $action;
        $this->table = DB_PREFIX . 'tokens';
        $this->life = $life;

        if ($type == 'set') { 
            $hotaru->csrfToken = $this->csrfkey($hotaru);   // set a new token
        } else {
            $result = $this->checkcsrf($hotaru);            // check existing token, then clear it
            $hotaru->csrfToken = $this->csrfkey($hotaru);   // set a new token
            return $result;                                 // return result of check
        }
    }


    public function csrfkey($hotaru) {
        $key = md5(microtime() . $this->sid . rand());
        $stamp = time() + (60 * $this->life);
        $sql = "INSERT INTO " . $this->table . " (token_sid, token_key, token_stamp, token_action) VALUES (%s, %s, %d, %s)";
        $hotaru->db->query($hotaru->db->prepare($sql, $this->sid, $key, $stamp, $this->action));
        return $key;
    }


    public function checkcsrf($hotaru)
    {
        $this->cleanOld($hotaru);
        
        $key = $hotaru->cage->post->testAlnum('csrf');                  // try to get csrf token from POST
        if (!$key) { $key = $hotaru->cage->get->testAlnum('csrf'); }    // try to get csrf token from GET
        if (!$key) { return false; }

        $cleanKey = preg_replace('/[^a-z0-9]+/','',$key);
        if (strcmp($key,$cleanKey) != 0) {
            return false;
        } else {
            $sql = "SELECT token_sid FROM " . $this->table . " WHERE token_sid = %s AND token_key = %s AND token_action = %s";
            $results = $hotaru->db->get_results($hotaru->db->prepare($sql, $this->sid, $cleanKey, $this->action));
            if ($results) {
                foreach ($results as $row) {
                    $valid = $row->token_sid;
                }
            }
            if (isset($valid)) {
                $sql = "DELETE FROM " . $this->table . " WHERE token_sid = %s";
                $hotaru->db->query($hotaru->db->prepare($sql, $valid));
                return true;
            }
        }
    }


    private function cleanOld($hotaru)
    {
        // remove expired keys
        $exp = time();
        $sql = "DELETE FROM " . $this->table . " WHERE token_stamp < %d";
        $hotaru->db->query($hotaru->db->prepare($sql, $exp));
        return true;
    }


    public function logout($hotaru)
    {
        $sql = "DELETE FROM " . $this->table . " WHERE token_sid = %s";
        $hotaru->db->query($hotaru->db->prepare($sql, $this->sid));
        return true;
    }
}
?>