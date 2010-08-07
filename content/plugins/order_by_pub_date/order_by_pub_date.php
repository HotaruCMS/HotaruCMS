<?php
/**
 * name: Order By Pub Date
 * description: Order the front page by published date
 * version: 0.5
 * folder: order_by_pub_date
 * class: OrderByPubDate
 * requires: bookmarking 0.1
 * hooks: bookmarking_functions_preparelist, post_rss_feed, post_update_post
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
 * @copyright Copyright (c) 2010
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://hotarucms.com
 */
class OrderByPubDate
{
    /**
     * Override the default sort filters for the front page
     */
    public function bookmarking_functions_preparelist($h)
    {
        if ($h->cage->get->keyExists('sort')) { 
            $sort = $h->cage->get->testAlnumLines('sort');
            if ($sort) {
                switch ($sort) 
                {
                    case 'top-24-hours':
                        $start = date('YmdHis', time_block());
                        $end = date('YmdHis', strtotime("-1 day"));
                        break;
                    case 'top-48-hours':
                        $start = date('YmdHis', time_block());
                        $end = date('YmdHis', strtotime("-2 days"));
                        break;
                    case 'top-7-days':
                        $start = date('YmdHis', time_block());
                        $end = date('YmdHis', strtotime("-7 days"));
                        break;
                    case 'top-30-days':
                        $start = date('YmdHis', time_block());
                        $end = date('YmdHis', strtotime("-30 days"));
                        break;
                    case 'top-365-days':
                        $start = date('YmdHis', time_block());
                        $end = date('YmdHis', strtotime("-365 days"));
                        break;
                    default:
                        // no default
                }
                
                // filter to posts made popular by published date
                if (isset($end) && isset($start) && ($sort != 'top-all-time')) {
                    unset ($h->vars['filter']['(post_date >= %s AND post_date <= %s)']);
                    $h->vars['filter']['(post_pub_date >= %s AND post_pub_date <= %s)'] = array($end, $start);
                }
                
                // order by pub date
                $h->vars['orderby'] = "post_votes_up DESC, post_pub_date DESC";
            }

            return true;
        }
        
        if ($h->pageName == 'popular') { 
            $h->vars['orderby'] = 'post_pub_date DESC';
            return true;
        }
    }
    

    /**
     * Override the default rss feed for top posts
     */
    public function post_rss_feed($h)
    {
        if (isset($h->vars['postRssStatus']) && ($h->vars['postRssStatus'] == 'top')) {
            $h->vars['postRssOrderBy'] = "post_pub_date DESC";
        }
    }


    /**
     * Set pub date if post status is top and pubDate is not set (i.e. 0000-00-00 00:00:00)
     */
    public function post_update_post($h)
    {
        if (($h->post->status == 'top') && (substr($h->post->pubDate, 0, 4) == '0000')) 
		{
			$sql = "UPDATE " . TABLE_POSTS . " SET post_pub_date = CURRENT_TIMESTAMP WHERE post_id = %d";
			$h->db->query($h->db->prepare($sql, $h->post->id));
		}
    }
}