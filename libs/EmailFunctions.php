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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
    
class EmailFunctions
{
    protected $to           = '';
    protected $subject      = '';
    protected $body         = '';
    protected $headers      = '';
    protected $type         = '';
    
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
     * Constructor
     *
     * @param string $to - defaults to SITE_EMAIL
     * @param string $subject - defaults to "No Subject";
     * @param string $body - returns false if empty
     * @param string $headers e.g. "From: " . SITE_EMAIL . "\r\nReply-To: " . SITE_EMAIL . "\r\nX-Priority: 3\r\n";
     * @param string $type - default is "email", but you can write to a "log" file, print to "screen" or "return" the content
     * @return array - only if $type = "return"
     */
    public function __construct($to = '', $subject = '', $body = '', $headers = '', $type = 'email')
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
        $this->headers = $headers;
        $this->type = $type;
        
        if (!$this->to) { $this->to = SITE_EMAIL; }
        if (!$this->subject) { $this->subject = "No Subject"; }
        if (!$this->body) { return false; }
        if (!$this->headers) { $this->headers = "From: " . SITE_EMAIL . "\r\nReply-To: " . SITE_EMAIL . "\r\nX-Priority: 3\r\n"; }
    }
    
    /**
     * Send emails
     *
     * @param string $to - defaults to SITE_EMAIL
     * @param string $subject - defaults to "No Subject";
     * @param string $body - returns false if empty
     * @param string $headers default is "From: " . SITE_EMAIL . "\r\nReply-To: " . SITE_EMAIL . "\r\nX-Priority: 3\r\n";
     * @param string $type - default is "email", but you can write to a "log" file, print to "screen" or "return" an array of the content
     * @return array - only if $type = "return"
     */
    public function doEmail()
    {
        // OVERRIDE THE TYPE HERE BY UNCOMMENTING THE LINE BELOW - Very handy when developing offline with WampServer, etc.
        // $this->type = 'log'; // this will write all emails to email_log.txt in the cache folder INSTEAD of sending them.
        
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
                echo "Headers: " . $this->headers . "<br /><br />";
                echo "To: " . $this->to . "<br /><br />";
                echo "Subject: " . $this->subject . "<br /><br />";
                $this->body = nl2br($this->body);
                echo "Body: " . $this->body . "<br /><br />";
                break;
            case 'return':
                return array('headers' => $this->headers, 'to' => $this->to, 'subject' => $this->subject, 'body' => $this->body, 'type' => $this->type);
                break;
            default:
                mail($this->to, $this->subject, $this->body, $this->headers);
        }
    }
}

?>
