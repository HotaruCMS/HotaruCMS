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
	 * Send Message
	 */
	public function sendMessage($h)
	{
		$result = $h->sendMessage($this->to, '', $this->subject, $this->body);
		
		if (is_array($result))
		{
			// error array!
			$this->errors = $result; 
			return false;
		} 
		else 
		{
			// must be the insert id:
			$this->id = $result;
		}
		
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
		$email_message = $h->lang['messaging_email_greeting'] . $this->to . "," . $skip_line;
		
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
}
?>