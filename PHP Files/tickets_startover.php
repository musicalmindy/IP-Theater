<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');

session_start();

if(apiSession('orderId')) {
	$sql = "
		DELETE
		FROM ip_orders
		WHERE id = :id
		LIMIT 1;";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':id', apiSession('orderId'), PDO::PARAM_STR);
	$sth->execute();
}

session_destroy();	//	destroy session data
header('Location: //' . $thisDomain . '/tickets.php');

?>
