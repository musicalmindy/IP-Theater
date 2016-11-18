<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');

if($castListIsSecret && !apiGet('nr')) {	//	'nr' = "NO REDIRECT"
	header('Location: http://' . $_SERVER['HTTP_HOST'] . '/' . $currentShow->getAbbrLower());
}

$title = "{$currentShow->getTitle()} Cast Bios";
require_once('_inc/ipt_2.php');

//	FETCH ALL CAST MEMBERS, INCLUDING BIOS
$selections = "
	p.id,
	p.firstName,
	p.lastName,
	p.dateOfBirth,
	p.gender,
	b.bio,
	c.role,
	c.group1,
	c.group2,
	c.group3,
	c.group4,
	c.featured,
	c.primaryCast,
	c.showId,
	a.auditionNumber
	";

$sql = "
	SELECT $selections
	FROM ip_people p
	INNER JOIN ip_cast c ON p.id = c.personId
	INNER JOIN ip_auditions a ON p.id = a.personId
	LEFT JOIN ip_cast_bios b ON b.castId = c.id
	WHERE c.showId = {$currentShow->getId()}
	AND a.showId = {$currentShow->getId()}
	AND (
		b.approved = 1
		OR b.approved IS NULL
	)
	ORDER BY
		c.primaryCast DESC,
		p.lastName,
		p.firstName";

$sth = $dbh->prepare($sql);
$sth->execute();
$cast = sthFetchObjects($sth);

$castBios = '';

$funnyBios = array();

$performances = $currentShow->getPerformances();
if ($developerMode || strtotime($performances[0]->getDate()) < strtotime('now')) {
	$funnyBios = array(
		//	entries 2014

		"<firstName> is very proud of <possessivePronoun> organized stamp collection! Now on display at the Nelson Art Gallery!",
		"<firstName> currently holds the Midwest high school record for sending 17,621 instagrams!",
		"<firstName> is Vice-President of the Save the Orangutans Foundation.",
		"<firstName> is perfecting <possessivePronoun> impersonation of Donald Trump.",
		"<firstName> has an extensive glass unicorn collection.",
		"<firstName> is training for a hot dog eating competition.",
		"<firstName> is very skilled in building toothpick and marshmallow scale-model skyscrapers.",
		"<firstName> is a die-hard Justin Bieber fan. #belieber",
		"<firstName> is an Olympic-level dumpster diver.",
		"<firstName> recently emerged after a year of solitude; <subjectPronoun> is also very fond of easy-cheese and crackers.",
		"<firstName> has never kissed a chipmunk or painted daisies on a big red rubber ball.",
		"<firstName> would like you to know that <subjectPronoun> loves cats; <subjectPronoun> loves every kind of cat; <subjectPronoun> just wants to hug all them... But <subjectPronoun> can't.",
		"<firstName> enjoys candle-light dinners, long walks on the beach, and getting caught in the rain.",
		"<firstName> likes to belly dance and eat jolly ranchers... but not always at the same time.",
		"<firstName> is going pro with national Cupid shuffle dance competitions.",
		"<firstName> just saved 15% or more on <possessivePronoun> car insurance by switching to Geico.",
		"<firstName> would like to boast that <possessivePronoun> bedroom is currently decorated with 9,227 Christmas lights.",
		"A liger is pretty much <firstName>'s favorite animal.",
		"<firstName> is all about that bass.",
		"<firstName> was the number one contributor to Gangnam style's 2.5 billion views.",

		//	new entries 2015

		"<firstName> is still heartbroken over the loss of BingBong in Disney's \"Inside Out\".",
		"<firstName> joins us fresh from a 3-week marathon of all 32 seasons of Dr. Who.",
		"<firstName> recently wandered onto the set of Jurassic World and tamed a pterodactyl.",
		"<firstName> is building a functional Ant-Man suit, but <subjectPronoun> still has a few kinks to work out.",
		"<firstName> insists that Team Jacob will eventually win out over Team Edward.",
		"<firstName> volunteers as tribute.",
		"<firstName> was born at a very young age.",
		"<firstName> is working toward an MBA with an emphasis in Fantasy Football.",
		"<firstName> is currently looking for sponsors for <possessivePronoun> Olympic fly fishing team.",
		"<firstName> was narrowly edged out in the 4th callback by Peter Dinklage for the role of Tyrion in Game of Thrones.",
		"<firstName> wanted to go to Hitachi station to pick up some power converters.",
		"May the odds be ever in <firstName>'s favor.",
		"<firstName> comes to us straight outta Compton.",
		"<firstName> is often plagued by nightmares of New York City being terrorized by Space Invaders, Pac-Man, and Donkey Kong.",
		"<firstName> solemnly swears that <subjectPronoun>'s up to no good.",
		"<firstName> likes to dance like Uma Thurman.",
		"The cold never bothered <firstName> anyway.",
		"<firstName>'s just gonna shake shake shake shake shake shake it off; <subjectPronoun>'ll shake it off.",
		"Watch <firstName> whip, and watch <objectPronoun> nae nae.",
		"<firstName> single-handedly build a 2-story cobblestone house... only to have it blown to bits by a creeper. :(",
		"<firstName> has a blank space, baby, and <subjectPronoun>'ll write your name.",
		"<firstName> doesn't mine at night.",
		"It's the hard knock life for <firstName>.",
		"<firstName> knows about popular.",
		"<firstName> just hasn't been the same since Zayn left One Direction.",
		"<firstName> thinks that <subjectPronoun> found <reflexivePronoun> a cheerleader.",
		"<firstName> was recently seen on the news wearing a blue shirt at the Royals' World Series parade."
	);
}
shuffle($funnyBios);

