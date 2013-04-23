<?php
/**
 * Tag functions
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
class TagFunctions
{
	/**
	 * Add tags to the tags table
	 */
	public function addTags($h, $post_id = 0, $new_tags = '')
	{
		// Tags table
		if ($new_tags) {
			$tags_array = explode(',', $new_tags);
			if ($tags_array) {
				foreach ($tags_array as $tag) {
					$sql = "INSERT INTO " . TABLE_TAGS . " SET tags_post_id = %d, tags_date = CURRENT_TIMESTAMP, tags_word = %s, tags_updateby = %d";
					$h->db->query($h->db->prepare($sql, $post_id, urlencode(str_replace(' ', '_', trim($tag))), $h->currentUser->id));
				}
			}
		}
	}
	
	
	/**
	 * Delete tags from the tags table
	 */
	public function deleteTags($h, $post_id = 0)
	{
		$sql = "DELETE FROM " . TABLE_TAGS . " WHERE tags_post_id = %d";
		$h->db->query($h->db->prepare($sql, $post_id));
	}
}
?>
