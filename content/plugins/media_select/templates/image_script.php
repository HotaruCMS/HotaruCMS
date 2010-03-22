<?php
/**
 * Create the crop scrip for the Post Images PLugin
 */
$cropboxwidth = 250;
?>
<script type="text/javascript">

var imgWidth = 200; // default w
var imgHeight = 100; // default h
var imgSize;
var JcropObject;
function changeImage(){
	imgLink = $('#post_img').val();
	$('#cropbox_wrapper').addClass('loading');
	var img = new Image();
	$(img).load(function() {    // when image has loaded...
		$(this).css('display', 'none'); // hide image by default
		$('#cropbox_wrapper, #preview_wrapper').removeClass('loading').html('<img src="'+imgLink+'" title="" alt="" />');
		orig_size = originalImgSize('<img src="'+imgLink+'" alt="" title="" />');
		$('#cropbox_wrapper img').attr('id','cropbox');
		$('#preview_wrapper img').attr('id','preview').show();
		imgHeight = parseInt($('#cropbox').height());
		imgWidth = parseInt($('#cropbox').width());
		thumbAspectRatio = 200/100;
		imgAspectRatio = imgWidth/imgHeight;
		boxHeight = imgHeight;
		boxWidth = imgWidth;
		offsetY = 0;
		offsetX = 0;
		if(thumbAspectRatio >= imgAspectRatio){
			boxWidth = imgWidth;
			boxHeight = imgWidth *1/thumbAspectRatio;
			offsetY = (imgHeight - boxHeight)/2;
			offsetX = 0;
		}
		else {
			boxHeight = imgHeight;
			boxWidth = imgHeight * thumbAspectRatio;
			offsetY = 0;
			offsetX = (imgWidth - boxWidth)/2;
		}
		boxX1 = offsetX;
		boxY1 = offsetY;
		boxX2 = offsetX+boxWidth;
		boxY2 = offsetY+boxHeight;

		minSizeX = 200;
		minSizeY = 200;
		if(orig_size[0] > 200){
			minSizeX = 200;
		}
		if(orig_size[1] > 200){
			minSizeY = 200;
		}
		$('#cropbox').Jcrop({
			onChange: showPreview,
			onSelect: showPreview,
			boxWidth: imgWidth,
			boxHeight: imgHeight,
			aspectRatio: thumbAspectRatio,
			minSize: [minSizeX,minSizeY], // default w, h
			setSelect: [boxX1,boxY1,boxX2,boxY2]
			
		});
	}).attr('src', imgLink);
}
function showPreview(coords){
	if (parseInt(coords.w) > 0){
		var rx = 200 / coords.w;
		var ry = 200 / coords.h;
		jQuery('#preview').css({
			width: Math.round(rx*imgWidth) + 'px',
			height: Math.round(ry*imgHeight) + 'px',
			marginLeft: '-' + Math.round(rx * coords.x) + 'px',
			marginTop: '-' + Math.round(ry * coords.y) + 'px'
		});
	}
	setImageCoords(coords);
}
function setImageCoords(c){
	$('#post_img_coords').val(c.x+' , '+c.y+' , '+c.w+' , '+c.h+' , '+imgWidth+' , '+imgHeight);
}
function getRemoteImages(btn,url){
	btn.attr("disabled", "disabled").text("loading...");
	$('#thumbs_from_source_space').html('<i>Loading images</i>');
	$.post('http://japanreporter.com/index.php',{ type:'postImages', url:url }, function(data) {
		gridHtml = '';
		$(data).find('img').each(function(){
			src = $(this).attr('src');
			alt = $(this).attr('alt');
			title = $(this).attr('title');
			orig_size = originalImgSize('<img src="'+src+'" alt="'+alt+'" title="'+title+'" />');
			height = orig_size[1];
			width = orig_size[0];
			if(width >= 50 && height >= 50){
				gridHtml += '<a href="#" onclick="selectPostImage($(this)); return false;"><img src="'+src+'" alt="'+alt+'" title="'+title+'" />';
			}
		});
		$('#thumbs_from_source_space').html(gridHtml);
		btn.text("again?!?").removeAttr("disabled");
	});
}
function selectPostImage(imglink){
	src = imglink.children('img').attr('src');
	$('#post_img').val(src);
	changeImage();
}
function originalImgSize(html){
	$('#get_image_original_size').html(html);
	height = $('#get_image_original_size img:first').height();
	width = $('#get_image_original_size img:first').width();
	$('#get_image_original_size').html('');
	return [width,height];
}
</script>
<style type="text/css">
#thumbs_from_source_space {
}
#thumbs_from_source_space a {
	padding:1px;
    float:left;
    border:none;
	display:block;
	height:50px;
	width:50px;
	border:#CCC solid 1px;
	margin:1px;
	text-align:center;
}
#thumbs_from_source_space a:hover {
	padding:1px;
	border:#666 solid 1px;
}
#thumbs_from_source_space img {
	max-height:50px;
	height:auto !important;
	height:50px;
	max-width:50px;
	width:auto !important;
	width:50px;
}
#cropbox_wrapper {
	width:<?php echo $cropboxwidth; ?>px;
}
#cropbox_wrapper img {
	max-width:<?php echo $cropboxwidth; ?>px;
	width: expression(this.width > <?php echo $cropboxwidth; ?> ? <?php echo $cropboxwidth; ?>: true);
}
#preview_wrapper {
	width:<?php echo $h->vars['post_images_settings']['w']; ?>px;
	height:<?php echo $h->vars['post_images_settings']['h']; ?>px;
	overflow:hidden;
}
#get_image_original_size { clear:both; }
</style>