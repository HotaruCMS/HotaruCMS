<?php
/**
 * Bookmarklet
 */
?>

<a class="bookmarklet" href="javascript:q=(document.location.href);void(open('<?php echo BASEURL; ?>index.php?page=submit&url='+escape(q),'','resizable,location,scrollbars,menubar,toolbar,status'));"><?php echo $h->lang['bookmarklet_submit']; ?></a>

