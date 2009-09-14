<?php
/**
 *  File: /plugins/hello_universe/form_example.php
 *  Purpose: Example form.
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

global $hotaru, $cage, $lang;

$answer = $cage->post->getMixedString2('answer');
if(!$answer) { $answer = ""; }

?>

    <p class='breadcrumbs'><?php echo $lang["hello_universe_form_example"]; ?></p>
    
    This form is in the form_example.php file in the Hello Universe folder. 
    It's called via Function #1 in hello_universe.php and includes a special language file which is included using Function #4.
    
    <?php echo $lang["hello_universe_question"]; ?>
    
    <?php $hotaru->showMessage(); ?>
            
    <form name='update_form' action='<?php echo BASEURL; ?>index.php?page=form_example' method='post'>
    <table>
    <tr><td><?php $lang['hello_universe_answer']; ?> &nbsp; </td><td><input type='text' size=30 name='answer' value='<?php echo $answer ?>' /></td></tr>
    <input type='hidden' name='submit_example' value='true' />
    <tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' value='<?php echo $lang['hello_universe_form_submit']; ?>' /></td></tr>
    </table>
    </form>
    
    <p><a href='<?php echo BASEURL; ?>'><?php echo $lang["hello_universe_back_home"]; ?></a></p>
