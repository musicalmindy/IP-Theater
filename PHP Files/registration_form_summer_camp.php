<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');
// $title = $nextShow->getTitle() . ' Registration Form';
$title = $currentShow->getTitle() . ' Registration Form';
$body_class .= ' form_page registration_form';
require_once('_inc/ipt_2.php');

// $currentShow = $nextShow;
$campSeason = 'summer';
$campRegistrationFee = 195;

$likelySpam = false;
if(apiPost('submit') && (apiPost('questions') || apiPost('referral') || apiPost('website') !== 'http://')) {	//	if any of these bot-trap (hidden) fields are filled or changed, this is almost certainly spam
	$likelySpam = true;
}

$primaryCastMinimumAge = 12;

if(apiPost('submit') && !$likelySpam) {
	$sqlQueries = array();
	$newPerson = false;	//	assume false until proven true
	$newAudition = false;	//	assume false until proven true

	$showId = $currentShow->getId();
	$type = 'cast';

	$firstName = trim(apiPost('firstName'));
	$lastName = trim(apiPost('lastName'));
	$gender = apiPost('gender');
	$chosenWeek = apiPost('chosenWeek');
	$height = (intval(apiPost('ht_ft')) * $inchesPerFoot) + intval(apiPost('ht_in'));	//	height is stored only in inches
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

	// All of the fields that don't belong in camp registration, but are in the DB anyway
	$carpool = 'N';
	$whenAudition = $chosenWeek;	//	NOTE: this is changed to ChosenWeek!
	$experience = null;
	$dance = null;
	$roles = null;
	$acceptEnsemble = '2';
	$tech = null;
	$signature = null;
	$parentSignature = null;
	$date = date('Y-m-d');

	//	check if this auditionee is already in the system...
	$sql = "
		SELECT id
		FROM ip_people
		WHERE firstName = :firstName
		AND lastName = :lastName
		LIMIT 1";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':firstName', $firstName, PDO::PARAM_STR);
	$sth->bindParam(':lastName', $lastName, PDO::PARAM_STR);
	$sth->execute();
	$personId = $sth->fetchObject()->id;
	$sqlQueries[] = '//	check if this auditionee is already in the system...' . $sql;
	$testOnly = (strtolower($firstName) === 'test' && strtolower($lastName) === 'test');	//	use the name 'Test Test' to avoid inserting into the database

	if($personId) {	//	this person already has an ID in our system, update their information
		//	first make a back-up copy of the old person data so we don't lose something important...
		$sql = "
			INSERT INTO bak_ip_people
			SELECT p.*, CURRENT_TIMESTAMP()
			FROM ip_people p
			WHERE id = :personId;";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':personId', $personId, PDO::PARAM_INT);
		$sth->execute();
		$sqlQueries[] = "//	first make a back-up copy of the old person data so we don't lose something important..." . $sql;

		//	now overwrite their old person info with this new info...
		$sql = "
			UPDATE ip_people
			SET
				type = :type,
				firstName = :firstName,
				lastName = :lastName,
				gender = :gender,
				dateOfBirth = :dateOfBirth,
				height = :height,
				email = :email,
				phone = :phone,
				address = :address,
				city = :city,
				state = :state,
				zip = :zip,
				parentFirstName = :parentFirstName,
				parentLastName = :parentLastName,
				parentPhone = :parentPhone,
				parentEmail = :parentEmail,
				ip = :ip
			WHERE id = :personId
			LIMIT 1;";	//	there will only be one, but oh well
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':type', $type, PDO::PARAM_STR);
		$sth->bindParam(':firstName', $firstName, PDO::PARAM_STR);
		$sth->bindParam(':lastName', $lastName, PDO::PARAM_STR);
		$sth->bindParam(':gender', $gender, PDO::PARAM_STR);
		$sth->bindParam(':dateOfBirth', $dateOfBirth, PDO::PARAM_STR);
		$sth->bindParam(':height', $height, PDO::PARAM_INT);
		$sth->bindParam(':email', $email, PDO::PARAM_STR);
		$sth->bindParam(':phone', $phone, PDO::PARAM_STR);
		$sth->bindParam(':address', $address, PDO::PARAM_STR);
		$sth->bindParam(':city', $city, PDO::PARAM_STR);
		$sth->bindParam(':state', $state, PDO::PARAM_STR);
		$sth->bindParam(':zip', $zip, PDO::PARAM_STR);
		$sth->bindParam(':parentFirstName', $parentFirstName, PDO::PARAM_STR);
		$sth->bindParam(':parentLastName', $parentLastName, PDO::PARAM_STR);
		$sth->bindParam(':parentPhone', $parentPhone, PDO::PARAM_STR);
		$sth->bindParam(':parentEmail', $parentEmail, PDO::PARAM_STR);
		$sth->bindParam(':ip', $ip, PDO::PARAM_STR);
		$sth->bindParam(':personId', $personId, PDO::PARAM_INT);
		$sth->execute();
		$sqlQueries[] = "//	now overwrite their old person info with this new info..." . $sql;

		//	check if their audition is already in the system...
		$sql = "
			SELECT id
			FROM ip_auditions
			WHERE personId = :personId
			AND showId = :showId
			LIMIT 1";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':personId', $personId, PDO::PARAM_INT);
		$sth->bindParam(':showId', $showId, PDO::PARAM_INT);
		$sth->execute();
		$auditionId = $sth->fetchObject()->id;
		$sqlQueries[] = "//	check if their audition is already in the system..." . $sql;

		if($auditionId) {	//	they've already filled out the form before, update their previous one
			//	first make a back-up copy of the old audition data so we don't lose something important...
			$sql = "
				INSERT INTO bak_ip_auditions
				SELECT a.*, CURRENT_TIMESTAMP()
				FROM ip_auditions a
				WHERE id = :auditionId;";
			$sth = $dbh->prepare($sql);
			$sth->bindParam(':auditionId', $auditionId, PDO::PARAM_INT);
			$sth->execute();
			$sqlQueries[] = "//	first make a back-up copy of the old audition data so we don't lose something important..." . $sql;

			//	now overwrite their old audition data with this new info...
			$sql = "
				UPDATE ip_auditions
				SET
					showId = :showId,
					personId = :personId,
					whenAudition = :whenAudition,
					height = :height,
					experience = :experience,
					dance = :dance,
					conflicts = :conflicts,
					roles = :roles,
					acceptEnsemble = :acceptEnsemble,
					tech = :tech,
					carpool = :carpool,
					signature = :signature,
					parentSignature = :parentSignature,
					comments = :comments,
					date = :date,
					ip = :ip
				WHERE id = :auditionId
				LIMIT 1";
			$sth = $dbh->prepare($sql);
			$sth->bindParam(':showId', $showId, PDO::PARAM_INT);
			$sth->bindParam(':personId', $personId, PDO::PARAM_INT);
			$sth->bindParam(':whenAudition', $whenAudition, PDO::PARAM_STR);
			$sth->bindParam(':height', $height, PDO::PARAM_INT);
			$sth->bindParam(':experience', $experience, PDO::PARAM_STR);
			$sth->bindParam(':dance', $dance, PDO::PARAM_STR);
			$sth->bindParam(':conflicts', $conflicts, PDO::PARAM_STR);
			$sth->bindParam(':roles', $roles, PDO::PARAM_STR);
			$sth->bindParam(':acceptEnsemble', $acceptEnsemble, PDO::PARAM_STR);
			$sth->bindParam(':tech', $tech, PDO::PARAM_STR);
			$sth->bindParam(':carpool', $carpool, PDO::PARAM_STR);
			$sth->bindParam(':signature', $signature, PDO::PARAM_STR);
			$sth->bindParam(':parentSignature', $parentSignature, PDO::PARAM_STR);
			$sth->bindParam(':comments', $comments, PDO::PARAM_STR);
			$sth->bindParam(':date', $date, PDO::PARAM_STR);
			$sth->bindParam(':ip', $ip, PDO::PARAM_STR);
			$sth->bindParam(':auditionId', $auditionId, PDO::PARAM_INT);
			$sth->execute();
			$sqlQueries[] = "//	now overwrite their old audition data with this new info..." . $sql;
		} else {	//	this is the first time they've filled out the audition form for this show
			$newAudition = true;
		}
	} else {	//	they are a new person
		$newPerson = true;
		$newAudition = true;
	}

	if($newPerson) {
		//	insert this new person into database and retrieve their personId (for the audition)...
		$sql = "
			INSERT INTO ip_people (
				type,
				firstName,
				lastName,
				gender,
				dateOfBirth,
				height,
				email,
				phone,
				address,
				city,
				state,
				zip,
				parentFirstName,
				parentLastName,
				parentPhone,
				parentEmail,
				dateCreated,
				ip
			) VALUES (
				:type,
				:firstName,
				:lastName,
				:gender,
				:dateOfBirth,
				:height,
				:email,
				:phone,
				:address,
				:city,
				:state,
				:zip,
				:parentFirstName,
				:parentLastName,
				:parentPhone,
				:parentEmail,
				:date,
				:ip
			);";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':type', $type, PDO::PARAM_STR);
		$sth->bindParam(':firstName', $firstName, PDO::PARAM_STR);
		$sth->bindParam(':lastName', $lastName, PDO::PARAM_STR);
		$sth->bindParam(':gender', $gender, PDO::PARAM_STR);
		$sth->bindParam(':dateOfBirth', $dateOfBirth, PDO::PARAM_STR);
		$sth->bindParam(':height', $height, PDO::PARAM_INT);
		$sth->bindParam(':email', $email, PDO::PARAM_STR);
		$sth->bindParam(':phone', $phone, PDO::PARAM_STR);
		$sth->bindParam(':address', $address, PDO::PARAM_STR);
		$sth->bindParam(':city', $city, PDO::PARAM_STR);
		$sth->bindParam(':state', $state, PDO::PARAM_STR);
		$sth->bindParam(':zip', $zip, PDO::PARAM_STR);
		$sth->bindParam(':parentFirstName', $parentFirstName, PDO::PARAM_STR);
		$sth->bindParam(':parentLastName', $parentLastName, PDO::PARAM_STR);
		$sth->bindParam(':parentPhone', $parentPhone, PDO::PARAM_STR);
		$sth->bindParam(':parentEmail', $parentEmail, PDO::PARAM_STR);
		$sth->bindParam(':date', $date, PDO::PARAM_STR);
		$sth->bindParam(':ip', $ip, PDO::PARAM_STR);

		if (!$testOnly) {
			$sth->execute();
			$personId = $dbh->lastInsertId();
		}
		$sqlQueries[] = "//	insert this new person into database and retrieve their personId (for the audition)..." . $sql;
	}

	if($newAudition) {
		//	insert this new audition into database using the known personId...
		$sql = "
			INSERT INTO ip_auditions (
				showId,
				personId,
				whenAudition,
				height,
				experience,
				dance,
				conflicts,
				roles,
				acceptEnsemble,
				tech,
				carpool,
				signature,
				parentSignature,
				comments,
				date,
				ip
			) VALUES (
				:showId,
				:personId,
				:whenAudition,
				:height,
				:experience,
				:dance,
				:conflicts,
				:roles,
				:acceptEnsemble,
				:tech,
				:carpool,
				:signature,
				:parentSignature,
				:comments,
				:date,
				:ip
			);";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':showId', $showId, PDO::PARAM_INT);
		$sth->bindParam(':personId', $personId, PDO::PARAM_INT);
		$sth->bindParam(':whenAudition', $whenAudition, PDO::PARAM_STR);
		$sth->bindParam(':height', $height, PDO::PARAM_INT);
		$sth->bindParam(':experience', $experience, PDO::PARAM_STR);
		$sth->bindParam(':dance', $dance, PDO::PARAM_STR);
		$sth->bindParam(':conflicts', $conflicts, PDO::PARAM_STR);
		$sth->bindParam(':roles', $roles, PDO::PARAM_STR);
		$sth->bindParam(':acceptEnsemble', $acceptEnsemble, PDO::PARAM_STR);
		$sth->bindParam(':tech', $tech, PDO::PARAM_STR);
		$sth->bindParam(':carpool', $carpool, PDO::PARAM_STR);
		$sth->bindParam(':signature', $signature, PDO::PARAM_STR);
		$sth->bindParam(':parentSignature', $parentSignature, PDO::PARAM_STR);
		$sth->bindParam(':comments', $comments, PDO::PARAM_STR);
		$sth->bindParam(':date', $date, PDO::PARAM_STR);
		$sth->bindParam(':ip', $ip, PDO::PARAM_STR);
		if (!$testOnly) {
			$sth->execute();
		}
		$sqlQueries[] = "//	insert this new audition into database using the known personId..." . $sql;
	}

	//	Send an email to myself...
	// $to = $config['my_email'];
	$to = $config['mindy_email'];
	$message = "<b>name:</b> $firstName $lastName<br>\n"
		. "<b>dateOfBirth:</b> $dateOfBirth (age: " . getAge($dateOfBirth) . ")<br>\n"
		. "<b>gender:</b> $gender<br>\n"
		. "<b>chosenWeek:</b> $chosenWeek<br>\n"
		. "<b>height:</b> " . floor($height / $inchesPerFoot) . "'" . ($height % $inchesPerFoot) . '"' . "<br>\n"
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
	$subject = $currentShow->getAbbrUcFirst() . " Registration: $firstName $lastName";
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
	$to = $email . ($parentEmail ? ", $parentEmail" : '');
	$message = "<p>Hi $firstName!<br><br>\n"
		. "Thank you for registering for Immeasurable Productions' $campSeason camp: {$currentShow->getTitle()}!<br><br>\n"
		. "If you have paid your camp tuition (or the $50 minimum registration deposit) then you are guaranteed a spot in the camp! Any outstanding payments will be due on the day of auditions.<br><br>\n"
		. "Congratulations!<br><br>\n"
		. "You will receive more information by email in the coming months about auditions, costumes, and rehearsals!<br><br>\n"
		. "We are so excited to work with you this $campSeason!<br><br>\n"
		. "Mindy Moritz<br>\n"
		. "Immeasurable Productions<br>\n"
		. "<a href='www.IPTheater.com'>www.IPTheater.com</a><br>\n";
	$subject = "You are registered for $campSeason camp: {$currentShow->getAbbrUcFirst()}!";
	$headers = "From: Mindy Moritz <{$config['mindy_email']}>\n"
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
	header("Location: pay.php?amount=" . (getAge($dateOfBirth) >= $primaryCastMinimumAge ? $campRegistrationFee : $campRegistrationFeeSecondary) . "&name=" . rawurlencode($parentName) . "&email={$parentEmail}&phone=" . rawurlencode($parentPhone) . "&comments={$currentShow->getAbbrUcFirst()}%20Camp%20Tuition:%20" . rawurlencode($firstName . " " . $lastName));
}

