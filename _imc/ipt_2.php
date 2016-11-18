<?php 	/* IPTheater.com */

//	nav stuff
$navLinks = array(
	array('href' => '/', 'title' => "Immeasurable Productions Home",  'faIcon' => 'home', 'navText' => 'Home'),
	// array('href' => 'show_survey', 'title' => "Cast Your Vote for this year's musical!", 'faIcon' => 'calendar', 'navText' => 'Winter ' . date('Y')),
	// array('href' => $currentShow->getAbbr(), 'title' => $currentShow->getTitle(), 'faIcon' => 'music', 'navText' => $currentShow->getTitle()),
	// array('href' => 'tickets', 'title' => "On sale now!", 'faIcon' => 'ticket', 'navText' => 'Tickets'),
	// array('href' => 'audition_form', 'title' => $currentShow->getTitle() . " Audition Form", 											'faIcon' => 'pencil-square-o', 			'navText' => 'Audition Form'),
	// array('href' => 'joseph', 'title' => "Joseph (Spring Break Camp!)", 'faIcon' => 'thumbs-o-up', 'navText' => 'Joseph (spring break)'),
	array('href' => $currentShow->getAbbr(), 'title' => $currentShow->getTitle() . " (Summer Camp!)", 'faIcon' => 'music', 'navText' => "{$currentShow->getTitle()} ({$currentShow->getSeason()})"),
	array('href' => $nextShow->getAbbr(), 'title' => $nextShow->getTitle(), 'faIcon' => 'music', 'navText' => "{$nextShow->getTitle()} ({$nextShow->getSeason()})"),
	array('href' => 'past_productions', 'title' => "Past Productions", 'faIcon' => 'picture-o', 'navText' => 'Past Productions'),
	array('href' => 'contact', 'title' => "Contact Us", 'faIcon' => 'envelope', 'navText' => 'Contact Us')
);

$fullNav = '';
$collapseNav = '';
foreach($navLinks as $n) {
	$currentPage = $thisPageBaseName === $n['href'] || ($thisPageBaseName === 'index' && $n['href'] === '/');
	$fullNav .= "<a class='btn btn-primary" . ($currentPage ? " active" : "") . "' href='" . $n['href'] . "' title=\"" . $n['title'] . "\"><i class='fa fa-" . $n['faIcon'] . "'></i> " . $n['navText'] . "</a>\n";
	$collapseNav .= "<li" . ($currentPage ? " class='active'" : "") . "><a href='" . $n['href'] . "' title=\"" . $n['title'] . "\"><i class='fa fa-" . $n['faIcon'] . "'></i> " . $n['navText'] . "</a></li>\n";
}

$header = "
	<!DOCTYPE html>
	<html lang='en'>
	<head>
		<meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'><!-- for Bootstrap -->
		<meta name='description' content='Immeasurable Productions'>
		<meta name='keywords' content='Immeasurable Productions'>
		<title>$title</title>
		<link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/latest/css/bootstrap.min.css'>
		<link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/latest/css/bootstrap-theme.min.css'>
		<link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css'>
		<link rel='stylesheet' href='_inc/animations.css'>
		<link rel='stylesheet' href='//jeremymoritz.com/support/bootstrap-jm-custom.css'>
		<link rel='stylesheet/less' href='_inc/ipt.less'>
		<link rel='shortcut icon' href='favicon.ico'>
		<script src='//cdnjs.cloudflare.com/ajax/libs/less.js/1.4.1/less.min.js'></script>
		$head_content
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-45476268-1', 'iptheater.com');
			ga('send', 'pageview');
		</script>
	</head>
	<body" . ($body_class ? " class='$body_class'" : "") . " id='$thisPageBaseName'>
		<div class='container'>
			<header>
				<h1>Immeasurable Productions</h1>
			</header>
			<section class='content'>
				<h1>Immeasurable Productions Musicals Page</h1>
				<nav class='btn-group btn-group-justified hidden-xs'>
					$fullNav
				</nav>
				<nav class='navbar navbar-fixed-top navbar-inverse visible-xs'>
					<div class='navbar-header'>
					<button class='navbar-toggle navbar-brand pull-right' data-toggle='collapse' data-target='#collapse'>
						<span class='sr-only'>Toggle navigation</span>
						<i class='fa fa-bars'></i>
						MENU
					</button>
					</div>
					<div class='collapse navbar-collapse' id='collapse'>
						<ul class='nav navbar-nav text-left'>
							$collapseNav
						</ul>
					</div>
				</nav>";

$footer = "
				<footer>
					<span><a href='pay.php'><i class='fa fa-lg fa-money'></i> Pay</a></span>
					Website Created by <a href='http://www.JeremyMoritz.com'>Jeremy Moritz</a>
				</footer>
				$foot_content
			</section><!-- /.content -->
		</div><!-- /.container -->
		<script src='//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js'></script>
		<script src='//cdnjs.cloudflare.com/ajax/libs/knockout/2.3.0/knockout-min.js'></script>
		<script src='//cdnjs.cloudflare.com/ajax/libs/lodash.js/3.10.1/lodash.min.js'></script>
		<script src='//maxcdn.bootstrapcdn.com/bootstrap/latest/js/bootstrap.min.js'></script>
		<script src='//cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.10.8/jquery.tablesorter.min.js'></script>
		<script src='//momentjs.com/downloads/moment.js'></script>
		<script src='//jeremymoritz.com/cdn/jquery.formatter.min.js'></script>
		<script src='_inc/css3-animate-it.js'></script>
		<script src='_inc/ipt.js'></script>
		$js_content
	</body>
	</html>";
?>
