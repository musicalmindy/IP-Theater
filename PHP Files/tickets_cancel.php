<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');

$title = "{$currentShow->getTitle()} Ticket Order Canceled";
require_once('_inc/ipt_2.php');

session_start();

if(apiSession('orderId')) {
	$sql = "
		DELETE 
		FROM ip_orders 
		WHERE id = :id
		AND paid = 0
		LIMIT 1;";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':id', apiSession('orderId'), PDO::PARAM_STR);
	$sth->execute();
}
	
session_destroy();	//	destroy session data

$pageContent = "
	<h2>Ticket Order Canceled</h2>
	<p>We're sorry to see you've decided to cancel your order.  If you would like to start the order process over from the beginning, please click the following link to <a href='tickets.php' class='btn btn-primary'><i class='fa fa-lg fa-ticket'></i> Order New {$currentShow->getTitle()} Tickets</a>.</p>";

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

