<div id="saved_posts_page" class="users_content">

<?php
	$profile = $h->vars['user']->getProfileSettingsData($h, 'user_profile', $h->vars['user']->id);
	$output = '
		<h2 class="widget_head widget_save_post_title">'.$h->lang['save_post_title'].'</h2>
		
		<p>' . $h->lang['save_post_page_description'] . '</p>
		
		<div class="widget_body widget_save_post_body">
			<ul id="save_post_widget">'.PHP_EOL;
	if ( count($profile['saved_posts']) == 0 ){
		$output .= '				<li id="save_post_widget_empty">'.$h->lang['save_post_empty'].'</li>'.PHP_EOL;
	} else {
		foreach ( $profile['saved_posts'] as $key=>$post_id ) {
			$h->readPost($post_id);
			$output .= '				<li id="save_post_widget_'.$post_id.'"><span class="save_post_widget_item"></span><a href="'.$h->url(array('page'=>$post_id)).'" title="'.$h->post->title.'" alt="'.$h->post->title.'">' . $h->post->title . '</a></li>'.PHP_EOL;
		}
	}
	$output .= '			</ul>
		</div>';
	echo $output;
?>

</div>