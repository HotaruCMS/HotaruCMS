<?php

/* http://www.phpclasses.org/browse/package/5313.html */

class csrf
{
    public  $action = 'unspecified'; // action page the script is good for
    public  $life = 20; // minutes for which key is good
    private $table = '';
    private $sid; // session id of user
    private $db; // db database object


    public function csrf($db)
    {
        $sid = session_id();
        $this->db =& $db;
        $this->table = DB_PREFIX . 'tokens';
        $this->sid  = preg_replace('/[^a-z0-9]+/i','',$sid);
    }


    public function csrfkey() {
        $key = md5(microtime() . $this->sid . rand());
        $stamp = time() + (60 * $this->life);
        $sql = "INSERT INTO " . $this->table . " (token_sid, token_key, token_stamp, token_action) VALUES (%s, %s, %d, %s)";
        $this->db->query($this->db->prepare($sql, $this->sid, $key, $stamp, $this->action));
        return $key;
    }


    public function checkcsrf($key)
    {
        $this->cleanOld();
        $cleanKey = preg_replace('/[^a-z0-9]+/','',$key);
        if (strcmp($key,$cleanKey) != 0) {
            return false;
        } else {
            $sql = "SELECT token_sid FROM " . $this->table . " WHERE token_sid = %s AND token_key = %s AND token_action = %s";
            $results = $this->db->get_results($this->db->prepare($sql, $this->sid, $cleanKey, $this->action));
            if ($results) {
                foreach ($results as $row) {
                    $valid = $row->token_sid;
                }
            }
            if (isset($valid)) {
                $sql = "DELETE FROM " . $this->table . " WHERE token_sid = %s";
                $this->db->query($this->db->prepare($sql, $valid));
                return true;
            }
        }
    }


    private function cleanOld()
    {
        // remove expired keys
        $exp = time();
        $sql = "DELETE FROM " . $this->table . " WHERE token_stamp < %d";
        $this->db->query($this->db->prepare($sql, $exp));
        return true;
    }


    public function logout()
    {
        $sql = "DELETE FROM " . $this->table . " WHERE token_sid = %s";
        $this->db->query($this->db->prepare($sql, $this->sid));
        return true;
    }
}
?>