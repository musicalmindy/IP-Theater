<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');

if($castListIsSecret && !apiGet('nr')) {	//	'nr' = "NO REDIRECT"
	header('Location: http://' . $_SERVER['HTTP_HOST'] . '/' . $currentShow->getAbbrLower());
}

$title = "{$currentShow->getTitle()} Cast List";
require_once('_inc/ipt_2.php');

//	FETCH ALL CAST MEMBERS
$selections = "
	p.id,
	p.firstName,
	p.lastName,
	p.dateOfBirth,
	p.gender,
	c.role,
	c.group1,
	c.group2,
	c.group3,
	c.group4,
	c.featured,
	c.primaryCast,
	a.auditionNumber
	";

$sql = "
	SELECT $selections
	FROM ip_people p
	INNER JOIN ip_cast c ON p.id = c.personId
	INNER JOIN ip_auditions a ON p.id = a.personId
	WHERE c.showId = {$currentShow->getId()}
	AND a.showId = {$currentShow->getId()}
	ORDER BY c.primaryCast DESC, p.lastName, p.firstName";

$sth = $dbh->prepare($sql);
$sth->execute();
$cast = sthFetchObjects($sth);

$castTableHeaders = "
	<th>Name</th>
	<th>Role(s)</th>";
$castTableRows = "";

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

	$explodeRole = explode('/', $c->role);
	$displayRole = count($explodeRole) > 1 ? $explodeRole[1] : $c->role;

	$castTableRows .= "
		<tr data-person-id='$c->id' data-person-name='$c->firstName $c->lastName' data-age='" . getAge($c->dateOfBirth) . "'>
			<td><img src='$imgSrc' alt='$c->firstName $c->lastName' class='img-circle'> $c->firstName $c->lastName</td>
			<td>" . ($c->featured ? "<b>{$explodeRole[0]}</b>" . ($groups ? ", $groups" : "") : $groups) . (($c->featured && count($explodeRole) === 1) ? "" : ($c->primaryCast ? " ({$displayRole})" : "")) . "</td>
		</tr>";
}

?>
<?=$header;?>
<body id='<?=$thisPageBaseName;?>'>
	<?=$topper;?>
		<?=$navbar;?>
		<section class='main'>
			<h1>Immeasurable Productions</h1>
			<h2><img src='_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions'><br>
				<img src='_img/<?=$currentShow->getAbbrLower();?>-logo_250.png' alt='<?=$currentShow->getTitle();?>' id='<?=$currentShow->getAbbrLower();?>_logo'></h2>
			<p>Thank you so much to all of our auditionees!  If you did not make the list this time, please consider auditioning for us again in the future.  Remember that every production has different demands, and many of our cast members did not make the show the first time they auditioned.  That's just show business.  Don't give up!</p>
			<p>If you were cast in the show, congratulations!  We are excited to get to know each and every one of you and work together on this great production!  Please make special note of our mandatory first cast/parent meeting: <?=date('l, F jS, g:ia', strtotime($castMeetingStart));?> - <?=date('g:ia', strtotime($castMeetingEnd));?>.  Details are on our <a href='cast_info.php' class='btn btn-info'><i class='fa fa-info-circle'></i> <?=$currentShow->getTitle();?> Cast Info Page</a>.</p>
			<p>Please note that every cast member has a named track.  If your character name appears in bold, it is a named role in the script (usually with lines, though not always).  If your character name appears in parentheses, this denotes an ensemble track.</p>
			<!-- <p class='strong'>TWO IMPORTANT NOTES FOR ALL CAST MEMBERS TO READ:</p>
			<ol>
				<li>Every cast member (regardless of role) has the opportunity to tap dance in our opening number!  Simply put, if you learn the choreography, you will be in the dance!  If you wish to perform in this number, please check out our <a href='cast_info.php' class='btn btn-warning'>Cast Information Page</a> to download the choreography notes, watch the instructional videos, and learn the dance before rehearsals start on December 26th!</li>
				<li>Each of the four non-dancing roles was double-cast in order to allow all of our performers the chance to utilize and develop their dancing talents in this show:
					<ul class='no-bullet'>
						<li>* Denotes a role that will be performed on Dec 31st Evening, Jan 2nd Evening, and Jan 3rd Matinee.</li>
						<li>** Denotes a role that will be performed on Jan 1st Matinee, Jan 1st Evening, and Jan 3rd Evening.</li>
					</ul>
				</li>
			</ol> -->
			<p>One last thing: <a href='tickets.php' class='btn btn-success'><i class='fa fa-ticket'></i> Tickets are on sale now</a>, so you can go ahead and reserve the best seats in the house!</p>
			<h3><?=$currentShow->getTitle();?> Cast List</h3>
			<table class='table table-bordered table-striped table-hover table-condensed tablesorter'>
				<thead>
					<tr>
						<?=$castTableHeaders;?>
					</tr>
				</thead>
				<tbody>
					<?=$castTableRows;?>
				</tbody>
			</table>
			<!--<p>Cast Size: <?=count($cast);?></p>-->
		</section>
	<?=$footer;?>
</body>
</html>

