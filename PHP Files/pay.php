<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once('_inc/ipt_1.php');
$title = "Make a Payment to Immeasurable Productions!";
require_once('_inc/ipt_2.php');

//	various variables
$page = apiPost('p', 1);

session_set_cookie_params(600); //	session lasts 10 minutes
session_start();

$pageContent = "";

switch($page) {
	case 1:
		session_destroy();	//	going to the first page destroys all session data
		session_start();
		$_SESSION['p'] = 1;
		$pageContent = "
			<h3>Make a payment to Immeasurable Productions using the form below:</h3>
			<h4>Step 1: Enter Name and Amount...</h4>
			<div class='row'>
				<div class='col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2'>
					<form action='" . $_SERVER['PHP_SELF'] . "' method='post' role='form' class='iptForm'>
						<div class='row top-buffer'>
							<div class='col-sm-4 form-label'><label>Name</label></div>
							<div class='col-sm-8'><input type='text' name='name' " . (apiGet('name') ? "value='" . apiGet('name') . "' " : "") . "placeholder='required' class='form-control' required></div>
						</div>
						<div class='row top-buffer'>
							<div class='col-sm-4 form-label'><label>Amount</label></div>
							<div class='col-sm-8'><input type='text' name='amount' " . (apiGet('amount') ? "value='" . apiGet('amount') . "' " : "") . "class='formatter format-money form-control' placeholder='XX.XX' required></div>
						</div>
						<div class='row top-buffer'>
							<div class='col-sm-4 form-label'><label>Email</label></div>
							<div class='col-sm-8'><input type='text' name='email' " . (apiGet('email') ? "value='" . apiGet('email') . "' " : "") . "placeholder='email@example.com' class='form-control'></div>
						</div>
						<div class='row top-buffer'>
							<div class='col-sm-4 form-label'><label>Phone</label></div>
							<div class='col-sm-8'><input type='text' name='phone' " . (apiGet('phone') ? "value='" . apiGet('phone') . "' " : "") . "class='formatter format-phone form-control' placeholder='optional'></div>
						</div>
						<div class='row top-buffer'>
							<div class='col-sm-4 form-label'><label>Comments</label></div>
							<div class='col-sm-8'><textarea name='comments' placeholder='How should this payment be applied?' class='form-control' required>" . (apiGet('comments') ? apiGet('comments') : "") . "</textarea></div>
						</div>
						<input type='hidden' name='p' value='2'>
						<button type='submit' class='btn btn-success top-buffer'><i class='fa fa-lg fa-money'></i> Review and Enter Payment Details</button>
					</form>
				</div>
			</div>";
		break;
	case 2:
		$_SESSION['p']++;
		//	if arrived with back button
		if(!apiPost('name') || apiSession('p') !== 2) {
			header('Location: //' . $_SERVER['SERVER_NAME'] . '/pay.php');
		}

		$pageContent = "
			<h4>Step 2: Verify and Pay...</h4>
			<p>Your payment information:</p>
			<ul class='jm list-group'>
				<li class='list-group-item'><label>Name:</label> " . apiPost('name') . "</li>
				<li class='list-group-item'><label>Amount:</label> " . apiPost('amount') . "</li>
				" . (apiPost('email') ? "<li class='list-group-item'><label>Email:</label> " . apiPost('email') . "</li>" : "") . "
				" . (apiPost('phone') ? "<li class='list-group-item'><label>Phone:</label> " . apiPost('phone') . "</li>" : "") . "
				<li class='list-group-item'><label>Comments:</label> " . apiPost('comments') . "</li>
			</ul>
			<p>Click the 'Pay Now' button below to make your payment using any major credit card or a PayPal account.</p>
			<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
				<input type='hidden' name='cmd' value='_xclick'>
				<input type='hidden' name='business' value='order@iptheater.com'>
				<input type='hidden' name='item_name' value='Payment'>
				<input type='hidden' name='currency_code' value='USD'>
				<input type='hidden' name='return' value='http://" . $_SERVER['SERVER_NAME'] . "/pay_thankyou.php'>
				<input type='hidden' name='cancel_return' value='http://" . $_SERVER['SERVER_NAME'] . "/pay_cancel.php'>
				<input type='hidden' name='notify_url' value='http://" . $_SERVER['SERVER_NAME'] . "/ipn_pay.php'>
				<input type='hidden' name='amount' value='" . apiPost('amount') . "'>
				<input type='image' src='_img/paynow.gif' name='submit' alt='Make your Payment with any credit card or PayPal account'>
				<img alt='' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'>
				<input type='hidden' name='custom' value='" . htmlspecialchars(serialize($_POST), ENT_QUOTES) . "'>
			</form>
			<br><br>
			<a href='pay_cancel.php' onclick='javascript:return confirm(\"Delete the Payment?\")' class='btn btn-info'><i class='fa fa-times'></i> Cancel this Payment</a>";
		break;
}
?>

<?=$header;?>
	<section class='main'>
		<div class='row'>
			<div class='col-xs-12'>
				<h2><img src='/_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions' class='img-responsive inline-block'></h2>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm-10 col-sm-offset-1'>
				<?=$pageContent;?>
			</div>
		</div>
	</section>
<?=$footer;?>
