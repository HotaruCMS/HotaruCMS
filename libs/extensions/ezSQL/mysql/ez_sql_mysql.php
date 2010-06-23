<?php

    /**********************************************************************
    *  Author: Justin Vincent (jv@jvmultimedia.com)
    *  Web...: http://twitter.com/justinvincent
    *  Name..: ezSQL_mysql
    *  Desc..: mySQL component (part of ezSQL databse abstraction library)
    *
    */

    /**********************************************************************
    *  ezSQL error strings - mySQL
    */

    $ezsql_mysql_str = array
    (
        1 => 'Require $dbuser and $dbpassword to connect to a database server',
        2 => 'Error establishing mySQL database connection. Correct user/password? Correct hostname? Database server running?',
        3 => 'Require $dbname to select a database',
        4 => 'mySQL database connection is not active',
        5 => 'Unexpected error while trying to select database'
    );

    /**********************************************************************
    *  ezSQL Database specific class - mySQL
    */

    if ( ! function_exists ('mysql_connect') ) die('<b>Fatal Error:</b> ezSQL_mysql requires mySQL Lib to be compiled and or linked in to the PHP engine');
    if ( ! class_exists ('ezSQLcore') ) die('<b>Fatal Error:</b> ezSQL_mysql requires ezSQLcore (ez_sql_core.php) to be included/loaded before it can be used');

    class ezSQL_mysql extends ezSQLcore
    {

        var $dbuser = false;
        var $dbpassword = false;
        var $dbname = false;
        var $dbhost = false;

        /**********************************************************************
        *  Constructor - allow the user to perform a qucik connect at the
        *  same time as initialising the ezSQL_mysql class
        */

        function ezSQL_mysql($dbuser='', $dbpassword='', $dbname='', $dbhost='localhost')
        {
            $this->dbuser = $dbuser;
            $this->dbpassword = $dbpassword;
            $this->dbname = $dbname;
            $this->dbhost = $dbhost;
        }
        
        /**********************************************************************
        *  Set $h - global Hotaru object
        */

        function setHotaru($h)
        {
            $this->h = $h;
        }

        /**********************************************************************
        *  Short hand way to connect to mySQL database server
        *  and select a mySQL database at the same time
        */

        function quick_connect($dbuser='', $dbpassword='', $dbname='', $dbhost='localhost')
        {
            $return_val = false;
            if ( ! $this->connect($dbuser, $dbpassword, $dbhost,true) ) ;
            else if ( ! $this->selectDB($dbname) ) ;
            else $return_val = true;
            return $return_val;
        }

        /**********************************************************************
        *  Try to connect to mySQL database server
        */

        function connect($dbuser='', $dbpassword='', $dbhost='localhost')
        {
            global $ezsql_mysql_str; $return_val = false;

            // Must have a user and a password
            if ( ! $dbuser )
            {
                $this->register_error($ezsql_mysql_str[1].' in '.__FILE__.' on line '.__LINE__);
                $this->show_errors ? trigger_error($ezsql_mysql_str[1],E_USER_WARNING) : null;
            }
            // Try to establish the server database handle
            else if ( ! $this->dbh = @mysql_connect($dbhost,$dbuser,$dbpassword,true) )
            {
                $this->register_error($ezsql_mysql_str[2].' in '.__FILE__.' on line '.__LINE__);
                $this->show_errors ? trigger_error($ezsql_mysql_str[2],E_USER_WARNING) : null;
            }
            else
            {
                $this->dbuser = $dbuser;
                $this->dbpassword = $dbpassword;
                $this->dbhost = $dbhost;
                $return_val = true;
            }

            return $return_val;
        }

        /**********************************************************************
        *  Try to select a mySQL database
        */

        function selectDB($dbname='')
        {
            global $ezsql_mysql_str; $return_val = false;

            // Must have a database name
            if ( ! $dbname )
            {
                $this->register_error($ezsql_mysql_str[3].' in '.__FILE__.' on line '.__LINE__);
                $this->show_errors ? trigger_error($ezsql_mysql_str[3],E_USER_WARNING) : null;
            }

            // Must have an active database connection
            else if ( ! $this->dbh )
            {
                $this->register_error($ezsql_mysql_str[4].' in '.__FILE__.' on line '.__LINE__);
                $this->show_errors ? trigger_error($ezsql_mysql_str[4],E_USER_WARNING) : null;
            }

            // Try to connect to the database
            else if ( !@mysql_select_db($dbname,$this->dbh) )
            {
                // Try to get error supplied by mysql if not use our own
                if ( !$str = @mysql_error($this->dbh))
                      $str = $ezsql_mysql_str[5];

                $this->register_error($str.' in '.__FILE__.' on line '.__LINE__);
                $this->show_errors ? trigger_error($str,E_USER_WARNING) : null;
            }
            else
            {
                $this->dbname = $dbname;
                $return_val = true;
            }

            return $return_val;
        }

        /**********************************************************************
        *  Format a mySQL string correctly for safe mySQL insert
        *  (no mater if magic quotes are on or not)
        */

        function escape($str = '')
        {
            return mysql_escape_string(stripslashes($str));
        }

        /**********************************************************************
        *  Return mySQL specific system date syntax
        *  i.e. Oracle: SYSDATE Mysql: NOW()
        */

        function sysdate()
        {
            return 'NOW()';
        }

        /**********************************************************************
        *  Perform mySQL query and try to determine result value
        */

        function query($query = '')
        {

            // Initialise return
            $return_val = 0;

            // Flush cached values..
            $this->flush();

            // For reg expressions
            $query = trim($query);
            
            // Log how the function was called
            $this->func_call = "\$db->query(\"$query\")";

            // Keep track of the last query for debug..
            $this->last_query = $query;
            
            // Perform the query via std mysql_query function.. (Borrowed from Wordpress)
            if ( defined('SAVEQUERIES') && SAVEQUERIES )
                $this->timer_start();

            // Use core file cache function
            if ( $cache = $this->get_cache($query) )
            {
                // Nick edit: Although it cached queries with zero results, the get_cache 
                // function returns false (i.e 0) if there are zero rows, so if 0, I've made
                // it store and return "empty" instead, forcing the above queryto return true
                // and the cache (with no results) to be used. This saves making repeated SQL
                // queries that we already know return an empty set of results.
                // I did this because the pluginHook function ramps up the query counts 
                // but rarely returns anything!
                return $cache;
            } else {
                // echo $query . "<br />"; // for testing purposes
            }

            // Count how many queries there have been
            $this->num_queries++;
            
            // If there is no existing database connection then try to connect
            if ( ! isset($this->dbh) || ! $this->dbh )
            {
                $this->connect($this->dbuser, $this->dbpassword, $this->dbhost);
                $this->selectDB($this->dbname);
            }

		// Decide whether need for multisite siteid string to be added to query
		if (defined('MULTI_SITE') && MULTI_SITE == 'true' && !strpos($query, '_siteid')) { 
			$query = $this->whereMultiSite($query);
		} else {
		    // print "missing query" . $query . '<br/>';
		}

            // Perform the query via std mysql_query function..
            $this->result = @mysql_query($query,$this->dbh);
            
            if ( defined('SAVEQUERIES') && SAVEQUERIES )    // Borrowed from Wordpress
            $this->queries[] = array( $query, $this->timer_stop(), $this->get_caller() );

            // If there is an error then take note of it..
            if ( $str = @mysql_error($this->dbh) )
            {
                if (defined('DEBUG') && (DEBUG == 'true')) {
                    $subject = SITE_NAME . " Database Error";
                    $body = SITE_NAME . " Database Error\r\n\r\n";
                    $body .= "Date: " . date('d M Y H:i:s') . " (timezone: " . date_default_timezone_get() . ")\r\n\r\n";
                    $body .= "SQL query:\r\n";
                    $body .= $query . "\r\n\r\n";
                    
                    $body .= "PHP error log:\r\n";
                    $body .= $str . "\r\n\r\n";
                    
                    if(isset($this->h)) {
                        $body .=  "Current User: " . $this->h->currentUser->name . " (id: " . $this->h->currentUser->id .")\r\n";
                        $body .=  "User Role: " . $this->h->currentUser->role . "\r\n";
                        $body .=  "Page Name: " . $this->h->pageName . "\r\n";
                        $body .=  "Sub Page: " . $this->h->subPage . "\r\n";
                        $body .=  "Plugin: " . $this->h->plugin->folder . "\r\n\r\n";
                    }
                    
                    $body .= "If you need help, visit the forums at http://hotarucms.org\r\n";
                    
                    // we can avoid using the $h object (which we might not have) by calling EmailFunctions directly.
                    require_once(LIBS . 'EmailFunctions.php');
                    $emailFunctions = new EmailFunctions();
                    $emailFunctions->subject = $subject;
                    $emailFunctions->body = $body;
                    $emailFunctions->doEmail();
                }

                $is_insert = true;
                $this->register_error($str);
                $this->show_errors ? trigger_error($str,E_USER_WARNING) : null;
                return false;
            }

            // Query was an insert, delete, update, replace
            $is_insert = false;
            if ( preg_match("/^(insert|delete|update|replace)\s+/i",$query) )
            {
                $this->rows_affected = @mysql_affected_rows();

                // Take note of the insert_id
                if ( preg_match("/^(insert|replace)\s+/i",$query) )
                {
                    $this->insert_id = @mysql_insert_id($this->dbh);
                }

                // Return number fo rows affected
                $return_val = $this->rows_affected;
            }
            // Query was a select
            else
            {

                // Take note of column info
                $i=0;
                if (is_resource($this->result)) {
                    while ($i < @mysql_num_fields($this->result))
                    {
                        $this->col_info[$i] = @mysql_fetch_field($this->result);
                        $i++;
                    }
                }

                // Store Query Results
                $num_rows=0;
                if (is_resource($this->result)) {
                while ( $row = @mysql_fetch_object($this->result) )
                    {
                        // Store relults as an objects within main array
                        $this->last_result[$num_rows] = $row;
                        $num_rows++;
                    }
                }

                if (is_resource($this->result)) { @mysql_free_result($this->result); }

                // Log number of rows the query returned
                $this->num_rows = $num_rows;

                // Return number of rows selected
                $return_val = $this->num_rows;
            }

            // disk caching of queries
            $this->store_cache($query,$is_insert);

            // If debug ALL queries
            $this->trace || $this->debug_all ? $this->debug() : null ;

            return $return_val;

        }
        
        
        /**
         * Check if table exists
         *
         * @param string $table2check
         * @return bool
         *
         * Notes: This is a custom function for Hotaru CMS
         */
 
        function table_exists($table2check) {
	    $tables = $this->get_col("SHOW TABLES",0);
	    if (in_array(DB_PREFIX . $table2check, $tables)) { return true; }

            return false;
        }
        
        /**
         * Check if table empty
         *
         * @param string $table2check
         * @return bool
         *
         * Notes: This is a custom function for Hotaru CMS
         */
        function table_empty($table2check) {
            $rowcount = $this->get_var($this->prepare("SELECT COUNT(*) FROM " . DB_PREFIX . $table2check));
            if($rowcount && $rowcount > 0) {
                return false; // table not empty
            } else {
                return true; // table is empty
            }
        }

        /**
         * Check if table column exists
         *
         * @param string $table2check
         * @param string $column
         * @return bool
         *
         * Notes: This is a custom function for Hotaru CMS
         */
        function column_exists($table2check, $column)
        {
            $sql = "SHOW COLUMNS FROM " . DB_PREFIX . $table2check;
            foreach ($this->get_col($sql,0) as $column_name)
            {
                if ($column_name == $column) {
                    return true;
                } 
            }
            
            return false;
        }


	function whereMultisite($query)
	{   
	    $siteidtables = array('blocked'=>'blocked', 'posts'=>'post', 'comments'=>'comment', 'categories'=>'category', 'users'=>'user',
		'plugins'=>'plugin', 'pluginsettings'=>'plugin', 'tags'=>'tags', 'settings'=>'settings', 'miscdata'=>'miscdata',
		'widgets'=>'widget', 'pluginhooks'=>'pluginhooks');

	    //$const = eval(MS_TABLES);
	    $const = unserialize(MS_TABLES);       
	    if ($const) { $siteidtables = $const;} else { }

	    $before ="before: " . $query . "<br/><br/>";
	    $after = "no";
//print $before;
	    // Note, must be case sensitive to avoid text being inserted as from and then being picked up
	    if (strpos($query, ' FROM ')  !== false) {
		$array = explode('FROM ',$query);

		if ($array[0] != 'SHOW COLUMNS ') {

		    if (!isset($array[1])) { var_dump($array);  }
		    $array2 = explode(' ', $array[1]);
		    if ($array2[0] == '') { $table = $array2[1]; } else { $table = $array2[0]; }
		    
		    $array3 = explode(DB_PREFIX , $table);

		    $tablename = $array3[1];

		    $tablename = str_replace(',', '', $tablename);

		    if (array_key_exists($tablename, $siteidtables)) {
			if (stripos($query, $table)) {
			    if (stripos($query, 'WHERE') !== false) {
				$array = explode('WHERE ', $query);
				$query = $array[0] . ' WHERE ' . $siteidtables[$tablename] . '_siteid = ' . SITEID . " AND " . $array[1];
			    } else {
				$array = $array = explode('FROM ' . $table ,$query);
				$query = $array[0] . ' FROM ' . $table . ' WHERE ' . $siteidtables[$tablename] . '_siteid = ' . SITEID . $array[1];
			    }

			    $after =  "<span style='color:red; font-weight:bold;'>AFTER</span>: " . $query . "<br/><br/>";
			}
		    }
		}
	    } 


	    if (stripos($query, 'UPDATE ') !== false) {		
		$pattern = '/^UPDATE(.*?)\SET/';
		preg_match($pattern, $query, $matches);
		if ($matches) { $tablename = trim($matches[1]);	} else {

		}
		$tablename = str_ireplace(DB_PREFIX, '', $tablename);

		if (array_key_exists($tablename, $siteidtables)) {
			if (stripos($query, 'WHERE') !== false) {
			    $array = explode('WHERE ', $query);
			    $query = $array[0] . ' WHERE ' . $siteidtables[$tablename] . '_siteid = ' . SITEID . " AND " . $array[1];
			} else {
			    $query = $query . ' WHERE ' . $siteidtables[$tablename] . '_siteid = ' . SITEID;
			}

			$after =  "<br/><span style='color:red; font-weight:bold;'>AFTER</span>: " . $query . "<br/><br/>";
		}
	    }
	    
	    if (stripos($query, 'INSERT INTO ')  !== false) {
		$pattern = '/^INSERT INTO(.*?)\SET/';
		preg_match($pattern, $query, $matches);
		if ($matches) { $tablename = trim($matches[1]);	} else {
		    $pattern = '/^INSERT INTO(.*?)\(/';
		    preg_match($pattern, $query, $matches);
		    if ($matches) { $tablename = trim($matches[1]);	}
		}

		$tablename = str_ireplace(DB_PREFIX, '', $tablename);

		if (array_key_exists($tablename, $siteidtables)) {
			if (stripos($query, 'VALUES') !== false) {
			    $array = explode('INTO ' . DB_PREFIX . $tablename . ' (', $query);
			    $query = 'INSERT INTO ' . DB_PREFIX . $tablename . ' (' . $siteidtables[$tablename] . '_siteid, ' . $array[1];

//			    $array = explode('VALUES', $query);
//			    $values = $array[1];
//			    $pattern = '/\(.*?\)/';
//			    preg_match_all($pattern, $values, $matches);

			    $array = explode('VALUES', $query);
			    if (!$array[1]) { print 'no 2nd part of array'; $array = explode('VALUES(', $query);}
			    $right_side = str_replace("(", "(" . SITEID . ",", $array[1]);
			    $query = $array[0] . " VALUES " . $right_side;
			}
			else {
			    $array = explode('SET ', $query);
			    $query = $array[0] . " SET " . $siteidtables[$tablename] . "_siteid = " . SITEID . ", " . $array[1];
			}

			$after =  "<br/><span style='color:red; font-weight:bold;'>AFTER</span>: " . $query . "<br/><br/>";
		}		
	    }

	    if (stripos($query, 'REPLACE INTO ') !== false) {
		$pattern = '/^REPLACE INTO(.*?)\(/';		
		preg_match($pattern, $query, $matches);
		if ($matches) { $tablename = trim($matches[1]);	}

		$tablename = str_ireplace(DB_PREFIX, '', $tablename);

		if (array_key_exists($tablename, $siteidtables)) {		    
			if (stripos($query, 'VALUES') !== false) { 			    
			    $array = explode('INTO ' . DB_PREFIX . $tablename . ' (', $query);			    
			    $query = 'REPLACE INTO ' . DB_PREFIX . $tablename . ' (' . $siteidtables[$tablename] . '_siteid, ' . $array[1];			    
			    $array = explode('VALUES (', $query);
			    $query = $array[0] . ' VALUES (' . SITEID . ", " . $array[1];
			}
		}

		$after =  "<br/><span style='color:red; font-weight:bold;'>AFTER</span>: " . $query . "<br/><br/>";
	    }

	    //	    if (stripos($query, 'DELETE') !== false) {
	    //print ">>>>>>DELETE>>>>>>>>" . $tablename . '<<<<<<<<<<<<<<<<<<br/>';
	    // print $before;
	    //
	    //		if (array_key_exists($tablename, $siteidtables)) {
	    //		    if (stripos($query, $table)) {
	    //			print $query;
	    //			if (stripos($query, 'WHERE') !== false) {
	    //			    print "here";
	    //			    $array = explode('WHERE ', $query);
	    //			    $query = $array[0] . ' WHERE ' . $siteidtables[$tablename] . '_siteid = ' . SITEID . " AND " . $array[1];
	    //			} else {
	    //			    print "next";
	    //			    $array = explode('FROM ' . $table ,$query);
	    //			    $query = $array[0] . ' FROM ' . $table . ' WHERE ' . $siteidtables[$tablename] . '_siteid = ' . SITEID . $array[1];
	    //			}
	    //
	    //			$after =  "<span style='color:red; font-weight:bold;'>AFTER</span>: " . $query . "<br/><br/>";
	    //print $after;
	    //		    }
	    //		}
	    //	    }

	    if (stripos($query, 'TRUNCATE') !== false) {

	    }


	    //if ($after == 'no') { print $before; }
	   // print $before;
	//    print $after;
	    return $query;
	}
    }

?>
