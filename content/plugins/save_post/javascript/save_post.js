function save_post_remove_widget_item(id) {
	var sendurl = BASEURL +"content/plugins/save_post/save_post_functions.php";
	$this = $('li#save_post_widget_'+id);
	var remove_post_id = "remove_id="+id;
	
	$.ajax({
		type: 'post',
		url: sendurl,
		data: remove_post_id,
		error: 	function(XMLHttpRequest, textStatus, errorThrown) {
			 alert('err '+textStatus+", "+errorThrown);
		},
		success: function(json) {
			if ( $("li#save_post_widget_"+id).length !== 0 ) {
				$("li#save_post_widget_"+id).slideUp("fast", function() {
					$(this).remove();
					if ( $('ul#save_post_widget').children().length == 0 ) {
						$("ul#save_post_widget").prepend('<li id="save_post_widget_empty">'+save_post_label_empty+'</li>');
						$('li#save_post_widget_empty').hide().slideDown("fast");
					}
				});
			}
			if ( $("a#post_"+id).length !== 0 ) {
				$("a#post_"+id).removeClass('remove_post_item').addClass('save_post_item').text(save_post_label_save);
			};
		}
	});
}
	
function save_posts(id) {
	var sendurl = BASEURL +"content/plugins/save_post/save_post_functions.php";
	$this = $('a#post_'+id);
	
	var _save_post_save = true;
	if ( $($this).hasClass('save_post_item') ) {
		var save_post_id = "save_id="+id;
		var save_post_remove_class = 'save_post_item';
		var save_post_add_class = 'remove_post_item';
		var save_post_label = save_post_label_remove;
		
	} else if ( $($this).hasClass('remove_post_item') ) {
		var _save_post_save = false;
		var save_post_id = "remove_id="+id;
		var save_post_remove_class = 'remove_post_item';
		var save_post_add_class = 'save_post_item';
		var save_post_label = save_post_label_save;
	}
	
	$.ajax({
		type: 'post',
		url: sendurl,
		data: save_post_id,
		error: 	function(XMLHttpRequest, textStatus, errorThrown) {
			 alert('err '+textStatus+", "+errorThrown);
		},
		success: function(json) {
			$($this).removeClass(save_post_remove_class).addClass(save_post_add_class).text(save_post_label);
			if ( _save_post_save === false && $("li#save_post_widget_"+id).length !== 0 ) {
				$("li#save_post_widget_"+id).slideUp("fast", function() {
					$(this).remove();
					if ( $('ul#save_post_widget').children().length == 0 ) {
						$("ul#save_post_widget").prepend('<li id="save_post_widget_empty">'+save_post_label_empty+'</li>');
						$('li#save_post_widget_empty').hide().slideDown("fast");
					}
				});
			} else if ( _save_post_save === true ) {
				if ( $("li#save_post_widget_empty").length !== 0 ) {
					$("li#save_post_widget_empty").slideUp("fast", function() {
						$(this).remove();
					});
				}
				var saved_obj = jQuery.parseJSON(json);
				$("ul#save_post_widget").prepend('<li id="save_post_widget_'+saved_obj.id+'"><span class="save_post_widget_item"></span><a href="'+saved_obj.url+'" title="'+saved_obj.title+'" alt="'+saved_obj.title+'">'+saved_obj.title+'</a></li>');
				$('li#save_post_widget_'+saved_obj.id).hide().slideDown("fast");
			}
		}
	});
	
}