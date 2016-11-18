<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');

$title = "{$currentShow->getTitle()} Ticket Order Complete!";
require_once('_inc/ipt_2.php');

session_start();
if(!apiSession('orderId')) {
	header('Location: //' . $_SERVER['SERVER_NAME'] . '/tickets_startover.php');
}

if (apiGet('ticketbooth') && apiGet('orderId')) {	//	If someone buys tickets using ticketbooth mode and pays in person...
	$orderId = apiGet('orderId');

	$sql = "
		UPDATE ip_orders
		SET paid = 1
		WHERE id = :id";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':id', $orderId, PDO::PARAM_STR);
	$sth->execute();
}

$tixCount = count(apiSession('tickets'));
$performance = $currentShow->getPerformance(apiSession('performanceId'));
$theater = $performance->getTheater();

$pageContent = "
	<h2>Ticket Order Complete! Thank you for your business!</h2>
	<h3>Confirmation Number: <strong>" . apiSession('confirmationNumber') . "</strong></h3>
	<p>
		<strong>Purchased:</strong> " . $tixCount . " Ticket" . ($tixCount > 1 ? "s" : "") . "
		to {$currentShow->getTitle()}
		at {$performance->getDateTimeFormatted()}
		at {$theater->getName()}
		({$theater->getAddressFormatted()})
	</p>
	<p>We appreciate your order.  Your ticket" . ($tixCount > 1 ? "s" : "") . " will be
		" . (apiSession('mailTickets')
		? "mailed to the address you provided: <b>" . apiSession('address') . ", " . apiSession('city') . ", " . apiSession('state') . " " . apiSession('zip') . "</b>.  Please allow up to 5 business days"
		: "held for you at the theater Will Call window.  Please present your Confirmation Number (" . apiSession('confirmationNumber') . ") when you arrive") . "
	to receive your ticket" . ($tixCount > 1 ? "s" : "") . ".  If you would like additional tickets, please click the following link to <a href='tickets.php'>order more</a>!</p>";

session_destroy();	//	destroy session data

?>
<?=$header;?>
<body id='<?=$thisPageBaseName;?>'>
	<?=$topper;?>
		<?=$navbar;?>
		<div class='main'>
			<h1>Immeasurable Productions</h1>
			<h2><img src='_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions'><br>
				<img src='_img/<?=$currentShow->getAbbrLower();?>-logo_250.png' alt='<?=$currentShow->getTitle();?>' id='<?=$currentShow->getAbbrLower();?>_logo'></h2>
			<?=$pageContent;?>
		</div>
	<?=$footer;?>
</body>
</html>

