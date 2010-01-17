<?php
// The source code packaged with this file is Free Software.
// Portions are Copyright (C) http://franktank.com/blog/scripts/php-error-class/
// Portions are Copyright (C) 2008-2009 by the Social Web CMS Team <swcms@socialwebcms.com>.
// Portions are Copyright (C) 2008-2009 by the Hotaru CMS Team <http://hotarucms.org>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here: http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

/*
	show_user values:
	0 -> off
	1 -> on (default)

	show_developer values:
	0 -> off
	1 -> on (default)
	2 -> silent
	4 -> add context
	8 -> add backtrace
	16 -> font color white (red default)
	32 -> font color black (red default)
	add numbers together for more than one to be turned on e.g: add context + silent = 6
	matching ip address must be present for show_developer to be invoked
	
	add a valid email address or log file path to invoke these functions			[http://franktank.com/blog/]
*/
	
	class swcms_error_handler
	{
		
		//########################################################################
		//contructor for the class...
		function swcms_error_handler($ip=0, $show_user=1, $show_developer=1, $email=NULL, $log_file=NULL)
		{
			$this->ip = $ip;
			$this->show_user = $show_user;
			$this->show_developer = $show_developer;
			$this->email = mysql_escape_string($email);
			$this->log_file = $log_file;
			$this->log_message = NULL;
			$this->email_sent = false;
		
			$this->error_codes =  E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR;
			$this->warning_codes =  E_WARNING | E_CORE_WARNING | E_COMPILE_WARNING | E_USER_WARNING;
			
			//associate error codes with errno...
			$this->error_names = array('E_ERROR','E_WARNING','E_PARSE','E_NOTICE','E_CORE_ERROR','E_CORE_WARNING',
									   'E_COMPILE_ERROR','E_COMPILE_WARNING','E_USER_ERROR','E_USER_WARNING',
									   'E_USER_NOTICE','E_STRICT','E_RECOVERABLE_ERROR');
									   
			for($i=0,$j=1,$num=count($this->error_names); $i<$num; $i++,$j=$j*2)
				$this->error_numbers[$j] = $this->error_names[$i];
		}
		
		//########################################################################
		//error handling function...
		function handler($errno, $errstr, $errfile, $errline, $errcontext)
		{
			$this->errno = $errno;
			$this->errstr = $errstr;
			$this->errfile = $errfile;
			$this->errline = $errline;
			$this->errcontext = $errcontext;
			
			if($this->log_file)
				$this->log_error_msg();
				
			if($this->email)
				$this->send_error_msg();
			
			if($this->show_user)
				$this->error_msg_basic();
			
			if($this->show_developer && preg_match("/^$this->ip$/i", $_SERVER['REMOTE_ADDR'])) //REMOTE_ADDR : HTTP_X_FORWARDED_FOR
				$this->error_msg_detailed();
		
		   /* Don't execute PHP internal error handler */
		   return true;
		}

		
		//########################################################################
		//error reporting functions...
		function error_msg_basic()
		{
			$message = NULL;
			if($this->errno & $this->error_codes) $message .= "<b>ERROR:</b> There has been an error in the code.";
			if($this->errno & $this->warning_codes) $message .= "<b>WARNING:</b> There has been an error in the code.";

//			header('Location: error.php');
//			die();

			//if($message) $message .= ($this->email_sent)?" The developer has been notified.<br />\n":"<br />\n";
			//echo $message;
			//die();
		}
		
		function error_msg_detailed()
		{
			//settings for error display...
			$silent = (2 & $this->show_developer)?true:false;
			$context = (4 & $this->show_developer)?true:false;
			$backtrace = (8 & $this->show_developer)?true:false;
			
			switch(true)
			{
				case (16 & $this->show_developer): $color='white'; break;
				case (32 & $this->show_developer): $color='black'; break;
				default: $color='red';
			}
		
			$message =  ($silent)?"<!--\n":'';
			$message .= "<pre style='color:$color;'>\n\n";
			$message .= "file: ".print_r( $this->errfile, true)."\n";
			$message .= "line: ".print_r( $this->errline, true)."\n\n";
			$message .= "code: ".print_r( $this->error_numbers[$this->errno], true)."\n";
			$message .= "message: ".print_r( $this->errstr, true)."\n\n";
			$message .= ($context)?"context: ".print_r( $this->errcontext, true)."\n\n":'';
			$message .= ($backtrace)?"backtrace: ".print_r( debug_backtrace(), true)."\n\n":'';
			$message .= "</pre>\n";
			$message .= ($silent)?"-->\n\n":'';
			
			echo $message;
		}
		
		function send_error_msg()
		{		
			$message = "file: ".print_r( $this->errfile, true)."\n";
			$message .= "line: ".print_r( $this->errline, true)."\n\n";
			$message .= "code: ".print_r( $this->error_numbers[$this->errno], true)."\n";
			$message .= "message: ".print_r( $this->errstr, true)."\n\n";
			$message .= "log: ".print_r( $this->log_message, true)."\n\n";
			$message .= "context: ".print_r( $this->errcontext, true)."\n\n";
			//$message .= "backtrace: ".print_r( $this->debug_backtrace(), true)."\n\n";
			
			$this->email_sent = false;
			if(mail($this->email, 'Error: '.$this->errcontext['SERVER_NAME'].$this->errcontext['REQUEST_URI'], $message, "From: error@".$this->errcontext['HTTP_HOST']."\r\n"))
				$this->email_sent = true;
		}
		
		function log_error_msg()
		{
			$message =  "time: ".date("j M y - g:i:s A (T)", mktime())."\n";
			$message .= "file: ".print_r( $this->errfile, true)."\n";
			$message .= "line: ".print_r( $this->errline, true)."\n\n";
			$message .= "code: ".print_r( $this->error_numbers[$this->errno], true)."\n";
			$message .= "message: ".print_r( $this->errstr, true)."\n";
			$message .= "##################################################\n\n";

            /*
			if(!file_exists($this->log_file)){
				$message = '<' . '?php die(); ?' . '>' . "\n" . $message;
			}
			*/

			if (!$fp = fopen($this->log_file, 'a+')) 
				$this->log_message = "Could not open/create file: $this->log_file to log error."; $log_error = true;
			
			if (!fwrite($fp, $message)) 
				$this->log_message = "Could not log error to file: $this->log_file. Write Error."; $log_error = true;
			
			if(!$this->log_message)
				$this->log_message = "Error was logged to file: $this->log_file.";
				
			fclose($fp); 
		}
	
	}
?>