<?php
/**
 *  Sitemap Settings
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
 * @author    Justin Tiearney <admin@obzerver.com>
 * @copyright Copyright (c) 2010
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://obzerver.com
 */
class SitemapSettings extends Sitemap
{

	public function settings($h)
	{
		if ($h->cage->post->getAlpha('submitted') == 'true') { 
			$this->saveSettings($h);
			$h->message = $h->lang["sitemap_settings_saved"];
			$h->messageType = "green";
			$h->showMessage();
		}else if($h->cage->post->getAlpha('generate') == 'true') {
			$this->createSitemap($h);
			$h->message = $h->lang["sitemap_generated"];
			$h->messageType = "green";
			$h->showMessage();
		}else if($h->cage->post->getAlpha('newpass') == 'true') {
			$this->newPassword($h);
			$h->message = $h->lang["sitemap_password_generated"];
			$h->messageType = "green";
			$h->showMessage();
		}

		//Get settings from database
		$sitemap_settings = $h->getSerializedSettings();
		
        // show header
        echo "<h1>" . $h->lang["sitemap_settings_header"] . "</h1>\n";
        
		echo "<h3>" . $h->lang['sitemap_configure_sitemap'] . "</h3>\n";
		
		echo '<form name="input" action="'. BASEURL . 'admin_index.php?page=plugin_settings&amp;plugin=sitemap" method="post">';
		
		echo $h->lang['sitemap_compress'].'<input type="checkbox" name ="sitemap_compress" value="sitemap_compress" '.$sitemap_settings['sitemap_compress'].'"> <br />';
		echo $h->lang['sitemap_frequency'].'<select name ="sitemap_frequency">
			<option selected="yes">'.$sitemap_settings['sitemap_frequency'].'</option>
				<option>hourly</option><option>daily</option><option>weekly</option>
					<option>monthly</option><option>yearly</option></select> <br />';
		
		// Fetch priorities
		$priorities = $this->getPriorities($h);
		
		// Loop and build select/option
		foreach ( $priorities as $priority) {
			echo $h->lang[$priority].'<select name ="' . $priority . '">';
			$selected = '';
			for ( $i=10,$min=0; $i>$min; $i-- ) {
				$iteration = number_format($i/10, 1);
				$selected = '';
				
				// Check if current iteration = our setting
				if ( floatval($sitemap_settings[$priority]) === floatval($iteration) ) {
					$selected = ' selected="yes"';
				}
				echo '<option'.$selected.'>'.$iteration.'</option>';
			}
			echo '</select> <br />';
		}
		echo "<br />";
		echo '<input type="checkbox" name ="sitemap_cron" value="sitemap_cron" '.$sitemap_settings['sitemap_use_cron'].'">&nbsp;'.$h->lang['sitemap_use_cron'].' <br />';
		echo "<br />";
		
		echo '<input type="hidden" name="submitted" value="true">';
		echo '<input type="hidden" name="generate" value="false">';
		echo '<input type="hidden" name="newpass" value="false">';
		echo '<input type="submit" value="' . $h->lang['sitemap_form_save_settings'] . '" />';
		echo '<input type="hidden" name="csrf" value="' . $h->csrfToken . '" />';
		echo '</form>';
		
		echo "<br />";
		
		echo "<h3>" . $h->lang['sitemap_generate_sitemap'] . "</h3>\n";
		
		//Print where to find the sitemap
		echo $h->lang['sitemap_location'].' '.$sitemap_settings['sitemap_location'];
		if(strcmp($sitemap_settings['sitemap_compress'],'checked') == 0)
		{
			echo 'sitemap.gz';
		}else
		{
			echo 'sitemap.xml';
		}
		echo '<br />';
		
		//Display the last time you ran the sitemap creation tool
		echo $h->lang['sitemap_last_run'].' '.$sitemap_settings['sitemap_last_run'].'<br />';
		
		//Allow the user to run the sitemap creation tool
		echo '<form name="input" action="'. BASEURL . 'admin_index.php?page=plugin_settings&amp;plugin=sitemap" method="post">';
		echo '<input type="hidden" name="submitted" value="false">';
		echo '<input type="hidden" name="generate" value="true">';
		echo '<input type="hidden" name="newpass" value="false">';
		echo '<input type="submit" value="' . $h->lang['sitemap_form_new_sitemap'] . '" />';
		echo '<input type="hidden" name="csrf" value="' . $h->csrfToken . '" />';
		echo '</form>';
		
		echo "<br />";
		
		echo "<h3>" . $h->lang['sitemap_manual_sitemap'] . "</h3>\n";
		echo $h->lang['sitemap_manual_sitemap_note'] . "<p />";

		echo "cron job: 0 0 * * * wget -O - -q -t 1 &quot;".BASEURL."index.php?page=sitemap&passkey=".$sitemap_settings['sitemap_password'].'&quot;<br />';
		//
		echo '<form name="input" action="'. BASEURL . 'admin_index.php?page=plugin_settings&amp;plugin=sitemap" method="post">';
		echo '<input type="hidden" name="submitted" value="false">';
		echo '<input type="hidden" name="generate" value="false">';
		echo '<input type="hidden" name="newpass" value="true">';
		echo '<input type="submit" value="' . $h->lang['sitemap_form_new_password'] . '">';
		echo '<input type="hidden" name="csrf" value="' . $h->csrfToken . '" />';
		echo '</form>';

		echo $h->lang['sitemap_manual_instructions'];
	}
	
