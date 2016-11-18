<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');

// if ($currentShow->getSeason() !== 'winter' && !$developerMode) {
// 	header('Location: http://' . $_SERVER['HTTP_HOST'] . '/' . $currentShow->getAbbrLower());
// }

$title = "Order {$currentShow->getTitle()} Tickets Online!";
$radius = 8;	//	radius for clickable spots on map
$head_content = "
	<style type='text/css'>
		#overlay a {
			height: " . ($radius * 2) . "px;
			width: " . ($radius * 2) . "px;
		}
	</style>";

//	various variables
$page = apiPost('p', 1);
$mailingFee = 2;
$hoursBeforeShowtime = 2;	//		tickets may be orderd online up until 2 hours before showtime
$ticketBoothMode = filter_input(INPUT_GET, 'ticketbooth') ? true : false;	//	put "?ticketbooth=1" in query string... (allows same-day ticket ordering only and no processing fee)
$serviceCharge = $ticketBoothMode ? 0 : 0;

$theater = $currentShow->getTheater();
$reservedSeatingTheaters = array('goppert', 'octa');
$reservedSeating = in_array($theater->getAbbr(), $reservedSeatingTheaters) ? true : false;
$unavailableSeats = array();
$unavailableTickets = array();
$presaleCapacity = 0.8;	//	only 80% of the total capacity genAdmission tickets are available for presale

if($ticketBoothMode) {
	$body_class = 'ticketBoothMode';
}

require_once('_inc/ipt_2.php');

session_set_cookie_params(10 * 60); //	session lasts 10 minutes
session_start();

$pageContent = "";

