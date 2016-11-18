<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');
$title = 'Immeasurable Productions';
require_once('_inc/ipt_2.php');
?>

<?=$header;?>
	<section class='main'>
		<div class='row'>
			<div class='col-xs-12'>
				<h2><img src='_img/ip-logo-long_900_trans.png' alt='Immeasurable Productions' id='logo' class='img-responsive inline-block'></h2>
				<!-- <div class='animatedParent'>
					<h2 class='animated bounceInDown'><img src='_img/ip-logo-long_900_trans.png' alt='Immeasurable Productions' class='img-responsive inline-block'></h2>
				</div> -->
			</div>
		</div>
		<div class='row' id='carlee-with-bear'>
			<div class='col-sm-10 col-sm-offset-1' id='wesley'>

				<fieldset class='statement'>
					<legend>Change in Leadership Statement</legend>
					<p>Dear Friends,</p>
					<p>I would like to communicate my gratitude for all of the people who have been involved with Immeasurable Productions over the years.  Building this company with my sister Mindy has been an extremely rewarding experience that would not have been possible without the support of so many valued friends.</p>
					<p>Although I have been the sole owner of Immeasurable Productions, I've long believed that there would come an appropriate time to "pass the torch" of ownership to Mindy to lead the company on her own.</p>
					<p>That time has come. After lengthy discussion and valuable input from our dear friends on both sides of the issue, Mindy and I recognize that we hold differing viewpoints on a key matter of company policy.  Given the situation, I feel that the best course of action is for me to step down from my position of leadership at Immeasurable Productions. Being the new sole owner will allow Mindy the freedom and opportunity to run the organization in the way that she considers most fitting, and perhaps more in sync with the expressed wishes of our customer base.</p>

					<!--<p>That time has come. I have decided to step down from my position of leadership at Immeasurable Productions. Being the new sole owner will allow Mindy the freedom and opportunity to run the organization in the way that she considers most fitting and in sync with the expressed wishes of our customer base.</p>-->

					<p>Many of you have worked with Mindy and know well that she is an excellent, capable leader who has earned the admiration and trust of the Kansas City theater community for decades.  I support Mindy in this new role, and I look forward to cheering from the sidelines as she carries the company forward.</p>
					<p>Sincerely,</p>
					<p>Jeremy Moritz<br>
						Immeasurable Productions</p>
				</fieldset>

				<h3>Welcome to IPTheater.com&mdash;the official website for Immeasurable Productions!</h3>
				<p>Immeasurable Productions is a family-owned-and-operated production company providing wholesome, high-quality musical theatre for teenagers and young adults in the Kansas City area.</p>

				<!-- <p class='center text-center'><a href='audition_workshop.php' class='btn btn-warning'><i class='fa fa-star-o'></i> Check out our upcoming Audition Workshop! <i class='fa fa-star-o'></i></a></p> -->
				<!-- <p>We have a history of producing excellent shows every winter, but this year, we are excited to announce our first-ever Summer Camp!</p>
				<a href='thirteen'><img src='_img/thirteen-logo_250.png' alt='13' class='img-responsive inline-block top-buffer center'></a>
				<h4>Music by: Jason Robert Brown<br>
					Book by: Dan Elish and Robert Horn<br>
					Director/Choreographer: Mindy Moritz<br>
					Vocal Director: Nick Perry<br>
					Assistant Director: Jenn Harvey</h4>
				<h4><a href='thirteen' class='btn btn-success'><i class='fa fa-comment-o'></i> Click here for more info!</a></h4>
				<hr> -->

				<?php
					// if(!$castListIsSecret || apiGet('nr')) {
					// 	echo "<p>We are pleased to announce the cast list for " . $currentShow->getTitle() . "!</p>
					// 		<h4><a class='btn btn-danger' href='cast_list_joseph.php'><i class='fa fa-list-alt'></i> " . $currentShow->getTitle() . " Cast List</a></h4>
					// 		<hr>";
					// }
				?>

				<p>Several times each year, we produce a fun, high-quality musical within a one-week rehearsal schedule.
					We recently finished a run of <em><?=$previousShow->getTitle();?><!-- </em>&mdash;<a href='past_productions.php' class='btn btn-info'><i class='fa fa-camera'></i> Pictures Here</a> -->.  Thank you to all who were involved in making this show a success!
				</p>

				<p>This <?=$currentShow->getSeason();?>, we are excited to be producing...</p>
				<h3>
					<a href='<?=$currentShow->getAbbr();?>.php'>
						<img src='_img/<?=$currentShow->getAbbr();?>-logo_400.png' alt='<?=$currentShow->getTitle();?>' class='img-responsive center'>
					</a>
				</h3>
				<h3>
					<a href='<?=$currentShow->getAbbr();?>.php' class='btn btn-success'>
						<i class='fa fa-music'></i> Click Here to Learn More!
						<i class='fa fa-music'></i>
					</a>
				</h3>
				<!-- <h3><a href='past_productions.php'><img src='downloads/<?=$previousShow->getAbbr();?>%20Poster.jpg' alt='<?=$previousShow->getTitle();?>' id='<?=$previousShow->getAbbr();?>_logo'></a></h3> -->

				<!-- <p>Check out our first annual spring break musical camp: March 14th-19th AND March 21st-26th, 2016!</p>
				<h3><a href='joseph.php'><img src='_img/joseph-logo_400.png' alt='Joseph' class='img-responsive center'></a></h3> -->
			</div>
		</div>
		<!--
		<div class='row'>
			<div class='col-sm-10 col-sm-offset-1'>
				<p>If you are interested in one of Immeasurable Productions' original musicals, just follow the links below to check out the official sites for <a href='http://rhythmcity.org'>Rhythm City</a> and <a href='http://rhythmcityjunior.com'>Rhythm City Junior</a>!</p>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm-4 col-sm-offset-2 text-center'>
				<a href='http://rhythmcity.org'><img src='_img/rc-logo_250.png' alt='Rhythm City' class='logos shows' id='rc_logo'><br>Rhythm City</a>
			</div>
			<div class='col-sm-4 text-center'>
				<a href='http://rhythmcityjunior.com'><img src='_img/rcjr-logo_250.png' alt='Rhythm City Junior' class='logos shows' id='rcjr_logo'><br>Rhythm City Junior</a>
			</div>
		</div>
		-->
		<section class='box-of-theaters'>
			<div class='row'>
				<div class='col-sm-10 col-sm-offset-1'>
					<p>Although Immeasurable Productions is not directly affiliated with any other theatre company, we encourage you to support the following companies, which also provide wholesome, family-oriented theatre in Kansas City:</p>
				</div>
			</div>
			<div class='row'>
				<div class='col-md-10 col-md-offset-1'>
					<div class='row'>
						<div class='col-sm-4 logos3'>
							<a href='http://culturehouse.com' target='_blank' rel='nofollow'><img src='_img/tch.png' alt='Culture House' class='logos'><br>The Culture House</a>
						</div>
						<div class='col-sm-4 logos3'>
							<a href='http://www.act1kc.com' target='_blank' rel='nofollow'><img src='_img/act1.png' alt='Act One' class='logos'><br>Act One</a>
						</div>
						<div class='col-sm-4 logos3'>
							<a href='http://www.stagerightperformingarts.com' target='_blank' rel='nofollow'><img src='_img/stage-right.png' alt='Stage Right' class='logos'><br>Stage Right</a>
						</div>
					</div>
				</div>
			</div>
		</section>
		<div class='row'>
			<div class='col-sm-12' id="girls-on-ledge">
				<h3>Thank you for your support!</h3>
			</div>
		</div>
	</section>
<?=$footer;?>
