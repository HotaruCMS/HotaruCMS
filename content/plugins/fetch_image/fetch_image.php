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


?>