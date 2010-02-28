<?php

class MessagingFuncs
{
    protected $to           = '';
    protected $from         = '';
    protected $subject      = '';
    protected $body         = '';
    protected $errors       = array();
    
    /**
     * Access modifier to set protected properties
     */
    public function __set($var, $val)
    {
        $this->$var = $val;
    }
    
    
    /**
     * Access modifier to get protected properties
     * The & is necessary (http://bugs.php.net/bug.php?id=39449)
     */
    public function &__get($var)
    {
        return $this->$var;
    }
    
    /**
     * Find User - not being used yet!
     */
     public function findUser($h, $search = '')
     {
        if (strlen($search_term) < 3) { 
            array_push($this->errors, 'too_short');
            return false; 
        } 
        
        $h->vars['search_term'] = $search_term; // used to refill the search box after a search
        $where_clause = " WHERE user_username LIKE %s OR user_email LIKE %s"; 
        $sort_clause = ' ORDER BY user_date DESC'; // ordered by newest user first
        $search_term = '%' . $search_term . '%';
        $count_sql = "SELECT count(*) AS number FROM " . TABLE_USERS . $where_clause . $sort_clause;
        $count = $h->db->get_var($h->db->prepare($count_sql, $search_term, $search_term));
        $sql = "SELECT * FROM " . TABLE_USERS . $where_clause . $sort_clause;
        $query = $h->db->prepare($sql, $search_term, $search_term);
        $results = $h->db->get_results($query);
        if ($results) {
            return $results;
        } else {
            return false;
        }
     }
     

    /**
     * Get Box Count
     */
     public function getBoxCount($h, $box = 'inbox')
     {
        if ($box == 'inbox') { $type = "to"; } else { $type = "from"; }
        
        $sql = "SELECT count(*) AS number FROM " . DB_PREFIX . "messaging WHERE message_archived = %s AND message_" . $type . " = %d AND message_" . $box . " = %d";
        $count = $h->db->get_var($h->db->prepare($sql, 'N', $h->currentUser->id, 1));
        
        return $count;
     }
     
     
    /**
     * Get Box Query
     */
     public function getBoxQuery($h, $box = 'inbox')
     {
        if ($box == 'inbox') { $type = "to"; } else { $type = "from"; }
        
        $sql = "SELECT message_id, message_from, message_to, message_date, message_subject, message_read FROM " . DB_PREFIX . "messaging WHERE message_archived = %s AND message_" . $type . " = %d AND message_" . $box . " = %d ORDER BY message_date DESC";
        $query = $h->db->prepare($sql, 'N', $h->currentUser->id, 1);
        
        return $query;
     }
     
     
    /**
     * Mark message as read
     */
     public function markRead($h, $message_id = 0)
     {
        if (!$message_id) { return false; }
        
        $sql = "UPDATE " . DB_PREFIX . "messaging SET message_read = %d WHERE message_id = %d";
        $h->db->query($h->db->prepare($sql, 1, $message_id));
     }
     
     
    /**
     * Get Message
     */
     public function getMessage($h, $message_id = 0)
     {
        if (!$message_id) { return false; }
        
        $sql = "SELECT * FROM " . DB_PREFIX . "messaging WHERE message_id = %d";
        $message = $h->db->get_row($h->db->prepare($sql, $message_id));
        
        if ($message) { return $message; } else { return false; }
     }
     
     
    /**
     * Send Message
     */
     public function sendMessage($h)
     {
        // check for errors
        if (!$this->to) {       array_push($this->errors, 'no_to');         return false; }
        if (!$this->subject) {  array_push($this->errors, 'no_subject');    return false; }
        if (!$this->body) {     array_push($this->errors, 'no_body');       return false; }
        if ($h->userExists(0, $this->to) == "no") { 
                                array_push($this->errors, 'no_user');       return false; }
        
        // if no From field, assume current user
        if (!$this->from) { 
            $this->from = $h->currentUser->name;
        }
        
        // save to database
        $this->saveMessage($h);
        
        // code here to call sendEmailNotification IF PERMITTED
        $recipient = new UserAuth();
        $recipient_id = $h->getUserIdFromName($this->to);
        $recipient->getUserBasic($h, $recipient_id);
        $recipient_settings = $recipient->getProfileSettingsData($h, 'user_settings');
        if ($recipient_settings['pm_notify']) {
            $this->sendEmailNotification($h);
        }
        
        return true;
     }
     
     
    /**
     * Delete Message
     */
     public function deleteMessage($h, $message_id = 0, $box = 'inbox')
     {
        if (!$message_id) { return false; }
        
        $sql = "UPDATE " . DB_PREFIX . "messaging SET message_" . $box . " = %d WHERE message_id = %d";
        $h->db->query($h->db->prepare($sql, 0, $message_id));
     }
     
     
    /**
     * Save to database
     */
     private function saveMessage($h)
     {
        // we did checks in sendMessage so we know the data is okay, 
        // and this function private in case anyone tries to use it directly
        
        // get ids
        $from_id = $h->getUserIdFromName($this->from);  // get the ID of the sender
        $to_id = $h->getUserIdFromName($this->to);  // get the ID of the recipient
        
        // SQL
        $sql = "INSERT INTO " . DB_PREFIX . "messaging ";
        $sql .= "(message_from, message_to, message_date, message_subject, message_content, message_updateby) ";
        $sql .= "VALUES(%d, %d, CURRENT_TIMESTAMP, %s, %s, %d)";
        
        // prepare the query
        $query = $h->db->prepare($sql, $from_id, $to_id, urlencode($this->subject), urlencode($this->body), $h->currentUser->id);
        
        // save to database
        $h->db->query($query);
        
        // get last insert id (appended to link in email notification)
        $this->id = $h->db->get_var($h->db->prepare("SELECT LAST_INSERT_ID()"));
     }
     
     
    /**
     * Send Email Notification of new message
     */
     public function sendEmailNotification($h)
     {
        $next_line = "\r\n";
        $skip_line = "\r\n\r\n";
        
        $to_id = $h->getUserIdFromName($this->to);  // get the ID of the recipient
        $to_email = $h->getEmailFromId($to_id);     // get the email address of the recipient
        
        $email_subject = $h->lang['messaging_email_subject'];   // email subject (New message from SITE_NAME)
        
        // Hi username...
        $email_message = "Hi " . $this->to . "," . $skip_line;
        
        // You've been sent a private message from...
        $email_message .= $h->lang['messaging_email_message'] . $this->from . $skip_line;
        
        // The full content of the message sent
        $email_message .= '-------' . $next_line;
        $email_message .= $this->subject . $skip_line;
        $email_message .= $this->body . $next_line;
        $email_message .= '-------' . $skip_line;
        
        // *** PLEASE DON'T REPLY TO THIS EMAIL ***
        $email_message .= $h->lang['messaging_email_no_reply'] . $skip_line;
        
        // You can reply to the message on " . SITE_NAME . " here: 
        $email_message .= $h->lang['messaging_email_reply_here'] . BASEURL . "index.php?page=compose&reply=" . $this->id . $skip_line;
        
        // Thank you,
        $email_message .= $h->lang['messaging_email_thank_you'] . $next_line;
        
        // SITE_NAME Admin
        $email_message .= $h->lang['messaging_email_site_admin'] . $next_line;
        $email_message .= BASEURL . $next_line;

        // SEND EMAIL        
        $h->email($to_email, $email_subject, $email_message);
     }
}
?>