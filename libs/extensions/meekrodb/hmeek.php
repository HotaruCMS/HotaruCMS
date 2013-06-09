<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * 
 */

class hDB extends MeekroDB {
    
    
//    /**
//	 * Access modifier to set protected properties
//	 */
//	public function __set($var, $val)
//	{
//		$this->$var = $val;
//	}
//    
//    
//	/**
//	 * Access modifier to get protected properties
//	 * The & is necessary (http://bugs.php.net/bug.php?id=39449)
//	 */
//	public function &__get($var)
//	{
//		return $this->$var;
//	}
        
    /**********************************************************************
    *  Set $h - global Hotaru object
    */

    function setHotaru($h)
    {
        $this->h = $h;
        $h->vars['debug']['db_driver'] = 'mysqli';
    }
                
    
    function my_debugmode_handler($params) {
        /**
         * echo to screen if we want
         */
        //print_r($h);
        //if ($h->currentUser->isAdmin) {
            //echo "Command: " . $params['query'] . "<br>\n";
            //echo "Time To Run It: " . $params['runtime'] . " (milliseconds)<br>\n";
        //}
        echo "here";
        /*
         * save to log if we want
         */
        $h->openLog('speed_test');  // an extra param with 'w' would overwrite log file        
        $h->writeLog('speed_test', 'sql =>  ' . $params['query'] . "<br>\n");
        $h->writeLog('speed_test', 'This above query took ' . round($params['runtime'], 2) . ' seconds to complete.');
        $h->closeLog('speed_test');
    
    
    
        /**
         * mail error if we have mail errors turned on and if we have mail set
         */
//        $subject = SITE_NAME . " Database Error";
//        $body = SITE_NAME . " Database Error\r\n\r\n";
//        $body .= "Date: " . date('d M Y H:i:s') . " (timezone: " . date_default_timezone_get() . ")\r\n\r\n";
//        $body .= "SQL query:\r\n";
//        $body .= $query . "\r\n\r\n";
//
//        $body .= "PHP error log:\r\n";
//        $body .= $str . "\r\n\r\n";
//
//        if(isset($this->h)) {
//            $body .=  "Current User: " . $this->h->currentUser->name . " (id: " . $this->h->currentUser->id .")\r\n";
//            $body .=  "User Role: " . $this->h->currentUser->role . "\r\n";
//            $body .=  "Page Name: " . $this->h->pageName . "\r\n";
//            $body .=  "Sub Page: " . $this->h->subPage . "\r\n";
//            $body .=  "Plugin: " . $this->h->plugin->folder . "\r\n\r\n";
//        }
//
//        $body .= "If you need help, visit the forums at http://forums.hotarucms.org\r\n";
//
//        // we can avoid using the $h object (which we might not have) by calling EmailFunctions directly.
//        require_once(LIBS . 'EmailFunctions.php');
//        $emailFunctions = new EmailFunctions();
//        $emailFunctions->subject = $subject;
//        $emailFunctions->body = $body;
//        $emailFunctions->doEmail();

    }
    
    function my_error_handler($params) {
        echo "Error: " . $params['error'] . "<br>\n";
        echo "Query: " . $params['query'] . "<br>\n";
        die; // don't want to keep going if a query broke
    }
      
    function nonsql_error_handler($params) {
        echo "Error: " . $params['error'] . "<br>\n";
        echo "Query: " . $params['query'] . "<br>\n";
        die; // don't want to keep going if a query broke
    }
      
      

}

?>
