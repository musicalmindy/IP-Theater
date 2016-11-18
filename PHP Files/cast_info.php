<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once '_inc/ipt_1.php';

if($castListIsSecret && !apiGet('nr')) {	//	'nr' = "NO REDIRECT"
	header('Location: http://' . $_SERVER['HTTP_HOST'] . '/' . $currentShow->getAbbrLower());
}

$title = $currentShow->getTitle() . ' Cast Information Page';
require_once '_inc/ipt_2.php';
?>
<?=$header;?>
	<section class='main'>
		<div class='row'>
			<section class='col-sm-10 col-sm-offset-1'>
				<h2><img src='_img/<?=$currentShow->getAbbrLower();?>-logo_250.png' alt='<?=$currentShow->getTitle();?>' class='img-responsive inline-block top-buffer'></h2>
				<h2>Cast Information</h2>
				<p><?=$currentShow->getTitle();?> cast, check back here occasionally for the latest info about rehearsals, costume notes, scheduling and more!</p>
			</section>
		</div>
		<!-- <div class='row'>
			<section class='col-sm-10 col-sm-offset-1'>
				<h3>CAST MEETING</h3>
				<ul>
					<li>There is a mandatory cast meeting on
						<b><?=date('l, F jS', strtotime($castMeetingStart));?> from
						<?=date('g:ia', strtotime($castMeetingStart));?> to
						<?=date('g:ia', strtotime($castMeetingEnd));?></b>.</li>
					<li>The location for this meeting will be at NEW CITY CHURCH, 7456 Nieman Rd, Shawnee, KS (same location as auditions).</li>
					<li>We will distribute notes &amp; detailed rehearsal schedules, and inform you of everything you will need to know for this production.</li>
					<li>The first HALF of your production fee is due at this meeting.  The full production fee is $150/person or $250/family (only $100/person in Wildcat Crew), so plan to pay at least half of this amount (though the full amount is preferred) at the cast meeting.  Payment may be made by check, credit card, cash, or Paypal.  This production fee payment must be made in order to remain in the cast in December.  (Note: the second half payment is due at our first rehearsal on December 26th)</li>
					<li>You will also have the opportunity to pre-order pictures and buy a <?=$currentShow->getTitle();?> cast T-shirt or Sweatshirt if you wish.</li>
					<li>If you are unable to attend the cast meeting, please send a parent or friend or parent-of-a-friend to represent you, take notes, pick up your materials, and make your payment.</li>
					<li>If you have any questions or concerns regarding the cast meeting or your availability for it, please contact our Production Coordinator, Debbie Carter (<?=disguiseMail("<a href='mailto:debbie@iptheater.com'>debbie@iptheater.com</a>");?>).</li>
				</ul>
			</section>
		</div> -->
		<div class='row'>
			<section class='col-sm-10 col-sm-offset-1'>
				<h3>CAST LIST</h3>
				<p>Check out the <a class='btn btn-danger' href='cast_list.php' title='<?=$currentShow->getTitle();?> Cast List'><i class='fa fa-lg fa-pencil-square-o'></i> <?=$currentShow->getTitle();?> Cast List</a>!</p>
			</section>
		</div>
		<div class='row'>
			<section class='col-sm-10 col-sm-offset-1'>
				<h3>BEFORE THE FIRST REHEARSAL</h3>
				<p>Every cast member should do these before the first rehearsal:</p>
				<ul>
					<li>Fill out the <a href='bio_form.php' class='btn btn-primary'><i class='fa fa-align-left'></i> Bio Form</a> online.</li>
					<li>Download the <a href='downloads/<?=$currentShow->getAbbrLower();?>%20Vocal%20Book.pdf' class='btn btn-primary'><i class='fa fa-music'></i> <?=$currentShow->getTitle();?> Vocal Book</a>, print it and put it in a binder to bring to every rehearsal.</li>
					<li>Jocks (and other boys in "Get'cha Head in the Game"), buy a basketball and <a href='https://www.youtube.com/watch?v=ZNM8e4qReno' class='btn btn-primary'><i class='fa fa-dribbble'></i> Learn to Dribble</a> like a pro.</li>
					<li>Download the <a href='downloads/<?=$currentShow->getAbbrLower();?>%20Costumes.pdf' class='btn btn-primary'><i class='fa fa-user'></i> Costume Requirements</a> here (NOTE: Every cast member is required to provide for their own base costumes [2 outfits] and bring them to the first rehearsal for approval).</li>
					<li>If you missed the <?=date('M jS', strtotime($castMeetingStart));?> Cast Meeting, be sure to download the <a href='downloads/<?=$currentShow->getAbbr();?>%20Cast%20Meeting%20Notes%20Handout.pdf' class='btn btn-primary'><i class='fa fa-file-text-o'></i> Notes Handout</a>.</li>
					<!-- <li>Though not required, we encourage each cast member to briefly review these great <a href='downloads/<?=$currentShow->getAbbrLower();?>%20Hair%20and%20Makeup%20Tips.pdf' class='btn btn-primary'><i class='fa fa-scissors'></i> Hair and Makeup Tips</a>.</li> -->
				</ul>
			</section>
		</div>
		<div class='row'>
			<section class='col-sm-10 col-sm-offset-1'>
				<h3>REHEARSAL SCHEDULE</h3>
				<p>All rehearsals and performances will take place at the Goppert Theatre at Avila University (11901 Wornall, KCMO 64145)</p>
				<h4><a href='downloads/<?=$currentShow->getAbbrLower();?>%20Rehearsal%20Schedule.pdf' target='_blank'>
					<button class='btn btn-primary'><i class='fa fa-calendar'></i> Download the Rehearsal Schedule</button><br><br>
					<img src='_img/<?=$currentShow->getAbbrLower();?>-rehearsal-schedule.png' alt class='img-responsive center'>
				</a></h4>
			</section>
		</div>
		<div class='row'>
			<section class='col-sm-10 col-sm-offset-1'>
				<h3>TICKETS</h3>
				<p>Click here to <a href='tickets.php' class='btn btn-success'><i class='fa fa-ticket'></i> Order Your Tickets</a>!</p>
			</section>
		</div>
		<div class='row'>
			<section class='col-sm-10 col-sm-offset-1'>
				<h3>Facebook</h3>
				<p><strong>Facebook Event</strong> - Invite all your Facebook friends with this <a href='https://www.facebook.com/events/1061996080501702/' target='_blank' class='btn btn-primary'><i class='fa fa-facebook-official'></i> Facebook Event link</a>!</p>
				<p><strong>Facebook Cover Photo</strong> - Right-click on either of these images to save the high resolution version to your local computer.  Then upload it as your Facebook Cover photo (it's already sized perfectly to fit).  This is another fun way to promote the show to your Facebook friends!</p>
				<div class='fb_photos'>
					<div>
						<a href='downloads/<?=$currentShow->getAbbr();?>-Facebook-Cover-Photo.jpg'>
							<img src='downloads/<?=$currentShow->getAbbr();?>-Facebook-Cover-Photo.jpg' alt='' title='Right-Click to save this image to your computer!'>
						</a>
					</div>
					<div>
						<a href='downloads/<?=$currentShow->getAbbr();?>-Facebook-Cover-Photo-yearbook.jpg'>
							<img src='downloads/<?=$currentShow->getAbbr();?>-Facebook-Cover-Photo-yearbook.jpg' alt='' title='Right-Click to save this image to your computer!'>
						</a>
					</div>
				</div>
				<h3>Download the <?=$currentShow->getTitle();?> Poster to email to friends and family!</h3>
				<a href='downloads/<?=$currentShow->getAbbr();?>%20Poster.jpg' title='<?=$currentShow->getTitle();?> Poster'>
					<img src='downloads/<?=$currentShow->getAbbr();?>%20Poster.jpg' alt class='center poster'>
				</a>
				<h4>
					<a class='btn btn-danger' href='downloads/<?=$currentShow->getAbbr();?>%20Poster.jpg' title='<?=$currentShow->getTitle();?> Poster'>
						<i class='fa fa-lg fa-bullhorn'></i>
						<?=$currentShow->getTitle();?> Poster (JPEG Image)
					</a>
					<a class='btn btn-danger' href='downloads/<?=$currentShow->getAbbr();?>%20Poster.pdf' title='<?=$currentShow->getTitle();?> Poster'>
						<i class='fa fa-lg fa-bullhorn'></i>
						<?=$currentShow->getTitle();?> Poster (Printable PDF)
					</a>
				</h4>
			</section>
		</div>
	</section>
<?=$footer;?>
