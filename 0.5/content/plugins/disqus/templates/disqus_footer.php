<?php
/** PLUGIN TEMPLATE **
 * Plugin name: Disqus
 * Template name: plugins/disqus/disqus_footer.php
 * Template author: Nick Ramsay
 * License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 */

global $plugin;
$shortname = $plugin->plugin_settings('disqus', 'disqus_shortname');
?>

<!--
<script type="text/javascript"> //<![CDATA[ (function() { var links = document.getElementsByTagName('a'); var query = ''; for(var i = 0; i < links.length; i++) { if(links[i].href.indexOf('#disqus_thread') >= 0) { query += 'url' + i + '=' + encodeURIComponent(links[i].href) + '&'; } } document.write('<script type="text/javascript" src="http://disqus.com/forums/<?php echo $shortname; ?>/get_num_replies.js' + query + '"></' + 'script>'); })(); //]]> </script>
-->

    <script type="text/javascript">
    // <![CDATA[
        (function() {
            var links = document.getElementsByTagName('a');
            var query = '?';
            for(var i = 0; i < links.length; i++) {
                if(links[i].href.indexOf('#disqus_thread') >= 0) {
                    query += 'url' + i + '=' + encodeURIComponent(links[i].href) + '&';
                }
            }
            document.write('<script type="text/javascript" src="http://disqus.com/forums/<?php echo $shortname; ?>/get_num_replies.js' + query + '"></' + 'script>');
        })();
    //]]>
    </script>
