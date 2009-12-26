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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 *
 * USAGE:
 * $avatar = new Avatar($h);
 * $avatar->setAvatar($user_id, $size, $rating);
 * $avatar->getAvatar(); // returns the avatar for custom display... OR...
 * $avatar->showAvatar(); // displays the avatar using default HTML
 *
 */
class Avatar
{
    public $user_id     = 0;
    public $user_name   = '';
    public $user_email  = '';
    public $size        = 32;
    public $rating      = 'g';  // "global" used by Gravatar
    
    
    /**
     * constructor
     *
     * @param $h Hotaru object
     * @param $user_id
     * @param $size avatar size in pixels
     * @param $rating avatar rating (g, pg, r or x in Gravatar)
     */
    public function  __construct($h, $user_id = 0, $size = 32, $rating = 'g')
    {
        $this->user_id = $user_id;
        
        $user = new UserBase();
        $user->getUserBasic($h, $this->user_id);
        $this->user_email = $user->email;
        $this->user_name = $user->name;
        
        $this->size = $size;
        $this->rating = $rating;
        
        $vars = array(
            'user_id'=>$this->user_id,
            'user_name'=>$this->user_name,
            'user_email'=>$this->user_email,
            'size'=>$this->size,
            'rating'=>$this->rating
            );
        
        $h->pluginHook('avatar_set_avatar', '', $vars);
    }
    
    
    /**
     * get the plain avatar with no surrounding HTML div
     *
     * @return return the avatar
     */
    public function getAvatar($h)
    {
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
     * option to display the avatar linked to ther user's profile
     */
    public function linkAvatar($h)
    {
        $output = "<a href='" . $h->url(array('user' => $this->user_name)) . "' title='" . $this->user_name . "'>\n";
        $result = $h->pluginHook('avatar_get_avatar');
        if ($result) {
            foreach ($result as $key => $value) {
                $avatar = $value;
            }
            $output .=  $avatar; // uses the last avatar sent to this hook
        }
        $output .= "</a>\n";
        return $output;
    }


    /**
     * option to display the profile-linked avatar wrapped in a div
     */
    public function wrapAvatar($h)
    {
        $output = "<div class='avatar_wrapper'>";
        $output = "<a href='" . $h->url(array('user' => $this->user_name)) . "' title='" . $this->user_name . "'>\n";
        $result = $h->pluginHook('avatar_get_avatar');
        if ($result) {
            foreach ($result as $key => $value) {
                $avatar = $value;
            }
            $output .= $avatar; // uses the last avatar sent to this hook
        }
        $output .= "</a>\n";
        $output .= "</div>\n";
    }
}
?>
