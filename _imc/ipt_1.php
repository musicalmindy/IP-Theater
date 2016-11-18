<?php
date_default_timezone_set('America/Chicago');	//	Sets the timezone to be Central
ob_start();	//	starts output buffer (allows for setting php headers after declaring output, without errors)
ini_set('display_errors', 1);	//	show errors
$isLocal = in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'));
$ROOT = $isLocal ? $_SERVER['DOCUMENT_ROOT'] : '';
$_inc = '/_inc';
$_img = '/_img';
$downloads = '/downloads';
require_once("_inc/class_lib.php");

// if IE<=8, send them away!
if (preg_match('/(?i)msie [2-8]/', $_SERVER['HTTP_USER_AGENT'])) {
	header('Location: //' . $_SERVER['SERVER_NAME'] . '/unsupported.php');
}

//	VARIABLES
$developerMode = !is_null(filter_input(INPUT_GET, 'dev')) ? true : false;	//	Developer mode (for debugging and early testing stuff)
$thisDomain = $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] === '80' ? '' : ':' . $_SERVER['SERVER_PORT']);
$thisPage = $_SERVER['PHP_SELF'] . (isset($_SERVER['QUERY_STRING']) ? "?" . $_SERVER['QUERY_STRING'] : "");	//		returns current page + query string (e.g. "mypage.php?ugly=true")
$thisPageBaseName = pathinfo($_SERVER['PHP_SELF']);
$thisPageBaseName = $thisPageBaseName['filename'];
//	these variables allow me to set differences for individual pages (note: if further changes are needed, define $header variable on page)
$title = 'Immeasurable Productions';	//	title of page
$head_content = '';	//	additional head content
$foot_content = '';	//	additional footer content
$js_content = '';	//	additional javascript content (goes below footer)
$body_class = '';	//	additional classes applied to body

$inchesPerFoot = 12;	//	used in height inputs
$castListIsSecret = true;
$campRegistrationFee = 195;
$campRegistrationFeeSecondary = 100;
// $campSeason = 'summer';
$campSeason = 'spring break';

	/************
	*	Arrays	*
	************/

//	ARRAYS
require('config.php');

//	Array of all 50 states
$states_s2l = array(
	'--' => '',
	'AL' => 'Alabama',			'AK' => 'Alaska',			'AZ' => 'Arizona',			'AR' => 'Arkansas',
	'CA' => 'California',		'CO' => 'Colorado',			'CT' => 'Connecticut',		'DC' => 'District Of Columbia',
	'DE' => 'Delaware',			'FL' => 'Florida',			'GA' => 'Georgia',			'HI' => 'Hawaii',
	'ID' => 'Idaho',			'IL' => 'Illinois',			'IN' => 'Indiana',			'IA' => 'Iowa',
	'KS' => 'Kansas',			'KY' => 'Kentucky',			'LA' => 'Louisiana',		'ME' => 'Maine',
	'MD' => 'Maryland',			'MA' => 'Massachusetts',	'MI' => 'Michigan',			'MN' => 'Minnesota',
	'MS' => 'Mississippi',		'MO' => 'Missouri',			'MT' => 'Montana',			'NE' => 'Nebraska',
	'NV' => 'Nevada',			'NH' => 'New Hampshire',	'NJ' => 'New Jersey',		'NM' => 'New Mexico',
	'NY' => 'New York',			'NC' => 'North Carolina',	'ND' => 'North Dakota',		'OH' => 'Ohio',
	'OK' => 'Oklahoma',			'OR' => 'Oregon',			'PA' => 'Pennsylvania',		'RI' => 'Rhode Island',
	'SC' => 'South Carolina',	'SD' => 'South Dakota',		'TN' => 'Tennessee',		'TX' => 'Texas',
	'UT' => 'Utah',				'VT' => 'Vermont',			'VA' => 'Virginia',			'WA' => 'Washington',
	'WV' => 'West Virginia',	'WI' => 'Wisconsin',		'WY' => 'Wyoming'
);
$states_l2s = array_flip($states_s2l);
asort($states_l2s);
//	State options for forms
$stateOptions = '';
foreach($states_l2s as $st) {
	$display = $st ? $st : '--';
	$stateOptions .= "<option value='$st'>$display</option>";
}

	/*********************
	*	PDO Functions	*
	*********************/