?>
<?=$header;?>
	<section class='main'>
		<h1>Immeasurable Productions</h1>
		<h2><img src='_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions'><br>
			<img src='_img/<?=$currentShow->getAbbr();?>-logo_250.png' alt='<?=$currentShow->getTitle();?>' id='<?=$currentShow->getAbbr();?>_logo'>
		</h2>
		<h3>Registration Form for <?=$currentShow->getTitle();?></h3>
		<h3>(Open only for Boys in Week 2).</h3>
		<p>Please completely fill out and submit this form.</p>
		<hr>
		<form action='<?=$_SERVER['PHP_SELF'];?>' method='post' role='form' class='bs-condensed'>
			<div class='row'>
				<section class='col-sm-3' id="headshot">
				</section>
			</div>
			<div class='row'>
				<section class='col-sm-12'>
					<div class='row'>
						<label class='control-label text-right col-sm-3'>First Name:</label>
						<section class='col-sm-9 col-lg-6'>
							<input type='text' name='firstName' id='firstName' placeholder='First Name' class='form-control' required>
						</section>
					</div>
					<div class='row'>
						<label class='control-label text-right col-sm-3'>Last Name:</label>
						<section class='col-sm-9 col-lg-6'>
							<input type='text' name='lastName' id='lastName' placeholder='Last Name' class='form-control' required>
						</section>
					</div>
					<div class='row'>
						<label class='control-label text-right col-sm-3'>Gender:</label>
						<section class='col-sm-3'>
							<select name='gender' class='form-control' required><option value=''>&hellip;</option><option value='M'>M</option><option value='F'>F</option></select>
						</section>
					</div>
					<div class='row'>
						<label class='control-label text-right col-sm-3'>Height:</label>
						<section class='col-sm-9 col-lg-6 form-inline'>
							<input type='number' name='ht_ft' min='3' max='6' class='form-control' required>ft.
							<input type='number' name='ht_in' min='0' max='11' class='form-control' required>in.
						</section>
					</div>
					<div class='row'>
						<label class='control-label text-right col-sm-3'>Date of Birth:</label>
						<section class='col-sm-9 col-lg-6 form-inline'>
							<input
								type='number' name='dobMM' data-bind='value: dobMM' placeholder='MM' min='1' max='12' class='form-control' required>/<input
								type='number' name='dobDD' data-bind='value: dobDD' placeholder='DD' min='1' max='31' class='form-control' required>/<input
								type='number' name='dobYYYY' data-bind='value: dobYYYY, valueUpdate: "afterkeydown"' placeholder='YYYY' min='<?=date('Y', strtotime('-80 years'));?>' max='<?=date('Y', strtotime('-4 years'));?>' class='form-control' required>
								<span class='weak' data-bind="text: registrantAge() < 100 ? 'age ' + registrantAge() : ''"></span>
							<input type='hidden' name='dateOfBirth' data-bind='value: dateOfBirth'>
						</section>
					</div>
					<!-- <div class='row' data-bind='visible: registrantAge() < primaryCastMinimumAge()'>
						<section class='col-sm-9 col-lg-6 col-sm-offset-3'>
							<p><strong>Secondary Ensemble Registration</strong></p>
						</section>
					</div> -->
				</section>
			</div>
			<div class='row'>
				<label class='control-label text-right col-sm-3'>Phone:</label>
				<section class='col-sm-9 col-lg-6'>
					<input type='text' name='phone' class='formatter format-phone form-control' required>
				</section>
			</div>
			<div class='row'>
				<label class='control-label text-right col-sm-3'>Email:</label>
				<section class='col-sm-9 col-lg-6'>
					<input type='text' name='email' class='form-control' required>
				</section>
			</div>
			<div class='row'>
				<label class='control-label text-right col-sm-3'>T-Shirt Size:</label>
				<section class='col-sm-3'>
					<select name='tshirt' id='tshirt' class='form-control' required>
						<option value='' selected>Select One...</option>
						<option value='S'>Adult Small</option>
						<option value='M'>Adult Medium</option>
						<option value='L'>Adult Large</option>
						<option value='XL'>Adult Extra Large</option>
					</select>
				</section>
			</div>
			<div class='row'>
				<label class='control-label text-right col-sm-3'>Chosen Camp Week:</label>
				<section class='col-sm-3'>
					<select name='chosenWeek' class='form-control' required>
						<option value='' selected>Select One...</option>
						<option value='1'>Week 1 (July 11-17, 2016)</option>
						<option value='2'>Week 2 (July 18-24, 2016)</option>
					</select>
				</section>
			</div>
			<div class='row'>
				<label class='control-label text-right col-sm-3'>Address:</label>
				<section class='col-sm-9 col-lg-6'>
					<input type='text' name='address' class='form-control'>
				</section>
			</div>
			<div class='row'>
				<label class='control-label text-right col-sm-3'>Zip:</label>
				<section class='col-sm-9 col-lg-6'>
					<input type='text' name='zip' maxlength='10' class='formatter format-zip form-control' id='zip'>
				</section>
			</div>
			<div class='row'>
				<label class='control-label text-right col-sm-3'>City:</label>
				<section class='col-sm-9 col-lg-6'>
					<input type='text' name='city' id='city' class='form-control'>
				</section>
			</div>
			<div class='row'>
				<label class='control-label text-right col-sm-3'>State:</label>
				<section class='col-sm-9 col-lg-6'>
					<select name='state' id='state' class='form-control'><?=$stateOptions;?></select>
				</section>
			</div>
			<section data-bind='visible: registrantAge() < grownUpAge()'>
				<div class='row'>
					<label class='control-label text-right col-sm-3'>Parent/Guardian:</label>
					<section class='col-sm-9 col-lg-6'>
						<input type='text' name='parentName' data-bind="attr: {'required': registrantAge() < adultAge()}" class='form-control'>
					</section>
				</div>
				<div class='row'>
					<label class='control-label text-right col-sm-3'>Parent Phone:</label>
					<section class='col-sm-9 col-lg-6'>
						<input type='text' name='parentPhone' class='formatter format-phone form-control' data-bind="attr: {'required': registrantAge() < adultAge()}">
					</section>
				</div>
				<div class='row'>
					<label class='control-label text-right col-sm-3'>Parent Email:</label>
					<section class='col-sm-9 col-lg-6'>
						<input type='text' name='parentEmail' data-bind="attr: {'required': registrantAge() < adultAge()}" class='form-control'>
					</section>
				</div>
			</section>
			<div class='row'>
				<label class='col-sm-3 label-control text-right'>
					Please list any conflicts with the rehearsal schedule and/or performances (Week 1: July 11-17 or Week 2: July 18-24):
				</label>
				<section class='col-sm-9 col-lg-6'>
					<textarea name='conflicts' class='form-control' placeholder='Conflicts'></textarea>
				</section>
			</div>
			<div class='row'>
				<label class='col-sm-3 label-control text-right'>Comments:</label>
				<section class='col-sm-9 col-lg-6'>
					<textarea name='comments' class='form-control'></textarea>
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
					<input type='submit' name='submit' value='Submit Form And Pay Camp Tuition' onclick='return confirm("Is the form complete and accurate?")' class='btn btn-primary'>
				</section>
			</div>
		</form>
	</section>
<?=$footer;?>
