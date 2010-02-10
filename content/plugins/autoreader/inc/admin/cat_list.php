<?php $this->adminHeader() ?> 
  
<script type="text/javascript"  >
if(!window.XMLHttpRequest)
{
  var XMLHttpRequest = function()
  {
    try{ return new ActiveXObject(   "MSXML3.XMLHTTP")     } catch(e) {}
    try{ return new ActiveXObject(   "MSXML2.XMLHTTP.3.0") } catch(e) {}
    try{ return new ActiveXObject(   "MSXML2.XMLHTTP")     } catch(e) {}
    try{ return new ActiveXObject("Microsoft.XMLHTTP")     } catch(e) {}
  }
}
var chat_XMLHttp_getlist_rss = new XMLHttpRequest();


function get_rssfeed(blog_no)
{
	if(document.getElementById("rss_import_"+blog_no))
	{
	document.getElementById("rss_import_"+blog_no).innerHTML="<img src='/wp-content/plugins/profiler/loader2.gif' alt='finding rss'/>";	
	}
	if (chat_XMLHttp_getlist_rss.readyState % 4) return;
		
    	chat_XMLHttp_getlist_rss.open('get', "/wp-content/plugins/rssfeeds.php?blog_id="+blog_no,true);
	    chat_XMLHttp_getlist_rss.send(null);
	
		chat_XMLHttp_getlist_rss.onreadystatechange =function(){ getres_rssfeed(blog_no); }
}
function getres_rssfeed(blog_no)
{
	if(chat_XMLHttp_getlist_rss.readyState == 4 && chat_XMLHttp_getlist_rss.status == 200)
    {
		document.getElementById("rss_import_"+blog_no).innerHTML="";
		var rss_feed =chat_XMLHttp_getlist_rss.responseText;
		var rss_post_url=rss_feed.split("***");
		//rss_post_url = rss_post_url[1];
		//alert(rss_post_url[1]);
		if(rss_post_url[1])
		{						
			document.getElementById("rss_import_"+blog_no).value=rss_post_url[1];
			get_bulkpostts(rss_post_url[1],blog_no)
		}
	}
}	


function get_bulkpostts(feed_url,blog_no)
{
	if(document.getElementById("rss_import_"+blog_no))
	{
		document.getElementById("rss_import_"+blog_no).innerHTML="<img src='/wp-content/plugins/profiler/loader2.gif' alt='loading posts'/>";		
	}		
	if (chat_XMLHttp_getlist_rss.readyState % 4) return;
		
    	chat_XMLHttp_getlist_rss.open('get', "/wp-content/plugins/bulkposts.php?feed_url="+feed_url, true);
	    chat_XMLHttp_getlist_rss.send(null);
	
		chat_XMLHttp_getlist_rss.onreadystatechange =function(){ getres_bulkpostts(blog_no); }
}
function getres_bulkpostts(blog_no)
{
	if(chat_XMLHttp_getlist_rss.readyState == 4 && chat_XMLHttp_getlist_rss.status == 200)
    {
		var no_posts =chat_XMLHttp_getlist_rss.responseText;
		//alert(no_posts);
		/*var rss_post_url=rss_feed.split("***");*/
		if(no_posts)
				{						
			document.getElementById("rss_import_"+blog_no).innerHTML=no_posts;
		}
	}
}	
</script>



  <div class="wrap">
    <h2>Blog List<span style="float:right;font-size:10pt;" id="rss_result">..</span></h2> 
      
    <table class="widefat"> 
      <thead>
        <tr>
          <th scope="col" style="text-align: center">ID</th>
          <th scope="col"><?php _e('Title', 'wpomatic') ?></th>
          <th style="text-align: center" scope="col"><?php _e('Member', 'wpomatic') ?></th>
      	  <th style="text-align: center" scope="col"><?php _e('Total posts', 'wpomatic') ?></th>
      	  <th scope="col"><?php _e('Last post', 'wpomatic') ?></th>
      	  <th scope="col" colspan="4" style="text-align: center"><?php _e('Actions', 'wpomatic') ?></th>
        </tr>
      </thead>
      
      <tbody id="the-list">            
        <?php if(!$campaigns): ?>
          <tr> 
            <td colspan="5"><?php _e('No Blogs to display', 'wpomatic') ?></td> 
          </tr>  
        <?php else: ?>     
          <?php $class = '';   

	   $categories=  get_categories('orderby=name&order=ASC&hide_empty=0'); 

          foreach($categories as $cat) {
		          $postslist = get_posts('category='.$cat->cat_ID.'&numberposts=1&order=DESC');				    
					 foreach ($postslist as $post)  :
						 setup_postdata($post);

						$a = the_title();
					 endforeach;
            $class = ('alternate' == $class) ? '' : 'alternate'; ?>             
          <tr id='campaign-<?php echo $cat->cat_ID ?>' class='<?php echo $class ?> <?php if($_REQUEST['id'] == $cat->cat_ID) echo 'highlight'; ?>'> 
            <th scope="row" style="text-align: center"><?php echo $cat->cat_ID ?></th> 
            <td><?php echo attribute_escape($cat->cat_name) ?></td>          
            <td style="text-align: center"><?php echo _e($cat->active ? 'Yes' : 'No', 'wpomatic') ?></td>
            <td style="text-align: center"><?php echo $cat->count ?></td>        
            <td style="text-align: center"><?php echo $a;?></td>
            <td><a href="<?php echo $this->adminurl ?>&amp;s=edit&amp;id=<?php echo $cat->cat_ID ?>" class='edit'>Edit</a></td> 
			
			<td><?php echo "<a href='" . wp_nonce_url($this->adminurl . '&amp;s=cat_imagerefresh&amp;id=' . $cat->cat_ID, 'refresh-cat_' . $cat->cat_ID) . "' class='refresh' onclick=\"return confirm('" . __("You are about to refresh the blog image for this category '%s'. This action doesn't affect any of the  wp posts.\n'OK' to delete, 'Cancel' to stop.") ."')\">" . __('Img', 'wpomatic') . "</a>"; ?></td>   
            
			<td id="rss_import_<?php echo $cat->cat_ID ?>"><a onclick="Javascript:get_rssfeed('<? echo $cat->cat_ID;?>')">Import</a>	</td>
            
			<td><?php echo "<a href='" . wp_nonce_url($this->adminurl . '&amp;s=cat_reset&amp;id=' . $cat->cat_ID, 'reset-cat_' . $cat->cat_ID) . "' class='delete' onclick=\"return confirm('". __('Are you sure you want to reset this campaign? Resetting does not affect already created wp posts.', 'wpomatic') ."')\">" . __('Reset', 'wpomatic') . "</a>"; ?></td>
            
			<td><?php echo "<a href='" . wp_nonce_url($this->adminurl . '&amp;s=cat_delete&amp;id=' . $cat->cat_ID, 'delete-cat_' . $cat->cat_ID) . "' class='delete' onclick=\"return confirm('" . __("You are about to delete the campaign '%s'. This action doesn't remove campaign generated wp posts.\n'OK' to delete, 'Cancel' to stop.") ."')\">" . __('Delete', 'wpomatic') . "</a>"; ?></td>            
          </tr>              
          <?php } ?>                    
        <?php endif; ?>
      </tbody>
    </table>
    
    <div id="ajax-response"></div>
    
  </div>

<?php $this->adminFooter() ?>