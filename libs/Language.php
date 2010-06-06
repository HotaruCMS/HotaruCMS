<?php
/**
 * Language functions
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
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
class Language
{
	/**
	 * Include language pack
	 *
	 * @param string $pack - either "main" or "admin"
	 * @return array $lang_array
	 */
	public function includeLanguagePack($lang_array = array(), $pack = 'main')
	{
		$lang = $this->getLanguageCache();

		$file = BASE . 'content/' . $pack . '_language.php';
		
		if ($pack == 'install') {
			include_once(INSTALL . 'install_language.php');    // language file for install
		} 
		elseif (file_exists($file))
		{
			if (isset($lang_array['files'][$file]) && ($lang_array['files'][$file] == true)) {
				return $lang_array; // return what we've got. No need to reinclude the language
			}
			
			// include language file
			include(BASE . 'content/' . $pack . '_language.php');

			// add to list of included language files
			$lang_array['files'][$file] = true;
		}

		// Add new language to our lang property
		if (isset($lang) && !empty($lang)) {
			foreach($lang as $l => $text) {
				$lang_array[$l] = $text;
			}
		}
		
		return $lang_array;
	}
	
	
	/**
	 * Include a language file in a plugin
	 *
	 * @param string $folder name of plugin folder
	 * @param string $filename optional filename without file extension
	 *
	 * Note: the language file should be in a plugin folder named 'languages'.
	 * '_language.php' is appended automatically to the folder of file name.
	 */    
	public function includeLanguage($h, $folder = '', $filename = '')
	{
		if (!$folder) { $folder = $h->plugin->folder; }
		
		if ($folder) {
		
			// If not filename given, make the plugin name the file name
			if (!$filename) { $filename = $folder; }
			
			// check if this language is already cached
			$file1 = THEMES . THEME . 'languages/' . $filename . '_language.php';
			$file2 = PLUGINS . $folder . '/languages/' . $filename . '_language.php';
			if ($this->checkLanguageCached($h, $file1) || $this->checkLanguageCached($h, $file2)) {
				return true;
			}
			
			// First, look in the user's theme languages folder for a language file...
			
			if (file_exists($file1)) {
				$this->addLanguageFile($h, $file1);
			    
			// If still not found, look in the plugin folder for a language file... 
			} elseif (file_exists($file2)) {
				$this->addLanguageFile($h, $file2);
			}
			
			// Add new language to our lang property
			if (isset($lang)) {
				foreach($lang as $l => $text) {
					$h->lang[$l] = $text;
				}
			}
		}
	}
	
	
	/**
	 * Include a language file for a theme
	 *
	 * @param string $filename optional filename without '_language.php' file extension
	 *
	 * Note: the language file should be in a plugin folder named 'languages'.
	 * '_language.php' is appended automatically to the folder of file name.
	 */    
	public function includeThemeLanguage($h, $filename = 'main')
	{
		if ($filename == 'admin') {
			$this->includeAdminLanguage($h);
			return true;
		}
		
		// Look in the current theme for a language file...
		$file = THEMES . THEME . 'languages/' . $filename . '_language.php';
		
		// check if this language is already cached
		if ($this->checkLanguageCached($h, $file)) {
			return true;
		}
			
		if (file_exists($file)) {
			$this->addLanguageFile($h, $file);
		} 
	}
	
	
	/**
	 * Include admin_language.php
	 *
	 * Hotaru has already got the base admin_language.php file from /content, but
	 * all or parts of it can be overidden.
	 * 
	 * First Hotaru looks for admin_languages.php in the admin theme's "languages" folder
	 * Second, it looks for admin_languages.php in the user theme's "languages" folder.
	 * All files are merged with priority in this order: user theme, admin theme, content/admin_language.php
	 */    
	public function includeAdminLanguage($h)
	{
		// 1. We already have admin_language.php from content/admin_language.php
		
		// 2. Merge in anything from admin_language.php in admin theme languages folder
		
		$file = ADMIN_THEMES . ADMIN_THEME . 'languages/admin_language.php';
		// check if this language is already cached
		if (!$this->checkLanguageCached($h, $file)) {
			if (file_exists($file)) {
				$this->addLanguageFile($h, $file);
			}
		}
		
		// 2. Merge in anything from admin_language.php in user theme languages folder
		
		$file = THEMES . THEME . 'languages/admin_language.php';
		if (!$this->checkLanguageCached($h, $file)) {
			if (file_exists($file)) {
				$this->addLanguageFile($h, $file);
			}
		}
	}
	
	
	/**
	 * Use or create a language cache file
	 *
	 * @param int $timeout - cache length 1440 = 24 hours
	 * @return array|false
	 */
	public function getLanguageCache($timeout = 1440)
	{
		if (LANG_CACHE != 'true') { return false; }
		
		$cache_length = $timeout*60;   // seconds
		$cache = CACHE . 'lang_cache/';
		$file = $cache . "language.php";
		
		if (file_exists($file)) {
			$last_modified = filemtime($file);
			if ($last_modified <= (time() - $cache_length)) { 
				// delete cache
				@unlink($file);
				return false;
			} else {
				include($file);
				if (isset($lang) && !empty($lang)) {
					return $lang; // file exists and the language array has been included
				}
			}
		}
		
		return false;
	}
	
	
	/**
	 * Add to list of included language files
	 *
	 * @param string $file - language file path
	 * @return bool - true if already cached
	 */
	public function checkLanguageCached($h, $file = '')
	{
		if (isset($h->lang['files'][$file])) { return true; }
		
		return false;
	}
	
	
	/**
	 * Add to list of included language files
	 *
	 * @param string $file - language file path
	 */
	public function addLanguageFile($h, $file = '')
	{
		// add to list of included language files
		if (isset($h->lang['files'][$file])) { return false; } // already present

		// add name of file
		$h->lang['files'][$file] = true;
		
		// include file
		include_once($file); // this should contain $lang used below
		
		// add language
		if (isset($lang)) {
			foreach($lang as $l => $text) {
				$h->lang[$l] = $text;
			}
		}
		
		$h->vars['update_lang_cache'] = true;
		return true; // added
	}
	
	
	/**
	 * Write language cache
	 *
	 * @return bool
	 */
	public function writeLanguageCache($h, $timeout = 60)
	{
		if ($this->getLanguageCache()) 
		{
			// cache already exists. Does it need updating?
			if (isset($h->vars['update_lang_cache']) && ($h->vars['update_lang_cache'] == true)) {
				// update needed, fall through to code below...
			} else {
				return false;
			}
		}

		// If doesn't exist, create a new file
		$cache = CACHE . 'lang_cache/';
		$file = $cache . "language.php";
		$fh = fopen($file, 'w+') or die("Sorry, I can't open " . $file);
		if (flock($fh, LOCK_EX)) { // do an exclusive lock
			ftruncate($fh, 0);  // truncate file
			fwrite($fh, '<?php' . "\r\n");
			fwrite($fh, '$lang = ');
			fwrite($fh, var_export($h->lang, true));
			fwrite($fh, '; ?>' . "\r\n");
			flock($fh, LOCK_UN); // release the lock
		} else {
			echo "Couldn't get the lock for the language cache!";
			return false;
		}
		fclose($fh);

		return true;
	}
}
?>
