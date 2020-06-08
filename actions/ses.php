<?php
	session_start();

	function logout(){
		session_unset();
		session_destroy();
		exit;
	}

	if(isset($_POST['type'])){
		$_SESSION['type'] = $_POST['type'];
	}

	if(isset($_POST['lgout'])){
		logout();
	}

	if(isset($_POST['transacID'])){
		$_SESSION['transacID'] = $_POST['transacID'];
	}

	if(isset($_POST['currentYear'])){
		$_SESSION['currentYear'] = $_POST['currentYear'];
	}

	if(isset($_POST['transacDate'])){
		$exp = explode("_", $_POST['transacDate']);
		$_SESSION['transacDate'] = $exp[0];
		$_SESSION['butDir'] = $exp[1];
	}
?> 