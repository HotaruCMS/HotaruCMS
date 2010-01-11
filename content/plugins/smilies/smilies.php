<?php
/**
 * name: Smilies
 * description: Use smilies in comments
 * version: 0.3
 * folder: smilies
 * class: Smilies
 * type: smilies
 * requires: comments 1.2
 * hooks: show_comments_content, comment_manager_comment_content, comments_widget_comment_content
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
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

class Smilies
{
    /**
     * Displays "Hello World!" wherever the plugin hook is.
     */
    public function show_comments_content($h, $return = false)
    {
        $text = $h->comment->content;
        $path_to_smilies = BASEURL . 'content/plugins/smilies/images/';

        $smilies = array(
            ':mrgreen:' => 'icon_mrgreen.gif',
            ':neutral:' => 'icon_neutral.gif',
            ':twisted:' => 'icon_twisted.gif',
            ':arrow:' => 'icon_arrow.gif',
            ':shock:' => 'icon_eek.gif',
            ':smile:' => 'icon_smile.gif',
            ':???:' => 'icon_confused.gif',
            ':cool:' => 'icon_cool.gif',
            ':evil:' => 'icon_evil.gif',
            ':grin:' => 'icon_biggrin.gif',
            ':idea:' => 'icon_idea.gif',
            ':oops:' => 'icon_redface.gif',
            ':razz:' => 'icon_razz.gif',
            ':roll:' => 'icon_rolleyes.gif',
            ':wink:' => 'icon_wink.gif',
            ':cry:' => 'icon_cry.gif',
            ':eek:' => 'icon_surprised.gif',
            ':lol:' => 'icon_lol.gif',
            ':mad:' => 'icon_mad.gif',
            ':sad:' => 'icon_sad.gif',
            '8-)' => 'icon_cool.gif',
            '8-O' => 'icon_eek.gif',
            ':-(' => 'icon_sad.gif',
            ':-)' => 'icon_smile.gif',
            ':-?' => 'icon_confused.gif',
            ':-D' => 'icon_biggrin.gif',
            ':-P' => 'icon_razz.gif',
            ':-o' => 'icon_surprised.gif',
            ':-x' => 'icon_mad.gif',
            ':-|' => 'icon_neutral.gif',
            ';-)' => 'icon_wink.gif',
            '8)' => 'icon_cool.gif',
            '8O' => 'icon_eek.gif',
            ':(' => 'icon_sad.gif',
            ':)' => 'icon_smile.gif',
            ':?' => 'icon_confused.gif',
            ':D' => 'icon_biggrin.gif',
            ':P' => 'icon_razz.gif',
            ':o' => 'icon_surprised.gif',
            ':x' => 'icon_mad.gif',
            ':|' => 'icon_neutral.gif',
            ';)' => 'icon_wink.gif',
            ':!:' => 'icon_exclaim.gif',
            ':?:' => 'icon_question.gif',
        );

        foreach ( (array) $smilies as $smiley => $img ) {
            $smiliessearch[] = '/(\s|^)'.preg_quote($smiley, '/').'(\s|$)/';
            $smiley_masked = htmlspecialchars(trim($smiley), ENT_QUOTES);
            $smiliesreplace[] = " <img src='$path_to_smilies$img' alt='$smiley_masked' class='smilies' /> ";
        }

        $textarr = preg_split("/(<.*>)/U", $text, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between

        $stop = count($textarr); // loop stuff
        $output = '';
        for ($i = 0; $i < $stop; $i++) {
            $content = $textarr[$i];
            if ((strlen($content) > 0) && ('<' != $content{0})) { // If it's not a tag
                $content = preg_replace($smiliessearch, $smiliesreplace, $content);
            }
            $output .= $content;
        }

        if ($return) {
            return $output;  // the new, smiley-enhanced comment!
        } else {
            echo nl2br($output);  
            return true;
        }
    }
    
    
    /**
     * Show smilies in Comment Manager
     */
    public function comment_manager_comment_content($h)
    {
        $comment = $this->show_comments_content($h, true);
        $h->comment->content = $comment;
    }
    
    
    /**
     * Show smilies in the Comments Widget
     */
    public function comments_widget_comment_content($h)
    {
        $comment = $this->show_comments_content($h, true);
        $h->comment->content = $comment;
    }

}

?>