<?php
/**
 * Message functions, i.e. for the red and green success and error messages
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
class Messages
{
	/**
	 * Display a SINGLE success or failure message
	 *
	 * @param object $h
	 * @param string $msg
	 * @param string $msg_type ('green' or 'red')
	 * 
	 *  Usage:
	 *		Longhand:
	 *			$this->hotaru->message = "This is a message";
	 *			$this->hotaru->messageType = "green";
	 *			$this->hotaru->showMessage();
	 *
	 *		Shorthand:
	 *			$this->hotaru->showMessage("This is a message", "green");
	 */
	public function showMessage($h, $msg = '', $msg_type = '')
	{
		if ($msg != '') {
                        // for older hotaru plugins
                        if ($msg_type == 'red') $msg_type .= ' alert-error';
                        if ($msg_type == 'green') $msg_type .= ' alert-success';
                        if ($msg_type == 'blue') $msg_type .= ' alert-info';
                    
			echo "<div class='alert message " . $msg_type . "'>";
                            echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                            echo $msg;
                        echo "</div>";
		} elseif ($h->message != '') {
                    
                        $msg_type = $h->messageType;
                        // for older hotaru plugins
                        if ($msg_type == 'red') $msg_type .= ' alert-error';
                        if ($msg_type == 'green') $msg_type .= ' alert-success';
                        if ($msg_type == 'blue') $msg_type .= ' alert-info';
                    
			echo "<div class='alert message " . $msg_type . "'>";
                            echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                            echo $h->message;
                        echo "</div>";
		}
	}
	
	
	/**
	 * Displays ALL success or failure messages
	 *
	 * @param object $h
	 *
	 *  Usage:
	 *        $this->hotaru->messages['This is a message'] = "green";
	 *        $this->hotaru->showMessages();
	 */
	public function showMessages($h)
	{
		if ($h->messages) {
			foreach ($h->messages as $msg => $msg_params) {
                                // check whether we have an array here or a normal vars first
                                // old message type or new extra params type
                                if (is_array($msg_params)) {
                                    $msg_type = $msg_params[0];
                                    $msg_role = $msg_params[1];
                                    
                                    // If we are not on admin page then show the role with the message
                                    if (!$h->adminPage) $msg = "<strong>" . ucfirst($msg_role) . "</strong>: " . $msg;
                                } else {
                                    $msg_type = $msg_params;
                                    $msg_role = '';
                                }
                                
                                if (!$msg_role == '') {
                                    if ($msg_role !== $h->currentUser->role) continue;   //  go to the next for item 
                                }

                                // for older hotaru plugins
                                if ($msg_type == 'red') $msg_type .= ' alert-error';
                                if ($msg_type == 'green') $msg_type .= ' alert-success';
                                if ($msg_type == 'blue') $msg_type .= ' alert-info';
                
				echo "<div class='alert message " . $msg_type . "'>";
                                echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                                echo $msg . "</div>";
			}
		}
	}
        
        
        public function addMessage($h, $msg = '', $msg_type = '', $msg_role = '')
        {
                $h->messages[$msg] = array($msg_type, $msg_role);
                return true;
        }
}
?>
