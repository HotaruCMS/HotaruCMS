<?php 
/**
 * Theme name: admin_default
 * Template name: plugin_settings.php
 * Template author: shibuya246
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

?>

<h3>User Stats</h3>

<div class="row">
    <div class="col-md-8">
        <div id="graph1" style="width:100%;height:300px;"></div>
    </div>
    <div class="col-md-4">
        <div id="graph2" style="width: 250px; height: 150px;"></div>
    </div>
</div>

<script type="">
    $(document).ready(function(){
 
    $.ajax({
                // usually, we'll just call the same URL, a script
                // connected to a database, but in this case we only
                // have static example files so we need to modify the
                // URL
                url: "admin_index.php?page=ajax_stats&chart=users_bar",
                method: 'GET',
                dataType: 'json',
                success: onOutboundReceived
            });
 
    function onOutboundReceived(series) {
        var length = series.length;
        var finalData = series;
        var options = {
            bars: { show: true },
            points: { show: true, hoverable:true },
            grid: { hoverable: true, clickable: true },
            xaxis: {
                axisLabel: 'Month',
                axisLabelUseCanvas: true,
                axisLabelFontSizePixels: 13,
                axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
                axisLabelPadding: 15
            },
            yaxis: {
                axisLabel: 'Value',
                axisLabelUseCanvas: true,
                axisLabelFontSizePixels: 13,
                axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
                axisLabelPadding: 5
            },
        };
        $.plot($("#graph1"), finalData, options);
    }
    
    
    $.ajax({
                // usually, we'll just call the same URL, a script
                // connected to a database, but in this case we only
                // have static example files so we need to modify the
                // URL
                url: "admin_index.php?page=ajax_stats&chart=users_pie",
                method: 'GET',
                dataType: 'json',
                success: usersPie
            });
 
    function usersPie(series) {
        //var length = series.length;
        var finalData = series;
        
        var options = {
            series: {
                pie: {
                    show: true
                }
             },
            legend: {
                labelBoxBorderColor: "none"
             }
        };
        $.plot($("#graph2"), finalData, options);
    }
    
    
    
    
});
    </script>