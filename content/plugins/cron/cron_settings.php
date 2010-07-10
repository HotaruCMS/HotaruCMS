<?php
/**
 * File: plugins/cron/cron_settings.php
 * Purpose: The functions for cron.
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
class CronSettings extends Cron
{
	public function settings($h)
	{
	    echo "<h1>" . $h->lang["cron_settings_header"] . "</h1>";

	     // If the form has been submitted, go and save the data...
	    if ($h->cage->post->getAlpha('flush') == 'true') {
		$this->flushCronJobs($h);
	    }
	    
	    // If the form has been submitted, go and save the data...
	    if ($h->cage->post->getAlpha('restore') == 'true') {
		$this->restoreCronDefaults($h);
	    }



            //Get the settings from the database and put them in an array
            $cron_settings = $h->getSetting('cron_settings');

            $cron = $this->_get_cron_array($h);

            $schedules = $this->cron_get_schedules($h);
            $date_format =  'M j, Y @ G:i';
            if ($cron) {
               foreach ( $cron as $timestamp => $cronhooks ) {
                    foreach ( (array) $cronhooks as $hook => $events ) {
                        foreach ( (array) $events as $key => $event ) {                           
                            if (!is_int($hook)) {
                                $cron[ $timestamp ][ $hook ][ $key ][ 'date' ] = date( $date_format, (int) $timestamp );
                            }
                        }
                    }
                }
            }

            $schedules = $this->cron_get_schedules($h);
            $date_format =  'M j, Y @ G:i';
            ?>
                            

                <h3><?php echo $h->lang["cron_settings_available_schedules"]; ?></h3>
                <ul class="cron_schedules">
                    <?php foreach( $schedules as $schedule ) { ?>
                            <li><strong><?php echo $schedule[ 'display' ]; ?></strong>, </li>
                    <?php } ?>
                </ul>
		<br class="clearall"/>

                <h3><?php echo $h->lang["cron_settings_events"]; ?></h3>
                <table class="widefat fixed">
                    <thead>
                        <tr>
                            <th scope="col"><?php echo $h->lang["cron_settings_table_nextdue"]; ?></th>
                            <th scope="col"><?php echo $h->lang["cron_settings_table_schedule"]; ?></th>
                            <th scope="col"><?php echo $h->lang["cron_settings_table_hooks"]; ?></th>
                            <th scope="col"><?php echo $h->lang["cron_settings_table_args"]; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($cron) {
                            foreach ( $cron as $timestamp => $cronhooks ) {
                                foreach ( (array) $cronhooks as $hook => $events ) {
                                    foreach ( (array) $events as $event ) { ?>
                                    <tr>
                                        <th scope="row"><?php echo $event[ 'date' ]; ?> (<?php echo $timestamp; ?>)</th>
                                        <td>
                                            <?php
                                                if ( $event[ 'schedule' ] ) {
                                                    echo $schedules [ $event[ 'schedule' ] ][ 'display' ];
                                                } else {
                                                    ?><em>One-off event</em><?php
                                                }
                                            ?>
                                        </td>
                                        <td><?php echo $hook; ?></td>
                                        <td><?php if ( count( $event[ 'args' ] ) ) { ?>
                                            <ul>
                                                <?php foreach( $event[ 'args' ] as $key => $value ) { ?>
                                                    <strong>[<?php echo $key; ?>]:</strong> <?php echo $value; ?>
                                                <?php } ?>
                                            </ul>
                                        <?php } ?></td>
                                    </tr>
                                <?php }
                                }
                            }
                        } ?>
                    </tbody>
                </table>


		<?php

		// Form 1 - flush cron jobs
		echo "<form name='ping_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=cron' method='post'>";

		echo "<br /><br />";
		echo "<input type='hidden' name='flush' value='true' />";
		echo "<input type='submit' value='" . $h->lang["cron_button_flush_all"] . "' />";
		echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />";
		echo "</form>";

		// Form 2 - cron defaults
		echo "<form name='ping_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=cron' method='post'>";

		
		echo "<input type='hidden' name='restore' value='true' />";
		echo "<input type='submit' value='" . $h->lang["cron_button_reset_defaults"] . "' />";
		echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />";
		echo "</form>";

    }


    public function flushCronJobs($h) {
	$h->updateSetting('cron_settings', '');
	$h->vars['cron_settings'] ='';
	$crons = $this->_get_cron_array($h);
    }
    
    public function restoreCronDefaults($h) {

	    $timestamp = time();
	    $recurrence = "daily";
	    $hook = "SystemInfo:hotaru_version";
	    $args = array('timestamp' => $timestamp, 'recurrence' => $recurrence, 'hook' => $hook);

	    $this->cron_update_job($h, $args);

	    $hook = "SystemInfo:plugin_version_getAll";
	    $args = array('timestamp' => $timestamp, 'recurrence' => $recurrence, 'hook' => $hook);
	    $this->cron_update_job($h, $args);

	    $hook = "SystemInfo:hotaru_feedback";
	    $args = array('timestamp' => $timestamp, 'recurrence' => $recurrence, 'hook' => $hook);
	    $this->cron_update_job($h, $args);
	}  


}
?>



