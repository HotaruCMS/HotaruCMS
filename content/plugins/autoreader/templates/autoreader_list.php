<?php
    require_once(PLUGINS . 'autoreader/autoreader.php');
    $arSettings = new Autoreader();
    $campaigns = $arSettings->getCampaigns($h);

 ?>

  <div class="wrap">
    <h2>Campaigns</h2>     
  
    <table class="widefat"> 
      <thead>
        <tr>
          <th scope="col" style="text-align: center">ID</th>
          <th scope="col">Title</th>
          <th style="text-align: center" scope="col">Active</th>
      	  <th style="text-align: center" scope="col">Total Posts</th>
      	  <th scope="col">Last Active</th>
      	  <th scope="col" colspan="4" style="text-align: center">Actions</th>
        </tr>
      </thead>
      
      <tbody id="the-list">            
        <?php if(!$campaigns): ?>
          <tr> 
            <td colspan="5">No campaigns to display</td> 
          </tr>  
        <?php else: ?>     
          <?php $class = ''; ?>  
          
          <?php foreach($campaigns as $campaign): ?>
          <?php $class = ('alternate' == $class) ? '' : 'alternate'; ?>             
          <tr id='campaign-<?php echo $campaign->id ?>' class='<?php echo $class ?> <?php if($_REQUEST['id'] == $campaign->id) echo 'highlight'; ?>'> 
            <th scope="row" style="text-align: center"><?php echo $campaign->id ?></th> 
            <td><?php echo attribute_escape($campaign->title) ?></td>          
            <td style="text-align: center"><?php echo _e($campaign->active ? 'Yes' : 'No', 'wpomatic') ?></td>
            <td style="text-align: center"><?php echo $campaign->count ?></td>        
            <td><?php echo $campaign->lastactive?></td>
            <td><a href="<?php echo $this->adminurl ?>&amp;s=edit&amp;id=<?php echo $campaign->id ?>" class='edit'>Edit</a></td> 
            <td><?php echo "<a href='#' class='edit' onclick=\"return confirm('". __('Are you sure you want to process all feeds from this campaign?', 'wpomatic') ."')\">" . __('Fetch', 'wpomatic') . "</a>"; ?></td>
            <td><?php echo "<a href='#' class='delete' onclick=\"return confirm('". __('Are you sure you want to reset this campaign? Resetting does not affect already created wp posts.', 'wpomatic') ."')\">" . __('Reset', 'wpomatic') . "</a>"; ?></td>
            <td><?php echo "<a href='#' class='delete' onclick=\"return confirm('" . __("You are about to delete the campaign '%s'. This action doesn't remove campaign generated wp posts.\n'OK' to delete, 'Cancel' to stop.") ."')\">" . __('Delete', 'wpomatic') . "</a>"; ?></td>
          </tr>              
          <?php endforeach; ?>                    
        <?php endif; ?>
      </tbody>
    </table>
           
  </div>
  
