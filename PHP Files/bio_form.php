<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');

if($castListIsSecret && !apiGet('nr')) {	//	'nr' = "NO REDIRECT"
	header('Location: http://' . $_SERVER['HTTP_HOST'] . '/' . $currentShow->getAbbrLower());
}

$title = "{$currentShow->getTitle()} Bio Form";
require_once('_inc/ipt_2.php');

$likelySpam = false;
if(apiPost('submit') && (apiPost('questions') || apiPost('referral') || apiPost('website') !== 'http://')) {	//	if any of these bot-trap (hidden) fields are filled, this is almost certainly spam
	$likelySpam = true;
}

if(apiPost('submit') && !$likelySpam) {
	$sqlQueries = array();

	$firstName = apiPost('firstName');
	$lastName = apiPost('lastName');
	$bio = strip_tags(str_replace(array("\r", "\n"), ' ', apiPost('bio')));
	$ip = $_SERVER['REMOTE_ADDR'];

	//	check if this cast member is in the system...
	$sql = "
		SELECT
			c.id,
			c.role,
			c.group1,
			c.group2,
			c.group3,
			c.group4,
			c.featured,
			c.primaryCast,
			c.showId,
			p.dateOfBirth,
			p.email
		FROM ip_cast c
		INNER JOIN ip_people p ON c.personId = p.id
		WHERE p.firstName = :firstName
		AND p.lastName = :lastName
		AND c.showId = {$currentShow->getId()}
		LIMIT 1";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':firstName', $firstName, PDO::PARAM_STR);
	$sth->bindParam(':lastName', $lastName, PDO::PARAM_STR);
	$sth->execute();
	$castResult = $sth->fetchObject();
	$castId = $castResult->id;
	$sqlQueries[] = '//	check if this cast member is in the system...' . $sql;
	$testOnly = false;	//	change this value to avoid inserting into the database


	if ($castId) {
		//	check if this cast member already has an approved bio in the system...
		$sql = "
			SELECT id
			FROM ip_cast_bios
			WHERE castId = $castId
			AND approved = 1
			LIMIT 1";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$castBioIdExists = $sth->fetchObject()->id;
		$sqlQueries[] = '//	check if this cast member has a bio that is already in the system...' . $sql;
		$performances = $currentShow->getPerformances();
		$autoApproved = ($castBioIdExists || strtotime($performances[0]->getDate()) < strtotime('now')) ? 0 : 1;	//	auto-approve this if this is a first-time bio for this cast member and it's before the first performance
		//	insert this bio into database using the known $castId...
		$sql = "
			INSERT INTO ip_cast_bios (
				castId,
				bio,
				ip,
				approved
			) VALUES (
				:castId,
				:bio,
				:ip,
				$autoApproved
			);";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':castId', $castId, PDO::PARAM_INT);
		$sth->bindParam(':bio', $bio, PDO::PARAM_STR);
		$sth->bindParam(':ip', $ip, PDO::PARAM_STR);
		$sth->execute();
		$sqlQueries[] = "//	insert this new bio into database using the known castId..." . $sql;

		// $groups = $castResult->group1 === "Leads" ? "" : $castResult->group1 . ($castResult->group2 ? ", $castResult->group2" . ($castResult->group3 ? ", $castResult->group3" : "") : "");
		$groups = $castResult->group1 === "Leads" ? "" : $castResult->group1 . ($castResult->group2 ? ", $castResult->group2" : "");	//	only 2 groups
		$explodeRole = explode('/', $castResult->role);
		$displayRole = count($explodeRole) > 1 ? $explodeRole[1] : $castResult->role;

		// $formattedRole = ($castResult->role ? "<b>{$castResult->role}</b>" . ($groups ? ", $groups" : "") : $groups);
		$formattedRole = ($castResult->featured ? "<b>{$explodeRole[0]}</b>" . ($groups ? ", $groups" : "") : $groups) . (($castResult->featured && count($explodeRole) === 1) ? "" : ($c->primaryCast ? " ({$displayRole})" : ""));

		//	Send an email to myself...
		$to = $config['my_email'];
		$message = "<b>name:</b> $firstName $lastName<br>\n"
			. "<b>age:</b> " . getAge($castResult->dateOfBirth) . "<br>\n"
			. "<b>role/group:</b> $formattedRole<br>\n"
			. "<b>bio:</b> $bio<br>\n"
			. "<b>browser:</b> " . $_SERVER['HTTP_USER_AGENT'] . "<br>\n"
			. "<b>ip:</b> $ip<br>\n";
		$subject = "{$currentShow->getAbbr()} Bio: $firstName $lastName";
		$headers = "From: IPTheater <{$config['info_email']}>\n"
			. "Reply-To: $firstName $lastName <{$castResult->email}>\n"
			. "MIME-Version: 1.0\n"
			. "Content-type: text/html; charset=iso-8859-1\n"
			. "bcc: {$config['bcc_email']}";
		$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);	// Fix any bare linefeeds in the message to make it RFC821 Compliant
		$headers = preg_replace("#(?<!\r)\n#si", "\r\n", $headers); // Make sure there are no bare linefeeds in the headers
		mail($to,$subject,$message,$headers);

		foreach($sqlQueries as $sql) {
			file_put_contents('_inc/log.txt', $sql . "\n", FILE_APPEND);
		}

		//	Redirect to thank you page
		header('Location: ' . $_SERVER['PHP_SELF'] . "?formSubmitted=true&approved=$autoApproved");
	} else {
		//	Cast member is not in the system!  Redirect to error page
		header('Location: ' . $_SERVER['PHP_SELF'] . '?unfound=true');
	}
}

