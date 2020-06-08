<?php
	require "exp/phpFunc.php";

	/// DATABASE CONNECTION ///
	session_start();
	require "exp/connCommon.php";

	$sql = "SELECT * FROM simple WHERE ID='" . $_SESSION['transacID'] . "'";
	$result = mysqli_query($conn, $sql) or die("Query error!");
	if($result->num_rows > 0) $row = $result->fetch_assoc();

	if(isPositive($row['Amount'])) $be = true;
	else $be = false;

	$pageTitle = "Tranzakció szerkesztése";
	require "exp/pageCommons.php";
?>

<body>
	<div class="pagediv ">
		<div class="loginContainer " style="height:500px; width: fit-content;">
			<p id='wolp' style="text-align: center; font-weight: bold; font-size: 20px; margin-top:-10px"><u>Tranzakció szerkesztése</u></p>
			<form name="edit" id="edital" method="post" action="actions/editInDatabase.php" onsubmit="" autocomplete="off">
				<table >
					<tr>
						<th style="text-align: left">Azonosító: </th>
						<th> <input type='number' style="border-color: black" id='ID' value='<?php echo $row['ID'];?>' name='ID' readonly> </th>
					</tr>

					<tr>
						<th style="text-align: left">Név: </th>
						<th> <input type='text' value='<?php echo $row['Name'];?>' name='Name' id='Name'> </th>
					</tr>

					<tr>
						<th style="text-align: left">Összeg: </th>
						<th> <input type='number' value='<?php echo abs($row['Amount']);?>' name='Amount' id='Amount'> </th>
					</tr>

					<tr>
						<th style="text-align: left">Irány: </th>
						<th> KI <input type='radio' value="KI" name="dir" <?php if(!$be) echo "checked"; ?>> BE <input type='radio' value="BE" name="dir" id="dir" <?php if($be) echo "checked"; ?>> </th>
					</tr>

					<tr>
						<th style="text-align: left">Típus: </th>
						<th>
							<select name="Type" id="monID" class="manBoxStyle divcenter">
								<?php
									echo "<option selected='selected' value=". $row['Type'] .">". ucfirst($row['Type']) ."</option>";
									for($i = 1; $i < count($_SESSION['typeArray']); $i++){
										if($_SESSION['typeArray'][$i] == $row['Type']) continue;
										echo "<option value=". $_SESSION['typeArray'][$i] .">". $_SESSION['upperCaseTypeArray'][$i] ."</option>";
									}
								?>
							</select>
						</th>
					</tr>
					<tr>
						<th style="text-align: left">Dátum: </th>
						<th> <input type='date' value='<?php echo $row['Date'];?>' name='Date' id='Date'> </th>
					</tr>
					<tr >
						<th colspan="2" style="text-align: center"><input type='submit' id='subs' class="sub" value='Frissít' onclick=""></th>
						
					</tr>
					<tr >
						<th colspan="2" style="text-align: center"><input type='button'  class="sub" value='Bezárás' onclick="window.close()"></th>
						
					</tr>
				</table>
			</form>
			
			<form name='del' id='del' method="post" action="actions/deleteFromDatabase.php">
				<input type='hidden' value='<?php echo $row['ID'];?>' name='ID' readonly><br>
				<input type='button' value='Törlés' id='delbut' class="sub" style="background-color: red; color: white" onclick="confirmDeletion()">
			</form>			
		</div>
	</div>

	<script>
		function confirmDeletion(){
			var kons = confirm("Biztosan törölni szeretnéd a tranzakciót?");
			if(kons){
				document.getElementById('del').submit();
			}
		}
	</script>
</body>
</html>