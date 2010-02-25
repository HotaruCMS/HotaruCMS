<?php
/**
 * name: TOS AntiSpam
 * description: Adds Terms of Service checkbox and antispam question to registration and post submission forms
 * version: 0.1
 * folder: tos_antispam
 * class: TosAntispam
 * requires: user_signin 0.2
 * hooks: admin_plugin_settings, admin_sidebar_plugin_settings, install_plugin, user_signin_register_register_form, user_signin_register_error_check, submit_2_fields, submit_2_check_errors, submit_functions_process_submitted
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
 */

class TosAntispam
{
    /**
     * Default settings on install
     */
    public function install_plugin($h)
    {
        // Default settings 
        $tos_antispam_settings = $h->getSerializedSettings();
        if (!isset($tos_antispam_settings['registration'])) { $tos_antispam_settings['registration'] = "checked"; }
        if (!isset($tos_antispam_settings['post_submission'])) { $tos_antispam_settings['post_submission'] = ""; }
        if (!isset($tos_antispam_settings['question'])) { $tos_antispam_settings['question'] = "What color are polar bears?"; }
        if (!isset($tos_antispam_settings['choices'])) { $tos_antispam_settings['choices'] = array('blue'=>'blue', 'pink'=>'pink', 'white'=>'white', 'orange'=>'orange', 'green'=>'green'); }
        if (!isset($tos_antispam_settings['answer'])) { $tos_antispam_settings['answer'] = 'white'; }
        if (!isset($tos_antispam_settings['first_x_posts'])) { $tos_antispam_settings['first_x_posts'] = 1; }
        
        $h->updateSetting('tos_antispam_settings', serialize($tos_antispam_settings));
    }
    
    
    /**
     * Add TOS checkbox and drop down theme picker to registration form
     */
    public function user_signin_register_register_form($h)
    {
        $tos_antispam_settings = $h->getSerializedSettings();
        if (!$tos_antispam_settings['registration']) { return false; }
        
        if (!isset($h->vars['tos_check'])) { $h->vars['tos_check'] = ''; }
        if (!isset($h->vars['tos_answer_selected'])) { $h->vars['tos_answer_selected'] = "choose"; }
        
        $h->vars["tos_question"] = $tos_antispam_settings['question'];
        $h->vars["tos_choices"] = $tos_antispam_settings['choices'];
        
        $h->displayTemplate('tos_antispam');
    }
    
    /**
     * Get and check response from registration form
     */
    public function user_signin_register_error_check($h)
    {
        $tos_antispam_settings = $h->getSerializedSettings();
        if (!$tos_antispam_settings['registration']) { return false; }
        
        if (!$h->cage->post->keyExists('tos_check')) {
            $h->vars['reg_error'] = 1;
            $h->messages[$h->lang['tos_antispam_tos_unchecked']] = 'red';
        } else {
            $h->vars['tos_check'] = 'checked'; // check it in case it needs showing again because of an error elsewhere
        }
        
        if (!$h->cage->post->keyExists('tos_answer')) {
            $h->vars['tos_answer_selected'] = "choose";
            return false;
        }
                    
        $tos_answer = $h->cage->post->sanitizeTags('tos_answer');

        if (isset($tos_antispam_settings['choices'][$tos_answer]) 
            && ($tos_antispam_settings['choices'][$tos_answer] == $tos_antispam_settings['answer'])) {
                $h->vars['tos_answer_selected'] = $tos_answer;
        } else {
            $h->vars['reg_error'] = 1;
            $h->messages[$h->lang['tos_antispam_tos_wrong_answer']] = 'red';
            $h->vars['tos_answer_selected'] = "choose";
        }
    }

    /**
     * Add TOS and antispam question to submit form 2
     */
    public function submit_2_fields($h)
    {
        if ($h->pageName != 'submit2') { return false; }
        
        $tos_antispam_settings = $h->getSerializedSettings();
        if (!$tos_antispam_settings['post_submission']) { return false; }
        if (!$tos_antispam_settings['first_x_posts']) { return false; }
        if ($h->postsApproved() > $tos_antispam_settings['first_x_posts']) { return false; }
        
        if (!isset($h->vars['tos_check'])) {
            if (isset($h->vars['submitted_data']['submit_tos_check'])) { 
                $h->vars['tos_check'] = $h->vars['submitted_data']['submit_tos_check']; 
            } else {
                $h->vars['tos_check'] = '';
            }
        }
        
        if (!isset($h->vars['tos_answer_selected'])) {
            if (isset($h->vars['submitted_data']['submit_tos_selected'])) { 
                $h->vars['tos_answer_selected'] = $h->vars['submitted_data']['submit_tos_selected']; 
            } else {
                $h->vars['tos_answer_selected'] = "choose";
            }
        }
        
        $h->vars["tos_question"] = $tos_antispam_settings['question'];
        $h->vars["tos_choices"] = $tos_antispam_settings['choices'];

        $h->displayTemplate('tos_antispam');
    }
    
    
    /**
     * Assign form data to "submitted data" for saving
     */
    public function submit_functions_process_submitted($h)
    {
        if ($h->pageName != 'submit2') { return false; }

        $tos_antispam_settings = $h->getSerializedSettings();
        if (!$tos_antispam_settings['post_submission']) { return false; }
        if (!$tos_antispam_settings['first_x_posts']) { return false; }
        if ($h->postsApproved() > $tos_antispam_settings['first_x_posts']) { return false; }
        
        if ($h->cage->post->keyExists('tos_check')) {
            $tos_check = 'checked';
        } else {
            $tos_check = '';
        }

        $h->vars['submitted_data']['submit_tos_check'] = $tos_check;
        $h->vars['submitted_data']['submit_tos_selected'] = $h->cage->post->sanitizeTags('tos_answer');
    }
    
    
    /**
     * Check errors in Submit step 2
     */
    public function submit_2_check_errors($h)
    {
        if ($h->pageName != 'submit2') { return false; } // we don't want this in Edit Post
        
        $tos_antispam_settings = $h->getSerializedSettings();
        if (!$tos_antispam_settings['post_submission']) { return false; }
        if (!$tos_antispam_settings['first_x_posts']) { return false; }
        if ($h->postsApproved() > $tos_antispam_settings['first_x_posts']) { return false; }

        $error = 0;
        
        if (!$h->vars['submitted_data']['submit_tos_check']) {
            $error = 1;
            $h->messages[$h->lang['tos_antispam_tos_unchecked']] = 'red';
            $h->vars['tos_check'] = '';
        } else {
            $h->vars['tos_check'] = 'checked'; // check it in case it needs showing again because of an error elsewhere
        }
        
        $tos_answer = $h->vars['submitted_data']['submit_tos_selected'];
        if (isset($tos_antispam_settings['choices'][$tos_answer]) 
            && ($tos_antispam_settings['choices'][$tos_answer] != $tos_antispam_settings['answer'])) {
                $error = 1;
                $h->messages[$h->lang['tos_antispam_tos_wrong_answer']] = 'red';
                $h->vars['tos_answer_selected'] = "choose";
        } else {
            $h->vars['tos_answer_selected'] = $h->vars['submitted_data']['submit_tos_selected'];
        }
        
        if ($error == 1) { return true; }
    }
}

?>