foreach($cast as $c) {
	// $groups = $c->group1 === "Leads" ? "" : $c->group1 . ($c->group2 ? ", $c->group2" . ($c->group3 ? ", $c->group3" : "") : "");
	$groups = $c->group1 === "Leads" ? "" : $c->group1 . ($c->group2 ? ", $c->group2" : "");	//	only 2 groups

	$imgPrefix = "_img/auditions/";
	$showPrefix = "{$currentShow->getAbbrLower()}/{$currentShow->getAbbrLower()}";
	$imgSrc = "{$imgPrefix}{$showPrefix}_{$c->auditionNumber}.jpg";
	if(!file_exists($imgSrc)) {	//	if we don't have a pic with this audition number
		$imgSrc = "{$imgPrefix}{$showPrefix}_0{$c->auditionNumber}.jpg";	//	try zero-padding the audition number

		if(!file_exists($imgSrc)) {	//	if it still doesn't exist
			$imgSrc = "{$imgPrefix}nophoto_" . strtolower($c->gender) . ".jpg";	//	use a nophoto pic
		}
	}

	if (!$c->bio) {
		$firstName = $c->firstName;

		//	female version
		$subjectPronoun = 'she';
		$objectPronoun = 'her';
		$possessivePronoun = 'her';
		$reflexivePronoun = 'herself';

		if ($c->gender === 'M') {
			$subjectPronoun = 'he';
			$objectPronoun = 'him';
			$possessivePronoun = 'his';
			$reflexivePronoun = 'himself';
		}

		$c->bio = str_replace(array(
			'<firstName>', '<subjectPronoun>', '<objectPronoun>', '<possessivePronoun>', '<reflexivePronoun>'
		), array(
			$firstName, $subjectPronoun, $objectPronoun, $possessivePronoun, $reflexivePronoun
		), array_pop($funnyBios));
	}

	$explodeRole = explode('/', $c->role);
	$displayRole = count($explodeRole) > 1 ? $explodeRole[1] : $c->role;
	$maxShortBioLength = 200;
	$shortBio = strlen($c->bio) > $maxShortBioLength ? substr(substr($c->bio, 0, $maxShortBioLength), 0, strrpos(substr($c->bio, 0, $maxShortBioLength), ' ')) : $c->bio;

	$castBios .= "
		<section class='cast-bio col-sm-6 col-md-4 {$currentShow->getAbbr()}'>
			<div>
				<img src='$imgSrc' alt='$c->firstName $c->lastName'>
				<h5>
					<strong>$c->firstName $c->lastName</strong> (" . getAge($c->dateOfBirth) . ")<br>
					<em>" . ($c->featured ? "<b>{$explodeRole[0]}</b>" . ($groups ? ", $groups" : "") : $groups) . (($c->featured && count($explodeRole) === 1) ? "" : ($c->primaryCast ? " ({$displayRole})" : "")) . "</em>
				</h5>
				" . ($c->bio ? "
					<hr>
					" . (strlen($shortBio) < strlen($c->bio) ? "<p class='short-bio'>{$shortBio} <a class='hide-parent-on-click clickable'>[More...]</a></p>" : "") . "
					<p class='full-bio'>{$c->bio}</p>
				" : "") . "
				<div class='clearfix'></div>
			</div>
		</section>";
}

?>
<?=$header;?>
	<section class='main'>
		<h1>Immeasurable Productions</h1>
		<h2><img src='_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions'><br>
			<img src='_img/<?=$currentShow->getAbbrLower();?>-logo_250.png' alt='<?=$currentShow->getTitle();?>' id='<?=$currentShow->getAbbrLower();?>_logo'>
		</h2>
		<h3>Cast Bios</h3>
		<div class='row'>
			<?=$castBios;?>
		</div>
	</section>
<?=$footer;?>
