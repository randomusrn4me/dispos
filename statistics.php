<?php
	require "exp/phpFunc.php";

	session_start();
	require "exp/connCommon.php";

	$currentYear = $_SESSION['currentYear'];
	$collectedAmounts = array();
	$coefficients = array();
	
	for($i = 1; $i <= 12; $i++){
		$j = 0;
		$collectedAmounts[$i] = array();
		if($i < 10) $searchedMonth = "0" . $i;
		else $searchedMonth = $i;
		$sql = "SELECT * FROM simple WHERE Date like '" . $currentYear . "-" . $searchedMonth . "-%'";
		$result = mysqli_query($conn, $sql) or die("Query error!");
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()){
				$collectedAmounts[$i][$j++] = $row['Amount'];
			}
		} else {
			$coefficients[$i] = -1;
			continue;
		}
		$exp = 0;
		$gain = 0;

		for($k = 0; $k < count($collectedAmounts[$i]); $k++){
			if($collectedAmounts[$i][$k] < 0) $exp += abs($collectedAmounts[$i][$k]);
			else $gain += $collectedAmounts[$i][$k];
		}
		if($gain < 1) $coefficients[$i] = -1;
		else $coefficients[$i] = ($exp / $gain) * 100;
	}

	$pageTitle = "Statisztikák";
	require "exp/pageCommons.php";
?>
<body>
	<div class=" ">
		<div class="loginContainer " style="height:fit-content; width: fit-content;">
			<p id='wolp' style="text-align: center; font-weight: bold; font-size: 20px; margin-top:-10px">Együtthatók</p>
			<div>
				<?php
					$index = 0;
					for($i = 0; $i < 3; $i++){
						echo "<div ><ul class='statrow'>";
						for($j = 0; $j < 4; $j++){
							$coefficient = $coefficients[$index + 1];
							$colorArray = colorsByCoefficient($coefficient);
							echo "<li class='sor'>";
							echo "<div class='statdiv divcenter' style='background-color:" . $colorArray[0] . "; color:" . $colorArray[1] . "'> <b>$monthNames[$index]</b> <br><br> <span style='font-size: 25px;'>";
							$index++;
							if($coefficient == -1) echo '??';
							else echo number_format($coefficient, 1, ',', '.') . ' %';
							echo "</span></div></li>";
						}
						echo "</ul></div>";
					}
				?>
			</div>
		</div>
	</div>
</body>
</html>
