<?php
/**
 * file: plugins/widgets/widgets_functions.php
 * purpose: Voting functions that are performed behind the scenes with Ajax
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
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

//$json_array = array('result'=>'test_okay');
//echo json_encode($json_array); exit;
//echo $_SERVER['DOCUMENT_ROOT'];

require_once('../../../hotaru_settings.php');
require_once('../../../Hotaru.php');    // Not the cleanest way of getting to the root...

$h = new Hotaru();
$h->start();

if ($h->cage->post->testAlpha('plugin') == 'widgets' ) {
    $h->includeLanguage('widgets');
     // Get widget settings from the database...
     $widgets_settings = $h->getSerializedSettings('widgets');   
   
    $this_widget_function = $h->cage->post->testAlnumLines('widget');
    
    // get the name of this widget, e.g. widget_text_widget (function) -> text_widget (widget name)
    $this_widget_name = strstr($this_widget_function, '_'); // get every thing after "widget_" (returns the underscore)
    $this_widget_name = ltrim($this_widget_name, '_'); // strip the underscore off the front
    
    // get the name of the supporting plugin
	$this_plugin_name = $h->getPluginFromFunction($this_widget_function);
	if ($h->cage->post->testAlpha('action') == 'enable') {                  
		// enable a widget if plugin is active
		if ($h->isActive($this_plugin_name)) {
  			$widgets_settings['widgets'][$this_widget_name]['enabled'] = true;
 			$json_array = array('enabled'=>'true', 'message'=>$h->lang['widgets_order_enabled'], 'color'=>'green');
		} else {
			// don't enable it if the plugin is inactive		
			$widgets_settings['widgets'][$this_widget_name]['enabled'] = false;
			$json_array = array('enabled'=>'false', 'message'=>$h->lang['widgets_order_not_active'], 'color'=>'red');
		}						
	} 	
	elseif ($h->cage->post->testAlpha('action') == 'disable') {
 		$widgets_settings['widgets'][$this_widget_name]['enabled'] = false;
 		$json_array = array('enabled'=>'false', 'message'=>$h->lang['widgets_order_disabled'], 'color'=>'green');
	}
        elseif ($h->cage->post->testAlpha('action') == 'up') {
            $json_array = array('message'=>$h->lang['widgets_order_updated'], 'color'=>'green');
        }
        elseif ($h->cage->post->testAlpha('action') == 'down') {
            $json_array = array('message'=>$h->lang['widgets_order_updated'], 'color'=>'green');
        }
            
	// Save updated widgets settings
	$h->updateSetting('widgets_settings', serialize($widgets_settings), 'widgets');
    
	// Send back result data
	echo json_encode($json_array);
}

?>
