<?php
include('_inc/ipt_1.php');

if($castListIsSecret && !apiGet('nr')) {	//	'nr' = "NO REDIRECT"
	header('Location: http://' . $_SERVER['HTTP_HOST'] . '/' . $currentShow->getAbbrLower());
}

$title = 'Immeasurable Productions Cast Survey';
include('_inc/ipt_2.php');

//	SURVEY QUESTIONS
$questions = array(
	"",	//	NOTE: This is left blank because there is no Question #0
	"1. What is your age range?",
	"2. Was this your first time working with Immeasurable Productions?",
	"3. What was your overall impression of the whole experience?",
	"4. What was your <strong>favorite</strong> aspect about the week?",
	"5. What thing(s) can we improve or change to make it a better experience next year?",
	"6. How likely are you to return again for next year's musical?",
	"7. What musical(s) would you be most interested in for next year?",
	"8. Any further comments you can add about your experience with us are greatly appreciated:"
);

if(filter_input(INPUT_POST, 'submit')) {
	//	They've filled out the form and submitted it.  Now let's insert their info into the database!
	$showId = $currentShow->getId();
	$age = apiPost('age');
	$first = apiPost('first');
	$impression = (apiPost('impression') == 'Other') ? 'Other: ' . apiPost('imp_other') : apiPost('impression');
	$fav = apiPost('fav');
	$change = apiPost('change');
	$involved = apiPost('involved');
	$musical = apiPost('musical');
	$comments = apiPost('comments');
	$ip = $_SERVER['REMOTE_ADDR'];
	$date = date('Y-m-d H:i:s',strtotime('-1 hour'));	//	this accounts for our Central timezone
	//	Send a mailer to me
	$to = $config['survey_email'];
	$message = "<b>New IPT Survey Response:</b><br><br>\n"
		. $questions[1] . "<br><b>$age</b><br><br>\n"
		. $questions[2] . "<br><b>$first</b><br><br>\n"
		. $questions[3] . "<br><b>$impression</b><br><br>\n"
		. $questions[4] . "<br><b>$fav</b><br><br>\n"
		. $questions[5] . "<br><b>$change</b><br><br>\n"
		. $questions[6] . "<br><b>$involved</b><br><br>\n"
		. $questions[7] . "<br><b>$musical</b><br><br>\n"
		. $questions[8] . "<br><b>$comments</b><br><br>\n"
		. "Date: $date<br>\n"
		. "I.P.: $ip";
	$subject = "IPT Survey ($age)";
	$headers = "From: IPT Survey <" . $config['info_email'] . ">\n"
		. "Reply-To: IPT Survey <" . $config['info_email'] . ">\n"
		. "MIME-Version: 1.0\n"
		. "Content-type: text/html; charset=iso-8859-1\n"
		. "bcc: rhythmcity@gmail.com";
	$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);	// Fix any bare linefeeds in the message to make it RFC821 Compliant
	$headers = preg_replace("#(?<!\r)\n#si", "\r\n", $headers); 	// Make sure there are no bare linefeeds in the headers

	if(!isBlacklistedIP($ip)) {
		mail($to, $subject, $message, $headers);

		$sql = "
			INSERT INTO ip_cast_survey (
				`showId`,
				`age`,
				`first`,
				`impression`,
				`fav`,
				`change`,
				`involved`,
				`musical`,
				`comments`,
				`ip`,
				`date`
			) VALUES (
				:showId,
				:age,
				:first,
				:impression,
				:fav,
				:change,
				:involved,
				:musical,
				:comments,
				:ip,
				:date
			)";

		$sth = $dbh->prepare($sql);
		$sth->bindParam(':showId', $showId, PDO::PARAM_INT);
		$sth->bindParam(':age', $age, PDO::PARAM_STR);
		$sth->bindParam(':first', $first, PDO::PARAM_STR);
		$sth->bindParam(':impression', $impression, PDO::PARAM_STR);
		$sth->bindParam(':fav', $fav, PDO::PARAM_STR);
		$sth->bindParam(':change', $change, PDO::PARAM_STR);
		$sth->bindParam(':involved', $involved, PDO::PARAM_STR);
		$sth->bindParam(':musical', $musical, PDO::PARAM_STR);
		$sth->bindParam(':comments', $comments, PDO::PARAM_STR);
		$sth->bindParam(':ip', $ip, PDO::PARAM_STR);
		$sth->bindParam(':date', $date, PDO::PARAM_STR);
		$sth->execute();
	}

	$completedform = true;
}
?>

