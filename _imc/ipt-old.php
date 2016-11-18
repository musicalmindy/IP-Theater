<?php
ob_start();	//	starts output buffer (allows for setting php headers after declaring output, without errors)
putenv("TZ=US/Central");	//	Sets the timezone to be Central

//	these variables allow me to set differences for individual pages (note: if further changes are needed, define $header variable on page)
$title = isset($title) ? $title : "Immeasurable Productions";	//	title of page
$head_content = isset($head_content) ? $head_content : "";	//	additional head content

$currentShow = new stdClass();
$currentShow->title = 'Bye Bye Birdie';
$currentShow->abbr = 'BBB';

$header = "
	<!DOCTYPE html>
	<html lang='en'>
	<head>
		<meta charset='UTF-8'>
		<meta name='description' content='Immeasurable Productions'>
		<meta name='keywords' content='Immeasurable Productions'>
		<title>$title</title>
		<link rel='stylesheet/less' href='_inc/ipt.less'>
		<link rel='shortcut icon' href='favicon.ico'>
		<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
		<script src='//cdnjs.cloudflare.com/ajax/libs/knockout/2.3.0/knockout-min.js'></script>
		<script src='//cdnjs.cloudflare.com/ajax/libs/less.js/1.4.1/less.min.js'></script>
		<script src='//cdnjs.cloudflare.com/ajax/libs/lodash.js/2.1.0/lodash.min.js'></script>
		<!--[if lt IE 9]>
			<script src='http://html5shim.googlecode.com/svn/trunk/html5.js'></script>
		<![endif]-->
		$head_content
		<script>
			//	google analytics
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-23066588-1']);
			_gaq.push(['_setDomainName', '.iptheater.com']);
			_gaq.push(['_trackPageview']);
			
			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		</script>
	</head>";

$topper = "
	<header>
		<h1>Immeasurable Productions</h1>
	</header>
	<section class='content'>
		<h1>Immeasurable Productions Musicals Page</h1>";

$navbar = "
	<nav>
		<h1>Site Navigation</h1>
		<ul>
			<li><a href='/' title='Immeasurable Productions Home'>Home</a></li>
			<li><a href='" . strtolower($currentShow->abbr) . "' title='$currentShow->title'>$currentShow->title</a></li>
			<!--<li><a href='show_survey' title='Cast Your Vote for this year's musical!'>Winter 2013</a></li>-->
			<li><a href='audition_form.php' title='$currentShow->title Audition Form'>Audition Form</a></li>
			<!--<li><a href='footloose' title='Footloose'>Footloose</a></li>-->
			<li><a href='rhythmcity' title='Rhythm City Junior'>Rhythm City</a></li>
			<li><a href='contact' title='Contact Us'>Contact Us</a></li>
		</ul>
	</nav>";
	
$footer = "
		<footer>
			Website Created by <a href='http://www.JeremyMoritz.com'>Jeremy Moritz</a>
		</footer>
		<script src='_inc/ipt.js'></script>
	</section>";

	/************
	*	Arrays	*
	************/

//	ARRAYS
//	For configuring passwords, etc. for MySQL
$config = array(
	'host'				=>	'localhost',	//	alias for server508.webhostingpad.com
	'username'			=>	'rhythmci_jeremy',
	'password'			=>	'Gl0rify!',
	'db'				=>	'rhythmci_db',
	'info_email'		=>	'info@iptheater.com',
	'survey_email'		=>	'survey@iptheater.com'
	);

//	For configuring passwords, etc. for MySQL
//	Array of all 50 states
$states_s2l = array(
	'--' => '',
	'AL' => 'Alabama',			'AK' => 'Alaska',			'AZ' => 'Arizona',			'AR' => 'Arkansas',
	'CA' => 'California',			'CO' => 'Colorado',			'CT' => 'Connecticut',		'DC' => 'District Of Columbia',
	'DE' => 'Delaware',			'FL' => 'Florida',			'GA' => 'Georgia',			'HI' => 'Hawaii',
	'ID' => 'Idaho',				'IL' => 'Illinois',			'IN' => 'Indiana',			'IA' => 'Iowa',
	'KS' => 'Kansas',			'KY' => 'Kentucky',			'LA' => 'Louisiana',			'ME' => 'Maine',
	'MD' => 'Maryland',			'MA' => 'Massachusetts',		'MI' => 'Michigan',			'MN' => 'Minnesota',
	'MS' => 'Mississippi',		'MO' => 'Missouri',			'MT' => 'Montana',			'NE' => 'Nebraska',
	'NV' => 'Nevada',			'NH' => 'New Hampshire',		'NJ' => 'New Jersey',			'NM' => 'New Mexico',
	'NY' => 'New York',			'NC' => 'North Carolina',		'ND' => 'North Dakota',		'OH' => 'Ohio',
	'OK' => 'Oklahoma',			'OR' => 'Oregon',			'PA' => 'Pennsylvania',		'RI' => 'Rhode Island',
	'SC' => 'South Carolina',		'SD' => 'South Dakota',		'TN' => 'Tennessee',			'TX' => 'Texas',
	'UT' => 'Utah',				'VT' => 'Vermont',			'VA' => 'Virginia',			'WA' => 'Washington',
	'WV' => 'West Virginia',		'WI' => 'Wisconsin',			'WY' => 'Wyoming');
