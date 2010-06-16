<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>


<div id ="plugin_search_form">
    <form name='plugin_search_form' action='<?php echo SITEURL; ?>admin_index.php?page=plugin_search' method='post'>

	<table align="center">
		<tr>		
		<td><input id='admin_plugin_search' type='text' size=24 name='plugin_search' value='' /></td>
		<td>
		<input id='admin_plugin_search_button' type='submit' value='<?php echo $h->lang['admin_theme_plugin_search_submit']; ?>'  /></td>
		</tr>
	</table>

	<input type='hidden' name='page' value='plugin_search'>
    </form>

 </div>