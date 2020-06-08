<?php
	require "../exp/phpFunc.php";

	/// DATABASE CONNECTION ///
	session_start();
	require "../exp/connCommon.php";

	if( !(isset($_POST['Amount']) && isset($_POST['Name']) && isset($_POST['Type']) && isset($_POST['Date']) && isset($_POST['dir'])) ) {
		exit ("Hiányzó mezők!");
	}

	if(empty($_POST['Amount']) || empty($_POST['Name']) || empty($_POST['Type']) || empty($_POST['Date']) || empty($_POST['dir'])){
		exit("Minden mező kitöltése kötelező!");
	}

	if($_POST['Amount'] == 0) exit("Nulla összegű tranzakciók nem támogatottak!");
	//echo $_POST['dir']; die;
	if(!strcmp($_POST['dir'], "KI")) $Amount = -$_POST['Amount'];
	else $Amount = $_POST['Amount'];
	
	$sql = "INSERT INTO `simple` (`ID`, `Name`, `Amount`, `Date`, `Type`) VALUES ('".$_POST['ID']."', '".$_POST['Name']."', '".$Amount."', '".$_POST['Date']."', '".$_POST['Type']."')";
	if(!mysqli_query($conn, $sql)){
		echo "Error executing query! " . mysqli_error($conn) . "<br>";
		exit;
	}

	if($_POST['Name'] == '???' || isset($_SESSION['toBeClosed'])) {
		echo "<script>window.close();</script>";
		unset($_SESSION['toBeClosed']);
	} else if (isset($_POST['quick'])) header('Location: ../main.php');
	else header('Location: ../addTrans.php');
?> 