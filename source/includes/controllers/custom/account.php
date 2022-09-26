<?php

/*	--------------------------------------
	Execute immediately upon load
	-------------------------------------- */

	// parse query string
		$username = $tl->page['parameter1'];
		if (!$username && $logged_in) $authentication_manager->forceRedirect('/account/' . $logged_in['username']);
		
	// authenticate user
		if (!$logged_in) $authentication_manager->forceLoginThenRedirectHere();
		
		if ($username != $logged_in['username']) {
			if (!$logged_in['can_edit_users']) $authentication_manager->forceRedirect('/404');
		}

	// queries
		if ($logged_in['is_administrator']) $user = retrieveUsers(array('username'=>$username), null, null, null, 1);
		else $user = retrieveUsers(array('username'=>$username, 'enabled'=>'1'), null, null, null, 1);
		if (count($user) < 1) $authentication_manager->forceRedirect('/404');
		
		$termination = retrieveSingleFromDb('user_terminations', null, array('user_id'=>$user[0]['user_id']));
		
		$minimumProfileImageWidth = 0;
		foreach ($profileImageSizes as $size => $width) {
			if ($width > $minimumProfileImageWidth) $minimumProfileImageWidth = $width;
		}
		
	$tl->page['title'] = "My Account";
	$tl->page['section'] = "Profile";
		
