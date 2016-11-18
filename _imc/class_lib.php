<?php	//	classes

class Theater {
	//	PROPERTIES
	private $id;
	private $abbr;
	private $name;
	private $address;
	private $city;
	private $state;
	private $zip;

	//	CONSTRUCTOR
	function __construct($id = null, $abbr = null, $name = null, $address = null, $city = null, $state = null, $zip = null) {
		$this->setId($id);
		$this->setAbbr($abbr);
		$this->setName($name);
		$this->setAddress($address);
		$this->setCity($city);
		$this->setState($state);
		$this->setZip($zip);
	}

	//	GETTERS
	public function getId() {return $this->id;}
	public function getAbbr() {return $this->abbr;}
	public function getName() {return $this->name;}
	public function getAddress() {return $this->address;}
	public function getCity() {return $this->city;}
	public function getState() {return $this->state;}
	public function getZip() {return $this->zip;}

	//	SETTERS
	public function setId($id) {$this->id = intval($id);}
	public function setAbbr($abbr) {$this->abbr = $abbr;}
	public function setName($name) {$this->name = $name;}
	public function setAddress($address) {$this->address = $address;}
	public function setCity($city) {$this->city = $city;}
	public function setState($state) {$this->state = $state;}
	public function setZip($zip) {$this->zip = $zip;}

	//	METHODS
	public function getAddressFormatted() {return "{$this->getAddress()}, {$this->getCity()}, {$this->getState()} {$this->getZip()}";}
}

class Performance {
	//	PROPERTIES
	private $id;
	private $dateTime;
	private $theater;

	//	CONSTRUCTOR
	function __construct($id = null, $dateTime = '2000-01-01 00:00:00', $theater = null) {
		$this->setId($id);
		$this->setDateTime($dateTime);
		$this->setTheater($theater);
	}

	//	GETTERS
	public function getId() {return $this->id;}
	public function getDateTime() {return $this->dateTime;}
	public function getTheater() {return $this->theater;}

	//	SETTERS
	public function setId($id) {$this->id = intval($id);}
	public function setDateTime($dateTime) {$this->dateTime = $dateTime;}
	public function setTheater(Theater $theater = null) {$this->theater = $theater;}

	//	METHODS
	public function getDateTimeFormatted() {return date('ga \o\n D, M j, Y', strtotime($this->getDateTime()));}
	public function getDate() {return date('Y-m-d', strtotime($this->getDateTime()));}
}

class Show {
	//	PROPERTIES
	private $id;
	private $title;
	private $abbr;
	private $season;
	private $performances = array();

	//	CONSTRUCTOR
	function __construct($id = null, $title = null, $abbr = null, $season = null, $performances = array()) {
		$this->setId($id);
		$this->setTitle($title);
		$this->setAbbr($abbr);
		$this->setSeason($season);
		$this->setPerformances($performances);
	}

	//	GETTERS
	public function getId() {return $this->id;}
	public function getTitle() {return $this->title;}
	public function getAbbr() {return $this->abbr;}
	public function getAbbrLower() {return strtolower($this->getAbbr());}
	public function getAbbrUpper() {return strtoupper($this->getAbbr());}
	public function getAbbrUcFirst() {return ucwords($this->getAbbrLower());}
	public function getSeason() {return $this->season;}
	public function getPerformances() {return $this->performances;}
	public function getTheater() {
		$perfs = $this->getPerformances();
		$perf = $perfs[0];

		return $perf->getTheater();
	}

	//	SETTERS
	public function setId($id) {$this->id = intval($id);}
	public function setTitle($title) {$this->title = $title;}
	public function setAbbr($abbr) {$this->abbr = $abbr;}
	public function setSeason($season) {$this->season = $season;}
	public function setPerformances(array $performances) {$this->performances = $performances;}

