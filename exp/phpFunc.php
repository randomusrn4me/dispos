<?php
    /// FUNCTIONS ///
    function logout($connection){
        mysqli_close($connection);
        session_unset();
        session_destroy();
        exit;
    }

    function go($location){
        header($location);
        exit("");
	}

    function compare1($a, $b){
		$n = strcmp($a->Date, $b->Date);
		if($n != 0) return $n;

		$n = $a->Amount - $b->Amount;
		return $n;
    }
    
    function isPositive($number){
		if($number == 0) die('TRANSACTIONS WITH NO AMOUNT ARE NOT ALLOWED!');
		else if($number < 0) return false;
		else return true;
    }

    function colorsByCoefficient($coefficient){
        if($coefficient == -1){
            $backgroundColor = 'white'; $textColor = 'black';
        } else if($coefficient < 25){
			$backgroundColor = '#b3dee3'; $textColor = 'black';
		} else if($coefficient < 50) {
			$backgroundColor = '#5bf5ba'; $textColor = 'black';
		} else if($coefficient < 75) {
			$backgroundColor = '#c6e366'; $textColor = 'black';
		} else if($coefficient < 100) {
			$backgroundColor = '#e0be4c'; $textColor = 'black';
		} else if($coefficient < 130){
			$backgroundColor = '#e07d4c'; $textColor = 'black';
		} else if($coefficient < 160) {
			$backgroundColor = '#cf4e0e'; $textColor = 'white';
		} else if($coefficient < 200) {
			$backgroundColor = '#e6331c'; $textColor = 'white';
		} else if($coefficient < 250) {
			$backgroundColor = '#ba1414'; $textColor = 'white';
		} else {
			$backgroundColor = '#5e0000'; $textColor = 'white';
        }
        
        return array($backgroundColor, $textColor);
	}
	
    /// FIELDS ///
    $monthNames = array("Január", "Február", "Március", "Április", "Május", "Június", "Július", "Augusztus", "Szeptember", "Október", "November", "December");
	$dayNames = array("Mon" => "Hétfő", "Tue" => "Kedd", "Wed" => "Szerda", "Thu" => "Csütörtök", "Fri" => "Péntek", "Sat" => "Szombat", "Sun" => "Vasárnap");
	$languages = array("hun" => "Magyar", "eng" => "Angol", "ger" => "Deutsch");

    /// CLASSES ///
    class Transaction {
		public $ID;
		public $Name;
		public $Amount;
		public $Date;
		public $Type;

		function __construct($ID, $Name, $Amount, $Date, $Type){
			$this->ID = $ID;
			$this->Name = $Name;
			$this->Amount = $Amount;
			$this->Date = $Date;
			$this->Type = $Type;
		}
	}

?>