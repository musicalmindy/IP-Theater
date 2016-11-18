<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');
$title = "{$currentShow->getTitle()} Auditionees";
require_once('_inc/ipt_2.php');

$more = filter_input(INPUT_GET, 'more');
$withId = filter_input(INPUT_GET, 'withId');
$withAuditionNumber = filter_input(INPUT_GET, 'withAuditionNumber');
$gender = apiGet('gender');
$castOnly = false;	//	set this to true (manually) to only show auditionees who have been cast already

//	FETCH ALL AUDITIONEES
$selections = "
	p.id,
	p.firstName,
	p.lastName,
	p.dateOfBirth,
	p.gender,
	a.auditionNumber,
	a.height," . (
		$more ? "
			a.roles,
			a.experience,
			a.dance"
		: "
			a.date"
	);

$sql = "
	SELECT $selections
	FROM ip_people p
	INNER JOIN ip_auditions a ON p.id = a.personId " . (
		$castOnly ? "
			INNER JOIN ip_cast c ON c.personId = p.id
			WHERE c.showId = {$currentShow->getId()}"
		: "WHERE a.showId = {$currentShow->getId()}"
	) . (
		$gender ? "AND p.gender = '" . strtoupper($gender) . "'" : ""
	) . "
	ORDER BY a.auditionNumber, p.lastName, p.firstName;";

$sth = $dbh->prepare($sql);
$sth->execute();
$auditionees = sthFetchObjects($sth);

$auditioneesTableHeaders = "
	<th>#</th>
	<th>Name</th>
	<th>Age</th>" . (
		$more ? "
			<th>Height</th>
			<th>Roles</th>
			<th>Experience</th>
			<th>Dance</th>"
		: "
			<th>Date</th>"
	);

$auditioneesWithAuditionNumbers = array();
$auditioneesWithoutAuditionNumbers = array();
$boys = 0;
$girls = 0;

foreach($auditionees as $a) {
	$imgPrefix = "_img/auditions/";
	$showPrefix = "{$currentShow->getAbbrLower()}/{$currentShow->getAbbrLower()}";
	$imgSrc = "{$imgPrefix}{$showPrefix}_{$a->auditionNumber}.jpg";
	if(!file_exists($imgSrc)) {	//	if we don't have a pic with this audition number
		$imgSrc = "{$imgPrefix}{$showPrefix}_0{$a->auditionNumber}.jpg";	//	try zero-padding the audition number

		if(!file_exists($imgSrc)) {	//	if it still doesn't exist
			$imgSrc = "{$imgPrefix}nophoto_" . strtolower($a->gender) . ".jpg";	//	use a nophoto pic
		}
	}

	$row = "
		<tr>
			<td>" . ($withAuditionNumber ? $a->auditionNumber : ($withId ? $a->id : $a->auditionNumber)) . "</td>
			<td><img src='$imgSrc' alt='$a->firstName $a->lastName' class='img-circle'>" . ($more ? "<br>" : "") . " $a->firstName $a->lastName" . ($withId && $withAuditionNumber ? " (id: $a->id)" : "") . "</td>
			<td>" . getAge($a->dateOfBirth) . " (" . formatHeight($a->height) . ")<!-- {$a->gender} --></td>
			" . (
				$more ? "
				<td>" . formatHeight($a->height) . "</td>
				<td>$a->roles</td>
				<td>$a->experience</td>
				<td>$a->dance</td>"
				: "
				<td>$a->date</td>
				"
			) . "
		</tr>";
	if(is_null($a->auditionNumber)) {
		$auditioneesWithoutAuditionNumbers[] = $row;
	} else {
		$auditioneesWithAuditionNumbers[] = $row;
	}

	if($a->gender === 'M') {
		$boys++;
	} else {
		$girls++;
	}
}
$auditioneesTableRows = implode($auditioneesWithAuditionNumbers) . implode($auditioneesWithoutAuditionNumbers);

?>
<?=$header;?>
<body id='<?=$thisPageBaseName;?>'>
	<?=$topper;?>
		<?=$navbar;?>
		<section class='main'>
			<h1>Immeasurable Productions</h1>
			<h2><img src='_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions'><br>
				<img src='_img/<?=$currentShow->getAbbrLower();?>-logo_250.png' alt='<?=$currentShow->getTitle();?>' id='<?=$currentShow->getAbbrLower();?>_logo'></h2>
			<h3>Auditionees</h3>
			<table class='table table-bordered table-striped table-hover table-condensed tablesorter'>
				<thead>
					<tr>
						<?=$auditioneesTableHeaders;?>
					</tr>
				</thead>
				<tbody>
					<?=$auditioneesTableRows;?>
				</tbody>
			</table><!--
			<p>Total: <?=count($auditionees);?></p>
			<p>Boys: <?=$boys;?></p>
			<p>Girls: <?=$girls;?></p>-->
		</section>
	<?=$footer;?>
</body>
</html>

