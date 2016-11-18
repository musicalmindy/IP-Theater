<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');

$title = "{$currentShow->getTitle()} Ticket Contest Standings!";
require_once('_inc/ipt_2.php');

$pageContent = "";

//  GET ALL THE TICKETS FOR THIS ORDER
$sql = "
	SELECT
		o.referredBy
	FROM ip_tickets t
	INNER JOIN ip_orders o ON o.id = t.orderId
	INNER JOIN ip_performances p ON p.id = t.performanceId
	WHERE o.paid = 1
	AND p.showId = {$currentShow->getId()}";
$sth = $dbh->prepare($sql);

$sth->execute();
$tickets = sthFetchObjects($sth);	//	fetch all of the tickets

if(count($tickets)) {
	$pageContent = "
		<table class='table table-bordered table-striped table-hover table-condensed tablesorter iptTable'>
			<thead>
				<tr>
					<th>Cast Member</th>
					<th># Sold</th>
				</tr>
			</thead>
			<tbody>";

	$referredNames = array();

	foreach($tickets as $t) {
		$referredBy = ucwords(strtolower(trim($t->referredBy)));
		if(array_key_exists($referredBy, $referredNames)) {
			$referredNames[$referredBy]++;
		} else {
			$referredNames[$referredBy] = 1;
		}
	}

	arsort($referredNames);

	foreach($referredNames as $name => $count) {
		$pageContent .= "
				<tr>
					<td>$name</td><td>$count</td>
				</tr>";
	}

	$pageContent .= "
			</tbody>
		</table>
		<p>Total Tickets Sold: " . count($tickets) . "</p>";
} else {
	$pageContent = "
		<h4><i class='fa fa-lg fa-frown-o'></i> No Tickets Sold <i class='fa fa-lg fa-frown-o'></i></h4>";
}

?>
<?=$header;?>
<body id='<?=$thisPageBaseName;?>'>
	<?=$topper;?>
		<?=$navbar;?>
		<div class='main'>
			<h1>Immeasurable Productions</h1>
			<h2><img src='_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions'><br>
				<img src='_img/<?=$currentShow->getAbbrLower();?>-logo_250.png' alt='<?=$currentShow->getTitle();?>' id='<?=$currentShow->getAbbrLower();?>_logo'></h2>
			<h3><?=$currentShow->getTitle();?> Tickets Contest!</h3>
			<?=$pageContent;?>
		</div>
	<?=$footer;?>
</body>
</html>

