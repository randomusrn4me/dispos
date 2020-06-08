<?php
    $address = "127.0.0.1";
	$dbUser = $_SESSION['userN'];
	$dbPass = $_SESSION['passW'];
	$db = "transactions";
	$conn = mysqli_connect($address, $dbUser, $dbPass, $db) ;
	if(!$conn) {
		echo "Cannot establish connection to the database!";
		exit;
    }
?>