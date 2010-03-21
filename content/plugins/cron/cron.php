<?php
/**
 * name: Cron
 * description: Enables setting of cron jobs
 * version: 0.1
 * folder: cron
 * class: Cron
 * type: cron
 * hooks: install_plugin, admin_plugin_settings, admin_sidebar_plugin_settings, theme_index_top, admin_theme_index_top, cron_schedule_event, cron_update_job, cron_delete_job, cron_flush_hook, cron_hotaru_version, cron_hotaru_feedback, admin_theme_main_stats_post_version
 * author: shibuya246
 * authorurl: http://shibuya246.com
 *
 * PHP version 5
 *
 * cron view functions modelled on plugin: Cron GUI developed for wordpress by Simon Wheatley http://simonwheatley.co.uk/wordpress/cron-gui
 * main cron functions ported from wordpress http://wordpress.org
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
 * @author    shibuya246 <blog@shibuya246.com>
 * @copyright Copyright (c) 2010
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://shibuya246.com
 */
class Cron
{
	/*
	 * Setup the default settings
	 * */
    public function install_plugin($h)
    {
        $timestamp = time();
        $recurrence = "daily";
        $hook = "cron_hotaru_version";
	$this->cron_schedule_event($h, $timestamp, $recurrence, $hook);

        if (SYS_FEEDBACK == 'true') {
            $hook = "cron_hotaru_feedback";
            $this->cron_schedule_event($h, $timestamp, $recurrence, $hook);
        }
    }  

   public function theme_index_top($h) {
        $this->admin_theme_index_top($h);
   }

    public function admin_theme_index_top($h) {       
        $h->vars['cron_settings'] = $h->getSerializedSettings();
        $this->checkrunCron($h);       
        //$this->run_cron($h);
    }

