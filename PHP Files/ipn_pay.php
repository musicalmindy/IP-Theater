<?php //  Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');

function logIt($message = "", $endLine = "\n", $logFile = '_inc/logs/ipn_log.txt') {
	file_put_contents($logFile, $message . $endLine, FILE_APPEND);
}

if(apiPost('receiver_id') === $config['paypal_code']) {
	asort($_POST);
	$ipnPostInfo = "";
	foreach($_POST as $k => $v) {
		$ipnPostInfo .= "$k => $v<br>\n";
	}

	$custom = unserialize(htmlspecialchars_decode(apiPost('custom'), ENT_QUOTES));

	//  Send an email to myself...
	$to = $config['order_email'];
	$message = "<b>name:</b> {$custom['name']}<br>\n"
		. "<b>amount:</b> " . apiPost('payment_gross') . "<br>\n"
		. "<b>email:</b> {$custom['email']}<br>\n"
		. "<b>phone:</b> {$custom['phone']}<br>\n"
		. "<b>comments:</b> {$custom['comments']}<br>\n"
		. "<b>date:</b> " . date('Y-m-d g:i a') . "<br>\n"
		. "<b>browser:</b> " . $_SERVER['HTTP_USER_AGENT'] . "<br>\n"
		. "<b>ip:</b> " . $_SERVER['REMOTE_ADDR'] . "<br>\n"
		. "<b>ipnPostInfo:</b> {$ipnPostInfo}";
	$subject = "IPT Payment ($" . apiPost('payment_gross') . "): " . $custom['name'];
	$headers = "From: IPTheater <{$config['info_email']}>\n"
		. "MIME-Version: 1.0\n"
		. "Content-type: text/html; charset=iso-8859-1\n"
		. "Reply-To: {$custom['name']} <{$custom['email']}>\n"
		. "bcc: rhythmcity@gmail.com";
	$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message); // Fix any bare linefeeds in the message to make it RFC821 Compliant
	$headers = preg_replace("#(?<!\r)\n#si", "\r\n", $headers); // Make sure there are no bare linefeeds in the headers
	mail($to, $subject, $message, $headers);

	//  Send an email to buyer...
	if(isValidEmail($custom['email'])) {
		$to = $custom['email'];
		$message = "
			<p>Your Payment to Immeasurable Productions is Complete!</p>
			<p>Amount: " . apiPost('payment_gross') . "</p>
			<p>We appreciate your payment.</p>
			<p>Thank you for your business!</p>
			<p>Immeasurable Productions<br>
				<a href='http://" . $_SERVER['HTTP_HOST'] . "'>www.IPTheater.com</a></p>";
		$subject = "Payment made to Immeasurable Productions";
		$headers = "From: IPTheater <{$config['info_email']}>\n"
			. "MIME-Version: 1.0\n"
			. "Content-type: text/html; charset=iso-8859-1\n"
			. "bcc: rhythmcity@gmail.com";
		$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message); // Fix any bare linefeeds in the message to make it RFC821 Compliant
		$headers = preg_replace("#(?<!\r)\n#si", "\r\n", $headers); // Make sure there are no bare linefeeds in the headers
		mail($to,$subject,$message,$headers);
	}
}

?>
