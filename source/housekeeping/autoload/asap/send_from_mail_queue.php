<?php

	if ($currentDatabase == 'staging' || $currentDatabase == 'production') {

		$maxMessagesToSend = 10;

		// retrieve unsent mail in order of priority, and then in order of earliest queued first
//			$unsentMail = retrieveFromDb('mail_queue', null, null, null, null, null, $tablePrefix . "mail_queue.sent_on = '0000-00-00 00:00:00' AND " . $tablePrefix . "mail_queue.failed_attempts < '" . $tl->settings['Maximum allowable failures per email address'] . "'", null, $tablePrefix . 'mail_queue.priority DESC, ' . $tablePrefix . 'mail_queue.queued_on ASC');
		// stop sending monitoring emails temporarily (from no-reply to no-reply)
		// stop ordering by priority for now, descending index is not available with current database engine
		$unsentMail = retrieveFromDb('mail_queue', null, null, null, null, null, "sent_on = '0000-00-00 00:00:00' AND failed_attempts < " . $tl->settings['Maximum allowable failures per email address'] . " AND NOT (from_email = 'no-reply@msf-listen.org' AND to_email = 'no-reply@msf-listen.org')", null, false, $maxMessagesToSend);
		$output .= "Found " . count($unsentMail) . " queued messages\n";

			if (count($unsentMail)) {

				for ($counter = 0; $counter < min(count($unsentMail), $maxMessagesToSend); $counter++) {

					$output .= "Preparing to send email to " . $unsentMail[$counter]['to_email'] . " (mail_id " . $unsentMail[$counter]['mail_id'] . ")\n";

					$success = $notifier->sendFromMailQueue($unsentMail[$counter]['mail_id']);

					if ($success) {
						// update date sent
							updateDbSingle('mail_queue', array('sent_on'=>date('Y-m-d H:i:s')), array('mail_id'=>$unsentMail[$counter]['mail_id']));
						// update log
							$output .= "Successfully sent email to " . $unsentMail[$counter]['to_email'] . " (mail_id " . $unsentMail[$counter]['mail_id'] . ")\n";
					}
					else {
						// update failure count
							updateDbSingle('mail_queue', array('failed_attempts'=>($unsentMail[$counter]['failed_attempts'] + 1)), array('mail_id'=>$unsentMail[$counter]['mail_id']));
						// update log
							$output .= "Unable to send email to " . $unsentMail[$counter]['to_email'] . " (mail_id " . $unsentMail[$counter]['mail_id'] . ")\n";
					}

				}

			}

	}
?>
