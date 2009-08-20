sfHover = function() {
    var sfEls = document.getElementById("category_bar").getElementsByTagName("li");
    for (var i=0; i<sfEls.length; i++) {
        sfEls[i].onmouseover=function() {
            this.className+=" hover";
        }
        sfEls[i].onmouseout=function() {
            this.className=this.className.replace(new RegExp(" hover\\b"), "");
        }
    }
}
if (window.attachEvent) window.attachEvent("onload", sfHover);
