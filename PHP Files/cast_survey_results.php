<?php
require_once('_inc/ipt_1.php');
$title = 'Immeasurable Productions Cast Survey Results';
require_once('_inc/ipt_2.php');

$currentShow = new Show(61, '13', 'thirteen');

$sql = "
	SELECT * 
	FROM ip_cast_survey 
	WHERE showId = " . $currentShow->getId() . "
	ORDER BY age DESC, id DESC";
$sth = $dbh->prepare($sql);
$sth->execute();
$rows = sthFetchObjects($sth);	//	array of rows

?>
<?=$header;?>
	<section class='main'>
		<div class='row'>
			<div class='col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2'>
				<h2><img src='_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions' class='img-responsive inline-block'><br>
					<img src='_img/<?=$currentShow->getAbbrLower();?>-logo_250.png' alt='<?=$currentShow->getTitle();?>' id='bbb_logo'></h2>
				<h3>Cast Survey Results (RAW)</h3>
				<p>NOTE: for an organized clean list, wait until most of the surveys have been completed, then ask Jeremy to make a colored spreadsheet.</p>
				<?=print_r($rows);?>
			</div>
		</div>
	</section>
<?=$footer;?>