$states_l2s = array_flip($states_s2l);
asort($states_l2s);
	
	/*********************
	*	PDO Functions	*
	*********************/

####	CONNECT TO THE DATABASE		######
try {
	$dbh = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['db'], $config['username'], $config['password'], array(PDO::ATTR_PERSISTENT => true));
} catch (PDOException $e) {
	die($e->getMessage() . "\n Please contact us to tell us about this error... jeremy@jeremyandchristine.com");
}

#takes a pdo statement handle and returns an array of row objects
function sthFetchObjects($sth) {
	$out = array();
	while($o = $sth->fetchObject()) {
		$out[] = $o;
	}
	return $out;
}

	/*********************
	*	Functions		*
	*********************/

//	THESE REPLACE THE $_GET, $_POST, etc. (can pass in a default value if none is found)
function apiGet($key,$default=false) {return isset($_GET[$key]) ? $_GET[$key] : $default;}
function apiPost($key,$default=false) {return isset($_POST[$key]) ? $_POST[$key] : $default;}
function apiCookie($key,$default=false) {return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;}
function apiSession($key,$default=false) {return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;}
	//	Check to see if a parameter has been set and if so, return it
function apiSnag($key,$default=false) {
	if(apiGet($key,$default)) {return apiGet($key,$default);
	} elseif(apiPost($key,$default)) {return apiPost($key,$default);
	} elseif(apiCookie($key,$default)) {return apiCookie($key,$default);
	} elseif(apiSession($key,$default)) {return apiSession($key,$default);
	} else {return $default;
	}
}
	//	try to snag a value; if it's not there, use the default passed-in; also allows for explicit typing of varName to make sure people are passing the correct type (i.e. integer)
function apiSnagType($varName,$default=false,$type=false) {
	$retVal = $default;	
	if(apiSnag($varName)) {
		if(!$type || gettype(apiSnag($varName)) == $type) {	//	if type is set, make sure it's the right type
			$retVal = apiSnag($varName);
		}
	}
	return $retVal;
}

	//	Checks if an email is valid
function isValidEmail($email) {
	return preg_match("/^[a-zA-Z]\w+(\.\w+)*\@\w+(\.[0-9a-zA-Z]+)*\.[a-zA-Z]{2,4}$/", $email);
}

	//	converts "<a href='mailto:abc@example.com'>abc@example.com</a>" to "<a href='mailt&#111;:abc&#64;example.c&#111;m'>abc&#64;example.c&#111;m</a>" to hide from spamBots
function disguiseMail($mail) {
	return str_replace('@','&#64;',str_replace('o','&#111;',$mail));
}

	//	return the age in years if given a birthday and (optionally) another date (also may optionally pad it to a certain length like 2)
function getAge($iBirthdayTimestamp, $iCurrentTimestamp = false, $padZeros = 2) {	//	by default, it pads it left to a length of 2 (zerofill)
	$iBirthdayTimestamp = preg_match('/^\d{4}-\d{2}-\d{2}$/', $iBirthdayTimestamp) ? strtotime($iBirthdayTimestamp) : $iBirthdayTimestamp;
	$iCurrentTimestamp = $iCurrentTimestamp ? $iCurrentTimestamp : time();	//	default is today
	
	$iDiffYear  = date('Y', $iCurrentTimestamp) - date('Y', $iBirthdayTimestamp);
	$iDiffMonth = date('n', $iCurrentTimestamp) - date('n', $iBirthdayTimestamp);
	$iDiffDay   = date('j', $iCurrentTimestamp) - date('j', $iBirthdayTimestamp);
	
	// If birthday has not happen yet for this year, subtract 1.
	if ($iDiffMonth < 0 || ($iDiffMonth == 0 && $iDiffDay < 0)) {
		$iDiffYear--;
	}
	
	$iDiffYear = str_pad($iDiffYear, $padZeros, '0', STR_PAD_LEFT);	//	pad the age
	return $iDiffYear;
}
?>
