<?php

if(!$h->numActivePlugins()) { ?>

<div style="padding:15px 25px;" class="hero-unit">
        <h2>Welcome to Hotaru CMS</h2>
        <p>It looks like you are just getting started with your new Hotaru CMS website.</p>
        <p>We are looking forward to seeing what you create..</p>
        <p>Time to install some plugins and open your site.</p>
        <p><br/><a href="<?php echo $h->url(array(), 'admin'); ?>" class="btn btn-primary">Admin Login</a></p>
</div>

<?php }
else if ($h->isActive('submit') && (!isset($postCount) || $postCount < 1)) { ?>
    <div style="padding:15px 25px;" class="hero-unit">
            <h2>Welcome to Hotaru CMS</h2>
            <p>It looks like you are just getting started with your new Hotaru CMS website. Why not submit your first post and publish it to the homepage straight away.</p>
            <p><a href="<?php echo $h->url(array(), 'submit'); ?>" class="btn btn-primary">Submit Your First Post</a></p>
    </div>
<?php } 

else { ?>

<div style="padding:15px 25px;" class="hero-unit">
        <h2>Welcome to Hotaru CMS</h2>
        <p>We are looking forward to seeing what you create with your Hotaru CMS website.</p>
            
</div>

<?php } ?>