    public function admin_theme_main_stats_post_version($h) {
        $hotaru_version_settings = $h->getSerializedSettings('cron', 'hotaru_latest_version');
        $hotaru_latest_version = $hotaru_version_settings['version'];
        if ($hotaru_latest_version == $h->version) {
            echo "<li>Latest version installed</li>";
        }
        else {
            echo "<li><a href='http://hotarucms.org/forumdisplay.php?23-Download-Hotaru-CMS'>Update to v." . $hotaru_latest_version . "</a></li>";
        }
    }


/**
 * @param int $timestamp Timestamp for when to run the event.
 * @param string $hook Action hook to execute when cron is run.
 * @param array $args Optional. Arguments to pass to the hook's callback function.
 */
public function cron_schedule_single_event( $timestamp, $hook, $args = array()) {
	// don't schedule a duplicate if there's already an identical event due in the next 10 minutes
	$next = cron_next_scheduled($hook, $args);
	if ( $next && $next <= $timestamp + 600 )
		return;

	$crons = _get_cron_array();
	$key = md5(serialize($args));
	$crons[$timestamp][$hook][$key] = array( 'schedule' => false, 'args' => $args );
	uksort( $crons, "strnatcasecmp" );
	_set_cron_array( $crons );
}


public function cron_schedule_event($h, $timestamp, $recurrence, $hook, $args = array()) {   
	$crons = $this->_get_cron_array($h);
	$schedules = $this->cron_get_schedules($h);
	$key = md5(serialize($args));       
	if ( !isset( $schedules[$recurrence] ) )
		return false;
        
	$crons[$timestamp][$hook][$key] = array( 'schedule' => $recurrence, 'args' => $args, 'interval' => $schedules[$recurrence]['interval'] );
	uksort( $crons, "strnatcasecmp" );
       
        $this->_set_cron_array($h, $crons );
}

/**
 * Reschedule a recurring event.
 *
 * @since 2.1.0
 *
 * @param int $timestamp Timestamp for when to run the event.
 * @param string $recurrence How often the event should recur.
 * @param string $hook Action hook to execute when cron is run.
 * @param array $args Optional. Arguments to pass to the hook's callback function.
 * @return bool|null False on failure. Null when event is rescheduled.
 */
public function cron_reschedule_event($h, $timestamp, $recurrence, $hook, $args = array()) {
	$crons = $this->_get_cron_array($h);
	$schedules =  $this->cron_get_schedules($h);
	$key = md5(serialize($args));
	$interval = 0;

	// First we try to get it from the schedule
	if ( 0 == $interval )
		$interval = $schedules[$recurrence]['interval'];
	// Now we try to get it from the saved interval in case the schedule disappears
	if ( 0 == $interval )
		$interval = $crons[$timestamp][$hook][$key]['interval'];
	// Now we assume something is wrong and fail to schedule
	if ( 0 == $interval )
		return false;

	$now = time();

    if ( $timestamp >= $now )
        $timestamp = $now + $interval;
    else
        $timestamp = $now + ($interval - (($now - $timestamp) % $interval));   
	$this->cron_schedule_event($h, $timestamp, $recurrence, $hook, $args );
}

/**
 * Unschedule a previously scheduled cron job.
 *
 * The $timestamp and $hook parameters are required, so that the event can be
 * identified.
 *
 * @since 2.1.0
 *
 * @param int $timestamp Timestamp for when to run the event.
 * @param string $hook Action hook, the execution of which will be unscheduled.
 * @param array $args Arguments to pass to the hook's callback function.
 * Although not passed to a callback function, these arguments are used
 * to uniquely identify the scheduled event, so they should be the same
 * as those used when originally scheduling the event.
 */
public function cron_unschedule_event($h, $timestamp, $hook, $args = array() ) {
	$crons = $this->_get_cron_array($h);       

	$key = md5(serialize($args));       
	unset( $crons[$timestamp][$hook][$key] );
	if ( empty($crons[$timestamp][$hook]) )
		unset( $crons[$timestamp][$hook] );
	if ( empty($crons[$timestamp]) )             
		unset( $crons[$timestamp] );      
	$this->_set_cron_array($h, $crons );
}

/**
 * Unschedule all cron jobs attached to a specific hook.
 *
 * @since 2.1.0
 *
 * @param string $hook Action hook, the execution of which will be unscheduled.
 * @param mixed $args,... Optional. Event arguments.
 */
function cron_clear_scheduled_hook($h, $hook ) {
	$args = array_slice( func_get_args(), 1 );

	while ( $timestamp = wp_next_scheduled( $hook, $args ) )
		$this->cron_unschedule_event($h, $timestamp, $hook, $args );
}

/**
 * Retrieve the next timestamp for a cron event.
 *
 * @since 2.1.0
 *
 * @param string $hook Action hook to execute when cron is run.
 * @param array $args Optional. Arguments to pass to the hook's callback function.
 * @return bool|int The UNIX timestamp of the next time the scheduled event will occur.
 */
public function cron_next_scheduled( $hook, $args = array() ) {
	$crons = _get_cron_array();
	$key = md5(serialize($args));
	if ( empty($crons) )
		return false;
	foreach ( $crons as $timestamp => $cron ) {
		if ( isset( $cron[$hook][$key] ) )
			return $timestamp;
	}
	return false;
}

/**
 * Send request to run cron through HTTP request that doesn't halt page loading.
 *
 * @since 2.1.0
 *
 * @return null Cron could not be spawned, because it is not needed to run.
 */
public function spawn_cron($h, $local_time = 0 ) {

	if ( !$local_time )
		$local_time = time();

	if ( defined('DOING_CRON') || isset($_GET['doing_cron']) )
		return;

	/*
	 * do not even start the cron if local server timer has drifted
	 * such as due to power failure, or misconfiguration
	 */
	$timer_accurate = $this->check_server_timer( $local_time );
	if ( !$timer_accurate )
		return;

	/*
	* multiple processes on multiple web servers can run this code concurrently
	* try to make this as atomic as possible by setting doing_cron switch
	*/
        $flag = $h->getSetting('doing_cron');

	if ( $flag > $local_time + 10*60 )
		$flag = 0;

	// don't run if another process is currently running it or more than once every 60 sec.
	if ( $flag + 60 > $local_time )
		return;

	//sanity check
	$crons = $this->_get_cron_array($h);
	if ( !is_array($crons) )
		return;

	$keys = array_keys( $crons );
	if ( isset($keys[0]) && $keys[0] > $local_time )
		return;	

	 $h->updateSetting('doing_cron',  $local_time);

	//$cron_url = get_option( 'siteurl' ) . '/wp-cron.php?doing_wp_cron';
	//wp_remote_post( $cron_url, array('timeout' => 0.01, 'blocking' => false, 'sslverify' => apply_filters('https_local_ssl_verify', true)) );

        $this->checkrunCron($h);
}

/**
 * Run scheduled callbacks or spawn cron for all scheduled events.
 *
 * @since 2.1.0
 *
 * @return null When doesn't need to run Cron.
 */
public function run_cron($h) {
	if ( false === $crons = $this->_get_cron_array($h) )
		return;

	$local_time = time();
	$keys = array_keys( $crons );
	if ( isset($keys[0]) && $keys[0] > $local_time )
		return;
       
	$schedules = $this->cron_get_schedules($h);
	foreach ( $crons as $timestamp => $cronhooks ) {
		if ( $timestamp > $local_time ) break;
		foreach ( (array) $cronhooks as $hook => $args ) {
			if ( isset($schedules[$hook]['callback']) && !call_user_func( $schedules[$hook]['callback'] ) )
				continue;
			$this->spawn_cron($h, $local_time );
			break 2;
		}
	}
}

/**
 * Retrieve supported and filtered Cron recurrences.
 *
 * The supported recurrences are 'hourly' and 'daily'. A plugin may add more by
 * hooking into the 'cron_schedules' filter. The filter accepts an array of
 * arrays. The outer array has a key that is the name of the schedule or for
 * example 'weekly'. The value is an array with two keys, one is 'interval' and
 * the other is 'display'.
 *
 * The 'interval' is a number in seconds of when the cron job should run. So for
 * 'hourly', the time is 3600 or 60*60. For weekly, the value would be
 * 60*60*24*7 or 604800. The value of 'interval' would then be 604800.
 *
 * The 'display' is the description. For the 'weekly' key, the 'display' would
 * be <code>__('Once Weekly')</code>.
 *
 * For your plugin, you will be passed an array. you can easily add your
 * schedule by doing the following.
 * <code>
 * // filter parameter variable name is 'array'
 *	$array['weekly'] = array(
 *		'interval' => 604800,
 *		'display' => __('Once Weekly')
 *	);
 * </code>
 *
 * @since 2.1.0
 *
 * @return array
 */
public function cron_get_schedules($h) {
	$schedules = array(
		'hourly' => array( 'interval' => 3600, 'display' => 'Once Hourly' ),
		'twicedaily' => array( 'interval' => 43200, 'display' => 'Twice Daily' ),
		'daily' => array( 'interval' => 86400, 'display' => 'Once Daily' ),
                'weekly' => array( 'interval' => 604800, 'display' => 'Once Weekly' ),
	);       
        
	//return array_merge( apply_filters( 'cron_schedules', array() ), $schedules );
        return $schedules;
}

/**
 * Retrieve Cron schedule for hook with arguments.
 *
 * @since 2.1.0
 *
 * @param string $hook Action hook to execute when cron is run.
 * @param array $args Optional. Arguments to pass to the hook's callback function.
 * @return string|bool False, if no schedule. Schedule on success.
 */
public function cron_get_schedule($hook, $args = array()) {
	$crons = _get_cron_array();
	$key = md5(serialize($args));
	if ( empty($crons) )
		return false;
	foreach ( $crons as $timestamp => $cron ) {
		if ( isset( $cron[$hook][$key] ) )
			return $cron[$hook][$key]['schedule'];
	}
	return false;
}

//
// Private functions
//

/**
 * Retrieve cron info array option.
 *
 */
public function _get_cron_array($h)  {
        if (isset($h->vars['cron_settings'])) {
            $cron = $h->vars['cron_settings'];
            if ( ! is_array($cron) )
		return false;

            return $cron;
        }
        else { 
            return false;
        }
}

