<?php
/**
 * name: Submit Light
 * description: Reduces Submit to two steps
 * version: 0.4
 * folder: submit_light
 * class: SubmitLight
 * requires: submit 2.4
 * extends: Submit
 * hooks: theme_index_top
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

class SubmitLight extends Submit
{
    /**
     * Do Submit 2
     */
    public function doSubmit2($h, $funcs = array())
    {
        // check if data has been submitted
        $submitted = $funcs->checkSubmitted($h, 'submit2');
        
        // not submitted so reload data from step 1 (or step 2 if editing)
        if (!$submitted) {
            // if coming from step 1, get the key from the url
            $key = $h->cage->get->testAlnum('key');
            
            // use the key in the step 2 form
            $h->vars['submit_key'] = $key; 
            
            // load submitted data:
            $submitted_data = $funcs->loadSubmitData($h, $key);
            
            // merge defaults from "checkSubmitted" with $submitted_data...
            $merged_data = array_merge($h->vars['submitted_data'], $submitted_data);
            $h->vars['submitted_data'] = $merged_data;
            
            // not sure if this is completely necessary, but it's worth having...
            if ($h->vars['submitted_data']['submit_id']) {
                $h->post->id = $h->vars['submitted_data']['submit_id'];
                $h->post->readPost($h);
            }
        }
        
        // submitted so save data and proceed to step 3 when no more errors
        if ($submitted) {
            $key = $funcs->processSubmitted($h, 'submit2');
            $errors = $funcs->checkErrors($h, 'submit2', $key);
            if (!$errors) {
                $funcs->processSubmission($h, $key);
                $postid = $h->post->id; // got this from addPost in Post.php
                $this->doSubmitConfirm($h, $funcs); // THIS IS THE LINE THAT DIFFERS FROM THE ORIGINAL
            }
            $h->vars['submit_key'] = $key; // used in the step 2 form
        }
    }
}
?>
