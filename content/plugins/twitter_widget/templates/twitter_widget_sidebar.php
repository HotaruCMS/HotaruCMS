
<div class="woork">
From Woork: <a href="http://woork.blogspot.com">http://woork.blogspot.com</a></div>
<div class="twitter_container">
<?php 

foreach($twitter_status->status as $status){
	echo '<div class="twitter_status">';
	foreach($status->user as $user){
		echo '<img src="'.$user->profile_image_url.'" class="twitter_image">';
		echo '<a href="http://www.twitter.com/'.$user->name.'">'.$user->name.'</a>: ';
	}
	echo $status->text;
	echo '<br/>';
	echo '<div class="twitter_posted_at"><strong>Posted at:</strong> '.$status->created_at.'</div>';
	echo '</div>';
}
?>
</div>