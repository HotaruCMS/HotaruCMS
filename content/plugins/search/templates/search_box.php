<?php
/**
 * Template for Search
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

if ($this->current_user->getPermission('can_search') == 'yes') { $disabled = ''; } else { $disabled = 'disabled'; }
?>

<h2><?php echo $hotaru->lang['search_title']; ?></h2>
<form name='search_form' id='search_form' action='<?php echo BASEURL; ?>index.php?page=search' method='get'> 
    <input id="search_input" type="text" value="<?php echo $hotaru->lang['search_text']; ?>" size="15" name="search" id="searchsite" 
        onblur="if (this.value == '') {this.value = '<?php echo $hotaru->lang['search_text']; ?>';}"  
        onfocus="if (this.value == '<?php echo $hotaru->lang['search_text']; ?>') {this.value = '';}"
    />
    <input type="hidden" id="dosearch" />
    <input id="search_button" type="submit" id="search-submit" value="<?php echo $hotaru->lang['search_submit']; ?>" <?php echo $disabled; ?> />
</form>


