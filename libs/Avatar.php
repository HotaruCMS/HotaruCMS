<?php
/**
 * Avatar class. Plugins that provide avatars should hook into this.
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
 *
 * USAGE:
 * $avatar = new Avatar($h, $user_id, $size, $rating);
 * $avatar->getAvatar($h); // returns the avatar for custom display... OR...
 * $avatar->linkAvatar($h); // displays the avatar linked to the user's profile
 * $avatar->wrapAvatar($h); // displays the avatar linked and wrapped in a div
 *
 * Shortcuts:
 * $h->setAvatar($user_id, $size, $rating);
 * $h->getAvatar(); $h->linkAvatar(); $h->wrapAvatar();
 *
 */
class Avatar
{
	public $user_id     = 0;
	public $user_name   = '';
	public $user_email  = '';
	public $size        = 32;
	public $rating      = 'g';  // "global" used by Gravatar
        public $img_class   = '';   // css options for image shape
	public $valid       = true;
	
	
	/**
	 * constructor
	 *
	 * @param $h Hotaru object
	 * @param $user_id
	 * @param $size avatar size in pixels
	 * @param $rating avatar rating (g, pg, r or x in Gravatar)
	 */
	public function  __construct($h, $user_id = 0, $size = 32, $rating = 'g', $img_class = '')
	{
		if (!$user_id) { return false; }
		
		$this->user_id = $user_id;
		
		$user = new UserBase();
		$user->getUserBasic($h, $this->user_id);
		$this->user_email = $user->email;
		$this->user_name = $user->name;
		
		$this->size = $size;
		$this->rating = $rating;
                $this->img_class = $img_class;
		
		$this->setVars($h);
	}
	
	
	/**
	 * Add Avatar properties to a vars array for plugins to use
	 */
	public function setVars($h)
	{
		$vars = array(
			'user_id'=>$this->user_id,
			'user_name'=>$this->user_name,
			'user_email'=>$this->user_email,
			'size'=>$this->size,
			'rating'=>$this->rating,
                        'img_class'=>$this->img_class
			);
		
		$h->pluginHook('avatar_set_avatar', '', $vars);
	}
	
	
	/**
	 * test the avatar to see if it's valid
	 *
	 * @return bool
	 */
	public function testAvatar($h)
	{
		if (!$this->user_id) { return false; }
		
		$result = $h->pluginHook('avatar_test_avatar');
		
		if (!$result) {
			$this->valid = false;
			return false;
		} 
		
		$this->valid = true;
		return $result[key($result)];   // returns the result (i.e. Gravatar url in the case of Gravatar)
	
	}
	
	
	/**
	 * get the plain avatar with no surrounding HTML div
	 *
	 * @return return the avatar
	 */
	public function getAvatar($h)
	{
		if (!$this->user_id) { return false; }
		
		$result = $h->pluginHook('avatar_get_avatar');
		if ($result) {
			foreach ($result as $key => $value) {
				$avatar = $value;
			}
			return $avatar; // returns the last avatar sent to this hook
		}
		
		return false;
	}
	
	
	/**
	 * option to display the avatar linked to ther user's profile (image obtained from plugin)
	 */
	public function linkAvatar($h)
	{
		if (!$this->user_id) { return false; }
		
		$output = "<a href='" . $h->url(array('user' => $this->user_name)) . "' title='" . $this->user_name . "'>";
		$result = $h->pluginHook('avatar_get_avatar');
		if ($result) {
			foreach ($result as $key => $value) {
				$avatar = $value;
			}
			$output .= $avatar; // uses the last avatar sent to this hook
		}
		$output .= "</a>";
		return $output;
	}
	
	
	/**
	 * option to display the profile-linked avatar wrapped in a div (image obtained from plugin)
	 */
	public function wrapAvatar($h)
	{
		if (!$this->user_id) { return false; }
		
		$output = "<div class='avatar_wrapper'>";
		$output .= "<a href='" . $h->url(array('user' => $this->user_name)) . "' title='" . $this->user_name . "'>";
		$result = $h->pluginHook('avatar_get_avatar');
		if ($result) {
			foreach ($result as $key => $value) {
				$avatar = $value;
			}
			$output .= $avatar; // uses the last avatar sent to this hook
		}
		$output .= "</a>";
		$output .= "</div>";
		return $output;
	}
	
	
	/**
	 * option to display the avatar linked to ther user's profile (image already set)
	 */
	public function linkAvatarImage($h, $avatar_image = '')
	{
		if (!$this->user_id) { return false; }
		
		$output = "<a href='" . $h->url(array('user' => $this->user_name)) . "' title='" . $this->user_name . "'>";
		$output .= $avatar_image; // avatar in img tags
		$output .= "</a>";
		return $output;
	}
	
	
	/**
	 * option to display the profile-linked avatar wrapped in a div (image already set)
	 */
	public function wrapAvatarImage($h, $avatar_image = '')
	{
		if (!$this->user_id) { return false; }
		
		$output = "<div class='avatar_wrapper'>";
		$output .= "<a href='" . $h->url(array('user' => $this->user_name)) . "' title='" . $this->user_name . "'>";
		$output .= $avatar_image; // avatar in img tags
		$output .= "</a>";
		$output .= "</div>";
		return $output;
	}
}
?>
