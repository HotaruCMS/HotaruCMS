<?php
$this->setLayoutVar('pageTitle', 'LightVC Skeleton App');
?>

<h1>Skeleton App</h1>

<p>You have a skeleton app setup with reasonable defaults. &nbsp;You can customize these defaults (such as where things are located) in the <span class="path">config/application.php</span> file.</p>

<p>This skeleton app uses mod_rewrite (rules specified in the <span class="path">webroot/.htaccess</span> file) and sets up the following routes (specified in <span class="path">config/routes.php</span>) out of the box:</p>

<ul>
	<li>"/" actives the page controller's view action with "home" for the page_name parameter.</li>
	<li>"/page/page_name" actives the page controller's view action with "page_name" for the page_name parameter.</li>
	<li>"/controller/action/params" actives the "controller" controller's "action" action with the remaining URL used to populate the action method's arguments.</li>
</ul>

<p>If you need to, consult LightVC documentation on <a href="http://lightvc.org/docs/user_guide/configuration/web_server/">configuring your web server</a>, such as how to setup lighttpd rewrite rules.</p>

<h2>Your action items:</h2>

<ul>
	<li>Customize and add layouts to <span class="path">views/layouts/</span>.</li>
	<li>Customize and add styles to <span class="path">webroot/css/</span>.</li>
	<li>Customize and add static pages in <span class="path">views/page/</span>.</li>
	<li>
		Create new controllers in <span class="path">controllers/</span>.
		<ul class="note">
			<li>There is already an AppController (<span class="path">classes/AppController.class.php</span>) which is specifying the default layout and CSS to use across all controllers.</li>
		</ul>
	</li>
	<li>Add to your application level config in <span class="path">config/application.php</span>.</li>
	<li>Add to your route config in <span class="path">config/routes.php</span>.</li>
	<li>Add your own model/ORM (e.g. <a href="http://coughphp.com">CoughPHP</a>, <a href="http://propel.phpdb.org/trac/">Propel</a>, <a href="http://en.wikipedia.org/wiki/List_of_object-relational_mapping_software#PHP">etc.</a>).</li>
</ul>

<p>Have Fun!<br /><a href="http://lightvc.org/">LightVC Website</a></p>
