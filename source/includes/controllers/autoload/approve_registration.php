<?php

	function approveRegistration($registrationID, $registeredBy = false) {

		global $tl;
		global $profileImageSizes;
		global $avatar_manager;
		global $logger;
		global $tablePrefix;

		// check for errors
			if (!$registrationID) {
				$tl->page['console'] .= "No registration ID provided. ";
				return false;
			}

			$registration = retrieveSingleFromDb('registrations', null, array('registration_id'=>$registrationID));
			if (!count($registration)) {
				$tl->page['console'] .= "No registrant found. ";
				return false;
			}

			$matchingUsers = retrieveSingleFromDb('users', null, null, null, null, null, "username = '" . $registration[0]['username'] . "' OR email = '" . $registration[0]['email'] . "'");
			if (count($matchingUsers)) {
				$tl->page['console'] .= "Existing username and/or email address found in users. ";
				return false;
			}

		// copy registrant to user table
			$userID = insertIntoDb('users', array('username'=>$registration[0]['username'], 'password_hash'=>$registration[0]['password_hash'], 'first_name'=>$registration[0]['first_name'], 'last_name'=>$registration[0]['last_name'], 'email'=>$registration[0]['email'], 'primary_phone'=>$registration[0]['primary_phone'], 'primary_phone_sms'=>$registration[0]['primary_phone_sms'], 'secondary_phone'=>$registration[0]['secondary_phone'], 'secondary_phone_sms'=>$registration[0]['secondary_phone_sms'], 'country_id'=>$registration[0]['country_id'], 'region_id'=>$registration[0]['region_id'], 'other_region'=>$registration[0]['other_region'], 'city'=>$registration[0]['city'], 'registered_on'=>$registration[0]['registered_on'], 'ok_to_contact'=>'1', 'enabled'=>'1', 'referred_by'=>$registration[0]['referred_by'], 'registered_by'=>$registeredBy));
			if (!$userID) {
				$tl->page['console'] .= "Unable to create user record for some reason. ";
				return false;
			}
				
		// add registered by (since )
			if (!$registeredBy) updateDbSingle('users', array('registered_by'=>$userID), array('user_id'=>$userID));

		// retrieve Gravatar
			foreach ($profileImageSizes as $type=>$width) {
				if ($width > @$largestWidth) $largestWidth = $width;
			}
			$gravatarPath = $avatar_manager->retrieveGravatar($registration[0]['email'], @$largestWidth);
			if ($gravatarPath) $avatar_manager->createProfileImage($registration[0]['username'], $gravatarPath);

		// check if no existing admins
			$existingUsers = retrieveUsers(array('is_administrator'));
			if (!count($existingUsers)) {
				updateDbSingle('users', array('is_administrator'=>'1'), array('user_id'=>$userID));
				insertIntoDb('user_permissions', array('user_id'=>$userID, 'can_edit_content'=>'1', 'can_update_settings'=>'1', 'can_edit_settings'=>'1', 'can_edit_users'=>'1', 'can_send_email'=>'1', 'can_run_housekeeping'=>'1'));
			}
					
		// delete registrant data
			deleteFromDbSingle('registrations', array('registration_id'=>$registrationID));

		// update log
			$name = ($registration[0]['first_name'] || $registration[0]['last_name'] ? trim($registration[0]['first_name'] . " " . $registration[0]['last_name']) : $registration[0]['username']);
			$activity = $name . " has completed registration";
			$logger->logItInDb($activity, null, array('user_id=' . $userID, 'registration_id=' . $registrationID));

			$users = retrieveUsers();
						
		// send welcome email
			emailNewUser($name, $registration[0]['email']);

		// notify admins, if necessary
			$notifications_widget = new notifications_widget_TL();
			$recipients = $notifications_widget->retrieveAdminRecipients([$tablePrefix . 'notification_recipients_x_types.type_id'=>'1']); // successful registration
			
			for ($counter = 0; $counter < count($recipients); $counter++) {
				$emailSent = emailAdministratorAboutSuccessfulRegistrant($recipients[$counter]['email'], $name);
			}

		return $userID;

	}

?>
