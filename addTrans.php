<?php
	require "exp/phpFunc.php";

	/// DATABASE CONNECTION ///
	session_start();
	require "exp/connCommon.php";
	
	$unk = false;
	if(isset($_SESSION['type'])){
		switch($_SESSION['type']){
			case "KI?": $unk = true;
			case "KI": $be = false; break;
			case "BE?": $unk = true;
			case "BE": $be = true; break;
		}
	}
	
	if(isset($_SESSION['transacDate']) && isset($_SESSION['butDir'])){
		$date = $_SESSION['transacDate'];
		if($_SESSION['butDir'] == "OUT") $be = false;
		else $be = true;
		$_SESSION['toBeClosed'] = true;
	} else $date = date('Y').'-'.date('m').'-'.date('d');
	
	$pageTitle = "Tranzakció hozzáadása";
	require "exp/pageCommons.php";
?>

<body>
	<div class="pagediv ">
		<div class="loginContainer " style="height:500px; width: fit-content;">
			<p id='wolp' style="text-align: center; font-weight: bold; font-size: 20px; margin-top:-10px"><u>Tranzakció hozzáadása</u></p>
			<form name="edit" id="edital" method="post" action="actions/addToDatabase.php" onsubmit="" autocomplete="off">
				<table>
					<tr>
						<th style="text-align: left">Azonosító: </th>
						<th> <input type='number' id='ID' name='ID'> </th>
					</tr>

					<tr>
						<th style="text-align: left">Név: </th>
						<th> <input autofocus type='text' value='<?php if($unk) echo '???';?>' name='Name' id='Name'> </th>
					</tr>

					<tr>
						<th style="text-align: left">Összeg: </th>
						<th> <input type='number' name='Amount' id='Amount'> </th>
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
									echo "<option selected='selected' value=" . $_SESSION['typeArray'][0] . ">" . $_SESSION['upperCaseTypeArray'][0] . "</option>";
									for($i = 1; $i < count($_SESSION['typeArray']); $i++){
										echo "<option value=" . $_SESSION['typeArray'][$i]. ">" . $_SESSION['upperCaseTypeArray'][$i] . "</option>";
									}
								?>
							</select>
						</th>
					</tr>

					<tr>
						<th style="text-align: left">Dátum: </th>
						<th> <input type='date' value='<?php echo $date ?>' name='Date' id='Date'> </th>
					</tr>

					<tr >
						<th colspan="2" style="text-align: center"><input type='submit' id='subs' class="sub" value='Hozzáad' onclick=""></th>
					</tr>

					<tr >
						<th colspan="2" style="text-align: center"><input type='button'  class="sub" value='Bezárás' onclick="window.close()"></th>
					</tr>
				</table>
			</form>
		</div>
	</div>
</body>
</html>

<?php
	unset($_SESSION['transacDate']);
	unset($_SESSION['butDir']);
?>