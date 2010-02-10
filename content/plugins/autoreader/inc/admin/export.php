<?php require_once(WPOTPL . '/helper/form.helper.php' ) ?>

<?php $this->adminHeader() ?>

  <?php if(isset($error)): ?>
  <div id="export-warning" class="error">
    <p><?php echo $error ?></p>
  </div>
  <?php endif; ?>

  <div class="wrap">
    <h2><?php _e('Export', 'wpomatic') ?></h2>
    <p><?php _e('<strong>Note:</strong> This tool only exports lists of feeds, not custom options.', 'wpomatic') ?></p>
    
    <form action="" method="post" accept-charset="utf-8">
      <?php if($campaigns): ?>
      <ul id="export_campaigns">
        <?php foreach($campaigns as $campaign): ?>
        <li>
          <?php echo checkbox_tag('export_campaign[]', $campaign->id, isset($_REQUEST['export_campaign']) && _data_value($_REQUEST['export_campaign'], $campaign->id), 'id=export_campaign_' . $campaign->id) ?>
          <?php echo label_for('export_campaign_' . $campaign->id, $campaign->title . ' <strong>' . $campaign->id . '</strong>') ?>
        </li>
        <?php endforeach ?>
      </ul>
    
      <p class="submit"><?php echo submit_tag(__('Export', 'wpomatic')) ?></p>    
      <?php else: ?>
      <p><?php _e('No campaigns to show', 'wpomatic') ?>
      <?php endif ?>
    </form>
  </div>

<?php $this->adminFooter() ?>