<?php
	require "exp/phpFunc.php";

	/// DATABASE CONNECTION ///
	session_start();
	require "exp/connCommon.php";

	if($_POST['ID'] == "") $id = "%"; else $id= $_POST['ID'];
	if($_POST['Name'] == "") $name = "%"; else $name = '%'.$_POST['Name'].'%';
	if($_POST['Date'] == "") $date = "%"; else $date = '%'.$_POST['Date'].'%';
	if($_POST['Type'] == "") $type = "%"; else $type = $_POST['Type'];
	
	if($_POST['Amount'] == "") $amount = "%";
	else {
		$amount = abs($_POST['Amount']);
		if( isset($_POST['dir']) && !strcmp($_POST['dir'], "KI") ) $amount = -$amount;
		//if(!isset($_POST['dir'])) $amount = "%". $amount;
	}

	$sql = "SELECT * FROM simple WHERE ID LIKE '$id' AND Name LIKE '$name' AND Date LIKE '$date' AND Amount LIKE '$amount' AND Type LIKE '$type'";
	$result = mysqli_query($conn, $sql) or die("Query error!");
	$resultArray = array();
	$i = 0;
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()) $resultArray[$i++] = $row;
	}

	$pageTitle = "Keresés";
	require "exp/pageCommons.php";
?>

<body>
	<div class="">
		<div class="loginContainer " style="height:fit-content; width: fit-content;">
			<p id='wolp' style="text-align: center; font-weight: bold; font-size: 20px; margin-top:-10px"><u>Keresés eredménye</u></p>
			<p id='wolp' style="text-align: center; font-weight: bold; font-size: 15px; margin-top:-10px"><?php echo "Találatok száma: " . count($resultArray); ?></p>
			<div class="divcenter">
				<table class="transtable" style="">
					<tr style="" class="topRow">
						<th class="srcRow" style="min-width:90px;"> Dátum </th>
						<th class="srcRow"> Irány </th>
						<th class="srcRow"> Név </th>
						<th class="srcRow"> Összeg</th>
						<th class="srcRow"> Típus</th>
						<th class="srcRow"> Azonosító</th>
					</tr>
						<?php
							for($i = 0; $i < count($resultArray); $i++){
								echo "<tr><th class='srcRow' style='width: fit-content;padding:5px;'>";
								echo $resultArray[$i]['Date']."</th><th>";
								if($resultArray[$i]['Amount'] < 0) echo "Kiadás</th>";
								else echo "Bevétel</th>";
								echo"<th class='srcRow'>" . $resultArray[$i]['Name'] . "</th>";
								echo"<th class='srcRow'>" . number_format(abs($resultArray[$i]['Amount']), 0, '.', '.') . " Ft</th>";
								echo"<th class='srcRow'>" . ucfirst($resultArray[$i]['Type']) . "</th>";
								echo"<th class='srcRow'>" . $resultArray[$i]['ID'] . "</th></tr>";	
							}
						?>
				</table>
				<input type='button'  class="sub" value='Bezárás' onclick="window.close()">
			</div>
		</div>
	</div>
</body>
</html>