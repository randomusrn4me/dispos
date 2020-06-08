<?php
	require "../exp/phpFunc.php";

	/// DATABASE CONNECTION ///
	session_start();
	require "../exp/connCommon.php";
	
	$sql = "UPDATE `variables` SET `NumContent` = '".$_POST['amountOffset']."' WHERE `variables`.`Details` ='amount offset'";
	if(!mysqli_query($conn, $sql)){
		echo "Error executing query! " . mysqli_error($conn) . "<br>";
		exit;
	}
	
	$sql = "UPDATE `variables` SET `TextContent` = '".$_POST['lan']."' WHERE `variables`.`Details` ='language'"; 
	if(!mysqli_query($conn, $sql)){
		echo "Error executing query! " . mysqli_error($conn) . "<br>";
		exit;
	}

	echo "<script>window.close();</script>";
?> 