####	CONNECT TO THE DATABASE		######
try {
	$dbh = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['db'], $config['username'], $config['password'], array(PDO::ATTR_PERSISTENT => false));
} catch (PDOException $e) {
	die($e->getMessage() . "\n Please contact us to tell us about this error... jeremy@jeremymoritz.com");
}

#takes a pdo statement handle and returns an array of row objects
// 	NOTE: IF YOU WANT JUST THE FIRST ROW AS AN OBJECT, USE $sth->fetchObject() INSTEAD
function sthFetchObjects($sth) {
	$out = array();
	while($o = $sth->fetchObject()) {
		$out[] = $o;
	}
	return $out;
}

	/********************
	*	Functions		*
	********************/

//	THESE REPLACE THE $_GET, $_POST, etc. (can pass in a default value if none is found)
function apiGet($key, $default = false) {return isset($_GET[$key]) ? (is_array($_GET[$key]) ? $_GET[$key] : filter_input(INPUT_GET, $key)) : $default;}
function apiPost($key, $default = false) {return isset($_POST[$key]) ? (is_array($_POST[$key]) ? $_POST[$key] : filter_input(INPUT_POST, $key)) : $default;}
function apiCookie($key, $default = false) {return isset($_COOKIE[$key]) ? (is_array($_COOKIE[$key]) ? $_COOKIE[$key] : filter_input(INPUT_COOKIE, $key)) : $default;}
function apiSession($key, $default = false) {return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;}
	//	Check to see if a parameter has been set and if so, return it
function apiSnag($key, $default = false) {
	if (apiGet($key, $default)) {return apiGet($key, $default);
	} elseif (apiPost($key, $default)) {return apiPost($key, $default);
	} elseif (apiCookie($key, $default)) {return apiCookie($key, $default);
	} elseif (apiSession($key, $default)) {return apiSession($key, $default);
	} else {return $default;
	}
}
	//	try to snag a value; if it's not there, use the default passed-in; also allows for explicit typing of varName to make sure people are passing the correct type (i.e. integer)
function apiSnagType($varName, $default = false, $type = false) {
	$retVal = $default;
	if (apiSnag($varName)) {
		if (!$type || gettype(apiSnag($varName)) == $type) {	//	if type is set, make sure it's the right type
			$retVal = apiSnag($varName);
		}
	}
	return $retVal;
}

	//	determines if the user is on a mobile browser (phone, iPad, etc.)