?>
<?=$header;?>
	<section class='main'>
		<h1>Immeasurable Productions</h1>
		<h2><img src='_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions'><br>
			<img src='_img/<?=$currentShow->getAbbrLower();?>-logo_250.png' alt='<?=$currentShow->getTitle();?>' id='<?=$currentShow->getAbbrLower();?>_logo'>
		</h2>

<?php
	if(apiGet('formSubmitted')) {
		echo "<h3>Thank you for filling out the bio form!</h3>"
			. "<p>" . (apiGet('approved') ? "You will find your bio online now" : "After it has been approved by our staff, you will find your bio online") . " at <a href='bios.php'>www.IPTheater.com/bios</a>.</p>";
	} elseif(apiGet('unfound')) {
		echo "<h3>Oops!  Your name is not found in our <a href='cast_list.php' target='_blank'>cast list</a>!</h3>
			<p>Please click the back button to check the spelling and capitalization of your name to ensure that it exactly matches our cast list.  If you feel you've reached this message in error, please copy/paste your bio in an email and send it to " . disguiseMailLink($config['info_email']) . ", and we'll upload it for you.</p>";
	} else {
?>
		<h3>Bio Form for <?=$currentShow->getTitle();?></h3>
		<p>Please complete this form in order for your bio to be included in our <a href='bios.php' class='btn btn-success'><i class='fa fa-info-circle'></i> Cast Bios page</a>!</p>
		<p></p>
		<p>
			A few possible ideas you might wish to include in your bio:&nbsp;
			<button class='btn btn-info collapsed' type='button' data-toggle='collapse' data-target='#collapseList' aria-expanded='false' aria-controls='collapseList'>
				<i class='fa fa-caret-right fa-lg fa-fw'></i>
				<i class='fa fa-caret-down fa-lg fa-fw'></i>
			</button>
		</p>
		<div class='well collapse' id='collapseList'>
			<ul>
				<li>Shows you've been a part of</li>
				<li>Hobbies you enjoy</li>
				<li>School you attend</li>
				<li>Favorite roles you've played</li>
				<li>Favorite quote or Bible verse</li>
				<li>People you wish to thank</li>
				<li>Something funny or random</li>
			</ul>
		</div>
		<hr>
		<form action='<?=$_SERVER['PHP_SELF'];?>' method='post' role='form' class='bs-condensed'>
			<div class='row'>
				<section class='col-sm-6'>
					<div class='row'>
						<section class='col-sm-6'>
							<input type='text' name='firstName' id='firstName' placeholder='First Name' class='form-control' data-bind='value: firstName' required>
						</section>
						<section class='col-sm-6'>
							<input type='text' name='lastName' id='lastName' placeholder='Last Name' class='form-control' data-bind='value: lastName' required>
						</section>
					</div>
					<div class='row top-buffer'>
						<section class='col-xs-12'>
							<textarea name='bio' rows='12' maxlength='1000' class='form-control' placeholder='Your bio goes here.  Please write in third person form (using pronouns like "she" or "him" instead of "I" or "me").' data-bind='value: bio, valueUpdate: "keypress"'></textarea>
						</section>
					</div>
				</section>
				<section class='col-sm-6'>
					<aside id='bio_preview'></aside>
					<p data-bind='text: bio'></p>
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
					<input type='submit' name='submit' value='Submit Bio' onclick='return confirm("Is your bio complete and perfect with no spelling/grammar/capitalization errors?")' class='btn btn-primary' data-bind='enable: formIsFilled()'>
				</section>
			</div>
		</form>
<?php
	}
?>
	</section>
<?=$footer;?>
