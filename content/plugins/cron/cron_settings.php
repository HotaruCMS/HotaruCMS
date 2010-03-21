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
            //Get the settings from the database and put them in an array
            $cron_settings = $h->getSetting('cron_settings');
            //$cron_settings = unserialize($sitemap_settings);
            //$cron = $this->_get_cron_array($h);

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

//            echo "<pre>";
//            print_r($cron);
//            echo "</pre>";
            $schedules = $this->cron_get_schedules($h);
            $date_format =  'M j, Y @ G:i';
            ?>

            <div class="wrap" id="cron-gui">
                <div id="icon-tools" class="icon32"><br /></div>
                <h2>What's in Cron?</h2>

                <h3>Available schedules</h3>
                <ul>
                    <?php foreach( $schedules as $schedule ) { ?>
                            <li><strong><?php echo $schedule[ 'display' ]; ?></strong></li>
                    <?php } ?>
                </ul>

                <h3>Events</h3>
                <table class="widefat fixed">
                    <thead>
                        <tr>
                            <th scope="col">Next due (GMT/UTC)</th>
                            <th scope="col">Schedule</th>
                            <th scope="col">Hook</th>
                            <th scope="col">Arguments</th>
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
            </div>
    <?php
    }
      
}
?>



