<?php 
    require_once('../../../hotaru_settings.php'); // need this to get BASEURL

    header('Content-Type: text/javascript; charset=UTF-8');
    header("Expires: " . gmdate("D, d M Y H:i:s", time() + 3600) . " GMT");
    header("Cache-Control: max-age=3600, must-revalidate");
    
    $server = BASEURL;
    $url_mod = 0; //change value to 1 if you are having problems with 406 errors, otherwise don't touch!
?>

function vote()
{
    var check = window.parent.submit_url;
    
    if (!check) { 
        var url1 = document.URL; 
    } else { 
        var url1 = window.parent.submit_url; 
    }
    
    <?php if($url_mod == 1) { ?>
        url1 = url1.replace(/http:\/\//i,'');
    <?php } ?>
    var url2 = '<?php echo $server; ?>index.php?page=evb&url='+url1;
    document.write('<iframe name="hotaru_submit" width="54" height="71" scrolling="no" frameborder="0" src="'+url2+'"></iframe>');    
}

vote();