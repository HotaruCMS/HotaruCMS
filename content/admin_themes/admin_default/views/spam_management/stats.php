<?php 
/**
 * Theme name: admin_default
 * Template name: stats.php
 * Template author: shibuya246
 *
 * PHP version 5
 *
 * LICENSE: Hotaru CMS is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version. 
 *
 * Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE. 
 *
 * You should have received a copy of the GNU General Public License along 
 * with Hotaru CMS. If not, see http://www.gnu.org/licenses/.
 * 
 * @category  Content Management System
 * @package   HotaruCMS
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
?>

<?php 
$spamAction = $h->spamLogGetAll();
//print_r($spamAction);
        
//        // gets query and total count for pagination
//        $users_query = $h->getUsers(0, 'query');
//        $users_count = $h->getUsers(0, 'count');
//        
//        $limit = 30;
//        // pagination 
//        $h->vars['pagedResults'] = $h->pagination($users_query, $users_count, $limit, 'users');
//        
//        $h->template('users_browse');
//        
//        if ($h->vars['pagedResults']) { echo $h->pageBar($h->vars['pagedResults']); }
//        
        
        
?>

<table class="table table-striped">
      <caption>Anti-spam action</caption>
      <thead>
        <tr>
          <th>Email</th>
          <th>Type</th>
          <th>Plugin</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
          <?php 
            if ($spamAction) {
                foreach ($spamAction as $item) { ?>
                    <tr>
                      <td><?php echo $item->spamlog_email; ?></td>
                      <td><?php echo $item->spamlog_type; ?></td>
                      <td><?php echo $item->spamlog_pluginfolder; ?></td>
                      <td><?php echo $item->spamlog_updatedts; ?></td>
                    </tr>
                <?php }
            } ?>
      </tbody>
    </table>
<?php 

