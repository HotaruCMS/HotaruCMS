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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2010, Hotaru CMS
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
                            echo '<button type="button" class="close" data-dismiss="alert">×</button>';
                            echo $msg;
                        echo "</div>";
		} elseif ($h->message != '') {
                    
                        $msg_type = $h->messageType;
                        // for older hotaru plugins
                        if ($msg_type == 'red') $msg_type .= ' alert-error';
                        if ($msg_type == 'green') $msg_type .= ' alert-success';
                        if ($msg_type == 'blue') $msg_type .= ' alert-info';
                    
			echo "<div class='alert message " . $msg_type . "'>";
                            echo '<button type="button" class="close" data-dismiss="alert">×</button>';
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
			foreach ($h->messages as $msg => $msg_type) {
                                // for older hotaru plugins
                                if ($msg_type == 'red') $msg_type .= ' alert-error';
                                if ($msg_type == 'green') $msg_type .= ' alert-success';
                                if ($msg_type == 'blue') $msg_type .= ' alert-info';
                
				echo "<div class='alert message " . $msg_type . "'>";
                                echo '<button type="button" class="close" data-dismiss="alert">×</button>';
                                echo $msg . "</div>";
			}
		}
	}
}
?>
