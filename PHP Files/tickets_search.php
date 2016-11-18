<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');

$title = "{$currentShow->getTitle()} Ticket Search!";
require_once('_inc/ipt_2.php');

$pageContent = "";

if(apiGet('searchBy')) {
	$whereClause = '';

	switch(apiGet('searchBy')) {
		case 'confirmationNumber':
			$whereClause .= 't.orderId = :id';
			break;
		case 'name':
			$whereClause .= 'o.name LIKE :name';
			break;
		case 'phone':
			$whereClause .= 'o.phone LIKE :phone';
			break;
		case 'email':
			$whereClause .= 'o.email = :email';
			break;
		case 'referredBy':
			$whereClause .= 'o.referredBy LIKE :referredBy';
			break;
		case 'performanceId':
			$whereClause .= 't.performanceId = :performanceId';
	}

	//  GET ALL THE TICKETS FOR THIS ORDER
	$sql = "
		SELECT
			t.id,
			t.performanceId,
			t.seatId,
			s.section,
			s.row,
			s.number,
			o.name,
			o.phone,
			o.email,
			o.confirmationNumber,
			o.mailTickets,
			o.referredBy,
			o.dateTime
		FROM ip_tickets t
		INNER JOIN ip_seats s ON t.seatId = s.id
		INNER JOIN ip_orders o ON o.id = t.orderId
		INNER JOIN ip_performances p ON t.performanceId = p.id
		WHERE o.paid = 1
		AND p.showId = {$currentShow->getId()}
		AND $whereClause
		ORDER BY t.performanceId ASC, o.dateTime ASC, t.seatId ASC";
	$sth = $dbh->prepare($sql);

	switch(apiGet('searchBy')) {
		case 'confirmationNumber':
			$sth->bindParam(':id', apiGet('textToFind'), PDO::PARAM_STR);
			break;
		case 'name':
			$likeName = '%' . apiGet('textToFind') . '%';
			$sth->bindParam(':name', $likeName, PDO::PARAM_STR);
			break;
		case 'phone':
			$likePhone = '%' . substr(apiGet('textToFind'), -4);	//	just the last 4 digits
			$sth->bindParam(':phone', $likePhone, PDO::PARAM_STR);
			break;
		case 'email':
			$sth->bindParam(':email', apiGet('textToFind'), PDO::PARAM_STR);
			break;
		case 'referredBy':
			$likeReferredBy = '%' . apiGet('textToFind') . '%';
			$sth->bindParam(':referredBy', $likeReferredBy, PDO::PARAM_STR);
			break;
		case 'performanceId':
			$sth->bindParam(':performanceId', apiGet('textToFind'), PDO::PARAM_STR);
	}

	$sth->execute();
	$tickets = sthFetchObjects($sth);	//	fetch all of the tickets

	if(count($tickets)) {
		// phpConsoleLog(json_encode($tickets));

		$pageContent = "
			<table class='table table-bordered table-striped table-hover table-condensed tablesorter'>
				<thead>
					<tr>
						<th>Section</th>
						<th>Row</th>
						<th>Seat</th>
						<th>Name</th>
						<th>Performance</th>
						<th>Ordered</th>
						<th>Referred By</th>
						<th>Confirmation #</th>
					</tr>
				</thead>
				<tbody>";

		$confirmationNumber = $tickets[0]->confirmationNumber;

		foreach($tickets as $t) {
			$separator = false;
			if($confirmationNumber !== $t->confirmationNumber) {
				$separator = true;	//	separate this order from the one before it

				$confirmationNumber = $t->confirmationNumber;
			}

			$pageContent .= "
					<tr data-seat-id='{$t->seatId}' data-ticket-id='{$t->id}'" . ($t->mailTickets ? " class='mailed" . ($separator ? " separator" : "") . "'" : ($separator ? " class='separator'" : "")) . ">
						<td>" . ($t->mailTickets ? "<i class='fa fa-lg fa-envelope-o'></i> " : "") . "{$t->section}</td>
						<td>{$t->row}</td>
						<td>{$t->number}</td>
						<td>{$t->name}</td>
						<td class='perf_{$t->performanceId}'><strong>" . circleNumber($t->performanceId) . "</strong> {$currentShow->getPerformance($t->performanceId)->getDateTimeFormatted()}</td>
						<td>" . formatDateTime($t->dateTime) . "</td>
						<td>{$t->referredBy}</td>
						<td>{$t->confirmationNumber}</td>
					</tr>";
		}

		$pageContent .= "
				</tbody>
			</table>
			<p>Total Tickets: " . count($tickets) . "</p>
			<h3><a class='btn btn-primary' href='" . $_SERVER['PHP_SELF'] . "'><i class='fa fa-lg fa-search'></i> Clear Data</a></h3>";
	} else {
		$pageContent = "
			<h4><i class='fa fa-lg fa-frown-o'></i> No orders returned.  Please adjust your search parameters and try again. <i class='fa fa-lg fa-frown-o'></i></h4>";
	}
}

$pageContent .= "
	<form action='" . $_SERVER['PHP_SELF'] . "' method='get' class='iptForm'>
		<p>
			<select name='searchBy'>
				<option value='confirmationNumber'" . (apiGet('searchBy') && apiGet('searchBy') !== 'confirmationNumber' ? "" : " selected") . ">Confirmation #</option>
				<option value='name'" . (apiGet('searchBy') === 'name' ? " selected" : "") . ">Name</option>
				<option value='phone'" . (apiGet('searchBy') === 'phone' ? " selected" : "") . ">Phone</option>
				<option value='email'" . (apiGet('searchBy') === 'email' ? " selected" : "") . ">Email</option>
				<option value='referredBy'" . (apiGet('searchBy') === 'referredBy' ? " selected" : "") . ">Referred By</option>
				<option value='performanceId'" . (apiGet('searchBy') === 'performanceId' ? " selected" : "") . ">Performance ID</option>
			</select>:
			<input type='text' name='textToFind'></p>
		<button type='submit' class='btn btn-success'><i class='fa fa-lg fa-ticket'></i> Find Tickets!</button>
	</form>";

?>
<?=$header;?>
<body id='<?=$thisPageBaseName;?>'>
	<?=$topper;?>
		<?=$navbar;?>
		<div class='main'>
			<h1>Immeasurable Productions</h1>
			<h2><img src='_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions'><br>
				<img src='_img/<?=$currentShow->getAbbrLower();?>-logo_250.png' alt='<?=$currentShow->getTitle();?>' id='<?=$currentShow->getAbbrLower();?>_logo'></h2>
			<h3><?=$currentShow->getTitle();?> Ticket Search!</h3>
			<?=$pageContent;?>
		</div>
	<?=$footer;?>
</body>
</html>

