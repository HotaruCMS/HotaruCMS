<?php
/**
 * Template for SocialBar
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
 * @author    shibuya246 <blog@shibuya246.com>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <title><?php echo $h->getTitle(); ?></title>

        <?php
            // plugin hook
            $result = $h->pluginHook('header_meta');
            if (!isset($result) || !is_array($result)) { ?>
                <meta name="description" content="<?php echo $h->lang['header_meta_description']; ?>" />
                <meta name="keywords" content="<?php echo $h->lang['header_meta_keywords']; ?>" />
        <?php } ?>

    <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js?ver=1.4.2'></script>
    <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js?ver=1.7.2'></script>

    <link rel="stylesheet" href="<?php echo BASEURL . 'content/plugins/socialbar/css/socialbar.css'; ?>" type="text/css" />
</head>
<body>

   <div style="display: block;" id="share_bubble" onclick="shareBar.hideBubble();">
            <h5>Share you opinion with others in !</h5>
            <small>Complete the set to compare and opinions with others.</small>
        </div>

        <div style="width: 1280px;" id="share_bar">
        <div class="nav">
            <h1><a href="/">Logo</a></h1>
        </div>
        <div class="poll">
            <div class="slider">
                <h5>Vote for this article?</h5>
                    <a href="#" id="button_10" class="button Yes"><span>Yes <b>| <span class="count"><?php echo $h->vars['votesUp']; ?></span></b></span></a>
                    <a href="#" id="button_-10" class="button No"><span>No <b>| <span class="count"><?php echo $h->vars['votesDown']; ?></span></b></span></a>
            </div>
            <div class="responses">
                <?php // $h->pluginHook('sb_base_show_post_middle', 'who_voted'); ?>
                <ul>
                <?php                    
                    if($h->isActive('avatar')) {
                        foreach ($h->vars['socialbar']['voters'] as $voter) {
                            $h->setAvatar($voter->user_id, 32); ?>
                             <li>
                                 <div class="avatar_container">
                                    <a href="<?php echo $h->url(array('user' => $voter->user_username)); ?>">
                                        <span class="rounded_avatar">
                                                <?php
                                                echo $h->wrapAvatar();
                                                ?>
                                            <span class="rounded_corners">
                                                <img src="<?php echo BASEURL; ?>content/plugins/socialbar/images/imgcorners_35px.png" alt="" />
                                            </span>
                                       </span>                                      
                                    </a>
                                 </div>
                             </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div class="more">
            <a href="/">More News</a>
            <span class="pipe">|</span>
            <a href="">Share</a>
            <span class="pipe">|</span>
            <a href="/" target="_top">Comment</a>
            <span class="pipe">|</span>
            <a href="<?php echo $h->post->origUrl ?>" target="_top" onclick="shareBarClose()">Close</a>
        </div>
    </div>


<script type='text/javascript'>

     $(".button").click(function(){

            var $button = $(this);
            var button = $(this).attr('id').split('_');
            var rating = button[button.length-1];
            var formdata = 'post_id=<?php echo $h->post->id; ?>&rating=' + rating;
            var sendurl = BASEURL + 'content/plugins/vote/vote_functions.php';

             $.ajax(
                {
                type: 'post',
                url: sendurl,
                data: formdata,
                 beforeSend: function () {
                            $button.addClass('vote_color_top_clicked');
                    },
                error: 	function(XMLHttpRequest, textStatus, errorThrown) {
                                //$('#error_message').html('There was an error with the data');
                },
                success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
                        if (data.error) {
                            $('#error_message').html(data.error);
                        }
                        else
                        {
                            $('#error_message').html(data.result);
                        }
                },
                dataType: "json"
            });

        return false;
     });
     </script>



</body>
</html>

<iframe style="height: 795px; width: 1280px;" src="<?php echo $h->post->origUrl ?>" id="source" frameborder="0" scrolling="auto"></iframe>

