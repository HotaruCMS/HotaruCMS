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
    <form name='plugin_search_form' class='form-inline text-right' action='<?php echo SITEURL; ?>admin_index.php?page=plugin_search#tab_search' method='post'>
	
		<input id='admin_plugin_search' type='text' name='plugin_search' value='<?php echo $search; ?>' /></td>
		<input id='admin_plugin_search_button' class='btn btn-primary' type='submit' value='<?php echo $h->lang('admin_theme_plugin_search_submit'); ?>'  /></td>
		<input type='hidden' name='page' value='plugin_search'>
	
    </form>

</div>


<?php

if ($plugins) {    
    foreach ($plugins as $plugin) {
	//var_dump($plugin);
        $ahref= SITEURL . 'admin_index.php?page=plugin_management&action=update&plugin=' . strtolower($plugin['post_title']) . '&version=' . $plugin['post_title'] . '#tab_search';
	echo "<div class='plugin_col'><a href='" . $ahref . "' class='button'>Install </button>" . urldecode($plugin['post_title']) . "</a> " . urldecode($plugin['post_content']) . "</div>";
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