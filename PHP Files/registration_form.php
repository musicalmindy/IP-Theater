<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');
$title = "Registration Form - Joseph and the Amazing Technicolor Dreamcoat";
$body_class .= ' form_page registration_form';
require_once('_inc/ipt_2.php');

// $formSubmitted = apiGet('formSubmitted');

$likelySpam = false;
if(apiPost('submit') && (apiPost('questions') || apiPost('referral') || apiPost('website') !== 'http://')) {	//	if any of these bot-trap (hidden) fields are filled, this is almost certainly spam
	$likelySpam = true;
}

if(apiPost('submit') && !$likelySpam) {
	$firstName = trim(apiPost('firstName'));
	$lastName = trim(apiPost('lastName'));
	$gender = apiPost('gender');
	$chosenWeek = apiPost('chosenWeek');
	$height = (intval(apiPost('ht_ft')) * 12) + intval(apiPost('ht_in'));	//	height is stored only in inches
	$dateOfBirth = apiPost('dateOfBirth');
	$phone = trim(apiPost('phone'));
	$email = trim(apiPost('email'));
	$tshirt = trim(apiPost('tshirt'));
	$address = apiPost('address') ? trim(apiPost('address')) : null;
	$city = apiPost('city') ? trim(apiPost('city')) : null;
	$state = apiPost('state') ? apiPost('state') : null;
	$zip = apiPost('zip') ? trim(apiPost('zip')) : null;
	$parentName = apiPost('parentName') ? trim(apiPost('parentName')) : null;
		$parentFirstName = $parentName ? substr($parentName, 0, strrpos($parentName, ' ')) : null;
		$parentLastName = $parentName ? substr($parentName, strrpos($parentName, ' ') + 1) : null;
	$parentPhone = apiPost('parentPhone') ? trim(apiPost('parentPhone')) : null;
	$parentEmail = apiPost('parentEmail') ? trim(apiPost('parentEmail')) : null;
	$conflicts = apiPost('conflicts') ? apiPost('conflicts') : null;
	$signature = trim(apiPost('signature'));
	$parentSignature = apiPost('parentSignature') ? trim(apiPost('parentSignature')) : null;
	$comments = apiPost('comments') ? apiPost('comments') : null;
	$date = date('Y-m-d');
	$ip = $_SERVER['REMOTE_ADDR'];

	//	Send an email to myself...
	$to = 'mindy@iptheater.com';
	$message = "<b>name:</b> $firstName $lastName<br>\n"
		. "<b>dateOfBirth:</b> $dateOfBirth (age: " . getAge($dateOfBirth) . ")<br>\n"
		. "<b>gender:</b> $gender<br>\n"
		. "<b>chosenWeek:</b> $chosenWeek<br>\n"
		. "<b>height:</b> " . floor($height / 12) . "'" . ($height % 12) . '"' . "<br>\n"
		. "<b>phone:</b> $phone<br>\n"
		. "<b>email:</b> $email<br>\n"
		. "<b>conflicts:</b> " . nl2br($conflicts) . "<br>\n"
		. "<b>comments:</b> " . nl2br($comments) . "<br>\n"
		. "<b>tshirt:</b> $tshirt<br>\n"
		. "<b>address:</b> $address<br>\n"
		. "<b>city:</b> $city<br>\n"
		. "<b>state:</b> $state<br>\n"
		. "<b>zip:</b> $zip<br>\n"
		. "<b>parentName:</b> $parentName<br>\n"
		. "<b>parentPhone:</b> $parentPhone<br>\n"
		. "<b>parentEmail:</b> $parentEmail<br>\n"
		. "<b>date:</b> $date<br>\n"
		. "<b>browser:</b> " . $_SERVER['HTTP_USER_AGENT'] . "<br>\n"
		. "<b>ip:</b> $ip<br>\n";
	$subject = "Joseph Registration: $firstName $lastName";
	$headers = "From: IPTheater <{$config['info_email']}>\n"
		. "Reply-To: $firstName $lastName <$email>\n"
		. "MIME-Version: 1.0\n"
		. "Content-type: text/html; charset=iso-8859-1\n"
		. "cc: {$config['my_email']}\n"
		. "bcc: {$config['bcc_email']}";
	$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);	// Fix any bare linefeeds in the message to make it RFC821 Compliant
	$headers = preg_replace("#(?<!\r)\n#si", "\r\n", $headers); // Make sure there are no bare linefeeds in the headers
	mail($to,$subject,$message,$headers);

	//	Send an email to registrant...
	$to = $email;
	$message = "<p>Hi $firstName!<br><br>\n"
		. "Thank you for registering for Immeasurable Productions' Spring Break Camp: Joseph and the Amazing Technicolor Dreamcoat!<br><br>\n"
		. "If you have paid your fee in full then you are guaranteed a spot in the show!<br><br>\n"
		. "Congratulations!<br><br>\n"
		. "You will receive more information by email in the coming months about auditions, costumes, and rehearsals!<br><br>\n"
		. "We are so excited to work with you this Spring Break!<br><br>\n"
		. "Mindy Moritz<br>\n"
		. "Immeasurable Productions<br>\n"
		. "<a href='www.IPTheater.com'>www.IPTheater.com</a><br>\n";
	$subject = "You are registered for Spring Break Camp: Joseph and the Amazing Technicolor Dreamcoat!";
	$headers = "From: Mindy Moritz <mindy@iptheater.com>\n"
		. "MIME-Version: 1.0\n"
		. "Content-type: text/html; charset=iso-8859-1\n"
		. "bcc: {$config['bcc_email']}";
	$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);	// Fix any bare linefeeds in the message to make it RFC821 Compliant
	$headers = preg_replace("#(?<!\r)\n#si", "\r\n", $headers); // Make sure there are no bare linefeeds in the headers
	mail($to, $subject, $message, $headers);
	if ($parentEmail && $parentEmail !== $email) {
		mail($parentEmail, $subject, $message, $headers);	//	send the same email to parent
	}

	//	Redirect to payment page
	header("Location: pay.php?amount={$campRegistrationFee}&name={$parentName}&email={$parentEmail}&phone={$parentPhone}&comments=Joseph%20Registration%20Fee:%20{$firstName}%20{$lastName}");
}