function isMobile() {
	$user_agent = $_SERVER['HTTP_USER_AGENT'];	// Get the user agent
	// Array of known mobile user agents.  This list is from the 21 October 2010 WURFL File.  Most mobile devices send a pretty standard string that can be covered by one of these.  I believe I have found all the agents (as of the date above).  It's advised to periodically check this list against the WURFL file, available at: http://wurfl.sourceforge.net/ (though i don't know how to read that crazy-large xml file)
	$mobile_agents = Array(
		'240x320', 'acer', 'acoon', 'acs-', 'abacho', 'ahong', 'airness', 'alcatel', 'amoi', 'android', 'anywhereyougo.com', 'applewebkit/525', 'applewebkit/532', 'asus', 'audio', 'au-mic', 'avantogo', 'becker', 'benq', 'bilbo', 'bird', 'blackberry', 'blazer', 'bleu', 'cdm-', 'compal', 'coolpad', 'danger', 'dbtel', 'dopod', 'elaine', 'eric', 'etouch', 'fly ' , 'fly_', 'fly-', 'go.web', 'goodaccess', 'gradiente', 'grundig', 'haier', 'hedy', 'hitachi', 'htc', 'huawei', 'hutchison', 'inno', 'ipad', 'ipaq', 'ipod', 'jbrowser', 'kddi', 'kgt', 'kwc', 'lenovo', 'lg ', 'lg2', 'lg3', 'lg4', 'lg5', 'lg7', 'lg8', 'lg9', 'lg-', 'lge-', 'lge9', 'longcos', 'maemo', 'mercator', 'meridian', 'micromax', 'midp', 'mini', 'mitsu', 'mmm', 'mmp', 'mobi', 'mot-', 'moto', 'nec-', 'netfront', 'newgen', 'nexian', 'nf-browser', 'nintendo', 'nitro', 'nokia', 'nook', 'novarra', 'obigo', 'palm', 'panasonic', 'pantech', 'philips', 'phone', 'pg-', 'playstation', 'pocket', 'pt-', 'qc-', 'qtek', 'rover', 'sagem', 'sama', 'samu', 'sanyo', 'samsung', 'sch-', 'scooter', 'sec-', 'sendo', 'sgh-', 'sharp', 'siemens', 'sie-', 'softbank', 'sony', 'spice', 'sprint', 'spv', 'symbian', 'tablet', 'talkabout', 'tcl-', 'teleca', 'telit', 'tianyu', 'tim-', 'toshiba', 'tsm', 'up.browser', 'utec', 'utstar', 'verykool', 'virgin', 'vk-', 'voda', 'voxtel', 'vx', 'wap', 'wellco', 'wig browser', 'wii', 'windows ce', 'wireless', 'xda', 'xde', 'zte'
	);
	$is_mobile = false;	//	innocent until proven guilty (of being a mobile device)
	foreach ($mobile_agents as $device) {
		if (stristr($user_agent, $device)) {	//	check user-agent to see if it's in the list
			$is_mobile = true;	//	if so, it's a mobile device
			break;	// we don't need to test anymore once we get a "True" value
		}
	}
	return $is_mobile;	//	function returns true if it's mobile or false it's not
}

	//	Checks if an email is valid
function isValidEmail($email) {
	return preg_match("/^[^@]+@[^@]+$/", $email);
}

	//	converts "<a href='mailto:abc@example.com'>abc@example.com</a>" to "<a href='mailt&#111;:abc&#64;example.c&#111;m'>abc&#64;example.c&#111;m</a>" to hide from spamBots
function disguiseMail($mail) {
	return str_replace('@','&#64;',str_replace('o','&#111;', $mail));
}
	//	converts "abc@example.com" to "<a href='mailt&#111;:abc&#64;example.c&#111;m'>abc&#64;example.c&#111;m</a>" to hide from spamBots
