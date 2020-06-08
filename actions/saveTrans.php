<?php
	require "../exp/phpFunc.php";

	/// DATABASE CONNECTION ///
	session_start();
	require "../exp/connCommon.php";

	$name = "simple" . date("Y.m.d-H_i_s") . ".sql";
	$sql = "SELECT * INTO OUTFILE 'backups/$name' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n' FROM simple;"; 
	if(!mysqli_query($conn, $sql)){
		echo "Error executing query! " . mysqli_error($conn) . "<br>";
		exit;
	}

	$fileName = "C:\\xampp\mysql\data\backups\\".$name;
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	header('Content-Type: ' . finfo_file($finfo, $fileName));
	finfo_close($finfo);
	header('Content-Disposition: attachment; filename='.basename($fileName));
	header('Expires: 0');
    header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($fileName));
	ob_clean();
    flush();
    readfile($fileName);
	echo "<script>window.close();</script>";
?> 