?>
<?=$header;?>
	<section class='main'>
		<h1>Immeasurable Productions</h1>
		<h2><img src='_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions'><br>
			<img src='_img/thirteen-logo_400.png' alt='Joseph and the Amazing Technicolor Dreamcoat' id='thirteen_logo'>
		</h2>
		<h3>Registration Form for Joseph and the Amazing Technicolor Dreamcoat</h3>
		<p>Please completely fill out and submit this form.</p>
		<hr>
		<form action='<?=$_SERVER['PHP_SELF'];?>' method='post' role='form' class='bs-condensed form-inline'>
			<div class='row'>
				<section class='col-sm-3' id="headshot">
				</section>
			</div>
			<div class='row'>
				<section class='col-sm-12'>
					<strong>Name:</strong>
					<input type='text' name='firstName' id='firstName' placeholder='First Name' class='form-control' required>
					<input type='text' name='lastName' id='lastName' placeholder='Last Name' class='form-control' required>
					&nbsp; &nbsp;
					<strong>M/F:</strong>
					<select name='gender' class='form-control' required><option value=''>&hellip;</option><option value='M'>M</option><option value='F'>F</option></select>
					&nbsp; &nbsp;
					<strong>Height:</strong>
					<input type='number' name='ht_ft' min='3' max='6' class='form-control' required>ft.
					<input type='number' name='ht_in' min='0' max='11' class='form-control' required>in.
					&nbsp; &nbsp;
					<strong>Date of Birth:</strong>
					<input
						type='number' name='dobMM' data-bind='value: dobMM' placeholder='MM' min='1' max='12' class='form-control' required>/<input
						type='number' name='dobDD' data-bind='value: dobDD' placeholder='DD' min='1' max='31' class='form-control' required>/<input
						type='number' name='dobYYYY' data-bind='value: dobYYYY, valueUpdate: "afterkeydown"' placeholder='YYYY' min='<?=date('Y', strtotime('-80 years'));?>' max='<?=date('Y', strtotime('-4 years'));?>' class='form-control' required>
						<span class='weak' data-bind="text: registrantAge() < 100 ? 'age ' + registrantAge() : ''"></span>
					<input type='hidden' name='dateOfBirth' data-bind='value: dateOfBirth'>
				</section>
			</div>
			<div class='row'>
				<section class='col-sm-12'>
					<strong>Phone:</strong><input type='text' name='phone' class='formatter format-phone form-control' required>
					<strong>Email:</strong><input type='text' name='email' class='form-control' required>
					<strong>T-Shirt Size:</strong><select name='tshirt' id='tshirt' class='form-control' required>
						<option value='' selected>Select One...</option>
						<option value='S'>Adult Small</option>
						<option value='M'>Adult Medium</option>
						<option value='L'>Adult Large</option>
						<option value='XL'>Adult Extra Large</option>
					</select>
				</section>
			</div>
			<div class='row'>
				<section class='col-sm-12'>
					<strong>Chosen Camp Week:</strong><select name='chosenWeek' class='form-control' required>
						<option value=''>Select One...</option>
						<option value='1' selected>Week 1 (Closed for BOYS)</option>
						<option value='2' disabled>Week 2 (Closed)</option>
					</select>
				</section>
			</div>
			<div class='row'>
				<section class='col-sm-12'>
					<strong>Address:</strong><input type='text' name='address' class='form-control'>
					<strong>Zip:</strong><input type='text' name='zip' maxlength='10' class='formatter format-zip form-control' id='zip'>
					<strong>City:</strong><input type='text' name='city' id='city' class='form-control'>
					<strong>State:</strong><select name='state' id='state' class='form-control'><?=$stateOptions;?></select>
				</section>
			</div>
			<div class='row' data-bind='visible: registrantAge() < grownUpAge()'>
				<section class='col-sm-12'>
					<strong>Parent/Guardian:</strong><input type='text' name='parentName' data-bind="attr: {'required': registrantAge() < adultAge()}" class='form-control'>
					<strong>Phone:</strong><input type='text' name='parentPhone' class='formatter format-phone form-control' data-bind="attr: {'required': registrantAge() < adultAge()}">
					<strong>Email:</strong><input type='text' name='parentEmail' data-bind="attr: {'required': registrantAge() < adultAge()}" class='form-control'>
				</section>
			</div>
			<div class='row'>
				<p>
				</p>
			</div>
			<div class='row'>
				<section class='col-sm-12'>
					Please list any conflicts with the rehearsal schedule and/or performances (Week 1: July 13-19 or Week 2: July 20-26):
					<textarea name='conflicts' class='form-control'></textarea>
				</section>
			</div>
			<div class='row'>
				<section class='col-sm-12'>
					<strong>Comments:</strong><textarea name='comments' class='form-control'></textarea>
				</section>
			</div>
			<div class='row bot-trap'>
				<section class='col-xs-12'>
					<!-- BOT-TRAP: THIS PART IS MEANT TO STOP SPAM SUBMISSIONS FROM ROBOTS (if you see this section, don't change these fields) -->
						<p class='bot-trap'><label>Robot Questions</label><textarea name='questions' rows='6' cols='40' class='form-control'></textarea></p>
						<p class='bot-trap'><label>Robot Referral</label><input type='text' name='referral' value='' class='form-control'></p>
						<p class='bot-trap'><label>Robot Website</label><input type='text' name='website' value='http://' class='form-control'></p>
					<!-- /BOT-TRAP -->
				</section>
			</div>
			<div class='row'>
				<section class='col-sm-12 text-center top-buffer'>
					<input type='submit' name='submit' value='Submit Registration Form And Pay <?=$campRegistrationFee;?> Registration Fee' onclick='return confirm("Is the form complete and accurate?")' class='btn btn-primary'>
				</section>
			</div>
		</form>
	</section>
<?=$footer;?>
