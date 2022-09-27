<?php

		$tl->salts = [
			'password' => getenv('ENC_PASSWORD'),
			'public_keys' => getenv('ENC_PUBKEY')
		];

		
?>
