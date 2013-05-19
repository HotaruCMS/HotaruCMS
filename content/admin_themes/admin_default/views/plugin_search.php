<?php

    $search = $h->cage->post->testAlnumLines('plugin_search');
    $sysinfo = new SystemInfo();

    if ($search) {	
	$plugins = $sysinfo->pluginSearch($h, $search);
    } else {
	$tags = $sysinfo->pluginTagCloud($h, 20);
	$plugins = null;
    }

?>


<div id ="plugin_search_form">
    <form name='plugin_search_form' action='<?php echo SITEURL; ?>admin_index.php?page=plugin_search#tab_search' method='post'>

	<table align="center">
		<tr>		
		<td><input id='admin_plugin_search' type='text' size=24 name='plugin_search' value='<?php echo $search; ?>' /></td>
		<td>
		<input id='admin_plugin_search_button' type='submit' value='<?php echo $h->lang['admin_theme_plugin_search_submit']; ?>'  /></td>
		</tr>
	</table>

	<input type='hidden' name='page' value='plugin_search'>
    </form>

 </div>


<?php

if ($plugins) {    
    foreach ($plugins as $plugin) {
	//var_dump($plugin);
	echo "<div class='plugin_col'><a href=''>" . urldecode($plugin['post_title']) . "</a> " . urldecode($plugin['post_content']) . "</div>";
	//post_content
    }
} else {
    if (isset($tags)) {
   //var_dump($tags);
        foreach ($tags as $tag) {	
            echo "<div class='plugin_col'><a href=''>" . urldecode($tag['tags_word']) . "</a> " . urldecode($tag['CNT']) . "</div>";
            //post_content
        }
    } else {
        echo "no plugins found for that search";
    }
}



?>