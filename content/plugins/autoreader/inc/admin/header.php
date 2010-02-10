<link rel="stylesheet" href="<?php echo $this->tplpath ?>/css/admin.css" type="text/css" media="all" title="" />
<?php if($this->newadmin): ?>
<link rel="stylesheet" href="<?php echo $this->tplpath ?>/css/admin-new.css" type="text/css" media="all" title="" />  
<?php endif ?>

<div id="wpomain">

  <div id="wpomenu" class="wrap">   
    <div> 
      <ul>
        <li <?php echo $current['home'] ?>><a id="menu_home" href="<?php echo $this->adminurl ?>&amp;s=home"><?php _e('Dashboard', 'wpomatic') ?></a></li>
		<li <?php echo $current['cat_list'] ?>><a id="menu_list" href="<?php echo $this->adminurl ?>&amp;s=cat_list"><?php _e('CatList', 'wpomatic') ?></a></li>
        <li <?php echo $current['list'] ?>><a id="menu_list" href="<?php echo $this->adminurl ?>&amp;s=list"><?php _e('Campaigns', 'wpomatic') ?></a></li>
        <li <?php echo $current['add'] ?>><a id="menu_add" href="<?php echo $this->adminurl ?>&amp;s=add"><?php _e('Add campaign', 'wpomatic') ?></a></li>
        <li <?php echo $current['options'] ?>><a id="menu_options" href="<?php echo $this->adminurl ?>&amp;s=options"><?php _e('Options', 'wpomatic') ?></a></li>
        <li <?php echo $current['import'] ?>><a id="menu_backup" href="<?php echo $this->adminurl ?>&amp;s=import"><?php _e('Import', 'wpomatic') ?></a></li>
        <li <?php echo $current['export'] ?>><a id="menu_backup" href="<?php echo $this->adminurl ?>&amp;s=export"><?php _e('Export', 'wpomatic') ?></a></li>
      </ul>
    </div>     
  </div>  

  <div id="wpocontent">