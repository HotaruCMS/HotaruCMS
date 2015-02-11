	
		<!-- step title -->
		<legend><?php echo $lang['install_step2']; ?></legend>

		<!-- Complete Step Progress Bar -->
		<div class='progress'>
			<div class='progress-bar progress-bar-info' role='progressbar' aria-valuenow='50' aria-valuemin='0' aria-valuemax='100' style='width: 50%'>
				<span class='sr-only'>50% Complete</span>
			</div>
		</div>
		
		
		<?php

		if ($db->get_col('SHOW TABLES',0)) {
			echo  '<p class="text-muted">' . $lang['install_step2_checking_tables'] . '<br/>';
			foreach ( $db->get_col("SHOW TABLES",0) as $table_name )
			{
				print '<span style="display:inline-block;" class="label label-default">' . $table_name . '</span>&nbsp;';
				drop_table($table_name); // table name
			}
			echo '</p><div class="alert alert-warning" role="alert"><i class="fa fa-trash"></i> ' . $lang['install_step2_deleting_table'] . "...</div>";
		} else {
			echo '<p class="text-success">' . $lang['install_step2_no_tables'] . '</p><br/><br />';
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
			echo "<div class='alert alert-success' role='alert'>" . $lang['install_step2_success'] . "</div>\n";
		} else {
			echo "<div class='alert alert-danger' role='alert'>" . $lang['install_step2_fail'] . "</div>\n";
		}

		$show_next = true;

		?>
		<div class='form-actions'>
			<!-- Previous/Next buttons -->
			<a href='index.php?action=install&step=1' class='btn btn-default' role='button'><i class='fa fa-arrow-left'></i> <?php echo $lang['install_back']; ?></a>
			<?php if ($show_next) { // and if db was connected ok ?>
				<a href='index.php?action=install&step=3' class='btn btn-default pull-right' role='button'><?php echo $lang['install_next']; ?> <i class='fa fa-arrow-right'></i></a>
			<?php } else { // link disbaled ?>		    
				<a class='btn btn-default disabled pull-right' href='#' role='button'><?php echo $lang['install_next']; ?> <i class='fa fa-arrow-right'></i></a>
			<?php } ?>
		</div>