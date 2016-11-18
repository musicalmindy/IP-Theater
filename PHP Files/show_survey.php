<?php
require_once('_inc/ipt_1.php');
$title = 'Immeasurable Productions Show Survey';
require_once('_inc/ipt_2.php');

$sql = "
	SELECT
		id,
		title
	FROM ip_shows
	WHERE flag = 1";
$sth = $dbh->prepare($sql);
$sth->execute();

$shows = sthFetchObjects($sth);	//	fetch all of the shows and put them in an array of objects
shuffle($shows);

if(apiPost('submit')) {
	//	They've filled out the form and submitted it.  Now let's insert their info into the database!
	$age = trim(apiPost('age'));
	$gender = trim(apiPost('gender'));
	$name = trim(apiPost('name'));
	$choice_1 = apiPost('choice_1');
	$choice_2 = apiPost('choice_2');
	$choice_3 = apiPost('choice_3');
	$choice_4 = apiPost('choice_4');
	$comment = apiPost('comment');
	$date = date('Y-m-d H:i:s',strtotime('-1 hour'));	//	this accounts for our Central timezone
	$ip = $_SERVER['REMOTE_ADDR'];

	function getShow($id, $shows) {
		foreach($shows as $show) {
			if ($id === $show->id) {
				return $show;
			}
		}
	}

	$likelySpam = false;
	if(apiPost('questions') || apiPost('referral') || apiPost('website') !== 'http://') {	//	if any of these bot-trap (hidden) fields are filled, this is almost certainly spam
		$likelySpam = true;
	}

	if(!isBlacklistedIP($ip) && !$likelySpam) {
		//	Send a mailer to me
		$to = $config['survey_email'];
		$message = "<b>IPT Show Survey:</b><br><br>\n"
			. "<b>Name:</b> $name<br>\n"
			. "<b>Age:</b> $age<br>\n"
			. "<b>Gender:</b> $gender<br>\n"
			. "<b>1st Choice:</b> " . getShow($choice_1, $shows)->title . "<br>\n"
			. "<b>2nd Choice:</b> " . getShow($choice_2, $shows)->title . "<br>\n"
			. "<b>3rd Choice:</b> " . getShow($choice_3, $shows)->title . "<br>\n"
			. "<b>4th Choice:</b> " . getShow($choice_4, $shows)->title . "<br>\n"
			. "<b>Comment:</b> " . stripslashes($comment) . "<br>\n"
			. "<b>Date:</b> $date<br>\n"
			. "<b>I.P.:</b> $ip";
		$subject = "IPT Show Survey (" . getShow($choice_1, $shows)->title . ")";
		$headers = "From: IPT Survey <" . $config['survey_email'] . ">\n"
			. "Reply-To: IPT Survey <" . $config['info_email'] . ">\n"
			. "MIME-Version: 1.0\n"
			. "Content-type: text/html; charset=iso-8859-1\n"
			. "bcc: rhythmcity@gmail.com";
		$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);	// Fix any bare linefeeds in the message to make it RFC821 Compliant
		$headers = preg_replace("#(?<!\r)\n#si", "\r\n", $headers); 	// Make sure there are no bare linefeeds in the headers

		mail($to,$subject,$message,$headers);

		$sql = "
			INSERT INTO ip_show_survey (
				name,
				age,
				gender,
				choice_1,
				choice_2,
				choice_3,
				choice_4,
				comment,
				ip,
				date
			) VALUES (
				:name,
				:age,
				:gender,
				:choice_1,
				:choice_2,
				:choice_3,
				:choice_4,
				:comment,
				:ip,
				:date
			)";

		$sth = $dbh->prepare($sql);
		$sth->bindParam(':name', $name, PDO::PARAM_STR);
		$sth->bindParam(':age', $age, PDO::PARAM_STR);
		$sth->bindParam(':gender', $gender, PDO::PARAM_STR);
		$sth->bindParam(':choice_1', $choice_1, PDO::PARAM_INT);
		$sth->bindParam(':choice_2', $choice_2, PDO::PARAM_INT);
		$sth->bindParam(':choice_3', $choice_3, PDO::PARAM_INT);
		$sth->bindParam(':choice_4', $choice_4, PDO::PARAM_INT);
		$sth->bindParam(':comment', $comment, PDO::PARAM_STR);
		$sth->bindParam(':ip', $ip, PDO::PARAM_STR);
		$sth->bindParam(':date', $date, PDO::PARAM_STR);
		$sth->execute();
	}

	$completedform = true;
} else {
	$showRows = "";
	foreach($shows as $show) {
		$showRows .= "
			<tr>
				<th>{$show->title}</th>
				<td><label><input type='radio' name='choice_1' value='{$show->id}' required class='form-control'></label></td>
				<td><label><input type='radio' name='choice_2' value='{$show->id}' required class='form-control'></label></td>
				<td><label><input type='radio' name='choice_3' value='{$show->id}' required class='form-control'></label></td>
				<td><label><input type='radio' name='choice_4' value='{$show->id}' required class='form-control'></label></td>
			</tr>\n";
	}
}
?>
<?=$header;?>
	<section class='main'>
		<div class='row'>
			<div class='col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2'>
				<h2><img src='_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions' class='img-responsive inline-block'></h2>
