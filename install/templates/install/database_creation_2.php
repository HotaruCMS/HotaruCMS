<!-- step title -->
<legend><?php echo $lang['install_step2']; ?></legend>

    <!-- Complete Step Progress Bar -->
    <div class="progress progress-info">
            <div class="bar" style="width: 50%;"></div>
    </div>
    
    
    <?php

    if ($db->get_col("SHOW TABLES",0)) {
        echo  $lang['install_step2_checking_tables']; 
        foreach ( $db->get_col("SHOW TABLES",0) as $table_name )
        {
            print $table_name . ', ';
                drop_table($table_name); // table name
        }
        echo '<br /><br />' . $lang['install_step2_deleting_table'] . "'...<br /><br />\n";
    } else {
        echo $lang['install_step2_no_tables'] . "<br/><br />\n";
    }


    $create_tables_problem = false;
    //create tables
    foreach ($tables as $table_name) {
        $error = '';
        create_table($table_name);
        $error = mysql_error();
        if ($error) {
            echo $error . ' ';
            $create_tables_problem = true;
        }
    }

    // Step content
    if (!$create_tables_problem) {
        echo "<div class='text-success'>" . $lang['install_step2_success'] . "</div>\n";
    } else {
        echo "<div class='text-error'>" . $lang['install_step2_fail'] . "</div>\n";
    }

    $show_next = true;

        
        ?>
	<div clasS="form-actions">
	<a class='btn' href='index.php?step=1'><?php echo $lang['install_back']; ?></a>
	<?php if ($show_next) { ?>		
		<a class="btn pull-right" href='index.php?step=3'><?php echo $lang['install_next']; ?></a>
	<?php } else { ?>		
		<a class='btn disabled pull-right'><?php echo $lang['install_next']; ?></a>
	<?php } ?>
        </div>