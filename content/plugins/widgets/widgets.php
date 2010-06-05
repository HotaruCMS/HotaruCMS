<?php
/**
 * name: Widgets
 * description: Manages the contents of the widget blocks
 * version: 0.9
 * folder: widgets
 * class: Widgets
 * hooks: theme_index_top, admin_theme_index_top, header_include, admin_header_include, admin_plugin_settings, admin_sidebar_plugin_settings, widget_block
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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


class Widgets
{ 
    /**
     * Set things up when the page is first loaded
     */
    public function admin_theme_index_top($h) { $this->theme_index_top($h); }
    public function theme_index_top($h)
    {
        // Create a new global object called "widget_block".
        require_once(LIBS . 'Widget.php');
        $h->vars['widgets'] = new Widget();
        $h->vars['widgets']->initializeWidgets($h);
    }

	
	/**
	 * This is the hook in the widget_block (sidebar) template. 
	 *
	 * It builds a new function name from the widget name and calls it.
	 */
	public function widget_block($h, $block_id = array(1))
	{
		$block_id = $block_id[0];

		$widgets = $h->vars['widgets']->getArrayWidgets($h);
		
		if (!$widgets) { return false; }
		
		foreach ($widgets as $widget => $details)
		{
			// Only show widgets intended for this block
			if (($details['block'] == $block_id) && $details['enabled'])
			{
				$this->singleWidget($h, $widget, $details);
			}
		}
	}


	/**
	 * Call individual widget
	 *
	 * @param $widget - widget name
	 * @param $details - array of widget details
	 */
	public function singleWidget($h, $widget = '', $details = array())
	{
		if (!$widget) { return false; }
		
		if (!$details) { 
			$details = $h->vars['widgets']->getArrayWidgets($h, $widget); 
		}
		
		if (!$details) { return false; } // the plugin for this widget is probably inactive
		
		$function_name = "widget_" . $widget;
		
		/*  include the plugin class if not already. This is usually done in the pluginHook
		    function, but if no other functions are used, we need to include it here: */
		require_once(PLUGINS . $details['plugin'] . "/" . $details['plugin'] . ".php");
		$h->includeLanguage($details['plugin']);  // same for language
		
		echo "<div class='widget'>\n";
		if ($details['class'] && method_exists($details['class'], $function_name)) 
		{   
			// must be a class object with a method that matches!
			$class = new $details['class']($widget);
			$class->$function_name($h, $details['args']);
		} 
		else 
		{
			/* For multiple instances of widgets, we need to strip the id off the end and use the argument as the identifier.
			   E.g. CHANGE widget_rss_show_1(1); 
			        TO     widget_rss_show(1); */
			
			$function_name_array = explode('_', $function_name);
			array_pop($function_name_array); 
			$function_name = implode('_', $function_name_array);
			if ($details['class'])
			{
				// must be a class object!
				$class = new $details['class']($widget);
				$class->$function_name($h, $details['args']);
			}
		}
		echo "</div>\n";
	}


    /**
     * Widget Settings Page
     */
    public function admin_plugin_settings($h)
    {
        if ($h->cage->get->testAlpha('plugin') != 'widgets') { return false; }
        
        echo "<h1>" . $h->lang["widgets_settings_header"] . "</h1>\n";
        
        echo '<div id="return_message"></div>';

        if ($h->cage->get->testAlpha('action')) {
        
            // Get widget settings from the database...
            $widgets_settings = $h->getSerializedSettings('widgets'); 
            
            // Get the list of widgets...
            $widgets = $h->vars['widgets']->getArrayWidgets($h);
            
            $last = count($widgets);
                
            $this_widget_function = $h->cage->get->testAlnumLines('widget');
            $this_widget_order = $h->cage->get->testInt('order');
            $this_widget_block = $h->cage->get->testInt('block');
            
            $this_widget_name = $h->vars['widgets']->getPluginFromFunction($h, $this_widget_function);
            
            if ($h->cage->get->testAlpha('action') == 'orderup') {
                if ($this_widget_order > 1) {
                    // find widget in the target spot...
                    foreach ($widgets as $widget => $details) {
                        if ($details['order'] == ($this_widget_order - 1)) {
                        
                            //Check if this widget and the target are in the same block
                            if ($widgets_settings['widgets'][$widget]['block'] == $widgets_settings['widgets'][$this_widget_function]['block']) {
                            
                                $widgets_settings['widgets'][$widget]['order'] = $details['order'] + 1;
                                $widgets_settings['widgets'][$this_widget_function]['order'] = $this_widget_order - 1;
                                $h->messages[$h->lang['widgets_order_updated']] = 'green';
                                break;
                            } else {
                                // In different blocks so don't change the order, just the block value (but only if greater than 1)
                                if ($widgets_settings['widgets'][$this_widget_function]['block'] > 1) {
                                    $widgets_settings['widgets'][$this_widget_function]['block']--;
                                }
                            }
                        }
                    }
                            
                } else {
                    // prevent moving into block 0:
                    if (($h->vars['widgets']->getLastWidgetBlock($widgets) > 1) && ($widgets_settings['widgets'][$this_widget_function]['block'] > 1)) {
                        $widgets_settings['widgets'][$this_widget_function]['block']--;
                    } else {
                        $h->messages[$h->lang['widgets_order_already_first']] = 'red';
                    }
                }
                
            } elseif ($h->cage->get->testAlpha('action') == 'orderdown') {
                if ($this_widget_order < $last) {
                    // find widget in the target spot...
                    foreach ($widgets as $widget => $details) {
                        if ($details['order'] == ($this_widget_order + 1)) {
                            // just increase the block?
                            if ($widgets_settings['widgets'][$widget]['block'] > $this_widget_block) {
                                $widgets_settings['widgets'][$this_widget_function]['block']++;
                            // or increase the order?
                            } else {
                                $widgets_settings['widgets'][$widget]['order'] = $details['order'] - 1;
                                $widgets_settings['widgets'][$this_widget_function]['order'] = $this_widget_order + 1;
                            }
                            $h->messages[$h->lang['widgets_order_updated']] = 'green';
                            break;
                        }
                    }
                } else {
                    $widgets_settings['widgets'][$this_widget_function]['block']++;
                    //$h->messages[$h->lang['widgets_order_already_last']] = 'red';
                }        
            } 
            elseif ($h->cage->get->testAlpha('action') == 'enable') 
            {
                // enable a widget
                if ($h->isActive($this_widget_name)) {
                    $widgets_settings['widgets'][$this_widget_function]['enabled'] = true;
                    $h->messages[$h->lang['widgets_order_enabled']] = 'green';
                } else {
                    // don't enable it if the plugin is inactive
                    $h->messages[$h->lang['widgets_order_not_active']] = 'red';
                }
            } 
            elseif ($h->cage->get->testAlpha('action') == 'disable') 
            {
                $widgets_settings['widgets'][$this_widget_function]['enabled'] = false;
                $h->messages[$h->lang['widgets_order_disabled']] = 'green';
            }
            
            // Save updated widgets settings
            $h->updateSetting('widgets_settings', serialize($widgets_settings));
            
        }
        
        $h->showMessages();
        $h->displayTemplate('widget_ordering', 'widgets');
        return true;
    }

}

?>