<?php
if(isset($completedform)) {
	echo "<h3>Thank you for your filling out the survey and for your interest in Immeasurable Productions!  Check back soon to see which show is announced!</h3>\n";
} else {
?>
				<h3>Cast Your Vote for This Year's Musical!</h3>
				<p>Here are the top musicals we are currently considering for our Winter <?=date('Y');?> season (in random order). Please let us know your preferences.  Note: all input fields are optional.  If you would like to receive an email when the show is announced, include your email in the comments section, and we'll add you to our email list!</p>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2'>
				<form action='<?=$_SERVER['PHP_SELF'];?>' method='post' role='form' class='iptForm'>
					<div class='row top-buffer'>
						<div class='col-sm-4 form-label'><label>Name</label></div>
						<div class='col-sm-8'><input type='text' name='name' placeholder='' class='form-control'></div>
					</div>
					<div class='row top-buffer'>
						<div class='col-sm-4 form-label'><label>Age Range</label></div>
						<div class='col-sm-8'>
							<select name='age' class='form-control'>
								<option value=''></option>
								<option value='0-13'>13 &amp; younger</option>
								<option value='14-15'>14-15</option>
								<option value='16-17'>16-17</option>
								<option value='18+'>18+</option>
								<option value='Parent'>Parent</option>
								<option value='Other'>Other</option>
							</select>
						</div>
					</div>
					<div class='row top-buffer'>
						<div class='col-sm-4 form-label'><label>Gender</label></div>
						<div class='col-sm-8'>
							<select name='gender' class='form-control'>
								<option value=''></option>
								<option value='F'>Female</option>
								<option value='M'>Male</option>
							</select>
						</div>
					</div>
					<div class='row top-buffer'>
						<div class='col-sm-4 form-label'><label>Top Picks</label></div>
						<div class='col-sm-8'>
							<table class='table' id='musicals'>
								<thead>
									<tr>
										<th rowspan="2">Musical</th>
										<th colspan="4">Preference</th>
									</tr>
									<tr>
										<th>1st</th>
										<th>2nd</th>
										<th>3rd</th>
										<th>4th</th>
									</tr>
								</thead>
								<tbody>
									<?=$showRows;?>
								</tbody>
							</table>
						</div>
					</div>
					<div class='row top-buffer'>
						<div class='col-sm-4 form-label'><label>Comments</label></div>
						<div class='col-sm-8'><textarea name='comment' rows='3' cols='30' placeholder='' class='form-control'></textarea></div>
					</div>

					<!-- BOT-TRAP: THIS PART IS MEANT TO STOP SPAM SUBMISSIONS FROM ROBOTS (if you see this section, don't change these fields) -->
						<p class='bot-trap'><label>Robot Questions</label><textarea name='questions' rows='6' cols='40'></textarea></p>
						<p class='bot-trap'><label>Robot Referral</label><input type='text' name='referral' value=''></p>
						<p class='bot-trap'><label>Robot Website</label><input type='text' name='website' value='http://'></p>
					<!-- /BOT-TRAP -->

					<div class='row top-buffer'>
						<div class='col-sm-4 form-label'></div>
						<div class='col-sm-8'>
							<button class='btn btn-primary'><i class='fa fa-lg fa-check-square-o'></i> Cast Your Votes!</button>
							<input type='hidden' name='submit' value='form-submitted'>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class='row'>
			<div class='col-xs-12'>
				<p>Thank you for your helping us serve you better!</p>
			</div>
		</div>
<?php
}	//	ending the ELSE statement above
?>

	</section>
<?=$footer;?>
