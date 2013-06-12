<?php
/**
 * Functions for sending emails
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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
    
class EmailFunctions
{
	protected $to           = '';
	protected $from         = '';
	protected $subject      = 'No Subject';
	protected $body         = '';
	protected $headers      = '';
	protected $type         = 'email';
	private $smtp   = NULL;
	
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
	 * Send emails - Note: properties must be set before calling this function
	 */
	public function doEmail()
	{
		if (!$this->body) { return false; }
		
		if (!$this->to) { $this->to = SITE_NAME . ' <' . SITE_EMAIL . '>'; }
		if (!$this->from) { $this->from = SITE_NAME . ' <' . SITE_EMAIL . '>'; }

		// Fixing the subject for non-ASCII characters:
		$this->subject = '=?UTF-8?B?'.base64_encode($this->subject).'?=';
		
		if (SMTP == 'true')
		{
			if (is_array($this->to)) { $to = $this->to['To']; } else { $to = $this->to; }
			if (!$this->headers) {
				$this->headers = array ('MIME-Version: 1.0\r\nFrom' => $this->from, '\r\nTo' => $to, 'Subject' => $this->subject);
			} else {
				$this->headers['To'] = $to;
			}

			// set content type to work with French accents, etc.
			if (!isset($this->headers['Content-Type'])) {
				$this->headers['Content-Type'] = 'text/plain; charset=UTF-8';
			}
		}
		else 
		{
			// if not using SMTP and no headers passed to this function, use default
			if (!$this->headers) { 
				$this->headers = "MIME-Version: 1.0\r\nFrom: " . $this->from . "\r\nReply-To: " . SITE_EMAIL . "\r\nX-Priority: 3\r\n";
			}

			// set content type to work with French accents, etc.
			if (stripos($this->headers, 'content-type') === false) {
				$this->headers .= 'Content-Type: text/plain; charset="UTF-8"'. "\r\n";
			}
		}

		switch ($this->type)
		{
			case 'log':
				require_once(LIBS . 'Debug.php');
				$debug = new Debug();
				$debug->openLog('email_log', 'a+');
				$content = $this->headers . "\r\n" . $this->to . "\r\n" . $this->subject . "\r\n" . $this->body . "\r\n\r\n";
				$content .= "**************************************************************\r\n\r\n";
				$debug->writeLog('email_log', $content);
				$debug->closeLog('email_log');
				break;
			case 'screen':
				echo "Headers: "; print_r($this->headers); echo "<br /><br />";
				echo "To: "; print_r($this->to); echo "<br /><br />";
				echo "Subject: " . $this->subject . "<br /><br />";
				$this->body = nl2br($this->body);
				echo "Body: " . $this->body . "<br /><br />";
				break;
			case 'return':
				return array('headers' => $this->headers, 'to' => $this->to, 'subject' => $this->subject, 'body' => $this->body, 'type' => $this->type);
				break;
			default:
				if (SMTP == 'true') {
					$this->doSmtpEmail();
				} else {
					$sentmail = mail($this->to, $this->subject, $this->body, $this->headers);					
				}
		}
	}
	
	
	/**
	 * Send email using SMTP authentication and SSL Encryption
	 */
	public function doSmtpEmail()
	{
		//  Only create a new smtp object if we don't already have one:
		if (!is_object($this->smtp))
		{
			$smtp_array = array (
				'host' => SMTP_HOST, 
				'port' => SMTP_PORT,
				'auth' => true, 
				'username' => SMTP_USERNAME, 
				'password' => SMTP_PASSWORD
			);
		
			require_once "Mail.php";
			$this->smtp = Mail::factory('smtp', $smtp_array);
		}
		
		$mail = $this->smtp->send($this->to, $this->headers, $this->body);
		
		if (PEAR::isError($mail)) {
			echo("<p>" . $mail->getMessage() . "</p>");
			exit;
		} 
	}
}

?>
