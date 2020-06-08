<?php 
	require "../exp/phpFunc.php";

	/// DATABASE CONNECTION ///
	session_start();
	require "../exp/connCommon.php";

	if(!strcmp($_POST['dir'], "KI")){
		$Amount = -$_POST['Amount'];
	} else $Amount = $_POST['Amount'];

	$sql = "UPDATE `simple` SET `Name` = '".$_POST['Name']."', `Amount` = '".$Amount."', `Date` = '".$_POST['Date']."', `Type` = '".$_POST['Type']."' WHERE `simple`.`ID` ='".$_POST['ID']."'"; 
	
	if(!mysqli_query($conn, $sql)){
		echo "Error executing query! " . mysqli_error($conn) . "<br>";
		exit;
	}
	echo "<script>window.close();</script>";
?> 