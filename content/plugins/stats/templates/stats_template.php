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

		
<h2 class="sidebar_widget_head"><?php echo $hotaru->lang['stats_title']; ?></h2>

<div id="sidebar_widget_body"> 
<!--<p>	<?php echo $hotaru->lang['stats_pretext'] ?>:  </p> -->

	<!-- Basic stats -->
	<div id="statBlock">
		<?php echo $hotaru->lang['members']; ?>: <span id="emphasizeStat"><?php echo $hotaru->vars['stats_numMembers']; ?> </span><br />
		<?php echo $hotaru->lang['posts']; ?>: <span id="emphasizeStat"><?php echo $hotaru->vars['stats_numPosts']; ?> </span><br />
		<?php echo $hotaru->lang['comments']; ?>: <span id="emphasizeStat"><?php echo $hotaru->vars['stats_numComments']; ?> </span><br />
		<?php echo $hotaru->lang['votes']; ?>: <span id="emphasizeStat"><?php echo $hotaru->vars['stats_numVotes']; ?> </span><br />
    </div>
    
	<!-- Average per member -->
	<div id="statBlock">
	    <?php echo $hotaru->lang['posts_per_member']; ?>: <span id="emphasizeStat"><?php echo round(($hotaru->vars['stats_numPosts'] / $hotaru->vars['stats_numMembers']), 2); ?> </span> <br />
	    <?php echo $hotaru->lang['comments_per_member']; ?>: <span id="emphasizeStat"><?php echo round(($hotaru->vars['stats_numComments'] / $hotaru->vars['stats_numMembers']), 2); ?> </span> <br />
	    <?php echo $hotaru->lang['votes_per_member']; ?>: <span id="emphasizeStat"><?php echo round(($hotaru->vars['stats_numVotes'] / $hotaru->vars['stats_numMembers']), 2); ?> </span> <br />
    </div>

    <!-- Newest member -->
	<div id="statBlock">
		<?php echo $hotaru->lang['newest_member']; ?>: 
			<a href="<?php echo $hotaru->url(array('user'=>$hotaru->vars['newestMemberName'])); ?> "><span id="emphasizeStat"><?php echo $hotaru->vars['newestMemberName']; ?></span></a><br />
    </div>

    <!-- Highest rated post -->
    <div id="statBlock">
    	<?php echo $hotaru->lang['highest_voted_post']; ?>: <span id="emphasizeStat">
    		<a href="<?php echo $hotaru->url(array('page'=>$hotaru->post->id)); ?>" ?> <span id="emphasizeStat"><?php echo $hotaru->post->title; ?></span></a><br />
    </div>

<!-- soon-to-be link to the main stats page
    <div id="statBlock" class="makeCentered">
    	<a href="<?php echo BASEURL; ?>"> <?php echo $hotaru->lang['complete_stats_anchor_text']; ?></a>
	</div>
-->

	
	
</div>