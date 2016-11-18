<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');
$title = "Grease Registration Form";
$body_class .= ' form_page';
require_once('_inc/ipt_2.php');

// $currentShow = $nextShow;
// $formSubmitted = apiGet('formSubmitted');

$likelySpam = false;
if(apiPost('submit') && (apiPost('questions') || apiPost('referral') || apiPost('website') !== 'http://')) {	//	if any of these bot-trap (hidden) fields are filled, this is almost certainly spam
	$likelySpam = true;
}

$formSubmittedMessage = '';

if(apiPost('submit') && !$likelySpam) {
	$firstName = trim(apiPost('firstName'));
	$lastName = trim(apiPost('lastName'));
	$gender = apiPost('gender');
	$dateOfBirth = apiPost('dateOfBirth');
	$phone = trim(apiPost('phone'));
	$email = trim(apiPost('email'));
	$chosenWeek = apiPost('chosenWeek');
	$parentName = apiPost('parentName') ? trim(apiPost('parentName')) : null;
		$parentFirstName = $parentName ? substr($parentName, 0, strrpos($parentName, ' ')) : null;
		$parentLastName = $parentName ? substr($parentName, strrpos($parentName, ' ') + 1) : null;
	$parentPhone = apiPost('parentPhone') ? trim(apiPost('parentPhone')) : null;
	$parentEmail = apiPost('parentEmail') ? trim(apiPost('parentEmail')) : null;
	$comments = apiPost('comments') ? apiPost('comments') : null;
	$date = date('Y-m-d');
	$ip = $_SERVER['REMOTE_ADDR'];

	//	Send an email to myself...
	$to = 'mindy@iptheater.com';
	$message = "<b>Grease Wait List Entry:</b><br><br>\n"
		. "<b>name:</b> $firstName $lastName<br>\n"
		. "<b>dateOfBirth:</b> $dateOfBirth (age: " . getAge(date('Y-m-d', strtotime($dateOfBirth))) . ")<br>\n"
		. "<b>chosenWeek:</b> $chosenWeek<br>\n"
		. "<b>comments:</b> " . nl2br($comments) . "<br>\n"
		. "<b>gender:</b> $gender<br>\n"
		. "<b>phone:</b> $phone<br>\n"
		. "<b>email:</b> $email<br>\n"
		// . "<b>parentName:</b> $parentName<br>\n"
		// . "<b>parentPhone:</b> $parentPhone<br>\n"
		// . "<b>parentEmail:</b> $parentEmail<br>\n"
		. "<b>date:</b> $date<br>\n"
		. "<b>ip:</b> $ip<br>\n";
	$subject = "Grease Wait List: $firstName $lastName";
	$headers = "From: IPTheater <{$config['info_email']}>\n"
		. "Reply-To: $firstName $lastName <$email>\n"
		. "MIME-Version: 1.0\n"
		. "Content-type: text/html; charset=iso-8859-1\n"
		. "cc: {$config['my_email']}\n"
		. "bcc: {$config['bcc_email']}";
	$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);	// Fix any bare linefeeds in the message to make it RFC821 Compliant
	$headers = preg_replace("#(?<!\r)\n#si", "\r\n", $headers); // Make sure there are no bare linefeeds in the headers
	mail($to, $subject, $message, $headers);

	//	Send an email to registrant...
	$to = $email;
	$message = "<p>Hi $firstName!<br><br>\n"
		. "We want to let you know that you are now on the official wait list for Immeasurable Productions' Summer Camp: Grease. "
		. "If a spot opens up, you will be contacted in order of your wait list number.<br><br>\n"
		. "Thank you for your interest in Immeasurable Productions' Grease!<br><br>\n"
		. "Mindy Moritz<br>\n"
		. "Immeasurable Productions<br>\n"
		. "<a href='www.IPTheater.com'>www.IPTheater.com</a><br>\n";
	$subject = "You are on the Wait List for Grease!";
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

	$formSubmittedMessage = '<h2>Thank you for filling out the form!  You have been added to the waiting list.</h2>';
}

?>
<?=$header;?>
	<section class='main'>
		<h1>Immeasurable Productions</h1>
		<h2><img src='_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions'><br>
			<img src='_img/<?=$currentShow->getAbbr();?>-logo_400.png' alt='Grease' id='<?=$currentShow->getAbbr();?>_logo'>
		</h2>
		<?=$formSubmittedMessage;?>
		<h3>Wait List Form for Grease</h3>
		<p>Please completely fill out and submit this form in order to be added to our waiting list to be contacted if a position opens up in the camp.</p>
		<hr>
		<div class='row'>
			<section class='col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3'>
				<form action='<?=$_SERVER['PHP_SELF'];?>' method='post' role='form' class='bs-condensed'>
					<div class='row'>
						<section class='col-sm-3' id="headshot">
						</section>
					</div>
					<div class='row'>
						<section class='col-xs-6'>
							<!-- <label class='sr-only'>Name:</label> -->
							<input type='text' name='firstName' id='firstName' placeholder='First Name' class='form-control' required>
						</section>
						<section class='col-xs-6'>
							<input type='text' name='lastName' id='lastName' placeholder='Last Name' class='form-control' required>
						</section>
					</div>
					<div class='row'>
						<label class='control-label col-xs-6 col-sm-2'>M/F:</label>
						<section class='col-xs-6 col-sm-2'>
							<select name='gender' class='form-control' required>
								<option value=''>&hellip;</option>
								<option value='M'>M</option>
								<option value='F'>F</option>
							</select>
						</section>
						<label class='control-label col-xs-6 col-sm-3'>Date of Birth:</label>
						<section class='col-xs-6 col-sm-5'>
							<input type='text' name='dateOfBirth' placeholder='MM/DD/YYYY' class='form-control' required>
						</section>
					</div>
					<div class='row'>
						<section class='col-sm-12'>
							<label class='sr-only'>Phone:</label><input type='text' name='phone' placeholder='Phone' class='formatter format-phone form-control' required>
							<label class='sr-only'>Email:</label><input type='text' name='email' placeholder='Email' class='form-control' required>
						</section>
					</div>
					<div class='row'>
						<label class='control-label col-xs-6'>Preferred Week:</label>
						<section class='col-xs-6'>
							<select name='chosenWeek' class='form-control' required>
								<option value='' selected>Select One...</option>
								<option value='week 1'>Week 1 (July 11-17, 2016)</option>
								<option value='week 2'>Week 2 (July 18-24, 2016)</option>
								<option value='either week'>Either</option>
							</select>
						</section>
					</div>
					<div class='row hidden'>
						<section class='col-sm-12'>
							<label class='sr-only'>Parent/Guardian:</label><input type='text' name='parentName' placeholder='Parent Name' class='form-control'>
							<label class='sr-only'>Phone:</label><input type='text' name='parentPhone' placeholder='Parent Phone' class='formatter format-phone form-control'>
							<label class='sr-only'>Email:</label><input type='text' name='parentEmail' placeholder='Parent Email' class='form-control'>
						</section>
					</div>
					<div class='row'>
						<section class='col-sm-12'>
							<label class='sr-only'>Comments:</label><textarea name='comments' placeholder='Comments' class='form-control'></textarea>
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
							<input type='submit' name='submit' value='Join the Waiting List!' onclick='return confirm("Is the form complete and accurate?")' class='btn btn-primary'>
						</section>
					</div>
				</section>
			</div>
		</form>
	</section>
<?=$footer;?>
