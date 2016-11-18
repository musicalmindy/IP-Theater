<?php
require_once '_inc/ipt_1.php';

$chosenShow = $previousShow;
if (apiGet('castInfo')) {
	$chosenShow = $currentShow;

	$sql = "
		SELECT
			c.role,
			c.group1,
			c.group2,
			c.group3,
			c.group4,
			c.featured,
			c.primaryCast,
			c.showId,
			p.dateOfBirth
		FROM ip_cast c
		INNER JOIN ip_people p ON c.personId = p.id
		WHERE p.firstName = :firstName
		AND p.lastName = :lastName
		AND c.showId = {$currentShow->getId()}
		LIMIT 1";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':firstName', apiGet('firstName'), PDO::PARAM_STR);
	$sth->bindParam(':lastName', apiGet('lastName'), PDO::PARAM_STR);
	$sth->execute();
	$castResult = $sth->fetchObject();

	// $groups = $castResult->group1 === "Leads" ? "" : $castResult->group1 . ($castResult->group2 ? ", $castResult->group2" . ($castResult->group3 ? ", $castResult->group3" : "") : "");
	$groups = $castResult->group1 === "Leads" ? "" : $castResult->group1 . ($castResult->group2 ? ", $castResult->group2" : "");	//	only 2 groups
	$explodeRole = explode('/', $castResult->role);
	$displayRole = count($explodeRole) > 1 ? $explodeRole[1] : $castResult->role;

	// $formattedRole = ($castResult->role ? "<b>{$castResult->role}</b>" . ($groups ? ", $groups" : "") : $groups);
	$formattedRole = ($castResult->featured ? "<b>{$explodeRole[0]}</b>" . ($groups ? ", $groups" : "") : $groups) . (($castResult->featured && count($explodeRole) === 1) ? "" : ($c->primaryCast ? " ({$displayRole})" : ""));
	$age = getAge($castResult->dateOfBirth);
}

$sql = "
	SELECT a.auditionNumber
	FROM ip_auditions a
	INNER JOIN ip_people p ON p.id = a.personId
	WHERE p.firstName = :firstName
	AND p.lastName = :lastName
	AND a.showId = {$chosenShow->getId()};";
$sth = $dbh->prepare($sql);
$sth->bindParam(':firstName', apiGet('firstName'), PDO::PARAM_STR);
$sth->bindParam(':lastName', apiGet('lastName'), PDO::PARAM_STR);
$sth->execute();
$auditioneeResult = $sth->fetchObject();	//	returns the first row as an object (or FALSE on failure)
$pictureFile = '';
if ($auditioneeResult) {
	$audNum = $auditioneeResult->auditionNumber;
	$pictureFile = "_img/auditions/{$chosenShow->getAbbrLower()}/{$chosenShow->getAbbrLower()}_" . (intval($audNum) < 10 ? str_pad($audNum, 2, '0', STR_PAD_LEFT) : $audNum) . '.jpg';
}

header('Content-Type: application/json');
$returnArray = array('message' => 'FAILURE');
if (file_exists($pictureFile)) {
	$returnArray['filename'] = $pictureFile;
	$returnArray['message'] = 'SUCCESS';

	if (apiGet('castInfo')) {
		$returnArray['formattedRole'] = $formattedRole;
		$returnArray['age'] = $age;
	}
}
exit(json_encode($returnArray));
?>
