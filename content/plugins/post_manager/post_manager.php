<?php
/**
 * name: Post Manager
 * description: Manage posts.
 * version: 1.0
 * folder: post_manager
 * class: PostManager
 * hooks: hotaru_header, install_plugin, admin_header_include, admin_plugin_settings, admin_sidebar_plugin_settings, user_manager_role, user_manager_details, admin_theme_main_stats, admin_sidebar_posts
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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
 
class PostManager
{
    // Most work is done in post_manager_settings.php
    
    /**
     * Adds an icon in User Manager about the user having pending or buried posts
     */
    public function user_manager_role($h)
    {
        list ($icons, $user_role, $user) = $h->vars['user_manager_role'];
        
        // Check to see if this user has any pending or buried posts:
        $sql = "SELECT post_id, post_status FROM " . TABLE_POSTS . " WHERE post_author = %d AND (post_status = %s OR post_status = %s) ORDER BY post_date DESC";
        $flags = $h->db->get_results($h->db->prepare($sql, $user->user_id, 'pending', 'buried'));
        $h->vars['post_manager_flags'] = $flags;
        
        if ($flags) {
            $unique_array = array();
            $title = $h->lang["post_man_flagged_reasons"];
            foreach ($flags as $flag) {
                if (!in_array($flag->post_status, $unique_array)) {
                    $title .= $flag->post_status . ", ";
                    array_push($unique_array, $flag->post_status);
                }
            }
            $title = rstrtrim($title, ", ");
            $icons .= " <img src = '" . BASEURL . "content/plugins/post_manager/images/flag_red.png' title='" . $title . "'>";
            $h->vars['user_manager_role'] = array($icons, $user_role, $user);
        }
    }
    
    
    /**
     * Adds a note in User Manager about the user having pending or buried posts
     */
    public function user_manager_details($h)
    {
        list ($output, $user) = $h->vars['user_manager_details'];
        
        // Check to see if this user has any pending or buried posts:
        $sql = "SELECT post_id, post_status FROM " . TABLE_POSTS . " WHERE post_author = %d AND (post_status = %s OR post_status = %s) ORDER BY post_date DESC";
        
        if (!isset($h->vars['post_manager_flags'])) {
            $flags = $h->db->get_results($h->db->prepare($sql, $user->user_id, 'pending', 'buried'));
        } else {
            $flags = $h->vars['post_manager_flags']; // retrieve from memory
        }
        
        if ($flags) {
            $output .= "<br /><b>" . $h->lang["post_man_flagged_reasons"] . "</b>";
            foreach ($flags as $flag) {
                $h->readPost($flag->post_id);
                $output .= "<a href='" . $h->url(array('page'=>$flag->post_id)) . "' title='" . $h->lang["post_man_flags_title"] . $h->post->title . "'>" . $flag->post_status . "</a>, ";
            }
            $output = rstrtrim($output, ", ");
            $h->vars['user_manager_details'] = array($output, $user);
        }
    }


    /**
     * Show stats on Admin home page
     */
    public function admin_theme_main_stats($h, $vars)
    {
	    $stats = $h->post->stats($h);
	    $stats_archived = $h->post->stats($h, 'archived');

	    echo "<li>&nbsp;</li>";
	    if ($stats) {
		foreach ($stats as $stat) {
		    $posts[$stat[0]] = $stat[1];
		}
	    }

	    if (isset($vars) && (!empty($vars))) {
		foreach ($vars as $key => $value) {
			$key_lang = 'post_man_admin_stats_' . $key;
			echo "<li class='title'>" . $h->lang[$key_lang] . "</li>";
			foreach ($value as $stat_type) {
				if (isset($value) && !empty($value)) {

					switch ($stat_type) {
					    case 'all':
						if(isset($posts)) { $post_count = array_sum($posts); } else { $post_count = 0; }
						break;
					    case 'approved':						
						$post_count = 0;
						$array = array('top', 'new');
						foreach ($array as $item) {
						    if (isset($posts[$item])) { $post_count += $posts[$item]; }
						}
						break;
					    case 'archived' :
						if (isset($stats_archived)) { $post_count = $stats_archived; } else { $post_count = 0; }
						break;
					    default:
						if (isset($posts[$stat_type])) { $post_count = $posts[$stat_type]; } else { $post_count = 0; }
						break;
					}					

					if (!defined('SITEURL')) { define('SITEURL', BASEURL); }

					$link = "";
					$dontlink = array('archived');
					if (!in_array($stat_type, $dontlink)) {
					    $link = SITEURL . "admin_index.php?post_status_filter=$stat_type&plugin=post_manager&page=plugin_settings&type=filter&csrf=" . $h->csrfToken;
					}
					
					$lang_name = 'post_man_admin_stats_' . $stat_type;
					echo "<li>";
					if ($link) { echo "<a href='" . $link . "'>"; }
					echo $h->lang[$lang_name] . ": " . $post_count;
					if ($link) { echo "</a>"; }
					echo "</li>";
				}
			}
		}
	    }
    }
    
    /**
     * Add link to admin sidebar
     */
    public function admin_sidebar_posts($h)
    {
        $links = array(            
            'List Posts' => array('admin_index.php?page=plugin_settings&plugin=post_manager')
        );
        return $links;
    }
}

?>