    /**
     * Updates the CRON settings.
     *
     */
    public function _set_cron_array($h,$cron) {
            $h->updateSetting('cron_settings', serialize($cron));
            $h->vars['cron_settings'] = $cron;
    }

    // stub for checking server timer accuracy, using outside standard time sources
    public function check_server_timer( $local_time ) {
            return true;
    }

    public function cron_hotaru_feedback($h) {       
        $report = $h->generateReport("object");
       
        $query_vals = array(
            'api_key' => '',
            'format' => 'json',
            'method' => 'hotaru.systemFeedback.add',
            'args' => serialize($report)
        );

       $info = $this->sendApiRequest($h, $query_vals);
        
    }

    public function cron_hotaru_version($h) {
        $query_vals = array(
            'api_key' => '',
            'format' => 'json',
            'method' => 'hotaru.version.get'
        );

         $info = $this->sendApiRequest($h, $query_vals);
       
        // save the updated version number to the local db so we can display it on the admin panel until it gets updated.        
         if (isset($info['version']))
             $h->updateSetting('hotaru_latest_version', serialize($info), 'cron');
    }

    public function sendApiRequest($h, $query_vals) {

        // Generate the POST string
        $ret = '';
        foreach($query_vals as $key => $value) {
            $ret .= $key.'='.urlencode($value).'&';
        }

        $ret = rtrim($ret, '&');

        $ch = curl_init("http://api.hotarucms.org/index.php?page=api");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $ret);
        $response = curl_exec($ch);
        curl_close ($ch);