/*	--------------------------------------
	Execute only if a form post
	-------------------------------------- */
		
	if (count($_POST) > 0) {


		if ($_POST['formName'] == 'profileForm' && $_POST['deleteCurrentProfileImage'] == 'Y') {

			// delete profile images
				foreach ($profileImageSizes as $type=>$width) {
					@unlink('assets/profile_images/' . $encrypter->quickEncrypt($user[0]['username'], $tl->salts['public_keys']) . '_' . $type . '.jpg');
				}


			// update log
				if ($logged_in['user_id'] != $user[0]['user_id']) {
					$activity = $logged_in['full_name'] . " (" . $logged_in['user_id'] . ") has deleted the profile photo of " . $user[0]['full_name'] . " (" . $user[0]['user_id'] . ")";
					$logger->logItInDb($activity, null, array('user_id=' . $logged_in['user_id'], 'user_id=' . $user[0]['user_id']));
				}
				else {
					$activity = $logged_in['full_name'] . " (user_id " . $logged_in['user_id'] . ") has deleted his/her own profile photo";
					$logger->logItInDb($activity, null, array('user_id=' . $logged_in['user_id']));
				}

			// redirect
				$authentication_manager->forceRedirect('/account/' . $_POST['username'] . '/success=profile_image_deleted');
									
		}
		elseif ($_POST['formName'] == 'profileForm') {

			// clean input
				$_POST = $parser->trimAll($_POST);
				$checkboxesToParse = array('enabled', 'primary_phone_sms', 'secondary_phone_sms', 'ok_to_contact', 'anonymous', 'is_proxy', 'is_moderator', 'is_community_liaison', 'is_administrator', 'is_tester', 'can_edit_content', 'can_update_settings', 'can_edit_settings', 'can_edit_users', 'can_send_email', 'can_run_housekeeping');
				foreach ($checkboxesToParse as $checkbox) {
					if (isset($_POST[$checkbox])) $_POST[$checkbox] = 1;
					else $_POST[$checkbox] = 0;
				}
				
			// check for errors
				if (!$_POST['username']) $tl->page['error'] .= "Please provide a username. ";
				if (!$input_validator->isStringValid($_POST['username'], 'abcdefghijklmnopqrstuvwxyz0123456789-_', '')) $tl->page['error'] .= "Your username can only contain alphanumeric characters. ";
				if ($_POST['email'] && !$input_validator->validateEmailRobust($_POST['email'])) $tl->page['error'] .= "There appears to be a problem with your email address. ";
				if ($_POST['username'] != $user[0]['username']) {
					$numberOfExistingUsers = countInDb('users', 'user_id', array('username'=>$_POST['username']), null, null, null);
					$numberOfExistingRegistrants = countInDb('registrations', 'registration_id', array('username'=>$_POST['username']), null, null, null);
					if ($numberOfExistingUsers[0]['count'] + $numberOfExistingRegistrants[0]['count'] > 0) $tl->page['error'] .= "The username you've specified already belongs to another user. ";
				}
				if ($_POST['email'] && ($_POST['email'] != $user[0]['email'])) {
					$numberOfExistingUsers = countInDb('users', 'user_id', array('email'=>$_POST['email']), null, null, null);
					$numberOfExistingRegistrants = countInDb('registrations', 'registration_id', array('email'=>$_POST['email']), null, null, null);
					if ($numberOfExistingUsers[0]['count'] + $numberOfExistingRegistrants[0]['count'] > 0) $tl->page['error'] .= "The email address you've specified already belongs to another user. ";
				}
				if (!$_POST['country_id']) $tl->page['error'] .= "Please provide a country. ";
				
				if ($_FILES['profile_image']['tmp_name']) {
					if (!$file_manager->isImage($_FILES['profile_image']['tmp_name'])) $tl->page['error'] .= "An invalid image was uploaded; please upload a JPG, PNG or GIF. ";
					else {
						$dimensions = getimagesize($_FILES['profile_image']['tmp_name']);
						if ($dimensions[0] > 3000) $tl->page['error'] .= "Your uploaded profile image appears to be too large. Please make sure that the width is no more than 3000 pixels. ";
						elseif ($dimensions[0] < $profileImageSizes['verysmall']) $tl->page['error'] .= "Your uploaded profile image appears to be too small. Please make sure that the width is no less than " . floatval($profileImageSizes['verysmall']) . " pixels. ";
					}
				}
								
			// update profile
				if (!$tl->page['error']) {

					// update username and real name
						updateDb('users', array('username'=>$_POST['username'], 'first_name'=>$_POST['first_name'], 'last_name'=>$_POST['last_name']), array('user_id'=>$user[0]['user_id']), null, null, null, null, 1);

						if ($user[0]['user_id'] == $logged_in['user_id']) {
							$_SESSION['username'] = $_POST['username'];
							$cookieExpiryDate = time()+60*60*24 * floatval($tl->settings['Keep users logged in for']);
							setcookie("username", $_SESSION['username'], $cookieExpiryDate, '', '', 0);
						}
						
					// update location etc.
						updateDb('users', array('country_id'=>$_POST['country_id'], 'region_id'=>$_POST['region_id'], 'other_region'=>$_POST['region_other'], 'city'=>$_POST['city'], 'primary_phone'=>$_POST['primary_phone'], 'primary_phone_sms'=>$_POST['primary_phone_sms'], 'secondary_phone'=>$_POST['secondary_phone'], 'secondary_phone_sms'=>$_POST['secondary_phone_sms'], 'ok_to_contact'=>$_POST['ok_to_contact'], 'anonymous'=>$_POST['anonymous']), array('user_id'=>$user[0]['user_id']), null, null, null, null, 1);
						
					// update test mode, etc.
						if ($logged_in['can_edit_users']) {
							updateDb('users', array('is_moderator'=>$_POST['is_moderator'], 'is_proxy'=>$_POST['is_proxy'], 'is_administrator'=>$_POST['is_administrator'], 'is_community_liaison'=>$_POST['is_community_liaison'], 'is_tester'=>$_POST['is_tester']), array('user_id'=>$user[0]['user_id']), null, null, null, null, 1);
						}
						
					// update permissions
						if ($logged_in['can_edit_users']) {
							deleteFromDb('user_permissions', array('user_id'=>$user[0]['user_id']), null, null, null, null, 1);
							if ($_POST['is_administrator']) insertIntoDb('user_permissions', array('user_id'=>$user[0]['user_id'], 'can_edit_content'=>$_POST['can_edit_content'], 'can_update_settings'=>$_POST['can_update_settings'], 'can_edit_settings'=>$_POST['can_edit_settings'], 'can_edit_users'=>$_POST['can_edit_users'], 'can_send_email'=>$_POST['can_send_email'], 'can_run_housekeeping'=>$_POST['can_run_housekeeping']));
						}

					// update enabled
						if ($logged_in['can_edit_users'] && $logged_in['username'] != $user[0]['username']) {
							updateDb('users', array('enabled'=>$_POST['enabled']), array('user_id'=>$user[0]['user_id']), null, null, null, null, 1);
							if (!$_POST['enabled']) {
								deleteFromDb('user_terminations', array('user_id'=>$user[0]['user_id']), null, null, null, null, 1);
								insertIntoDb('user_terminations', array('reason'=>$_POST['reason']), array('user_id'=>$user[0]['user_id'], 'disabled_by'=>$logged_in['user_id'], 'disabled_on'=>date('Y-m-d H:i:s')));
							}
						}

					// update email, if different
						if ($_POST['email'] != $user[0]['email']) {
							if ($logged_in['is_administrator'] || !$_POST['email']) updateDb('users', array('email'=>$_POST['email']), array('user_id'=>$user[0]['user_id']), null, null, null, null, 1);
							else {
								$encryption = new encrypter_TL();
								$emailKey = $encryption->quickEncrypt($_POST['email'], rand(10000,99999));
								$expiryDate = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + $tl->settings['Email confirmation link active for'], date('Y'))); // one week
								
								deleteFromDb('user_keys', array('user_key'=>'Reset Email', 'user_id'=>$user[0]['user_id']));
								insertIntoDb('user_keys', array('user_key'=>'Reset Email', 'user_id'=>$user[0]['user_id'], 'hash'=>$emailKey, 'value'=>$_POST['email'], 'saved_on'=>date('Y-m-d H:i:s'), 'expiry'=>$expiryDate));
								
								$emailSent = emailNewEmailKey($logged_in['full_name'], addSlashes($_POST['email']), $emailKey);
								if (!$emailSent) $tl->page['error'] = "Unable to send your email reset link. Please try updating again, and if you continue to encounter difficulties, <a href='/contact' class='errorMessage'>let us know</a>. ";
							}
						}

					// update profile image
						if ($_FILES['profile_image']['tmp_name']) {

							// delete old image
								foreach ($profileImageSizes as $type=>$width) {
									@unlink('uploads/profile_images/' . $encrypter->quickEncrypt($user[0]['username'], $tl->salts['public_keys']) . '_' . $type . '.jpg');
								}
								
							// save new image
								foreach ($profileImageSizes as $type=>$width) {
									$destinationFile = $encrypter->quickEncrypt($user[0]['username'], $tl->salts['public_keys']) . '_' . $type . '.jpg';
									$destinationPath = 'uploads/profile_images/';
									$success = $media_converter->convertImage($_FILES['profile_image']['tmp_name'], $destinationFile, $destinationPath, $width, $width, null);
									if (!file_exists($destinationPath . $destinationFile)) $tl->page['error'] .= "Unable to create " . $type . " profile image .";
								}

						}
				}
				
				if (!$tl->page['error']) {
					// update log
						if ($logged_in['user_id'] != $user[0]['user_id']) {
							$activity = $logged_in['full_name'] . " has updated the profile of " . $user[0]['full_name'];
							$logger->logItInDb($activity, null, array('user_id=' . $logged_in['user_id'], 'user_id=' . $user[0]['user_id']));

						}
						else {
							$activity = $logged_in['full_name'] . " has updated his/her own profile";
							$logger->logItInDb($activity, null, array('user_id=' . $logged_in['user_id']));
						}

					// redirect
						if ($logged_in['user_id'] == $user[0]['user_id'] && $_POST['email'] != $user[0]['email']) $authentication_manager->forceRedirect('/account/' . $_POST['username'] . '/success=profile_updated_check_email');
						else  $authentication_manager->forceRedirect('/account/' . $_POST['username'] . '/success=profile_updated');
				}
				
		}

	}
	
/*	--------------------------------------
	Execute only if not a form post
	-------------------------------------- */
		
	else {
	}
		
?>