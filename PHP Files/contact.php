<?php	//	IPTheater.com
require_once('_inc/ipt_1.php');
$title = 'Immeasurable Productions Contact Form';
require_once('_inc/ipt_2.php');

$completedform = false;
if(filter_input(INPUT_POST, 'submit')) {
	//	They've filled out the form and submitted it.  Now let's insert their info into the database!
	$name = apiPost('name');
	$email = strtolower(apiPost('email'));
	$comments = apiPost('comments');
	$ip = $_SERVER['REMOTE_ADDR'];
	// $ip = $_SERVER['REMOTE_ADDR'];

	$date = date('Y-m-d H:i:s',strtotime('-1 hour'));	//	this accounts for our Central timezone

	//	Send a mailer to davey
		//	first adjust these variables to make sure there's no funnybusiness
	$name = get_magic_quotes_gpc() ? $name : addslashes($name);
	$email = isValidEmail($email) ? $email : 'INVALID_EMAIL (' . addslashes($email) . ')';
	$comments = get_magic_quotes_gpc() ? $comments : addslashes($comments);

	$to = $config['info_email'];
	$message = "<b>IP Web Contact:</b><br><br>\n"
		. "Name/Email: $name &lt;$email&gt;;<br>\n"
		. "Comments: $comments<br>\n"
		. "Date: $date<br>\n"
		. "I.P.: $ip";
	$subject = "IP Web Contact: $name";
	$headers = "From: IPTheater.com <" . $config['info_email'] . ">\n"
		. "Reply-To: $name <$email>\n"
		. "MIME-Version: 1.0\n"
		. "Content-type: text/html; charset=iso-8859-1\n"
		. "bcc: {$config['bcc_email']}";
	$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);	// Fix any bare linefeeds in the message to make it RFC821 Compliant
	$headers = preg_replace('#(?<!\r)\n#si', "\r\n", $headers); 	// Make sure there are no bare linefeeds in the headers

	$likelySpam = false;
	if(apiPost('questions') || apiPost('referral') || apiPost('website') !== 'http://') {	//	if any of these bot-trap (hidden) fields are filled, this is almost certainly spam
		$likelySpam = true;
	}

	if(!$likelySpam) {	//	avoid getting blank/spam emails
		mail($to,$subject,$message,$headers);

		//	send an autoresponder email
		$to = $email;
		$message = "Thank you for contacting us!<br><br>\n"
			. "You've been added to our email list, so you will be updated occasionally with the latest information about upcoming auditions and performances!<br><br>\n"
			. "Thanks for your interest in Immeasurable Productions!<br><br>\n"
			. "Jeremy Moritz<br>\n"
			. "Immeasurable Productions<br>\n"
			. "<a href='http://www.iptheater.com'>www.IPTheater.com</a>";
		$subject = "Thank you for your interest in Immeasurable Productions!";
		$headers = "From: IPTheater.com <" . $config['info_email'] . ">\n"
			. "Reply-To: IPTheater.com <" . $config['info_email'] . ">\n"
			. "MIME-Version: 1.0\n"
			. "Content-type: text/html; charset=iso-8859-1\n"
			. "bcc: {$config['bcc_email']}";
		$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);	// Fix any bare linefeeds in the message to make it RFC821 Compliant
		$headers = preg_replace('#(?<!\r)\n#si', "\r\n", $headers); 	// Make sure there are no bare linefeeds in the headers

		mail($to,$subject,$message,$headers);
	}

	$completedform = true;
}
?>
<?=$header;?>
	<section class='main'>
		<div class='row'>
			<div class='col-xs-12'>
				<h2><img src='_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions' class='img-responsive inline-block'></h2>
			</div>
		</div>
		<div class='row'>
			<div class='col-xs-12'>
				<h2>Contact Us</h2>
			</div>
		</div>
<?php
if($completedform) {
?>
		<div class='row'>
			<div class='col-sm-6 col-md-5 col-lg-6' id='form'>
				<h3>Thank you for your interest in <em>Immeasurable Productions</em>!</h3>
				<h3>We really appreciate it.</h3>
<?php
} else {
?>
		<div class='row'>
			<div class='col-sm-10 col-sm-offset-1 col-lg-8 col-lg-offset-2'>
				<h2>Fill out this form to be added to our email list to receive the latest updates from <em>Immeasurable Productions</em>!</h2>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm-7 col-md-6 col-md-offset-1'>
				<form action='<?=$_SERVER['PHP_SELF'];?>' method='post' role='form' class='iptForm'>
					<div class='row top-buffer'>
						<div class='col-sm-5 col-md-4 col-lg-3 form-label'><label>Name</label></div>
						<div class='col-sm-7 col-md-8 col-lg-9'><input type='text' name='name' class='form-control'></div>
					</div>
					<div class='row top-buffer'>
						<div class='col-sm-5 col-md-4 col-lg-3 form-label'><label>Email</label></div>
						<div class='col-sm-7 col-md-8 col-lg-9'><input type='text' name='email' class='form-control'></div>
					</div>
					<div class='row top-buffer'>
						<div class='col-sm-5 col-md-4 col-lg-3 form-label'><label>Comments or Questions</label></div>
						<div class='col-sm-7 col-md-8 col-lg-9'><textarea name='comments' rows='6' cols='23' class='form-control' placeholder='Add me to the email list!'></textarea></div>
					</div>

					<!-- BOT-TRAP: THIS PART IS MEANT TO STOP SPAM SUBMISSIONS FROM ROBOTS (if you see this section, don't change these fields) -->
						<p class='bot-trap'><label>Robot Questions</label><textarea name='questions' rows='6' cols='40'></textarea></p>
						<p class='bot-trap'><label>Robot Referral</label><input type='text' name='referral' value=''></p>
						<p class='bot-trap'><label>Robot Website</label><input type='text' name='website' value='http://'></p>
					<!-- /BOT-TRAP -->

					<div class='row top-buffer'>
						<div class='col-sm-7 col-md-8 col-lg-9 col-sm-offset-5 col-md-offset-4 col-lg-offset-3'><input type='submit' value='Send' name='submit' class='btn btn-primary form-control'></div>
					</div>
				</form>
<?php
}
?>
			</div>
			<div class='col-sm-5 col-md-4'>
				<address>
					<h3 class='text-left'>You're also welcome to contact us directly.</h3>
					<p><strong>Phone:</strong> <span>
<?php
if(isMobile()) {
	echo '<script>document.write("<a href=\'"+"te"+"l:+1"+"816"+"86674"+"89\' title=\'Give us a call!\'>(816"+") 866-"+"7489</a>");</script>';
} else {
	echo '<script>document.write("<a href=\'"+"cal"+"lto"+"://+1"+"81686"+"67489\' title=\'Give us a call!\'>(8"+"16) 866-7"+"489</a>");</script>';
}
?>
					</span><br>
					<strong>Email:</strong> <em><?=disguiseMail("<a href='mailto:" . $config['info_email'] . "'>" . $config['info_email'] . "</a>");?></em><br>
					<strong>Postal Mail:</strong><br>
					5502 Oakview<br>
					Shawnee, KS 66216</p>
				</address>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm-10 col-sm-offset-1'>
				<p><small>Please Note: Your information will be kept strictly confidential.</small></p>
			</div>
		</div>
	</section>
<?=$footer;?>
