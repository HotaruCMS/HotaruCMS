<?php

    $search = $h->cage->post->testAlnumLines('plugin_search');
    $sysinfo = new \Libs\SystemInfo();

    if ($search) {	
	$plugins = $sysinfo->pluginSearch($h, $search);
    } else {
	$tags = $sysinfo->pluginTagCloud($h, 20);
	$plugins = null;
    }
    //print_r($tags);
?>


<div id ="plugin_search_form" style="margin-top:10px;">
    <?php if (1==0) { ?>
    <form name='plugin_search_form' class='form-inline text-right' action='<?php echo SITEURL; ?>admin_index.php?page=plugin_search#tab_search' method='post'>
	
		<input id='admin_plugin_search' type='text' name='plugin_search' value='<?php echo $search; ?>' /></td>
		<input id='admin_plugin_search_button' class='btn btn-primary' type='submit' value='<?php echo $h->lang('admin_theme_plugin_search_submit'); ?>'  /></td>
		<input type='hidden' name='page' value='plugin_search'>
	
    </form>
    <?php } ?>
    
</div>


<div>
<?php

if ($plugins) {    
    
    foreach ($plugins as $plugin) {
	//var_dump($plugin);
        $ahref= SITEURL . 'admin_index.php?page=plugin_management&action=update&plugin=' . strtolower($plugin['post_title']) . '&version=' . $plugin['post_title'] . '#tab_search';
	?>
        <div class="plugin_col col-md-4">
            <a href='<?php echo $ahref; ?>' class='button'>Install </button><?php echo urldecode($plugin['post_title']); ?></a>
            <?php echo urldecode($plugin['post_content']); ?>
        </div>
        <?php       
    }
} else {
    if (isset($tags)) {
    //var_dump($tags);
        foreach ($tags['resources'] as $tag) {	
            $titleLink = str_replace(' ', '_',urldecode(strtolower($tag['title'])));
            $ahref= SITEURL . "admin_index.php?page=plugin_management&action=update&plugin=" . $titleLink . "&resourceId=" . $tag['id'] . "&versionId=" . $tag['version_id'] . "#tab_search";
                
            ?>
            <div class='plugin-card col-md-5'>
                <div class="plugin-card-top">
                    <div class="pull-right">
                        <a href="<?php echo $ahref; ?>" class="btn btn-default btn-xs">Install Now</a>
                        <div>
                            <a href="http://forums.hotarucms.org/resources/<?php echo $tag['id'];?>">more details</a>
                        </div>
                    </div>
                    
                    <a href="" class="plugin-icon pull-left">
                        <img src="http://forums.hotarucms.org/data/resource_icons/0/<?php echo $tag['id'];?>.jpg" width="80px" />
                    </a>
                    <div class="name">
                            <a href=''><?php echo urldecode($tag['title']); ?></a>
                    </div>
                    
                    <div class="desc">
                        <i><i class='text-muted fa fa-user'></i>&nbsp;<?php echo $tag['author_username'];?></i> 
                    </div>
                    
                </div>
                <div class="plugin-card-bottom">
                    <div class="pull-left"> 
                        <div class="vers column-rating">
                            <?php $rating = $tag['rating_sum']; ?>
                                <div class="star-rating" title="<?php echo $rating; ?> rating based on <?php echo $tag['times_rated']; ?> ratings">

                                    <?php
                                    for ($x=1; $x<=5; $x++) {
                                        $star ='fa-star-o';
                                        if ($rating >= $x) $star = 'fa-star';
                                        elseif ($rating >= $x - .5) $star = 'fa-star-half-o';
                                        
                                        echo '<div class="fa ' . $star . '"></div>';
                                      }
                                    ?>
                                    &nbsp;<span class="num-ratings">(<?php echo $tag['times_rated']; ?>)</span>
                                </div>	

                        </div>
                        <div class="column-downloaded">
                            <?php echo $tag['times_downloaded']; ?> downloads
                        </div>
                    </div>
                    <div class="pull-right">
                        
                    
                        <div class="">
                            <b>Version</b> <?php echo $tag['version_string'];?>
                        </div>
                        <div class="column-updated">
                                <strong>Last Updated:</strong> <span title="<?php echo date('d/m/Y',$tag['last_update']); ?>">
                                        <?php echo date('d/m/Y', $tag['last_update']); ?>					</span>
                        </div>
                    </div>
                </div>
               
            </div>
            <?php
        }
    } else {
        echo "<br/>No plugins found<br/>";
    }
}
?>
</div>
