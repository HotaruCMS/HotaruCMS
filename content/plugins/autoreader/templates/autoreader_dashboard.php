<?php
    require_once(PLUGINS . 'autoreader/autoreader.php');
    $arSettings = new Autoreader($h);
    $campaigns = $arSettings->getCampaigns($h);

    $logs = $arSettings->getLogs($h);


    //$logging = get_option('wpo_log');
    $logs = $arSettings->getLogs($h, 'limit=7');
    $nextcampaigns = $arSettings->getCampaigns($h,'fields=id,title,lastactive,frequency&limit=5' .
                                          '&where=active=1&orderby=UNIX_TIMESTAMP(lastactive)%2Bfrequency&ordertype=ASC');
    $lastcampaigns = $arSettings->getCampaigns($h,'fields=id,title,lastactive,frequency&limit=5&where=UNIX_TIMESTAMP(lastactive)>0&orderby=lastactive');
    $campaigns = $arSettings->getCampaigns($h,'fields=id,title,count&limit=5&orderby=count');


 ?>


        
  <div class="wrap">
    <h2>Dashboard</h2>

    <div id="sidebar">
      <div id="sidebar_logging">
        <a href="<?php echo $this->helpurl ?>logging" class="help_link">Help</a>
        <h3>&rsaquo; 'Latest log entries <a href="<?php echo $this->adminurl ?>&s=logs">(view all)</a></h3>
        <?php if(!$logs): ?>
        <p class="none">No actions to display</p>
        <?php else: ?>
        <ul id="logs">
          <?php foreach($logs as $log): ?>
          <li><?php echo WPOTools::timezoneMysql('F j, g:i a', $log->created_on) . ' &mdash; <strong>' . $log->message ?></strong></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <p id="log_status">Logging Status (<a title="We recommend keeping logging on only when experimenting with new feeds." href="<?php echo $this->adminurl ?>&amp;s=options">change</a>).</p>
      </div>
    </div>

    <div id="main">

      <p>Welcome to the Dashboard! This is the place where you can quickly watch activity, and keep track of your top campaigns and feeds.</p>

      <h3>Next campaigns to process</h3>
      <?php if(count($nextcampaigns) == 0): ?>
      <p class="none">No campaigns to display
      <?php else: ?>
        <ol class="campaignlist">
          <?php foreach($nextcampaigns as $campaign):
            $cl = $arSettings->getCampaignRemaining($h, $campaign);
            $cl = WPOTools::calcTime($cl, 0, 'd', false);
print_r($campaign);
            $timestr = '';
            if($cl['days']) $timestr .= $cl['days'] . 'd ';
            if($cl['hours']) $timestr .= $cl['hours'] . 'h ';
            if($cl['minutes']) $timestr .= $cl['minutes'] . 'm ';
            if($cl['seconds']) $timestr .= $cl['seconds'] . 's';
          ?>
          <li>
            <span class="details"><?php echo ($timestr) ? $timestr : 'Next!' ?></span>
            <a href="<?php echo $this->adminurl ?>&amp;s=list&amp;id=<?php echo $campaign->id ?>"><?php echo $campaign->title ?></a></li>
          <?php endforeach; ?>
        </ol>
      <?php endif; ?>

      <h3>Latest processed campaigns</h3>
      <?php if(count($lastcampaigns) == 0): ?>
      <p class="none">No campaigns to display
      <?php else: ?>
        <ol class="campaignlist">
          <?php foreach($lastcampaigns as $campaign): ?>
          <li>
            <span class="details"><?php echo WPOTools::timezoneMysql('F j, g:i a', $campaign->lastactive) ?></span>
            <a href="<?php echo $this->adminurl ?>&amp;s=list&amp;id=<?php echo $campaign->id ?>"><?php echo $campaign->title ?></a></li>
          <?php endforeach; ?>
        </ol>
      <?php endif; ?>

      <h3>Your top campaigns</h3>
      <?php if(count($campaigns) == 0): ?>
      <p class="none">No campaigns to display
      <?php else: ?>
      <ol class="campaignlist">
        <?php foreach($campaigns as $campaign): ?>
        <li>
          <span class="details"><?php echo $campaign->count ?></span>
          <a href="<?php echo $this->adminurl ?>&amp;s=list&amp;id=<?php echo $campaign->id ?>"><?php echo $campaign->title ?></a></li>
        <?php endforeach; ?>
      </ol>
      <?php endif; ?>

    </div>
  </div>