function disguiseMailLink($simpleEmailAddress) {
	return disguiseMail("<a href='mailto:$simpleEmailAddress'>$simpleEmailAddress</a>");
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

	//	format inches into feet and inches
function formatHeight($inches) {
	$inches = intval($inches);
	return floor($inches / 12) . "' " . ($inches % 12) . '"';
}

	//	format date and time like "2/14/14 at 7:05pm"
function formatDateTime($dateTime) {
	return date('n/j/y \a\t g:ia', strtotime($dateTime));
}

	//	puts a circle around any number from 1 to 20 (using hex code... for greater numbers, use css border-radius)
function circleNumber($num) {
	$num = intval($num);
	if ($num <= 20) {
		return '&#' . strval(9311 + $num) . ';';	//	9311 is the start of the decimal characters 0-20
		//	return '&#x' . strval(2459 + $num) . ';';	//	this works identically but uses hexcode instead of decimal code
	} elseif ($num <= 35) {
		return '&#' . strval(12860 + $num) . ';';	//	&#12881; is the start of the circled decimal characters 21-35
	}

	return strval($num) . '.';
}

	//	add a parameter to a page with or without a query string
function addToQueryString($page, $queryString) {
	return $page . (strpos($page, '?') ? '&' : '?') . $queryString;
}

	//	determines if the IP is on the blacklist
function isBlacklistedIP($ip) {
	//	Check to be sure their IP is not on the blacklist before submitting it
	$blackIPs = array();
	$blacklistJSON = json_decode(file_get_contents('http://jeremymoritz.com/support/ip_blacklist.json'));
	$ipIsBlacklisted = false;	//	innocent until proven guilty
	foreach($blacklistJSON->blacklist as $bIP) {
		if ($ip === $bIP) {
			$ipIsBlacklisted = true;
			break;
		}
	}
	// $ipParts = explode('.', $ip);
	// foreach($blackIPs as $blackIP) {
	// 	$blackIPParts = explode('.', $blackIP);
	// 	if ($ipParts[0] === $blackIPParts[0] && $ipParts[1] === $blackIPParts[1]) {
	// 		$ipIsBlacklisted = true;	//	if the first 2 parts of IP address are the on the blacklist, then kick this guy out!
	// 	}
	// }

	return $ipIsBlacklisted;	//	function returns true if blacklisted, else false
}

	/********************
	*	Objects			*
	********************/

// $previousShow = new Show(2, 'Bye Bye Birdie', 'bbb', 'winter');
// $previousShow = new Show(34, '42nd Street', 's42', 'winter');
// $previousShow = new Show(26, 'High School Musical', 'hsm', 'winter');
$previousShow = new Show(47, 'Joseph and the Amazing Technicolor Dreamcoat', 'joseph', 'spring');
$currentShow = new Show(42, 'Grease', 'grease', 'summer');
$nextShow = new Show(1, 'West Side Story', 'wss', 'winter');

// set auditions for current show
$auditionsStart = '2015-10-31 9:00am';
$auditionsEnd = '2015-10-31 2:30pm';
$callbacksStart = '2015-11-07 9:00am';
$callbacksEnd = '2015-11-07 2:30pm';
$castMeetingStart = '2015-11-21 10:00am';
$castMeetingEnd = '2015-11-21 12:00pm';

$approxAuditionTime = 90;	//	approx. number of minutes each auditionee needs to plan to stay

//	get production fee prices
$productionFeeSingle = 150;
$productionFeeFamily = 250;
$productionFeePreshow = $productionFeeSingle - 50;

// get performances
$sql = "
	SELECT
		p.id,
		p.dateTime,
		p.theaterId,
		t.abbr,
		t.name,
		t.address,
		t.city,
		t.state,
		t.zip
	FROM ip_performances p
	INNER JOIN ip_theaters t ON p.theaterId = t.id
	WHERE p.showId = {$currentShow->getId()};";
$sth = $dbh->prepare($sql);
$sth->execute();
$performances = sthFetchObjects($sth);	//	fetch all of the performances
foreach($performances as $p) {
	$theater = new Theater($p->theaterId, $p->abbr, $p->name, $p->address, $p->city, $p->state, $p->zip);
	$currentShow->addPerformance(new Performance($p->id, $p->dateTime, $theater));
}

if (isset($nextShow)) {
	// get performances for NEXT show
	$sql = "
		SELECT
			p.id,
			p.dateTime,
			p.theaterId,
			t.abbr,
			t.name,
			t.address,
			t.city,
			t.state,
			t.zip
		FROM ip_performances p
		INNER JOIN ip_theaters t ON p.theaterId = t.id
		WHERE p.showId = {$nextShow->getId()};";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	$performances = sthFetchObjects($sth);	//	fetch all of the performances
	foreach($performances as $p) {
		$theater = new Theater($p->theaterId, $p->abbr, $p->name, $p->address, $p->city, $p->state, $p->zip);
		$nextShow->addPerformance(new Performance($p->id, $p->dateTime, $theater));
	}
}

if ($developerMode) {
	//	this will negate the stuff above if in developer Mode
	unset($currentShow);

	if (!is_null(filter_input(INPUT_GET, 'show')) || !is_null(filter_input(INPUT_GET, 'abbr'))) {
		$abbr = strtolower(apiGet('show') ? apiGet('show') : apiGet('abbr'));
		$sql = "
			SELECT
				id,
				title
			FROM ip_shows
			WHERE abbr = :abbr";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':abbr', $abbr, PDO::PARAM_STR);
		$sth->execute();
		$shows = sthFetchObjects($sth);	//	fetch all of the rows (only 1)
		$currentShow = count($shows) ? new Show($shows[0]->id, $shows[0]->title, $abbr) : null;	//	there's only ever going to be one
	}

	//	change the $currentShow at will
	// $currentShow = $currentShow ? $currentShow : new Show(34, '42nd Street DEVELOPER', 's42', 'winter');
	$currentShow = $currentShow ? $currentShow : new Show(26, 'High School Musical DEVELOPER', 'hsm', 'winter');

	// get performances
	$sql = "
		SELECT
			p.id,
			p.dateTime,
			p.theaterId,
			t.abbr,
			t.name,
			t.address,
			t.city,
			t.state,
			t.zip
		FROM ip_performances p
		INNER JOIN ip_theaters t ON p.theaterId = t.id
		WHERE p.showId = {$currentShow->getId()};";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	$performances = sthFetchObjects($sth);	//	fetch all of the performances
	foreach($performances as $p) {
		$theater = new Theater($p->theaterId, $p->abbr, $p->name, $p->address, $p->city, $p->state, $p->zip);
		$currentShow->addPerformance(new Performance($p->id, $p->dateTime, $theater));
	}

	//	change the performances at will instead of relying on the DB
	//	(comment out the line above that sets performances from the DB call)
	// $currentShow->addPerformance(new Performance(50, '2017-03-15 12:00:00', $theater));
	// $currentShow->addPerformance(new Performance(51, '2017-03-15 17:00:00', $theater));
	// $currentShow->addPerformance(new Performance(52, '2017-03-16 17:00:00', $theater));

	$body_class = 'developerMode';
}


/**
	* Logs messages/variables/data to browser console from within php
	*
	* @param $name: message to be shown for optional data/vars
	* @param $data: variable (scalar/mixed) arrays/objects, etc to be logged
	* @param $jsEval: whether to apply JS eval() to arrays/objects
	*
	* @return none
	* @author Sarfraz
	*
	* HOW TO USE:
	* <?php
	*   logConsole($variableToLog);
	* ?>
	*/
function logConsole($name, $data = null, $jsEval = false) {
	if (! $name) return false;

	$isevaled = false;
	$type = ($data || gettype($data)) ? 'Type: ' . gettype($data) : '';

	if ($jsEval && (is_array($data) || is_object($data))) {
		$data = 'eval(' . preg_replace('#[\s\r\n\t\0\x0B]+#', '', json_encode($data)) . ')';
		$isevaled = true;
	} else {
		$data = json_encode($data);
	}

	# sanitalize
	$data = $data ? $data : '';
	$search_array = array("#'#", '#""#', "#''#", "#\n#", "#\r\n#");
	$replace_array = array('"', '', '', '\\n', '\\n');
	$data = preg_replace($search_array,  $replace_array, $data);
	$data = ltrim(rtrim($data, '"'), '"');
	$data = $isevaled ? $data : ($data[0] === "'") ? $data : "'" . $data . "'";

$js = <<<JSCODE
\n<script>
console.log('$name');
console.log('------------------------------------------');
console.log('$type');
console.log($data);
console.log('\\n');
</script>
JSCODE;

	echo $js;
} # end logConsole

//	write to the console with PHP
//	example use:
//		phpConsoleLog(json_encode($myObj));	//	log an object
function phpConsoleLog($data) {
	$displayData = is_array($data) ? implode(',', $data) : $data;
	$displayData = str_replace(array("\r\n", "\r", "\n"), "", $displayData);

	echo "<script>console.log('phpConsoleLog: " . $displayData . "');</script>";
}
?>
