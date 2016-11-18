<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');
$title = "Payment Canceled";
require_once('_inc/ipt_2.php');

$pageContent = "
	<h3>Payment Canceled.</h3>
	<p>We're sorry to see you've decided to cancel your payment.  If you would like to start the payment process over from the beginning, please click the following link to <a href='pay.php' class='btn btn-primary'><i class='fa fa-lg fa-money'></i> Make your payment</a>.</p>";

session_start();
session_destroy();	//	destroy session data
?>
<?=$header;?>
	<section class='main'>
		<div class='row'>
			<div class='col-xs-12'>
				<h2><img src='/_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions' class='img-responsive inline-block'></h2>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm-8 col-sm-offset-2'>
				<?=$pageContent;?>
			</div>
		</div>
	</section>
<?=$footer;?>
