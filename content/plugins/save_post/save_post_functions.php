<?php
/**
 * file: content/plugins/save_post/save_post_functions.php
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

require_once('../../../hotaru_settings.php');
require_once('../../../Hotaru.php');

$h = new Hotaru();
$h->start();

if ($h->currentUser->loggedIn) {
	
	$h->vars['user'] = new UserAuth();
	$profile = $h->vars['user']->getProfileSettingsData($h, 'user_profile', $h->currentUser->id);
	
	if ( !isset( $profile['saved_posts'] ) ) {
		$profile['saved_posts'] = array();
	}
	
	if ($h->cage->post->keyExists('save_id')) {
	    $post_id = $h->cage->post->testInt('save_id');
		if ( !in_array( $post_id, array_values($profile['saved_posts']) ) ) {
			$profile['saved_posts'][] = $post_id;
		}
		$h->readPost($post_id);
		echo json_encode( array('id'=>$post_id, 'url'=>$h->url(array('page'=>$post_id)), 'title'=>$h->post->title) );
	}
	else if ($h->cage->post->keyExists('remove_id')) {
		$post_id = $h->cage->post->testInt('remove_id');
		if ( in_array( $post_id, array_values($profile['saved_posts']) ) ) {
			$post_id = array($post_id);
			$profile['saved_posts'] = array_diff($profile['saved_posts'], $post_id);
		}
	}
	else {	
	}
	$profile['saved_posts'] = array_filter(array_reverse($profile['saved_posts']));
	$h->vars['user']->saveProfileSettingsData($h, $profile, 'user_profile', $h->currentUser->id);
}

?>