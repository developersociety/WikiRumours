<?php

	function get_last_completed_migration_id(){
		global $dbConnection;

		$query = "
		SELECT
			version
		FROM
			wr_db_changelog
		WHERE
			status='complete'
		ORDER BY
			timestamp DESC
		LIMIT
			1
		;";

		$result = $dbConnection->query($query) or die (
			'Unable to execute: ' . $query . ' : ' . $dbConnection->error
		);

		$actual_result = null;

		foreach($result as $row) {
			$actual_result = $row['version'];
		}

		$result->free();

		return $actual_result;


	}


	function display_migration_log() {
		global $dbConnection;

		echo '<h1>Migration Log</h1>';

		$columns = ['timestamp', 'version', 'status'];

		$query = 'SELECT ' . join(', ', $columns) . ' from wr_db_changelog;';

		$result = $dbConnection->query($query) or die ('Unable to execute: ' . $query . ' : ' . $dbConnection->error );

		echo '<table class="table table-hover table-condensed"><thead><tr>';
		foreach ($columns as $column) {
			echo '<td>' . $column . '</td>';
		}
		echo '</thead><tbody>';

		foreach($result as $row) {
			echo '<tr>';
			foreach ($columns as $column) {
				echo '<td>' . $row[$column] . '</td>';
			}
			echo '</tr>';
		}
		echo '</tbody></table>';

		$result->free();

	}

	function display_pending_migrations($latest_number) {
		$migrations_dir = __DIR__ . '/../../../../../db_migrations/';

		echo ' looking in: ' . $migrations_dir;

		$dir = opendir($migrations_dir);

		$possible_migrations = array();

		if ($dir !== false) {

			$count = 0;

			while (false !== ($entry = readdir($dir)) && $count< 1000) {
				if ($entry != "." && $entry != "..") {
					if (preg_match('/(\d\d\d\d)_(.*).sql/', $entry, $matches)) {
						$possible_migrations[(int) $matches[1]] =  $entry;
					}
				}
				$count++;
			}
			closedir($dir);
		} else {
			echo 'Cannot open directory.';
		}

		echo '<h3>All defined migrations</h3>';

		echo '<pre>';
		for($i=0;$i<9999;$i++) {
			if ($possible_migrations[$i]) {
				echo "$i - $possible_migrations[$i]";
				if ($i <= $latest_number) {
					echo ' - complete';
				}
				echo "\n";
			} else {
				break;
			}
		}
		echo '</pre>';
	}

	// Display Content:

	if (!$logged_in) {
		echo 'Private';
	} else {
		display_migration_log();
		$latest_number = get_last_completed_migration_id();
		echo "<b>Latest complete:</b> $latest_number\n<br>\n";
		display_pending_migrations($latest_number);
	}

?>
<!--
<form method="POST">
<input name="run" value="Run Migrations" type="submit">
</form>
-->
