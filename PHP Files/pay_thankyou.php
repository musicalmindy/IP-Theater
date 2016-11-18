<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');
$title = "Payment Complete!";
require_once('_inc/ipt_2.php');

$pageContent = "
	<h3>Payment Complete! Thank you for your business!</h3>";

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
