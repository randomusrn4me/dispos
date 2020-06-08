<?php
	require "exp/phpFunc.php";

	/// DATABASE CONNECTION ///
	session_start();
	require "exp/connCommon.php";

	$sql = 'SELECT NumContent FROM variables WHERE Details=\'amount offset\'';
	$sum = 0;
	$result = mysqli_query($conn, $sql) or die("Query error!");
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()) {
			$sum += $row['NumContent'];
		}
	}
		
	$sql = 'SELECT TextContent FROM variables WHERE Details=\'language\'';
	$result = mysqli_query($conn, $sql) or die("Query error!");
	if($result->num_rows > 0){
		while( $row = $result->fetch_assoc() ) $lan = $row['TextContent'];
		switch($lan){
			case "hun": ; break;
			case "eng": ; break;
			case "ger": ; break;
		}
	}

	$pageTitle = "Beállítások";
	require "exp/pageCommons.php";
?>

<body>
	<div class="pagediv ">
		<div class="loginContainer " style="height:500px; width: fit-content;">
			<p id='wolp' style="text-align: center; font-weight: bold; font-size: 20px; margin-top:-10px"><u>Beállítások</u></p>
			<form name="edit" id="edital" method="post" action="actions/changeSettings.php" onsubmit="" autocomplete="off">
				<table>
					<tr>
						<th style="text-align: left">
							Fordulóérték:
						</th>

						<th>
							<input type='number' value='<?php echo $sum ?>' name='amountOffset'>
						</th>
					</tr>

					<tr>
						<th style="text-align: left">
							Nyelv: 
						</th>

						<th>
							<select class="manBoxStyle" name='lan'> 
								<option value="hun" <?php if($lan == "hun") echo "selected='selected'"; ?>>Magyar</option>
								<option value="eng" <?php if($lan == "eng") echo "selected='selected'"; ?>>English</option>
								<option value="deu" <?php if($lan == "deu") echo "selected='selected'"; ?>>Deutsch</option>
							</select>
						</th>
					</tr>

					<tr >
						<th colspan="2" style="text-align: center"><input type='submit' id='subs' class="sub" value='Frissít' onclick=""></th>
					</tr>

					<tr >
						<th colspan="2" style="text-align: center"><input type='button' class="sub" value='Bezárás' onclick="window.close()"></th>
					</tr>
				</table>
			</form>
			<form name="saveTrans" method="post" action="actions/saveTrans.php">
				<table>
					<tr>
						<th style="text-align: left">
							Mentés:
						</th>

						<th>
							<input type='submit' style='width: fit-content;' id='subs' class="sub" value='Tranzakciók mentése' onclick="">
						</th>
					</tr>
				</table><br>
			</form>
	</div>
</body>
</html>