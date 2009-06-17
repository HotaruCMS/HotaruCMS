$(document).ready(function(){
	$(function(){
	
	  // Prepare the Easy Widgets
	
	  $.fn.EasyWidgets({
	  
	  callbacks : {
	      
		onChangePositions : function(str){
			widget_moved("http://localhost/HotaruCMS/",str);
		}
	      
	    }
	
	  });
	
	});
});