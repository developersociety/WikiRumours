<?php

	// set environment
		$currentDatabase = 'production'; // 2022-03-07 ondruska setting prodction as no other database exists yet
		$tablePrefix = "wr_";

	// define databases
		$databases = array();
	
		// development database
			$databases['dev'] = array(
				"Server" => getenv('DB_HOST'),
				"Name" => getenv('DB_DATABASE'),
				"User" => getenv('DB_USERNAME'),
				"Password" => getenv('DB_PASSWORD')
			);
	
		// staging database
			$databases['staging'] = array(
				"Server" => "",
				"Name" => "",
				"User" => "",
				"Password" => ""
			);
			
		// production database
			$databases['production'] = array(
				"Server" => getenv('DB_HOST'),
				"Name" => getenv('DB_DATABASE'),
				"User" => getenv('DB_USERNAME'),
				"Password" => getenv('DB_PASSWORD')
			);
	
?>
