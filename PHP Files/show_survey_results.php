<?php
require_once('_inc/ipt_1.php');
$title = 'Immeasurable Productions Show Survey Results';
require_once('_inc/ipt_2.php');

$sql = "
	SELECT DISTINCT(YEAR(`date`)) as yr
	FROM ip_show_survey
	ORDER BY yr DESC";
$sth = $dbh->prepare($sql);
$sth->execute();
$allSurveyYears = $sth->fetchAll(PDO::FETCH_COLUMN, 0);

$surveyYear = apiGet('year') ? apiGet('year') : $allSurveyYears[0];

$sql = "
	SELECT
		name,
		age,
		gender,
		choice_1,
		choice_2,
		choice_3,
		choice_4,
		comment,
		date,
		ip
	FROM ip_show_survey
	WHERE date LIKE '$surveyYear%'
	ORDER BY date DESC";
$sth = $dbh->prepare($sql);
$sth->execute();
$submissions = sthFetchObjects($sth);

$showIds = array();
$points = array(5, 4, 3, 2);	//	1st choice, 2nd, 3rd, 4th
$ipList = array();
$ipListDoubled = array();
$submissionCount = count($submissions);
$filteredCount = 0;
$filtered = apiGet('filtered', 1);	//	by default, results are filtered, use the "unfiltered" flag to turn filtering off
$detailsTable = array();

//	create the list of showIds (make sure that each show that was voted for is on the list
foreach($submissions as $sub) {
	foreach(array($sub->choice_1, $sub->choice_2, $sub->choice_3, $sub->choice_4) as $choiceId) {
		if (!in_array($choiceId, $showIds)) {
			$showIds[] = $choiceId;
		}
	}
}

$sql = "
	SELECT
		id,
		title
	FROM ip_shows
	WHERE id IN (" . implode(',', $showIds) . ")";
$sth = $dbh->prepare($sql);
$sth->execute();
$shows_unsorted = sthFetchObjects($sth);	//	array of objects

$shows = array();
foreach($shows_unsorted as $su) {
	$shows[$su->id] = new SurveyShow($su->id, $su->title);
}

function getShow($id) {
	global $shows;

	foreach($shows as $show) {
		if ($id === $show->getId()) {
			return $show;
			//break;
		}
	}
}


foreach($submissions as $sub) {
	if($filtered) {
		//	if filtered, allow 2 submissions per IP address
		if(in_array($sub->ip, $ipList)) {
			if(in_array($sub->ip, $ipListDoubled)) {	//	if it's in the doubled IP list, then skip it
				continue;
			}
			$ipListDoubled[] = $sub->ip;
		} else {
			$ipList[] = $sub->ip;
		}
	}

	//	we don't want to show "Parent"s or "Other" as male or female since they won't audition for us
	$genderIfRelevant = in_array($sub->age, array('Parent', 'Other')) ? 'U' : $sub->gender;

	//	TODO: refactor this into a function
	//	Check to see if these are on the list because of people who only vote for one show
	if(isset($shows[$sub->choice_1])) {
		$shows[$sub->choice_1]->setScore($shows[$sub->choice_1]->getScore() + $points[0]);
		$shows[$sub->choice_1]->setScoreByGender($genderIfRelevant, $shows[$sub->choice_1]->getScoreByGender($genderIfRelevant) + $points[0]);
	}
	if(isset($shows[$sub->choice_2])) {
		$shows[$sub->choice_2]->setScore($shows[$sub->choice_2]->getScore() + $points[1]);
		$shows[$sub->choice_2]->setScoreByGender($genderIfRelevant, $shows[$sub->choice_2]->getScoreByGender($genderIfRelevant) + $points[1]);
	}
	if(isset($shows[$sub->choice_3])) {
		$shows[$sub->choice_3]->setScore($shows[$sub->choice_3]->getScore() + $points[2]);
		$shows[$sub->choice_3]->setScoreByGender($genderIfRelevant, $shows[$sub->choice_3]->getScoreByGender($genderIfRelevant) + $points[2]);
	}
	if(isset($shows[$sub->choice_4])) {
		$shows[$sub->choice_4]->setScore($shows[$sub->choice_4]->getScore() + $points[3]);
		$shows[$sub->choice_4]->setScoreByGender($genderIfRelevant, $shows[$sub->choice_4]->getScoreByGender($genderIfRelevant) + $points[3]);
	}
	$filteredCount++;

	if(!($sub->choice_1 && $sub->choice_2 && $sub->choice_3 && $sub->choice_4)) {
		continue;
	}

	//	details table
	$detailsTable[] = "
		<tr>
			<td>" . date('n/j', strtotime($sub->date)) . "</td>
			<td>{$sub->name}</td>
			<td>{$sub->age}</td>
			<td>{$sub->gender}</td>
			<td>" . getShow($sub->choice_1)->getTitle() . "</td>
			<td>" . getShow($sub->choice_2)->getTitle() . "</td>
			<td>" . getShow($sub->choice_3)->getTitle() . "</td>
			<td>" . getShow($sub->choice_4)->getTitle() . "</td>
			<td>{$sub->comment}</td>
		</tr>";
}

