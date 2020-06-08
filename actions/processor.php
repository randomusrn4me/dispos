<?php
	require "../exp/phpFunc.php";
	
	$addr = "127.0.0.1";
	$usr = $_POST['userN'];
	$pw = $_POST['passW'];

	session_start();
	if(strlen($usr) < 1 || strlen($pw) < 1){
		$_SESSION['errno'] = 101;
		go('Location: ../index.php');
	}

	$db = "transactions";
	$conn = mysqli_connect($addr, $usr, $pw, $db);
	
	if (!$conn) {
		$_SESSION['errno'] = mysqli_connect_errno();
		switch(mysqli_connect_errno()){
			case 1049: echo "Database not found!"; break;
			case 2002: echo "Server connection error!"; break;
			case 1045: echo "Invalid username or password!"; break;
			default: echo "Unknown error!"; break;
		}
		go('Location: ../index.php');
	}

	$date = date('Y-m-d H:i:s');
	$ip = $_SERVER['REMOTE_ADDR'];
	$sql = "INSERT INTO `logins` (`LoginID`, `Username`, `Date`, `IP`) VALUES('', '$usr', '$date', '$ip')";
	if(!mysqli_query($conn, $sql)){
		echo "Error executing query! " . mysqli_error($conn) . "<br>";
		exit;
	}

	$_SESSION['userN'] = $usr;
	$_SESSION['passW'] = $pw;
	$_SESSION['conn'] = $conn;
	go('Location: ../main.php');
?>