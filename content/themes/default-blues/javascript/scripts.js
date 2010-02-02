function showVideo(id) {
	document.write("<object width=\"292\" height=\"225\"><param name=\"movie\" value=\"http://www.youtube.com/v/"+id+">&amp;hl=en&amp;fs=1\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed src=\"http://www.youtube.com/v/"+id+"&amp;hl=en&amp;fs=1\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"292\" height=\"225\"></embed></object>");
}

var autoSlide = true;
var autoSlideSecs = 10;

var proj = 0;
var interval = null;

function textSlideIn(proj) {
	$('ul.text').children('li:eq('+proj+')').animate({ right: "-1px" }, 700);
}
function textSlideOut(proj) {
	$('ul.text').children('li:eq('+proj+')').animate({ right: "-280px" }, 700);
}
function photoFadeIn(proj) {
	$('ul.photo').children('li:eq('+proj+')').fadeIn(700);
}
function photoFadeOut(proj) {
	$('ul.photo').children('li:eq('+proj+')').fadeOut(700);
}
function photoPutBehind(proj) {
	$('ul.photo').children('li:eq('+proj+')').css('z-index', 0);
}
function nextSlide() {
	$('ul.photo').children('li:eq('+proj+')').css("z-index", 2);
	setTimeout('photoFadeOut('+proj+')', 1000);
	setTimeout('photoPutBehind('+proj+')', 1500);
	textSlideOut(proj);
	proj++;
	var i = $('ul.text li').size() - 1;
	if (proj>i) proj = 0;
	setTimeout('photoFadeIn('+proj+')', 1000);		
	setTimeout('textSlideIn('+proj+')', 1500);
}

$(function() {
	if (autoSlide) interval = setInterval('nextSlide()', autoSlideSecs*1000);
	jQuery('.next').click(function() {
		clearInterval(interval);
		nextSlide();
		if (autoSlide) interval = setInterval('nextSlide()', autoSlideSecs*1000);
		return false;
	});
});