<?=$header;?>
	<section class='main'>
		<h1>Immeasurable Productions</h1>
		<h2>
			<img src='_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions'><br>
			<img src='_img/<?=$currentShow->getAbbrLower();?>-logo_250.png' alt='<?=$currentShow->getTitle();?>' id='bbb_logo'>
		</h2>
<?php
if(isset($completedform)) {
	echo("<h3>Thank you for your filling out the survey!  We appreciate you!</h3>\n");
} else {
?>
		<p>Exclusively for the cast, crew, and parents of cast members in <em><?=$currentShow->getTitle();?></em> Winter <?= date('Y'); ?></p>
		<p class='tiny'>NOTE: If you wish to elaborate on any answers, please use the "Comments" block at the bottom of the page.</p>
		<div id='form'>
			<form action='<?=$thisPage;?>' method='post'>
				<table>
					<tr class='question'><td><?=$questions[1];?></td></tr>
						<tr class='answer radio'><td>
							<input type='radio' name='age' id='age_12' value='12 &amp; under'><label for='age_12'>12 &amp; under</label><br>
							<input type='radio' name='age' id='age_13' value='13-14'><label for='age_13'>13-14</label><br>
							<input type='radio' name='age' id='age_15' value='15-16'><label for='age_15'>15-16</label><br>
							<input type='radio' name='age' id='age_17' value='17+'><label for='age_17'>17+</label><br>
							<input type='radio' name='age' id='age_parent' value='Parent'><label for='age_parent'>Parent</label></td></tr>
					<tr class='question'><td><?=$questions[2];?></td></tr>
						<tr class='answer radio'><td>
							<input type='radio' name='first' id='first_y' value='Yes'><label for='first_y'>Yes</label><br>
							<input type='radio' name='first' id='first_n' value='No'><label for='first_n'>No</label></td></tr>
					<tr class='question'><td><?=$questions[3];?></td></tr>
						<tr class='answer radio'><td>
							<input type='radio' name='impression' id='impression_loved' value='Totally loved it!  So glad I did it!'><label for='impression_loved'>Totally loved it!  So glad I did it!</label><br>
							<input type='radio' name='impression' id='impression_enjoyed' value='I enjoyed it. It was a good experience.'><label for='impression_enjoyed'>I enjoyed it. It was a good experience.</label><br>
							<input type='radio' name='impression' id='impression_okay' value='It was okay.'><label for='impression_okay'>It was okay.</label><br>
							<input type='radio' name='impression' id='impression_slept' value="Wish I would've done something else for Christmas break."><label for='impression_slept'>Wish I would've done something else for Christmas break.</label><br>
							<input type='radio' name='impression' id='impression_other' value='Other'><label for='impression_other'>Other:</label> <input type='text' name='imp_other' id='imp_other' onchange='document.getElementById("impression_other").checked=true'></td></tr>
					<tr class='question'><td><?=$questions[4];?></td></tr>
						<tr class='answer'><td><input type='text' name='fav' id='fav'></td></tr>
					<tr class='question'><td><?=$questions[5];?></td></tr>
						<tr class='answer'><td><input type='text' name='change' id='change'></td></tr>
					<tr class='question'><td><?=$questions[6];?></td></tr>
						<tr class='answer radio'><td>
							<input type='radio' name='involved' id='involved_a' value='Absolutely!'><label for='involved_a'>Absolutely!</label><br>
							<input type='radio' name='involved' id='involved_vl' value='Very likely.'><label for='involved_vl'>Very likely.</label><br>
							<input type='radio' name='involved' id='involved_p' value='Possibly.'><label for='involved_p'>Possibly.</label><br>
							<input type='radio' name='involved' id='involved_n' value='Not very likely.'><label for='involved_n'>Not very likely.</label><br>
							<input type='radio' name='involved' id='involved_d' value='Depends on the musical.'><label for='involved_d'>Depends on the musical.</label></td></tr>
					<tr class='question'><td><?=$questions[7];?></td></tr>
						<tr class='answer text'><td><input type='text' name='musical' id='musical'></td></tr>
					<tr class='question'><td><?=$questions[8];?></td></tr>
						<tr class='answer'><td><textarea name='comments' rows='6' cols='60'></textarea></td></tr>
					<tr class='question'><td><input type='submit' value='Submit' name='submit' class='btn btn-success'></td></tr>
				</table>
				<p>Thank you for your helping us serve you better!</p>
			</form>
		</div>
<?php
}	//	ending the ELSE statement above
?>
	</section>
<?=$footer;?>
