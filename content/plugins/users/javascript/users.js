jQuery('document').ready(function($) {

    // Javascript to enable link to tab
    var hash = document.location.hash;
    var prefix = "tab_";
    if (hash) {
        $('.nav-tabs a[href='+hash.replace(prefix,"")+']').tab('show');
        $('form').prop('action', window.location.hash);
    } 

    // Change hash for page-reload
    $('.nav-tabs a').on('shown', function (e) {
        window.location.hash = e.target.hash.replace("#", "#" + prefix);
        
        $('form').prop('action', function(i, val) {
            $('form').prop('action', window.location.hash);
        });
    });
    
});