function sortByScore($a, $b) {
	return intval($a->getScore()) < intval($b->getScore());
}
usort($shows, "sortByScore");

$highScore = 0;
$graphRows = "";
foreach($shows as $show) {
	$highScore = $show->getScore() > $highScore ? $show->getScore() : $highScore;
	$percent = ($show->getScore() / $highScore) * 100;	//	between 0 and 100
	$totalScoreByGender = $show->getScoreByGender('M') + $show->getScoreByGender('F') + $show->getScoreByGender('U');
	$percentM = $show->getScoreByGender('M') * 100 / $totalScoreByGender;
	$percentF = $percentM + ($show->getScoreByGender('F') * 100 / $totalScoreByGender);
	$colorM = '#4bf';
	$colorF = '#f69';
	$colorU = '#3b3';

	$graphRows .= "
		<tr>
			<th>" . $show->getTitle() . "</th>
			<td>
				<div class='bar' style='
					width: " . $percent . "%;
					background-image:
					linear-gradient(
						to right,
						$colorM $percentM%,
						$colorF $percentM%,
						$colorF $percentF%,
						$colorU $percentF%
    			);
				'></div>
			</td>" .
			// "<td>{$show->getScoreByGender('M')}-{$show->getScoreByGender('F')}-{$show->getScoreByGender('U')}</td>" .
		"</tr>\n";
}
$resultsGraph = "
	<table>
		<tbody>
			$graphRows
		</tbody>
	</table>";

?>
<?=$header;?>
	<section class='main'>
		<div class='row'>
			<div class='col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2'>
				<h2><img src='_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions' class='img-responsive inline-block'></h2>
				<section id="results_section">
					<h3>Results of the
						<select name='surveyYear' onchange='changeSurveyYear(<?=$filtered;?>)'>
							<?php
								foreach ($allSurveyYears as $yr) {
									echo "<option value='$yr'" . ($yr === $surveyYear ? " selected" : "") . ">$yr</option>\n";
								}
							?>
						</select>
						Show Survey</h3>

					<h4>(<?=$filtered ? 'Filtered by I.P. Address' : 'Unfiltered';?>)</h4>
					<aside>
						<button class='btn btn-primary' onclick='changeSurveyFiltering(<?=$filtered ? "0" : "1";?>, <?=$surveyYear;?>)'><i class='fa fa-align-left'></i> Change to <?=$filtered ? "un" : "";?>filtered results</button>
					</aside>
					<ul>
						<li>Total Submissions: <?=$submissionCount;?></li>
						<?=$filtered ? "<li>Filtered Submissions: $filteredCount</li>" : "";?>
					</ul>
					<?=$resultsGraph;?>
				</section>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-10 col-lg-offset-1">
				<h3><button class='btn btn-info' onclick='$("#details_section").show();$(this).hide()'><i class='fa fa-list'></i> Show Detailed Results</button></h3>
				<section id='details_section' style='display:none'>
					<h3>Detailed Results</h3>
					<table class='table table-striped'>
						<thead>
							<tr>
								<th>Date</th>
								<th>Name</th>
								<th>Age</th>
								<th>Gender</th>
								<th>1st</th>
								<th>2nd</th>
								<th>3rd</th>
								<th>4th</th>
								<th>Comment</th>
							</tr>
						</thead>
						<tbody>
							<?=join("\n", $detailsTable);?>
						</tbody>
					</table>
				</section>
			</div>
		</div>
		<div class="row past-surveys">
			<div class="col-lg-10 col-lg-offset-1">
				<h3>Other Surveys</h3>
				<div class="row">
					<?php
						foreach ($allSurveyYears as $yr) {
							if ($yr === $surveyYear) {
								continue;
							}

							echo "<div class='col-sm-6 col-md-4'>
									<img src='_img/zOld/show_survey-$yr.png' alt class='img-responsive' />
								</div>\n";
						}
					?>
				</div>
			</div>
		</div>
	</section>
<?=$footer;?>