       return json_decode($response, true);
    }


    public function checkrunCron($h) {
        if ( !empty($_POST) || defined('DOING_AJAX') || defined('DOING_CRON') )
            return;

       define('DOING_CRON', true);

       if ( false === $crons = $this->_get_cron_array($h) )
            return;

       $keys = array_keys( $crons );
       $local_time = time();

       if ( isset($keys[0]) && $keys[0] > $local_time )
           return;

       foreach ($crons as $timestamp => $cronhooks) {
            if ( $timestamp > $local_time )
                return;

            foreach ($cronhooks as $hook => $keys) {               
                foreach ($keys as $k => $v) {                   
                    $schedule = $v['schedule'];
                    if ($schedule != false) {
                            //$new_args = array($timestamp, $schedule, $hook, $v['args']);
                            $this->cron_reschedule_event($h, $timestamp, $schedule, $hook, $v['args']);
                    }
                    $this->cron_unschedule_event($h, $timestamp, $hook, $v['args']);
                    //call function to do task required for cron
                    //print "running hook..-> " . $hook. "     ";
                    $h->pluginHook($hook, '', $v['args']);
                }
            }
       }
    }

    public function cron_update_job($h, $cron_data)
    {
        $h->vars['cron_settings'] = $h->getSerializedSettings();
        
        $timestamp = (isset($cron_data['timestamp'])) ? $cron_data['timestamp'] : array();
        $recurrence = (isset($cron_data['recurrence'])) ? $cron_data['recurrence'] : array();
        $hook = (isset($cron_data['hook'])) ? $cron_data['hook'] : array();
        $args = (isset($cron_data['args'])) ? $cron_data['args'] : array();
        $cron_exists = false;

        // check whether already have existing event for this job
        //  match against hook and args. note args must match
        $current_crons = $this->_get_cron_array($h);
        foreach ($current_crons as $current_timestamp => $current_cronhooks) {
          foreach ($current_cronhooks as $current_hook => $current_keys) {
             if ($current_hook == $hook) {
               foreach ($current_keys as $current_md5 => $current_job) {
              //print_r( $current_job["args"]); print "   **    "; print_r($args);
            if ($current_job["args"] == $args) {
                $this->cron_schedule_event($h, $timestamp, $recurrence, $hook, $args);
                $this->cron_unschedule_event($h, $current_timestamp, $current_hook, $args);
                $cron_exists = true;
            }}}
          }
        }

        if (!$cron_exists) $this->cron_schedule_event($h, $timestamp, $recurrence, $hook, $args);
    }

    public function cron_delete_job($h, $cron_data)
    {        
        //load current cron jobs from memory space
        $h->vars['cron_settings'] = $h->getSerializedSettings();        
       
        $hook = (isset($cron_data['hook'])) ? $cron_data['hook'] : array();
        $args = (isset($cron_data['args'])) ? $cron_data['args'] : array();

        $current_crons = $this->_get_cron_array($h);
       
        foreach ($current_crons as $current_timestamp => $current_cronhooks) {
          foreach ($current_cronhooks as $current_hook => $current_keys) {
            foreach ($current_keys as $current_md5 => $current_job) { 
                foreach ($current_job as $current_set => $current_args) {               
                    if ($current_hook == $hook && $current_args == $args) {                        
                        $this->cron_unschedule_event($h, $current_timestamp, $current_hook, $current_args);
                    }
                }
            }
          }
        }
    }

     public function cron_flush_hook($h, $cron_data)
    {
        $h->vars['cron_settings'] = $h->getSerializedSettings();

        $hook = (isset($cron_data['hook'])) ? $cron_data['hook'] : array();
        $flush_count = 0;

        $current_crons = $this->_get_cron_array($h); 
        foreach ($current_crons as $current_timestamp => $current_cronhooks) { 
          foreach ($current_cronhooks as $current_hook => $current_keys) {
              if ($current_hook == $hook) {                            
               foreach ($current_keys as $current_md5 => $current_job) {
                    $flush_count ++;                  
                    //while ( list ($param, $value) = each ( $current_job ))                                                         
                    $this->cron_unschedule_event($h, $current_timestamp, $current_hook,  $current_job["args"]);
                    }
               }
          }
        }
        $array = array('count' => $flush_count);
        echo json_encode($array);
    }
    
}