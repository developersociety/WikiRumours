#!/usr/local/bin/php -q
<?php

	include __DIR__ . "/../wwwroot/initialize.php";
	if ($tl->settings['Enable cron connections'] && $tl->settings['Interval between cron connections'] > 0) include __DIR__ . "/../wwwroot/housekeeping.php";
		
?>
