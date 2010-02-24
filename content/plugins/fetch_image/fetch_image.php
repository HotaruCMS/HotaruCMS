<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>


<?php
function post_img_div($content) {
    preg_match_all("/<img src\=('|\")(.*)('|\") .*( |)\/>/", $content, $matches);
    for($i=0;$i<count($matches[0]);$i++)
    {
        if(!preg_match("/rel=[\"\']*nofollow[\"\']*/",$matches[0][$i]))
        {
            preg_match_all("/<a.*? href=\"(.*?)\"(.*?)>(.*?)<\/a>/i", $matches[0][$i], $matches1);
            $content = str_replace(">".$matches1[3][0]."</a>"," rel='nofollow'>".$matches1[3][0]."</a>",$content);
        }
    }
    return $content;
}



function get_post_image ($post_id=0, $width=0, $height=0, $img_script='',$annotate=false) {
	/// <summary>
	///		For Getting the Image
	///     GET-POST-IMAGE script by Tim McDaniels
	///	</summary>

	global $wpdb;
	//if ($annotate) { $annotate_id ='id="toAnnotate" ';} else {$annotate_id="";}

	if($post_id > 0) {

		 // select the post content from the db

		 $sql = 'SELECT post_content FROM ' . $wpdb->posts . ' WHERE id = ' . $wpdb->escape($post_id);
		 $row = $wpdb->get_row($sql);
		 $the_content = $row->post_content;
		 if(strlen($the_content)) {

			  // use regex to find the src of the image

			preg_match("/<img src\=('|\")(.*)('|\") .*( |)\/>/", $the_content, $matches);
			if(!$matches) {
				preg_match("/<img class\=\".*\" title\=\".*\" src\=('|\")(.*)('|\") .*( |)\/>/U", $the_content, $matches);
			}
			$imageid = "1-" . $post_id;
			$the_image = '';
			$the_image_src = $matches[2];
			$frags = preg_split("/(\"|')/", $the_image_src);
			if(count($frags)) {
				$the_image_src = $frags[0];
			}

			  // if src found, then create a new img tag

			  if(strlen($the_image_src)) {
				   if(strlen($img_script)) {

					    // if the src starts with http/https, then strip out server name

					    if(preg_match("/^(http(|s):\/\/)/", $the_image_src)) {
						     $the_image_src = preg_replace("/^(http(|s):\/\/)/", '', $the_image_src);
						     $frags = split("\/", $the_image_src);
						     array_shift($frags);
						     $the_image_src = '/' . join("/", $frags);
					    }
						if ($width < 0) {
							$the_image = $img_script . $the_image_src;   // Used for returning just result, no formatting
						}
						else
					    {
							$the_image = '<img id="leadimage-' . $imageid . '" alt="" src="' . $img_script . $the_image_src . '" />';
					    }
				   }
				   else {
					    $the_image = '<img id="leadimage-' . $imageid . '" alt="" src="' . $the_image_src . '" width="' . $width . '" height="' . $height . '" />';
				   }
			  }
                          else { $the_image = '<img id="leadimage-' . $imageid . '" alt="no image" src= "" width="80px" height="80px" />'; }
			  return $the_image;
		 }
	}
}


?>