	/*
	 * Used to save our plugin settings.
	 * */
	public function saveSettings($h)
	{
		//Get settings from database
		$sitemap_settings = $h->getSerializedSettings();
		
		//Change the compression of the sitemap
		if($h->cage->post->keyExists('sitemap_compress'))
		{
			$sitemap_settings['sitemap_compress'] = 'checked';
		}
		else
		{
			$sitemap_settings['sitemap_compress'] = '';
		}
		
		//Change the frequency of page updates
		if($h->cage->post->keyExists('sitemap_frequency'))
		{
			$sitemap_settings['sitemap_frequency'] = $h->cage->post->getAlpha('sitemap_frequency');
		}
		
		//Use the Cron plugin
		if($h->cage->post->keyExists('sitemap_cron'))
		{
			$sitemap_settings['sitemap_use_cron'] = 'checked';
			
			// set up cron job for sitemap generation:
			$hook = "sitemap_runcron";
			$timestamp = time();
			$recurrence = "daily"; 
			$cron_data = array('timestamp'=>$timestamp, 'recurrence'=>$recurrence, 'hook'=>$hook);
			$h->pluginHook('cron_update_job', 'cron', $cron_data); 
		}
		else
		{
			$sitemap_settings['sitemap_use_cron'] = '';
			
			// delete any existingcron job for sitemaps
			$hook = "sitemap_runcron";
			$cron_data = array('hook'=>$hook);
			$h->pluginHook('cron_delete_job', 'cron', $cron_data);
		}
		
		// Get priorities
		$priorities = $this->getPriorities($h);
		
		// Loop, check if exists in post, save
		foreach ( $priorities as $priority ) {
			if( $h->cage->post->keyExists($priority) && $h->cage->post->testFloat($priority) )
			{
				$sitemap_settings[$priority] = $h->cage->post->getRaw($priority);
			}
		}
		
		$h->updateSetting('sitemap_settings', serialize($sitemap_settings));
	}
	
	public function newPassword($h)
	{
		//Get settings from database
		$sitemap_settings = $h->getSerializedSettings();
		
		$sitemap_settings['sitemap_password'] = md5(rand());
		
		$h->updateSetting('sitemap_settings', serialize($sitemap_settings));
	}
	
	private function getPriorities($h)
	{
		return array( 'sitemap_priority_baseurl', 'sitemap_priority_categories', 'sitemap_priority_posts');
	}
}
?>
