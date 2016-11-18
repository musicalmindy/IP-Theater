<?php //  Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');

function logIt($message = "", $endLine = "\n", $logFile = '_inc/logs/ipn_log.txt') {
	file_put_contents($logFile, $message . $endLine, FILE_APPEND);
}

if(apiPost('receiver_id') === $config['paypal_code']) {
	$orderId = apiGet('orderId');
	logIt("\n\n-------------------------------\nNEW ORDER ID: " . apiGet('orderId') . "\n--------------------------------------");
	asort($_POST);
	$ipnPostInfo = "";

	foreach($_POST as $k => $v) {
		if($k === 'custom') {
			$custom = unserialize(htmlspecialchars_decode($v, ENT_QUOTES));

			foreach($custom as $sessionKey => $sessionValue) {
				logIt("(CUSTOM) $sessionKey => $sessionValue");
			};
		}
		$line = "$k => $v";
		logIt($line);
		$ipnPostInfo .= $line . "<br>\n";
	}

	$custom = unserialize(htmlspecialchars_decode(apiPost('custom'), ENT_QUOTES));

	if(floatval($custom['orderPrice']) === floatval(apiPost('payment_gross'))) {
		$sql = "
			UPDATE ip_orders
			SET
				paid = 1,
				address = :address,
				city = :city,
				state = :state,
				zip = :zip
			WHERE id = :id";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':id', $orderId, PDO::PARAM_STR);
		$sth->bindParam(':address', apiPost('address_street'), PDO::PARAM_STR);
		$sth->bindParam(':city', apiPost('address_city'), PDO::PARAM_STR);
		$sth->bindParam(':state', apiPost('address_state'), PDO::PARAM_STR);
		$sth->bindParam(':zip', apiPost('address_zip'), PDO::PARAM_STR);
		$sth->execute();

		//  GET ALL THE NECESSARY INFO ABOUT THIS ORDER FROM THE DATABASE
		$sql = "
			SELECT
				id,
				name,
				email,
				phone,
				address,
				city,
				state,
				zip,
				price,
				mailTickets,
				referredBy,
				comments,
				ip,
				dateTime,
				confirmationNumber
			FROM ip_orders
			WHERE id = :id";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':id', $orderId, PDO::PARAM_STR);
		$sth->execute();
		$order = $sth->fetchObject();

		//  GET ALL THE TICKETS FOR THIS ORDER
		$sql = "
			SELECT
				t.id,
				t.performanceId,
				s.section,
				s.row,
				s.number
			FROM ip_tickets t
			INNER JOIN ip_seats s ON t.seatId = s.id
			WHERE t.orderId = :id";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':id', $orderId, PDO::PARAM_STR);
		$sth->execute();
		$order->tickets = sthFetchObjects($sth);	//	fetch all of the tickets
		$order->ticketCount = count($order->tickets);
		$reservedSeating = $order->tickets[0]->section ? true : false;
		$performance = $currentShow->getPerformance($custom['performanceId']);

		//  Send an email to myself...
		$to = $config['order_email'];
		$message = "<b>name:</b> {$order->name}<br>\n"
			. "<b>tickets:</b> {$order->ticketCount}<br>\n"
			. "<b>performance:</b> {$performance->getDateTimeFormatted()}<br>\n"
			. "<b>receive Tickets:</b> " . ($order->mailTickets ? "BY MAIL" : "at will call") . "<br>\n"
			. "<b>email:</b> {$order->email}<br>\n"
			. "<b>phone:</b> {$order->phone}<br>\n"
			. "<b>address:</b> {$order->address}, {$order->city}, {$order->state} {$order->zip}<br>\n"
			. "<b>referredBy:</b> {$order->referredBy}<br>\n"
			. "<b>comments:</b> {$order->comments}<br>\n"
			. "<b>confirmationNumber:</b> {$order->confirmationNumber}<br>\n"
			. "<b>date:</b> " . date('Y-m-d g:i a') . "<br>\n"
			. "<b>browser:</b> " . $_SERVER['HTTP_USER_AGENT'] . "<br>\n"
			. "<b>ip:</b> " . $_SERVER['REMOTE_ADDR'] . "<br>\n";

		if ($reservedSeating) {
			$message .= "<b>Seats:</b><br>\n"
				. "<ul>\n";

			foreach($order->tickets as $t) {
				$message .= "<li>Section: {$t->section}, Row: {$t->row}, Seat: {$t->number}</li>\n";
			}

			$message .= "</ul>\n";
		}

		$message .= "<b>ipnPostInfo:</b> {$ipnPostInfo}";

		$subject = ($order->mailTickets ? "Mail " : "") . "{$currentShow->getAbbr()} Tickets ({$order->ticketCount}): " . $order->name;
		$headers = "From: IPTheater <{$config['info_email']}>\n"
			. "MIME-Version: 1.0\n"
			. "Content-type: text/html; charset=iso-8859-1\n"
			. "Reply-To: {$order->name} <{$order->email}>\n"
			. "bcc: rhythmcity@gmail.com";
		$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message); // Fix any bare linefeeds in the message to make it RFC821 Compliant
		$headers = preg_replace("#(?<!\r)\n#si", "\r\n", $headers); // Make sure there are no bare linefeeds in the headers
		mail($to,$subject,$message,$headers);

		//  Send an email to buyer...
		$to = $order->email;
		$message = "
			<p>Your {$currentShow->getTitle()} Ticket Order is Complete! Thank you for your business!</p>
			<p>Confirmation Number: <strong>{$order->confirmationNumber}</strong></p>
			<p>Purchased:
				{$order->ticketCount} Ticket" . ($order->ticketCount > 1 ? "s" : "") . "
				to {$currentShow->getTitle()}
				at {$performance->getDateTimeFormatted()}
				at {$performance->getTheater()->getName()}
				({$performance->getTheater()->getAddressFormatted()})
			</p>\n";

		if($reservedSeating) {
			$message .= "
				<p><strong>Seats:</strong></p>
				<ul>\n";

			foreach($order->tickets as $t) {
				$message .= "<li>Section: {$t->section}, Row: {$t->row}, Seat: {$t->number}</li>\n";
			}

			$message .= "</ul>\n";
		}

		$message .= "
			<p>We appreciate your order.  Your tickets will be
				" . ($order->mailTickets
				? "mailed to the address you provided: <b>{$order->address}, {$order->city}, {$order->state} {$order->zip}</b>.  Please allow up to 5 business days"
				: "held for you at the theater Will Call window.  Please present your Confirmation Number ({$order->confirmationNumber}) when you arrive") . "
			to receive your tickets.  If you would like additional tickets, please click the following link to <a href='http://" . $_SERVER['HTTP_HOST'] . "/tickets.php'>order more</a>!</p>
			<p>Thank you!</p>
			<p>Immeasurable Productions<br>
				<a href='http://" . $_SERVER['HTTP_HOST'] . "'>www.IPTheater.com</a></p>";
		$subject = "{$currentShow->getTitle()} Tickets Ordered ({$order->ticketCount})";
		$headers = "From: Immeasurable Productions <{$config['info_email']}>\n"
			. "MIME-Version: 1.0\n"
			. "Content-type: text/html; charset=iso-8859-1\n"
			. "bcc: rhythmcity@gmail.com";
		$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message); // Fix any bare linefeeds in the message to make it RFC821 Compliant
		$headers = preg_replace("#(?<!\r)\n#si", "\r\n", $headers); // Make sure there are no bare linefeeds in the headers
		mail($to,$subject,$message,$headers);
	} else {
		logIt('WARNING: values differ! ' . floatval($custom['orderPrice']) . ' !== ' . floatval(apiPost('payment_gross')));
	}
} elseif(apiGet('orderId')) {
	logIt("\nNO POST DATA FOR ORDER ID: " . apiGet('orderId'));
} elseif(count($_POST)) {
	logIt("\nNO ORDER ID PROVIDED FOR THIS POST DATA....");

	foreach($_POST as $k => $v) {
		logIt("$k => $v");
	}
} else {
	logIt("\nNO POST DATA OR ORDER ID PROVIDED");
}

?>
