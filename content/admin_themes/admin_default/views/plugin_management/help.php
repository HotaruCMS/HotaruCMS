<div>
    <h4><?php echo $h->lang("admin_theme_plugins_guide"); ?></h4>
    &raquo; <?php echo $h->lang("admin_theme_plugins_guide1"); ?><br />
    &raquo; <?php echo $h->lang("admin_theme_plugins_guide2"); ?><br />
    &raquo; <?php echo $h->lang("admin_theme_plugins_guide3"); ?><br />
    &raquo; <?php echo $h->lang("admin_theme_plugins_guide4"); ?><br />
</div>
<br/>

	<h4>Install order for a social bookmarking site</h4>

	<p>The main plugins should be installed in the following order:</p>

    <p>1. First, install the plugins that have no dependencies:</p>

    <ul>
        <li>Bookmarking</li>
        <li>User Signin</li>
        <li>Widgets</li>
    </ul>
    
    <p>2. Then install key plugins that depend on those:</p>

    <ul>
        <li>Users</li>
        <li>Submit</li>
        <li>Comments</li>
        <li>Category Manager</li>
    </ul>
    
    <p>3. Now those are done, the rest are easy. Here are the other essential plugins to install:</p>

    <ul>

        <li>Categories</li>
        <li>Search</li>
        <li>Tags</li>
        <li>Vote</li>
        <li>Post Manager</li>
        <li>User Manager</li>
        <li>Comment Manager</li>
    </ul>
    
    <p>4. Now you're free to pick and choose other plugins to enhance your site from those that remain.</p>
    
    <br/>

    When you're done, enable the DB_CACHE from the Admin -> Settings page
</p>
