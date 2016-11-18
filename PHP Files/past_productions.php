<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');
$title = 'Past Productions';
require_once('_inc/ipt_2.php');

$earliestYearToShow = '2012';
$sql = "
	SELECT
		p.date,
		s.id,
		s.title,
		s.abbr
	FROM ip_productions p
	INNER JOIN ip_shows s ON p.showId = s.id
	WHERE p.date > '$earliestYearToShow'
	ORDER BY p.date DESC";
$sth = $dbh->prepare($sql);
$sth->execute();
$productions = sthFetchObjects($sth);	//	array of objects

$prodHTML = "";
$productionPhotos = scandir("_img/productions");
shuffle($productionPhotos);
foreach($productions as $p) {
	$prod = new Show($p->id, $p->title, $p->abbr);

	$prodHTML .= "
		<section class='prod_{$prod->getAbbr()}'>
			<div class='row'>
				<div class='col-sm-3 show_logo'>
					<img src='_img/{$prod->getAbbr()}-logo_250.png' alt=''>
				</div>
				<div class='col-sm-9 text-left'>
					<strong>{$prod->getTitle()}</strong> - <em>" . date("M Y", strtotime($p->date)) . "</em>
				</div>
				<div class='col-xs-12'>
					<hr>";

	if ($prod->getAbbr() === 'fl') {
		$prodHTML .= "
					<div class='cutout hidden-xs'>
						<img src='_img/cutouts/clayton.png' alt=''>
					</div>";
	}

	$prodHTML .= "
				</div>
			</div>
			<div class='row photos {$prod->getAbbr()} five-columns-md'>";

	foreach($productionPhotos as $img) {
		if(strpos($img, "{$prod->getAbbr()}_") === 0) {
			$prodHTML .= "
				<div class='col-sm-3 col-md-2 col-lg-2'>
					<div class='pic_spacer'>
						<img src='_img/productions/$img' alt='' class='enlarge'>
					</div>
				</div>\n";
		}
	}

	$prodHTML .= "
			</div>
		</section>\n";
}

?>
<?=$header;?>
	<section class='main'>
		<div class='row'>
			<div class='col-xs-12'>
				<h2><img src='_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions' class='img-responsive inline-block'></h2>
			</div>
		</div>
		<div class='row'>
			<div class='col-xs-12'>
				<h2>Past Productions</h2>
			</div>
		</div>
		<section class='production_photos'>
			<?=$prodHTML;?>
		</section>
	</section>
<?=$footer;?>
