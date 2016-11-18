<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once '_inc/ipt_1.php';
$title = 'Grease Information Page';
$body_class .= ' show_info';
require_once '_inc/ipt_2.php';

// $currentShow = $nextShow;
$campSeason = $currentShow->getSeason();
$campRegistrationFee = 195;
?>
<?=$header;?>
	<section class='main'>
		<div class='row'>
			<div class='col-xs-12'>
				<h2>
					<img src='/_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions' class='img-responsive inline-block'>
					<br>Immeasurable Productions is offering its 2nd Annual Summer Camp!
					<br>This summer, we will produce the musical Grease: Student Edition!<br>
					<img src='_img/<?=$currentShow->getAbbr();?>-logo_400.png' alt='<?=$currentShow->getTitle();?>' class='img-responsive inline-block top-buffer'>
				</h2>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm-10 col-sm-offset-1'>
				<h2>
					Registration Open!
				</h2>
				<p>Ages 12-19</p>
				<p>Camp Tuition: $195</p>
				<p>Includes a camp T-shirt, pizza party, and a guaranteed place in the cast of this awesome, high energy show!</p>
				<h2>GREASE IS NOW CLOSED (except for boys in week 2).  If you would like to join the wait list, please click the button below:</h2>
				<h2><a class='btn btn-primary' href='wait_list.php' title='Join the Wait List!'><i class='fa fa-list-alt'></i> Grease Wait List</a></h2>
				<h2><a class='btn btn-success' href='registration_form_summer_camp.php' title='Fill out the Registration Form!'><i class='fa fa-lg fa-pencil-square-o'></i> <?=$currentShow->getTitle();?> Camp Registration Form</a></h2>
				<p>Grease is a fun musical set in the 50’s. It follows a gang of playfully rude high school boys led by Danny Zuko, the eccentric Pink Ladies, and Rydell High School’s newest good girl Sandy Dumbrowski. The Student Edition has all but one of the fun, memorable songs of Grease but also features cleaner dialogue and lyrics while still capturing the excitement, heart, and story of this memorable classic.</p>
				<p>The entire, full musical camp will be offered two different times:</p>
				<p><strong>WHEN:</strong><br>
					WEEK 1: July 11-17<br>
					OR<br>
					WEEK 2: July 18-24</p>
				<!-- <p><strong>Registration will begin soon!</strong>  <a href='contact.php'>Join our mailing list</a> to be notified as soon as registration is open!</p> -->
				<p><strong>Daily Schedule:</strong><br>
					Monday-Thursday: 9am-5pm* <br>
					Friday: 9am-5pm<br>
					1st Performance: Friday at 5pm<br>
					2nd Performance: Friday at 8pm<br>
					3rd Performance: Saturday at 2pm<br>
					4th Performance: Saturday at 8pm<br>
					5th Performance: Sunday at 1pm</p>
				<p>*Certain cast members may be asked to stay until 6:30pm on these days</p>
				<p>There will be a cast party in between shows on Saturday, and a required strike after the Sunday performance, so plan to stay for those events.</p>
				<p>There are only 45 spots available for each camp, so sign up quickly if interested. Camp tuition is $195, to be paid when registered. Payment plans and scholarships may be considered if there is need, but at least a $50 deposit is required to hold your camper's spot in the camp upon registration. Any remaining funds will be due at the time of auditions. You will receive a confirmation email upon registering, then more information will be emailed at a later date.</p>
				<h3>CAMP AUDITION INFORMATION:</h3>
				<p>Since we will be producing this show in one week, auditions will be held prior to camp to select leading/supporting roles. If you wish to be considered for one of these roles, you must audition.</p>
				<p><!-- It will be expected that each leading cast member arrives with individual lines memorized.  -->The auditions will be held on Friday, May 27th  from 9am-12:30pm, and Callbacks will be from 1pm-5pm. (Location to be determined)</p>
				<p>If you cannot attend the audition day (but still wish to be considered for a lead role), please send a video by May 26th.</p>
				<p>For auditions, you will need to prepare a 30-45 second song with background music on CD, smartphone, or mp3 player. After singing, you will be asked to learn a short dance combination. If you are asked to stay for callbacks, they will begin later in the afternoon. Callback materials will be available on the website about three weeks before auditions. (More specific audition information will be emailed to you at a later date if you are registered for camp.)</p>
				<p><strong>WHERE:</strong> All rehearsals and performances take place at Olathe Civic Theatre Association (OCTA) - <span>500 E. Loula Rd., Olathe, KS. 66061</span></p>
				<p>There are only 45 spots available for each week and this camp WILL fill up so don’t delay!</p>
				<h2>Camp is now closed (except for boys in week 2).  You're welcome to join the <a class='btn btn-primary' href='wait_list.php' title='Join the Wait List!'><i class='fa fa-list-alt'></i> Grease Wait List</a>.</h2>
				<h2><a class='btn btn-success' href='registration_form_summer_camp.php' title='Fill out the Registration Form!'><i class='fa fa-lg fa-pencil-square-o'></i> <?=$currentShow->getTitle();?> Camp Registration Form</a></h2>
				<p><?=$currentShow->getTitle();?> Staff:<br>
					Director/Choreographer: Mindy Moritz<br>
					Music Director: Nick Perry<br>
					(*Additional staff members TBA)</p>
				<div class='row text-center'>
					<div class='col-sm-12'>
						<img src='_img/<?=$currentShow->getAbbr();?>-stage-photo.png' alt='' class='img-responsive'>
					</div>
				</div>
				<div class='cutout hidden-xs'>
					<img src='_img/cutouts/maggie-christian.png' alt=''>
				</div>
			</div>
		</div>
	</section>
<?=$footer;?>
