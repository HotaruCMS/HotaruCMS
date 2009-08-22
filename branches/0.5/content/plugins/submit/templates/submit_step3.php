<?php
 
/* **************************************************************************************************** 
 *  File: /plugins/submit/submit_step3.php
 *  Purpose: Step 3 for submitting a new story - the preview
 *  Notes: This file is part of the Submit plugin. The main file is /plugins/submit/submit.php
 *  License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */
 
global $hotaru, $plugin, $post, $lang;
?>
    <div id="breadcrumbs"><a href='<?php echo baseurl ?>'><?php echo $lang['submit_form_home'] ?></a> &raquo; <?php echo $lang["submit_form_step3"] ?></div>
        
    <?php echo $lang["submit_form_instructions_3"] ?> <br /><br />
    
    <?php $hotaru->display_template('post', 'submit') ?>
    
    <div id="submit_edit_confirm">
    
        <!-- EDIT BUTTON -->
        <form name='submit_form_3' action='<?php baseurl ?>index.php?page=submit3' method='post'>
        <input type='hidden' name='post_id' value='<?php echo $post->post_id; ?>' />
        <input type='hidden' name='submit3' value='edit' />
        <input type='submit' name='submit' onclick="javascript:safeExit=true;" value='<?php echo $lang['submit_form_submit_edit_button'] ?>' />
        </form>    

        <!-- CONFIRM BUTTON -->
        <form name='submit_form_3' action='<?php baseurl ?>index.php?page=submit3' method='post'>
        <input type='hidden' name='post_id' value='<?php echo $post->post_id; ?>' />
        <input type='hidden' name='submit3' value='confirm' />
        <input type='submit' name='submit' onclick="javascript:safeExit=true;" value='<?php echo $lang['submit_form_submit_confirm_button'] ?>' />
        </form>
    </div>
    