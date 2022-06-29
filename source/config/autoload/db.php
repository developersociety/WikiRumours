<?php

	// set environment
		$currentDatabase = getenv("DB_ENV_TYPE");
		$tablePrefix = getenv("DB_TAB_PREFIX");

	// define databases
		$databases = array();
	
		// development database
			$databases['dev'] = array(
				"Server" => getenv("DB_HOST"),
				"Name" => getenv("DB_DATABASE"),
				"User" => getenv("DB_USERNAME"),
				"Password" => getenv("DB_PASSWORD")
			);
	
		// staging database
			$databases['staging'] = array(
				"Server" => getenv("DB_HOST"),
				"Name" => getenv("DB_DATABASE"),
				"User" => getenv("DB_USERNAME"),
				"Password" => getenv("DB_PASSWORD")
			);
			
		// production database
			$databases['production'] = array(
				"Server" => getenv("DB_HOST"),
				"Name" => getenv("DB_DATABASE"),
				"User" => getenv("DB_USERNAME"),
				"Password" => getenv("DB_PASSWORD")
			);
	
?>