switch($page) {
	case 1:	//	SELECT THE PERFORMANCE
		session_destroy();	//	going to the first page destroys all session data
		session_start();
		$_SESSION['orderId'] = $_SESSION['confirmationNumber'] = substr(uniqid(), -7);	//	for now, the confirmation and orderId are the same
		$_SESSION['p'] = 1;

		$pageContent = "
			<section id='choosePerformance'>
				<h2>Order your tickets now for {$currentShow->getTitle()}</h2>
				<h3>...and instantly get the best available seats!</h3>
				<form action='$thisPage' method='post'>
					<h4>Step 1: Choose a Performance...</h4>
					<div class='row'>
						<div class='col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3'>
							<ul class='list-group jm'>";

		if($ticketBoothMode) {
			$performancesToday = false;	//	see if any performances meet criteria for selling at ticketbooth
		}

		// die('<pre>' . print_r($currentShow->getPerformances(), 1) . '</pre>');

		foreach($currentShow->getPerformances() as $index => $p) {
			$weekIfApplicable = "";
			$soldOut = false;

			if($currentShow->getSeason() !== 'winter') {
				$weekIfApplicable = " <i class='weak'>(week " . ($index < (count($currentShow->getPerformances()) / 2) ? 1 : 2) . " cast)</i>";
			}

			if (!$reservedSeating) {
				//	FETCH ALL GA TICKETS (TO SEE IF WE'RE AT CAPACITY)
				$sql = "
					SELECT COUNT(*)
					FROM ip_tickets t
					INNER JOIN ip_orders o ON t.orderId = o.id
					WHERE t.performanceId = {$p->getId()}
					AND (
						o.paid = 1
						OR o.dateTime > '" . date('Y-m-d H:i:s', strtotime('-10 minutes')) . "'
					)";
				$sth = $dbh->prepare($sql);
				$sth->execute();
				$soldSeats = intval($sth->fetchColumn());

				//	FETCH TOTAL CAPACITY OF THEATER
				$sql = "
					SELECT capacity
					FROM ip_theaters
					WHERE id = {$theater->getId()}";
				$sth = $dbh->prepare($sql);
				$sth->execute();
				$totalCapacity = intval($sth->fetchColumn());

				// die('Number of soldSeats: ' . $soldSeats . ' and totalCapacity: ' . $totalCapacity);

				$soldOut = $soldSeats >= ($presaleCapacity * $totalCapacity) ? true : false;
			}

			if($ticketBoothMode) {
				// if(date('Y-m-d') === $p->getDate() && date('Y-m-d H:i') <= date('Y-m-d H:i', strtotime($p->getDateTime() . " +1 hour"))) {

				if(date('Y-m-d H:i') <= date('Y-m-d H:i', strtotime($p->getDateTime() . " +1 hour"))) {
					//		ticket booth operators may only sell tickets for today's show (and only as late as 1 hour after show has started).
					$pageContent .= "
								<li class='list-group-item'><label><input type='radio' name='performanceId' value='{$p->getId()}' required>{$p->getDateTimeFormatted()}$weekIfApplicable"
									. ($soldOut ? " <span class='text-danger'>SOLD OUT</span>" : "")
									. "</label></li>";
					$performancesToday = true;
				}
			} else {
				//		tickets may be orderd online up until 3 hours before showtime
				if(date('Y-m-d H:i') < date('Y-m-d H:i', strtotime($p->getDateTime() . " -{$hoursBeforeShowtime} hours"))) {
					$pageContent .= "
								<li class='list-group-item'><label><input type='radio' name='performanceId' value='{$p->getId()}' required" . ($soldOut ? " disabled" : "") . ">{$p->getDateTimeFormatted()}$weekIfApplicable"
									. ($soldOut ? " <span class='text-danger'>SOLD OUT</span>" : "")
									. "</label></li>";
				} elseif(date('Y-m-d') === $p->getDate()) {
					$ticketBoothLink = true;	//	make a ticketbooth link available on show days
					$pageContent .= "
								<li class='list-group-item'><label style='color: #f00'>{$p->getDateTimeFormatted()} &mdash; Tickets Available at the Door!</label></li>";
				}
			}
		}
		if($ticketBoothMode && !$performancesToday) {
			$pageContent .= "
								<li class='list-group-item'>NO PERFORMANCES AVAILABLE IN TICKETBOOTH MODE (ticket booth operators may only sell tickets for today's show (and only as late as 1 hour after show has started.)</li>";
		}

		$pageContent .= "
							</ul>
							<input type='hidden' name='p' value='2'>
							<button type='submit' class='btn btn-success'>Choose Your Seats <i class='fa fa-lg fa-arrow-circle-o-right'></i></button>
						</div>
					</div>
				</form>
			</section>";

		break;
	case 2:	//	CHOOSE SEATS (if applicable... otherwise, SELECT NUMBER OF GA TICKETS)
		$_SESSION['p']++;
		//	if arrived with back button
		if(!apiPost('performanceId') || apiSession('p') !== 2) {
			header('Location: //' . $thisDomain . '/tickets_startover.php');
		}

		$_SESSION['performanceId'] = apiPost('performanceId');

		if ($reservedSeating) {
			//	FETCH ALL TICKETS FROM COMPLETED ORDERS (TO SEE WHAT'S NOT AVAILABLE)
			$sql = "
				SELECT
					t.seatId
				FROM ip_tickets t
				INNER JOIN ip_orders o ON t.orderId = o.id
				WHERE t.performanceId = " . apiSession('performanceId') . "
				AND (
					o.paid = 1
					OR o.dateTime > '" . date('Y-m-d H:i:s', strtotime('-10 minutes')) . "'
				)";
			$sth = $dbh->prepare($sql);
			$sth->execute();
			$unavailableSeats = $sth->fetchAll(PDO::FETCH_COLUMN, 0);
		}

		//	FETCH ALL AVAILABLE SEATS
		$sql = "
			SELECT
				id,
				section,
				row,
				number,
				coordX,
				coordY,
				price
			FROM ip_seats
			WHERE theaterId = " . $theater->getId() . "
			" . (count($unavailableSeats) ? "AND id NOT IN (" . implode(", ", $unavailableSeats) . ")" : "");

		$sth = $dbh->prepare($sql);
		$sth->execute();
		$seats = sthFetchObjects($sth);	//	fetch all of the seats

		if ($reservedSeating) {
			$sections = array();

			foreach($seats as $s) {
				$sectNum = $s->section;
				if(!isset($sections[$sectNum])) {
					$sections[$sectNum] = new Section($sectNum);
				}

				$sections[$sectNum]->addRowIfNeeded($s->row);

				$sections[$sectNum]->getRow($s->row)->addSeat($s->id, $s->number, $s->coordX, $s->coordY, $s->price);
			}

			$overlay = "<section id='overlay'>";

			foreach($sections as $sect) {
				foreach($sections[$sect->getId()]->getRows() as $row) {
					foreach($row->getSeats() as $seat) {
						$fullId = "{$sect->getId()}-{$row->getId()}-{$seat->getNumber()}";

						$overlay .= "<a data-bind='click: function() {addRemoveSeat({$seat->getId()}, {$sect->getId()}, \"{$row->getId()}\", {$seat->getNumber()}, \"{$seat->getPrice()}\");}' data-seat='$fullId' style='left:" . ($seat->getCoordX() - $radius) . "px; top:" . ($seat->getCoordY() - $radius) . "px' title='Sect: {$sect->getId()}\nRow: {$row->getId()}\nSeat: {$seat->getNumber()}'></a>";
					}
				}
			}

			$overlay .= "</section>";

			$pageContent = "
				<section class='ko'>
					<h4>Step 2: Choose Your Seats...</h4>
					<h2>{$theater->getName()}<br>Seating Chart</h2>
					<section class='chart'>
						<img src='_img/{$theater->getAbbr()}_900.png' alt='{$theater->getName()} Seating Chart' id='{$theater->getAbbr()}'>
						$overlay
					</section>
					<section id='orderSummary' data-bind='visible: order().length'>
						<form action='$thisPage' method='post'>
							<ul data-bind='foreach: order' class='ticketPics'>
								<li>
									<div style='background-image:url(/_img/{$currentShow->getAbbrLower()}-logo_100.png)'>
										<em>" . date('ga D, M j, Y', strtotime($currentShow->getPerformance(apiSession('performanceId'))->getDateTime())) . "</em>
										<table>
											<thead><tr><th>Section</th><th>Row</th><th>Seat</th><th>Price</th></tr></thead>
											<tbody>
												<tr>
													<td data-bind='text: section'></td>
													<td data-bind='text: row'></td>
													<td data-bind='text: seat'></td>
													<td>\$<span data-bind='text: price'></span>
														<input type='hidden' name='tickets[]' data-bind=\"value: '" . apiSession('performanceId') . "_' + id + '_' + section + '_' + row + '_' + seat + '_' + price\"></td>
												</tr>
											</tbody>
										</table>
									</div>
								</li>
							</ul>
							<p class='clear'><strong data-bind=\"text: 'Total Tickets: ' + order().length\"></strong></p>
							<input type='hidden' name='p' value='3'>
							<input type='hidden' name='performanceId' value='" . apiSession('performanceId') . "'>
							<button type='submit' class='btn btn-success'>Reserve <span data-bind=\"text: (order().length === 1 ? 'This Seat' : 'These Seats')\"></span>! <i class='fa fa-lg fa-arrow-circle-o-right'></i></button>
						</form>
					</section>
				</section>";
		} else {
			$gaSeat = $seats[0];

			$pageContent = "
				<section class='ko'>
					<h4>Step 2: Select Your General Admission Tickets...</h4>
					<h2>All tickets for this performance are general admission seating.<br> How many tickets would you like?</h2>
					<select class='form-control'
						data-bind='options: generalAdmissionSeatOptions,
							optionsCaption: \"Select Seats...\",
							value: chosenGaTickets'
						required>
					</select>
					<section id='orderSummary' data-bind='visible: chosenGaTickets'>
						<form action='$thisPage' method='post'>
							<ul data-bind='foreach: _.range(0, chosenGaTickets())' class='ticketPics'>
								<li>
									<div style='background-image:url(/_img/{$currentShow->getAbbrLower()}-logo_100.png)'>
										<em>" . date('ga D, M j, Y', strtotime($currentShow->getPerformance(apiSession('performanceId'))->getDateTime())) . "</em>
										<table>
											<thead><tr><th>Section</th><th>Row</th><th>Seat</th><th>Price</th></tr></thead>
											<tbody>
												<tr>
													<td colspan='3'>Gen. Admission</td>
													<td>\${$gaSeat->price}</span>
														<input type='hidden' name='tickets[]' value='" . apiSession('performanceId') . "_{$gaSeat->id}_{$gaSeat->section}_{$gaSeat->row}_{$gaSeat->number}_{$gaSeat->price}'></td>
												</tr>
											</tbody>
										</table>
									</div>
								</li>
							</ul>
							<p class='clear'><strong data-bind=\"text: 'Total Tickets: ' + chosenGaTickets()\"></strong></p>
							<input type='hidden' name='p' value='3'>
							<input type='hidden' name='performanceId' value='" . apiSession('performanceId') . "'>
							<button type='submit' class='btn btn-success'>
								Reserve <span data-bind=\"text: (chosenGaTickets() === 1 ? 'This Seat' : 'These Seats')\"></span>!
								<i class='fa fa-lg fa-arrow-circle-o-right'></i>
							</button>
						</form>
					</section>
				</section>";
		}
		break;
	case 3:	//	ENTER PERSONAL INFO AND AGREE ON TOTAL PURCHASE PRICE
		$_SESSION['p']++;
		//	if arrived with back button, startover
		if(!apiPost('tickets') || apiSession('p') !== 3) {
			header('Location: //' . $thisDomain . '/tickets_startover.php');
		}

		$_SESSION['tickets'] = array();

		$allSeats = array();
		foreach(apiPost('tickets') as $tkt) {
			$_SESSION['tickets'][] = $tkt;

			list($performanceId, $seatId, $sec, $row, $seat, $price) = explode('_', $tkt);
			$allSeats[] = $seatId;
		}

		if($reservedSeating) {
			//	check if any of these seats are already taken
			$sql = "
				SELECT t.id
				FROM ip_tickets t
				INNER JOIN ip_orders o ON t.orderId = o.id
				WHERE performanceId = :performanceId
				AND seatId IN (" . implode(', ', $allSeats) . ")
				AND (
					o.paid = 1
					OR o.dateTime > '" . date('Y-m-d H:i:s', strtotime('-10 minutes')) . "'
				);";

			$sth = $dbh->prepare($sql);
			$sth->bindParam(':performanceId', apiSession('performanceId'), PDO::PARAM_INT);
			$sth->execute();
			$unavailableTickets = $sth->fetchAll(PDO::FETCH_COLUMN, 0);
		}

		if(count($unavailableTickets)) {
			$pageContent = "
				<section id='startOver'>
					<h4>Sorry! Some of the tickets you chose were just purchased by someone else right before you!</h4>
					<p>We apologize for the inconvenience.  Please <a href='tickets_startover.php'>click here to begin the order process again</a>.</p>
				</section>";
		} else {
			foreach(apiSession('tickets') as $tkt) {
				list($performanceId, $seatId, $sec, $row, $seat, $price) = explode('_', $tkt);

				//	insert order into database
				$sql = "
					INSERT INTO ip_tickets (
						performanceId,
						seatId,
						orderId
					) VALUES (
						:performanceId,
						:seatId,
						:orderId
					);";
				$sth = $dbh->prepare($sql);
				$sth->bindParam(':performanceId', $performanceId, PDO::PARAM_INT);
				$sth->bindParam(':seatId', $seatId, PDO::PARAM_INT);
				$sth->bindParam(':orderId', apiSession('orderId'), PDO::PARAM_STR);
				$sth->execute();
			}

			//	insert order into database
			$sql = "
				INSERT INTO ip_orders (
					id,
					paid,
					ip,
					dateTime,
					confirmationNumber
				) VALUES (
					:id,
					0,
					:ip,
					:dateTime,
					:confirmationNumber
				);";
			$sth = $dbh->prepare($sql);
			$sth->bindParam(':id', apiSession('orderId'), PDO::PARAM_STR);
			$sth->bindParam(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
			$dt = new DateTime('NOW');
			$sth->bindParam(':dateTime', $dt->format('c'), PDO::PARAM_STR);
			$sth->bindParam(':confirmationNumber', apiSession('confirmationNumber'), PDO::PARAM_STR);
			$sth->execute();

			//	is there enough time to mail the tickets before the show starts?  If not, "Will Call Only"
			$willCallOnly = $currentShow->getPerformance(apiSession('performanceId'))->getDateTime() < date('Y-m-d', strtotime('+10 days'));	//	true or false

			$tixCount = count(apiSession('tickets', array()));
			$pageContent = "
				<section id='basicInfo'>
					<form action='$thisPage' method='post' role='form'>
						<h4>Step 3: Basic Info...</h4>
						<p>The " . $tixCount . " ticket" . ($tixCount > 1 ? "s" : "") . " you selected " . ($tixCount > 1 ? "are" : "is") . " currently reserved for you (as long as you don't refresh your browser).  You have approximately 10 minutes to complete this order.  If the order is not completed and paid for in that time, your tickets will be released.</p>
						<div class='row'>
							<section class='col-sm-2'><label>Name:</label></section>
							<section class='col-sm-4'><input type='text' class='form-control' name='name' placeholder='Name' required></section>
						</div>
						<div class='row'>
							<section class='col-sm-2'><label>Email:</label></section>
							<section class='col-sm-4'><input type='text' class='form-control' placeholder='Email' name='email' required></section>
						</div>
						<div class='row'>
							<section class='col-sm-2'><label>Phone:</label></section>
							<section class='col-sm-4'><input type='text' placeholder='(###) ###-####' name='phone' class='form-control formatter format-phone' required></section>
						</div>
						<div class='row'>
							<section class='col-sm-2'><label>Referred By:</label></section>
							<section class='col-sm-4'><input type='text' class='form-control' name='referredBy' placeholder='Cast Member(s)'></section>
						</div>
						<div class='row'>
							<section class='col-sm-2'><label>Comments:</label></section>
							<section class='col-sm-4'><textarea class='form-control' name='comments'></textarea></section>
						</div>
						<div class='row'>
							<section class='col-sm-2'><label>Receive Tickets:</label></section>
							<section class='col-sm-4'><label><input type='radio' name='mailTickets' value='0' " . ($willCallOnly ? "checked='checked' " : "") . "required> at Will Call (FREE)</label>" . (
								$willCallOnly
								? ""
								: " or <br><label><input type='radio' name='mailTickets' value='1' required> by Mail (\$" . number_format($mailingFee, 2) . ")</label>"
							) . "</section>
						</div>
						<input type='hidden' name='p' value='4'>
						<button type='submit' class='btn btn-success'>Review Your Order <i class='fa fa-lg fa-arrow-circle-o-right'></i></button>
					</form>
				</section>";
		}
		break;
	case 4:	//	VERIFY AND PAY
		$_SESSION['p']++;
		//	if arrived with back button
		if(!apiPost('name') || apiSession('p') !== 4) {
			header('Location: //' . $thisDomain . '/tickets_startover.php');
		}

		//	convert POST data to SESSION data
		foreach($_POST as $key => $value) {
			$_SESSION[$key] = $value;
		}

		$pageContent = "
			<h4>Step 4: Verify and Pay...</h4>
			<section class='summary'>
				<p>Your order:</p>
				<ul class='ticketPics'>";

		$totalTicketCost = 0;
		logConsole(apiSession('tickets'));
		foreach(apiSession('tickets') as $tkt) {
			list($performanceId, $seatId, $sec, $row, $seat, $price) = explode('_', $tkt);

			$totalTicketCost += $price;

			$pageContent .= "
					<li>
						<div style='background-image:url(/_img/{$currentShow->getAbbrLower()}-logo_100.png)'>
							<em>" . date('ga D, M j, Y', strtotime($currentShow->getPerformance($performanceId)->getDateTime())) . "</em>
							<table>
								<thead><tr><th>Section</th><th>Row</th><th>Seat</th><th>Price</th></tr></thead>
								<tbody>
									<tr>
										" . (!$sec ? "<td colspan='3'>Gen. Admission</td>" : "<td>$sec</td><td>$row</td><td>$seat</td>") . "
										<td>\$$price</td>
									</tr>
								</tbody>
							</table>
						</div>
					</li>";
		}

		if (in_array(strtoupper(apiSession('referredBy')), array('PARNELL'))) {	//	SPECIAL! Free Tickets for Parnell employees!
			$totalTicketCost = 0;
			$serviceCharge = 1;
		}

		$_SESSION['orderPrice'] = $totalTicketCost + ($mailingFee * apiPost('mailTickets', 0)) + $serviceCharge;
		$custom = array(
			'performanceId' => apiSession('performanceId'),
			'orderPrice' => apiSession('orderPrice')
		);

		$pageContent .= "
				</ul>
				<table id='priceBreakdown'>
					<tbody>
						<tr><th>" . count(apiSession('tickets', array())) . " Tickets:</th><td>\$" . number_format($totalTicketCost, 2) . "</td></tr>
						<tr><th>Service Charge:</th><td>\$" . number_format($serviceCharge, 2) . "</td></tr>
						" . (apiPost('mailTickets') ? "<tr><th>Mailing Fee:</th><td>\$" . number_format($mailingFee, 2) . "</td></tr>" : "") . "
						<tr><th>Total Cost:</th><td>\$" . number_format(apiSession('orderPrice'), 2) . "</td></tr>
					</tbody>
				</table>
				<p>Your info:</p>
				<table id='infoBreakdown'>
					<tbody>
						<tr><th>Name:</th><td>" . apiSession('name') . "</td></tr>
						<tr><th>Email:</th><td>" . apiSession('email') . "</td></tr>
						<tr><th>Phone:</th><td>" . apiSession('phone') . "</td></tr>
						<tr><th>Referred By:</th><td>" . apiSession('referredBy') . "</td></tr>
						<tr><th>Comments:</th><td>" . apiSession('comments') . "</td></tr>
					</tbody>
				</table>
				<p>Click the 'Buy Now' button below to " . (apiPost('mailTickets') ? "enter your address and " : "") . "make your payment using any major credit card or a PayPal account.</p>
				<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
					<input type='hidden' name='cmd' value='_xclick'>
					<input type='hidden' name='business' value='order@iptheater.com'>
					<input type='hidden' name='item_name' value='{$currentShow->getTitle()} Tickets'>
					<input type='hidden' name='currency_code' value='USD'>
					<input type='hidden' name='return' value='http://" . $thisDomain . "/tickets_thankyou.php'>
					<input type='hidden' name='cancel_return' value='http://" . $thisDomain . "/tickets_cancel.php'>
					<input type='hidden' name='notify_url' value='http://" . $thisDomain . "/ipn.php?orderId=" . apiSession('orderId') . "'>
					<input type='hidden' name='amount' value='" . apiSession('orderPrice') . "'>
					<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif' name='submit' alt='Pay for your tickets with any credit card or PayPal account'>
					<img alt='' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'>
					<!--<input type='hidden' name='custom' value='" . htmlspecialchars(serialize($_SESSION), ENT_QUOTES) . "'>-->
					<input type='hidden' name='custom' value='" . serialize($custom) . "'>
				</form>
				<a href='tickets_cancel.php' onclick='javascript:return confirm(\"Delete the Entire Order?\")'>Cancel this Order</a>"
				. ($ticketBoothMode ? "<br><br><a class='btn btn-success' href='http://" . $thisDomain . "/tickets_thankyou.php?ticketbooth=1&amp;orderId=" . apiSession('confirmationNumber') . "'><i class='fa fa-lg fa-money'></i> Paid in Person (Cash/Check/CC)</a>" : "") .
			"</section>";

		//	update order in database
		$sql = "
			UPDATE ip_orders
			SET
				name = :name,
				email = :email,
				phone = :phone,
				price = :price,
				mailTickets = :mailTickets,
				referredBy = :referredBy,
				comments = :comments,
				dateTime = :dateTime
			WHERE id = :id;";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':id', apiSession('orderId'), PDO::PARAM_STR);
		$sth->bindParam(':name', apiSession('name'), PDO::PARAM_STR);
		$sth->bindParam(':email', apiSession('email'), PDO::PARAM_STR);
		$sth->bindParam(':phone', apiSession('phone'), PDO::PARAM_STR);
		$sth->bindParam(':price', apiSession('orderPrice'), PDO::PARAM_STR);
		$sth->bindParam(':mailTickets', apiSession('mailTickets'), PDO::PARAM_INT);
		$sth->bindParam(':referredBy', apiSession('referredBy'), PDO::PARAM_STR);
		$sth->bindParam(':comments', apiSession('comments'), PDO::PARAM_STR);
		$dt = new DateTime('NOW');
		$sth->bindParam(':dateTime', $dt->format('c'), PDO::PARAM_STR);
		$sth->execute();

		break;	//	break this case in switch statement
}

?>
<?=$header;?>
	<section class='main'>
		<div class='row'>
			<div class='col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2'>
				<h2><img src='_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions' class='img-responsive inline-block'></h2>
				<h2><img src='_img/<?=$currentShow->getAbbrLower();?>-logo_250.png' alt='<?=$currentShow->getTitle();?>' id='<?=$currentShow->getAbbrLower();?>_logo'></h2>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm-10 col-sm-offset-1'>
				<?=$pageContent;?>
			</div>
		</div>
		<!--<?=isset($ticketBoothLink) ? "<p><a href='" . addToQueryString($thisPage, 'ticketbooth') . "'><i class='fa fa-sm fa-ticket'></i></a></p>" : "";?>-->
	</section>
<?=$footer;?>

