<?php
/**
 * Follow - Followers
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
 * @author    shibuya246 <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

?>

<?php
    $page = $h->cage->get->testAlnumLines('page');
    $page == "followers" ? $follow_type = $h->lang["follow_list_followers"] : $follow_type = $h->lang["follow_list_following"];
?>

<div id="list_followers" class="users_content">

<h2><?php echo $follow_type . " (" . count($h->vars['follow_list']->items) .")"; ?></h2>
    

<table class="messaging_list">
    <tr class="messaging_list_headers">
        <td class="messaging_fom"><?php echo $follow_type; ?></td>
        <td class="messaging_subject"><?php echo $h->lang['follow_list_activity']; ?></td>
        <td class="messaging_delete"></td>
    </tr>
    
    <?php if (isset($h->vars['follow_list']->items)) { ?>

        <?php foreach ($h->vars['follow_list']->items as $user) { ?>
            <tr id="<?php echo $user->user_id; ?>">
            
                
                <td class="follow_user">		    
			 <?php if($h->isActive('avatar')) {
			     $h->setAvatar($user->user_id, 32);
			     echo $h->wrapAvatar();
			 } ?>
<!--		    <a href="<?php //echo $h->url(array('user'=>$user->user_username)); ?>">
			<?php //echo $user->user_username; ?>
		    </a>-->
		</td>
                
                <td class="follow_activity">
		    <?php if($h->isActive('activity')) {			
			     $Activity = new Activity();			     
			     $LatestActivity = $Activity->getLatestActivity($h, 1, $user->user_id);
			     $action = $LatestActivity[0];
			     echo $Activity->activityContent($h, $action);
			     echo "<br /><small>[" . date('g:ia, M jS', strtotime($action->useract_date)) . "]</small>";
			 } ?>
                    
                </td>                               

		<?php
		if ($user->user_id != $h->currentUser->id) {
		    $FollowFuncs = new FollowFuncs();
		    $FollowFuncs->checkFollow($h, 'following', $user->user_id) == 0 ? $type = 'Follow' : $type = 'Unfollow';

		    echo '<td class="follow_update"><center>';
		    echo '<input type="button" class="follow_button" name="'. $type. '_' . $user->user_id .'" id="' . $type . '_' . $user->user_id .'" value="' . $type .'">';
		    echo '</center></td>';
		 } ?>
            </tr>
        <?php } ?>
    
    <?php } else { ?>
        <tr><td colspan='4'><center><?php echo $h->lang['follow_no_followers']; ?></center></td></tr>
    <?php } ?>
    
</table>

    <?php echo $h->pageBar($h->vars['follow_list']); ?>
    
   
</div>


 <script type='text/javascript'>
    jQuery('document').ready(function($) {

    $(".follow_button").click(function(){

	    var button = $(this);
	    var array = $(this).attr('id').split('_');
            var user_id = array[array.length-1];
	    var type = array[array.length-2].toLowerCase();
            var formdata = 'action=' + type + '&user_id=' + user_id;
            var sendurl = BASEURL +"content/plugins/follow/templates/follow_update.php";

	    $.ajax(
		{
		type: 'post',
		url: sendurl,
		data: formdata,
		error: 	function(XMLHttpRequest, textStatus, errorThrown) {
				$(this).attr('value', 'error');
		},
		success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
			if (data.error) {
			    $('#error_message').html(data.error);
			}
			else
			{			    
			    $(button).attr('value', data.result)
			    $(button).attr('id', data.result + '_' +user_id);
			    $(button).attr('name', data.result + '_' + user_id);
			}
		},
		dataType: "json"
	    });
	 });
    });
 

 </script>