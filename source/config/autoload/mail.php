<?php

	$tl->mail = [
		"Host" =>				getenv('SMTP_HOST'),
		"Port" =>				"587",
		"Secure" =>				"",
		"User" =>				getenv('SMTP_USERNAME'),
		"Password" =>			getenv('SMTP_PASSWORD'),
		"OutgoingAddress" =>	getenv('SMTP_SENDER_EMAIL'),
		"IncomingAddress" =>	getenv('SMTP_SENDER_EMAIL'),
		"AddressForBackups" =>	""
	];
		
?>
