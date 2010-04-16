<?php
/**
 * File: /plugins/db_optimize/db_optimize_settings.php
 * Purpose: Admin settings for the DB Optimize plugin
 *
 * PHP version 5
 *
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
 * @author    shibuya246
 * @copyright Copyright (c) 2010, shbuya246
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

class DBOptimizeSettings
{
    /**
     * DB Optimize Settings Page
     */
    public function settings($h) {


	?>
	<table class="widefat fixed" cellspacing="0">
	<thead>
		<tr>
		<th scope="col">Table</th>
		<th scope="col">Size</th>
		<th scope="col">Status</th>
		<th scope="col">Space Save</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
		<th scope="col">Table</th>
		<th scope="col">Size</th>
		<th scope="col">Status</th>
		<th scope="col">Space Save</th>
		</tr>
	</tfoot>
	<tbody id="the-list">

	<?php
	$alternate = ' class="alternate"';
	$db_clean = DB_NAME;
	$tot_data = 0;
	$tot_idx = 0;
	$tot_all = 0;
	$total_gain = 0;
	$local_query = 'SHOW TABLE STATUS FROM '. DB_NAME;
	$result = mysql_query($local_query);
	if (mysql_num_rows($result)){
		while ($row = mysql_fetch_array($result))
		{
		    $tot_data = $row['Data_length'];
		    $tot_idx  = $row['Index_length'];
		    $total = $tot_data + $tot_idx;
		    $total = $total / 1024 ;
		    $total = round ($total,3);
		    $gain= $row['Data_free'];
		    $gain = $gain / 1024 ;
		    $total_gain += $gain;
		    $gain = round ($gain,3);
		    if (isset($_POST["optimize-db"])) {
			$local_query = 'OPTIMIZE TABLE '.$row[0];
					  $resultat  = mysql_query($local_query);
			//echo "optimization";
		    }

		    if ($gain == 0){
			echo "<tr". $alternate .">
			<td class='column-name'>". $row[0] ."</td>
			<td class='column-name'>". $total ." Kb"."</td>
			<td class='column-name'>" .  'Already Optimized' . "</td>
			<td class='column-name'>0 Kb</td>
			</tr>\n";
		    } else
		    {
			if (isset($_POST["optimize-db"])) {
			    echo "<tr". $alternate .">
			    <td class='column-name'>". $row[0] ."</td>
			    <td class='column-name'>". $total ." Kb"."</td>
			    <td class='column-name' style=\"color: #0000FF;\">" . 'Optimized' . "</td>
			    <td class='column-name'>". $gain ." Kb</td>
			    </tr>\n";
			}
			else {
			    echo "<tr". $alternate .">
			    <td class='column-name'>". $row[0] ."</td>
			    <td class='column-name'>". $total ." Kb"."</td>
			    <td class='column-name' style=\"color: #FF0000;\">" . 'Need to Optimize' . "</td>
			    <td class='column-name'>". $gain ." Kb</td>
			    </tr>\n";
			}
		    }
		    $alternate = ( empty( $alternate ) ) ? ' class="alternate"' : '';
		}
	}
?>
</tbody>
</table>

	<?php



    }

}
?>
