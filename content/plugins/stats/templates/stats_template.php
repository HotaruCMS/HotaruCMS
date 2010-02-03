<?php
/**
 * Template for Stats
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
 * @author    Kyle Carlson (rushnp774@gmail.com)
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

?>

		
<h2 class="widget_head"><?php echo $h->lang['stats_title']; ?></h2>

<div class="widget_body"> 
<!--<p>	<?php echo $h->lang['stats_pretext'] ?>:  </p> -->

	<!-- Basic stats -->
	<div class="statBlock">
		<?php echo $h->lang['members']; ?>: <span class="emphasizeStat"><?php echo $h->vars['stats_numMembers']; ?> </span><br />
		<?php echo $h->lang['posts']; ?>: <span class="emphasizeStat"><?php echo $h->vars['stats_numPosts']; ?> </span><br />
		<?php echo $h->lang['comments']; ?>: <span class="emphasizeStat"><?php echo $h->vars['stats_numComments']; ?> </span><br />
		<?php echo $h->lang['votes']; ?>: <span class="emphasizeStat"><?php echo $h->vars['stats_numVotes']; ?> </span><br />
    </div>
    
	<!-- Average per member -->
	<div class="statBlock">
	    <?php echo $h->lang['posts_per_member']; ?>: <span class="emphasizeStat"><?php echo round(($h->vars['stats_numPosts'] / $h->vars['stats_numMembers']), 2); ?> </span> <br />
	    <?php echo $h->lang['comments_per_member']; ?>: <span class="emphasizeStat"><?php echo round(($h->vars['stats_numComments'] / $h->vars['stats_numMembers']), 2); ?> </span> <br />
	    <?php echo $h->lang['votes_per_member']; ?>: <span class="emphasizeStat"><?php echo round(($h->vars['stats_numVotes'] / $h->vars['stats_numMembers']), 2); ?> </span> <br />
    </div>

    <!-- Newest member -->
	<div class="statBlock">
		<?php echo $h->lang['newest_member']; ?>: 
			<a href="<?php echo $h->url(array('user'=>$h->vars['newestMemberName'])); ?> "><span class="emphasizeStat"><?php echo $h->vars['newestMemberName']; ?></span></a><br />
    </div>

    <!-- Highest rated post -->
    <div class="statBlock">
    	<?php echo $h->lang['highest_voted_post']; ?>: 
    		<a href="<?php echo $h->url(array('page'=>$h->post->id)); ?>"> <span class="emphasizeStat"><?php echo $h->post->title; ?></span></a><br />
    </div>

<!-- soon-to-be link to the main stats page
    <div class="statBlock" class="makeCentered">
    	<a href="<?php echo BASEURL; ?>"> <?php echo $h->lang['complete_stats_anchor_text']; ?></a>
	</div>
-->

</div>