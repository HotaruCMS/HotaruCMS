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
        *  Short hand way to connect to mySQL database server
        *  and select a mySQL database at the same time
        */

        function quick_connect($dbuser='', $dbpassword='', $dbname='', $dbhost='localhost')
        {
            $return_val = false;
            if ( ! $this->connect($dbuser, $dbpassword, $dbhost,true) ) ;
            else if ( ! $this->select($dbname) ) ;
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

        function select($dbname='')
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
        *  Perform mySQL query and try to detirmin result value
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
                //echo $query . "<br />"; // for testing purposes
            }

            // Count how many queries there have been
            $this->num_queries++;
            
            // If there is no existing database connection then try to connect
            if ( ! isset($this->dbh) || ! $this->dbh )
            {
                $this->connect($this->dbuser, $this->dbpassword, $this->dbhost);
                $this->select($this->dbname);
            }

            // Perform the query via std mysql_query function..
            $this->result = @mysql_query($query,$this->dbh);
            
            if ( defined('SAVEQUERIES') && SAVEQUERIES )    // Borrowed from Wordpress
            $this->queries[] = array( $query, $this->timer_stop(), $this->get_caller() );

            // If there is an error then take note of it..
            if ( $str = @mysql_error($this->dbh) )
            {
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
            foreach ( $this->get_col("SHOW TABLES",0) as $table_name ) {
                if($table_name == DB_PREFIX . $table2check) { 
                    return true; 
                }
            }
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

    }

?>