	//	METHODS
	public function addPerformance(Performance $performance) {$this->performances[] = $performance;}
	public function getPerformance($performanceId) {
		$chosenPerformance = null;
		foreach($this->getPerformances() as $performance) {
			if($performance->getId() === intval($performanceId)) {
				$chosenPerformance = $performance;
				break;
			}
		}
		return $chosenPerformance;
	}
}

class Seat {
	//	PROPERTIES
	private $id;
	private $number;
	private $coordX;
	private $coordY;
	private $price;

	//	CONSTRUCTOR
	function __construct($id = null, $number = null, $coordX = null, $coordY = null, $price = 0) {
		$this->setId($id);
		$this->setNumber($number);
		$this->setCoordX($coordX);
		$this->setCoordY($coordY);
		$this->setPrice($price);
	}

	//	GETTERS
	public function getId() {return $this->id;}
	public function getNumber() {return $this->number;}
	public function getCoordX() {return $this->coordX;}
	public function getCoordY() {return $this->coordY;}
	public function getPrice() {return $this->price;}

	//	SETTERS
	public function setId($id) {$this->id = intval($id);}
	public function setNumber($number) {$this->number = $number;}
	public function setCoordX($coordX) {$this->coordX = $coordX;}
	public function setCoordY($coordY) {$this->coordY = $coordY;}
	public function setPrice($price) {$this->price = $price;}
}

class Row {
	//	PROPERTIES
	private $id;
	private $seats = array();

	//	CONSTRUCTOR
	function __construct($id = null) {
		$this->id = $id;
	}

	//	GETTERS
	public function getId() {return $this->id;}
	public function getSeats() {return $this->seats;}

	//	SETTERS
	public function setId($id) {$this->id = intval($id);}
	public function setSeats($seats) {$this->seats = $seats;}

	//	METHODS
	public function addSeat($seatId = null, $seatNumber = null, $coordX = null, $coordY = null, $price = 0) {
		$this->seats[] = new Seat($seatId, $seatNumber, $coordX, $coordY, $price);
	}
}

class Section {
	//	PROPERTIES
	private $id;
	private $rows = array();

	//	CONSTRUCTOR
	function __construct($id = null) {
		$this->id = $id;
	}

	//	GETTERS
	public function getId() {return $this->id;}
	public function getRows() {return $this->rows;}
	public function getRow($rowId) {return $this->rows[$rowId];}

	//	SETTERS
	public function setId($id) {$this->id = intval($id);}
	public function setRows($rows) {$this->rows = $rows;}

	//	METHODS
	public function addRowIfNeeded($rowId) {
		$found = false;
		foreach($this->rows as $r) {
			if($r->getId() === $rowId) {
				$found = true;
				break;
			}
		}
		if(!$found) {
			$this->rows[$rowId] = new Row($rowId);
		}
	}
}

//	Just for show survey
class SurveyShow {
	//	PROPERTIES
	protected $id;
	protected $title;
	protected $score = 0;
	protected $scoreByGender = array(
		"F" => 0,
		"M" => 0,
		"U" => 0	//	unknown
	);

	//	CONSTRUCTOR (to be run at instantiation time)
	function __construct($newId, $newTitle) {	//	must set properties at time of instantiation
		$this->id = $newId;
		$this->title = $newTitle;
	}

	//	GETTERS
	public function getId() {
		return $this->id;
	}
	public function getScore() {
		return $this->score;
	}
	public function getScoreByGender($gender) {
		if (!in_array($gender, array('F', 'M'))) {
			$gender = 'U';
		}

		return $this->scoreByGender[$gender];
	}
	public function getTitle() {
		return $this->title;
	}

	//	SETTERS
	public function setScore($newScore) {
		$this->score = $newScore;
	}

	//	METHODS
	public function setScoreByGender($gender, $newScore) {
		if (!in_array($gender, array('F', 'M'))) {
			$gender = 'U';
		}

		$this->scoreByGender[$gender] = $newScore;
	}
}
?>
