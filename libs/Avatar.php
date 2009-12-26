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
 * $avatar->setAvatar($size, $user_id, $user_email, $rating);
 * $avatar->getAvatar(); // returns the avatar for custom display... OR...
 * $avatar->showAvatar(); // displays the avatar using default HTML
 *
 */
class Avatar
{
    private $h;                 // Hotaru object
    public $size        = 32;
    public $user_id     = 0;
    public $user_email  = '';
    public $rating      = 'g';  // "global" used by Gravatar
    
    
    /**
     * constructor
     */
    public function  __construct($h)
    {
        $this->h = $h;
    }
    
    
    /**
     * prepare the iAvatar
     *
     * @param $size avatar size in pixels
     * @param $user_id
     * @param $user_email
     *
     * @return return the avatar
     */
    public function setAvatar($size = 32, $user_id = 0, $user_email = '', $rating = 'g')
    {
        $this->size = $size;
        $this->user_id = $user_id;
        $this->user_email = $user_email;
        $this->rating = $rating;
        
        $this->h->pluginHook('avatar_set_avatar', '', array('size'=>$this->size, 'user_id'=>$this->user_id, 'user_email'=>$this->user_email, 'rating'=>$this->rating));
    }
    
    
    /**
     * get the plain avatar with no surrounding HTML div
     *
     * @return return the avatar
     */
    public function getAvatar()
    {
        $result = $this->h->pluginHook('avatar_get_avatar');
        if ($result) {
            foreach ($result as $key => $value) {
                $avatar = $value;
            }
            return $avatar; // returns the last avatar sent to this hook
        }
        
        return false;
    }

    
    /**
     * option to display the avatar wrapped in default HTML
     */
    public function showAvatar()
    {
        echo "<div class='avatar_wrapper'>";
        $this->h->pluginHook('avatar_show_avatar');
        if ($result) {
            foreach ($result as $key => $value) {
                $avatar = $value;
            }
            echo $avatar; // echos the last avatar sent to this hook
        }
        echo "</div>";
    }
}
?>
