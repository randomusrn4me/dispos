<?php
	/// INITIALIZATION ///
	require "exp/phpFunc.php";
	session_start();

	/// DATABASE CONNECTION ///
	if(!isset($_SESSION['userN']) || !isset($_SESSION['passW'])){
		echo "Invalid login!";
		go("Location: index.php");
	}
	require "exp/connCommon.php";

	/// SETTING THE CURRENT DATE'S DETAILS ///
	if(isset($_POST['incomingMonth']) && isset($_POST['incomingYear'])){
		if( $_POST['incomingMonth'] < 10){
			$currentMonth = "0" . $_POST['incomingMonth']; // Current month in MM format
		} else $currentMonth = $_POST['incomingMonth'];
		$currentYear = $_POST['incomingYear'];
	} else{
		$currentMonth = date("m");
		$currentYear = date("Y");
	} 
	$_SESSION['currentYear'] = $currentYear;
	$currentMonthName = $monthNames[$currentMonth - 1];

	/// GETTING AMOUNT OFFSET ///
	$date = date('Y').'-'.date('m').'-'.date('d');
	$sql = 'SELECT NumContent FROM variables WHERE Details=\'amount offset\'';
	$sum = 0;
	$result = mysqli_query($conn, $sql) or die("Query error!");
	if($result->num_rows > 0){
		$row = $result->fetch_assoc();
		$sum += $row['NumContent'];
	}

	/// CALCULATING TRANSACTION SUM ///
	$sql = "SELECT * FROM simple";
	$result = mysqli_query($conn, $sql) or die("Query error!");
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()) {
			$sum += $row['Amount'];
		}
	}
	
	/// CREATING ARRAYS OF TYPES /// 
	$_SESSION['upperCaseTypeArray'] = array();
	$_SESSION['typeArray'] = array();
	$index = 0;
	$sql = "SELECT * FROM types";
	$result = mysqli_query($conn, $sql) or die("Query error!");
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()) {
			$_SESSION['typeArray'][$index] = $row['Type'];
			$_SESSION['upperCaseTypeArray'][$index++] = ucfirst($row['Type']);
		}
	}

	/// GETTING TRANSACTIONS AND THEIR DETAILS FROM THIS MONTH ///
	$sql = "SELECT * FROM simple WHERE Date like '" . $currentYear . "-" . $currentMonth . "-%'";
	$thisMonTransacArray = array();
	$numOfTransac = 0;
	$biggestExpense = 0;
	$biggestTaking = 0;
	$numThisMonExpenses = 0;
	$numThisMonTakings = 0;
	$numThisMonUnkExp = 0;
	$numThisMonUnkTak = 0;
	
	$result = mysqli_query($conn, $sql) or die("Query error!");
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()) {
			$tempTransac = new Transaction($row['ID'], $row['Name'], $row['Amount'], $row['Date'], $row['Type']);
			$thisMonTransacArray[$numOfTransac++] = $tempTransac;
		}
		usort($thisMonTransacArray, 'compare1');

		// COUNTING TRANSACTIONS, CALCULATING MAX/MIN VALUES
		foreach($thisMonTransacArray as $i){
			if($i->Amount < 0){
				$numThisMonExpenses++;
				if($i->Name == "???") $numThisMonUnkExp++;
				if($i->Amount < $biggestExpense) $biggestExpense = $i->Amount;
			}
			if($i->Amount > 0){
				$numThisMonTakings++;
				if($i->Name == "???") $numThisMonUnkTak++;
				if($i->Amount > $biggestTaking) $biggestTaking = $i->Amount;
			}
		}
		$biggestExpense = abs($biggestExpense);
	}

	/// COEFFICIENT ///
	$sumOfExpenses = 0;
	$sumOfTakings = 0;

	for($i = 0; $i < $numOfTransac; $i++){
		if($thisMonTransacArray[$i]->Amount < 0) $sumOfExpenses += $thisMonTransacArray[$i]->Amount;
		else $sumOfTakings += $thisMonTransacArray[$i]->Amount;
	}
	$sumOfExpenses = abs($sumOfExpenses);
	if($sumOfTakings < 1) $coefficient = -1;
	else $coefficient = ($sumOfExpenses / $sumOfTakings) * 100;

	$colorArray = colorsByCoefficient($coefficient);

	///  COLUMN LENGTHS ///
	$maxExpenseNumHelper = 1;
	$maxTakingNumHelper = 1;
	$wasExp = false;
	$wasTak = false;
	$maxNumDayExp = 0;
	$maxNumDayTak = 0;
	for($i = 0; $i < $numOfTransac; $i++){
		if(isPositive($thisMonTransacArray[$i]->Amount) == false){ //We're loking ar expenses
			if(!$wasExp) $maxNumDayExp++;
			$wasExp = true;
			while(($maxExpenseNumHelper + $i < $numOfTransac) && !strcmp($thisMonTransacArray[$i]->Date, $thisMonTransacArray[$i + $maxExpenseNumHelper]->Date)){
				if(isPositive($thisMonTransacArray[$i + $maxExpenseNumHelper]->Amount) == false){ 
					$maxExpenseNumHelper++;
					$maxNumDayExp++;
				} else break;
			}
		} else { //We're loking at income
			if(!$wasTak) $maxNumDayTak++;
			$wasTak = true;
			while(($maxTakingNumHelper + $i < $numOfTransac) && !strcmp($thisMonTransacArray[$i]->Date, $thisMonTransacArray[$i + $maxTakingNumHelper]->Date)){
				if(isPositive($thisMonTransacArray[$i + $maxTakingNumHelper]->Amount)){
					$maxTakingNumHelper++;
					$maxNumDayTak++;
				} else break;
			}
			}	
	}
	$numOfExpRows = $maxNumDayExp + 1;
	$numOfTakRows = $maxNumDayTak + 1;
?>

<!-- PAGE START -- PAGE START -- PAGE START -- PAGE START -- PAGE START -- PAGE START -- PAGE START -- PAGE START -- PAGE START -- PAGE START -- PAGE START -- PAGE START -- PAGE START -- PAGE START -->

<!doctype html>
<html lang="hu">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Tranzakciók | Dispositifs</title>
	<link rel="icon" href="pics/displogo.png" type="image/png">
	<link rel="stylesheet" href="exp/styleSheet.css" type="text/css">
	<script src="exp/jsFunc.js" type="text/javascript"></script>
	<script src="exp/jquery-3.5.1.min.js" type="text/javascript"></script>
	<script></script>
</head>
<body>
	<div class="pageEncapsulation" style="text-align: center;">
		<header class="quickAddHeader">
			<ul class="headlist">
				<li class="sor">
					<form name="qins" id="qinsID" method="post" action="actions/addToDatabase.php" autocomplete="off">
						<input type="text" style="width:140px" placeholder="Név" name="Name">
						<input type="number" style="width:110px" id='Amount' placeholder="Összeg" name="Amount">
						
						<select name="Type" id="monID" class="manBoxStyle divcenter">
								<?php
									echo "<option selected='selected' value=" . $_SESSION['typeArray'][0] . ">" . $_SESSION['upperCaseTypeArray'][0] . "</option>";
									for($i = 1; $i < count($_SESSION['typeArray']); $i++){
										echo "<option value=" . $_SESSION['typeArray'][$i]. ">" . $_SESSION['upperCaseTypeArray'][$i] . "</option>";
									}
								?>
						</select>
						<div class="manBoxStyle" style="background-color: white; width: 120px">KI <input id='dirk' type='radio' checked value="KI" name="dir"> BE <input id='dirb' type='radio' value="BE" name="dir"></div>
						<input type='date' style="width:170px" value='<?php echo $date ?>' name='Date' id='Date'>
						<input type='submit' id='subs' class="sub" style='width:110px' value='Hozzáad' onclick="">
						<input type="hidden" name="quick" value="quick">
						<input type="hidden" name="ID" value="">
						<input class="sub" id="emptied" style='width:90px; background-color: red; color: white' type='reset' value='Kiűrít' >
					</form>
				</li>
				<li class="sor">
					<div id="prognosis" class = "prognosis status divcenter">--</div>
				</li>
			</ul>
		</header>

		<!-- HEADER -- HEADER -- HEADER -- HEADER -- HEADER -- HEADER -- HEADER -- HEADER -- HEADER -- HEADER -- HEADER -- HEADER -- HEADER -- HEADER -- HEADER -- HEADER -- HEADER -- HEADER -- HEADER -- HEADER -->

		<header class="movingHeader" id="myHeader">
			<ul class = "headlist">
				<!-- Menu button -->
				<li class="sor">
					<div class="status divcenter" style='height:25px; float:left; margin-right:10px'>
						<span style="font-size:30px;cursor:pointer" onclick="openNav()"><img class="" style='width:20px;' src="pics/menu.png" alt="Menü"></span>
					</div>
				</li>

				<!-- Total sum -->
				<li class="sor">
					<div class="status divcenter" style="font-size:22px; height: 24px;">
						<b><?php echo number_format($sum, 0, '.', '.');?> Ft</b>
					</div>
				</li>

				<!-- Coefficient -->
				<li class="sor"> 
					<div class="status divcenter" style='<?php echo 'color:' . $colorArray[1] .'; background-color: '. $colorArray[0]; ?>'>
						<b><?php
							if($coefficient == -1) echo '??';
							else echo number_format($coefficient, 1, ',', '.') . ' %';
						?></b>
					</div>
				</li>

				<!-- Exp sum -->
				<li class="sor"> 
					<div class="status divcenter" style='background-color:#e0a380'>
						<img class="" style='width:20px; transform:rotate(180deg); filter: invert(18%) sepia(54%) saturate(2803%) hue-rotate(26deg) brightness(95%) contrast(102%); margin-left: -5px;' src="pics/arrow.png" alt="Ki Σ: ">
						<b><?php 
							echo " ". number_format($sumOfExpenses, 0, ',', '.') . ' Ft <span style="font-size=10px">' . " [" . $numThisMonExpenses . " db] </span>";
						?></b>
					</div>
				</li>

				<!-- Tak sum -->
				<li class="sor"> 
					<div class="status divcenter" style='background-color:#8ef5a9'>
						<img class="" style='width:20px; filter: invert(26%) sepia(66%) saturate(1636%) hue-rotate(61deg) brightness(96%) contrast(102%); margin-right: -5px;' src="pics/arrow.png" alt="Ki Σ: ">
						<b><?php
							echo " ". number_format($sumOfTakings, 0, ',', '.') . ' Ft ' . " [" . $numThisMonTakings . " db]";
						?></b>
					</div>
				</li>

				<!-- Refresh -->
				<li class="sor" style="margin-left:20px;">
					<div class = "refresh status divcenter">
						<img class="" style='width:20px;' src="pics/refr.png" alt="Frissítés">
					</div>
				</li>

				<!-- Logout -->
				<li class="sor">
					<div class = "lgout status divcenter">
						<img class="" style='width:20px;' src="pics/lgout.png" alt="Kijelentkezés">
					</div>
				</li>
			</ul>
		</header>

		<!-- TRANSTABLE -- TRANSTABLE -- TRANSTABLE -- TRANSTABLE -- TRANSTABLE -- TRANSTABLE -- TRANSTABLE -- TRANSTABLE -- TRANSTABLE -- TRANSTABLE -- TRANSTABLE -- TRANSTABLE -- TRANSTABLE -- TRANSTABLE -- TRANSTABLE -- TRANSTABLE -->
		
		<div class="divForTable divcenter">

			<table class="transtable" style="background-color: ;">
				<tr class="topRow">
					<th style="font-size: 30px;"> <?php echo $currentMonthName . '<span style="font-size: 20px"> <sub>' . $currentYear . '</sub></span>'; ?> </th>
					<th <?php echo "colspan='". $numOfExpRows . "'"?> >Kiadások</th>
					<th <?php echo "colspan='". $numOfTakRows . "'"?> >Bevételek</th>
				</tr>

				<?php
					$numDaysThisMon = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

					// CONSTRUCTING DATES AND TRANSACTIONS
					$offset = 0;
					for($i = 0; $i < $numDaysThisMon; $i++){
						echo "<tr>";

						// DATES
						$day = ($i < 9) ? ('0' . ($i + 1)) : ($i + 1);
						$reconstructedDate = strtotime($currentMonth.'/'.$day.'/'.$currentYear);
						$datetowrite = $currentYear . "-" . $currentMonth . "-" . $day;
						$shortWeekdayName = date('D', $reconstructedDate);
						echo "<th style='text-align: left;'>" . $currentYear . ". " . $currentMonthName . " " . $day . "., " . $dayNames[$shortWeekdayName] . "</th>";

						// EXPENSES
						for($j = 0; $j < $numOfExpRows; $j++){
							$split = array(-1, -1, -1);
							if($offset < $numOfTransac) $split = explode('-', $thisMonTransacArray[$offset]->Date);
							if($split[2] == $day && !isPositive($thisMonTransacArray[$offset]->Amount)){ //Ha ez a megfelelő nap és ez egy kiadás, akkor kiírjuk
								echo "<th><div class='exptransacbut divcenter' id='". $thisMonTransacArray[$offset]->ID ."'> <b>". $thisMonTransacArray[$offset]->Name . " </b><br>(". number_format(abs($thisMonTransacArray[$offset]->Amount), 0, '.', '.') .  " Ft) " . "</div></th>";
								$offset++;
							} else { // Egyébként jön egy üres gomb
								echo "<th><div class='emptyOuttransacbut divcenter' id='$datetowrite'>Üres<br>kiadás</div></th>";
							}
						}
						
						// TAKINGS
						for($j = 0; $j < $numOfTakRows; $j++){
							$split = array(-1, -1, -1);
							if($offset < $numOfTransac) $split = explode('-', $thisMonTransacArray[$offset]->Date);
							if($split[2] == $day && isPositive($thisMonTransacArray[$offset]->Amount)){
								echo "<th><div class='inctransacbut divcenter' id='". $thisMonTransacArray[$offset]->ID ."'> <b>". $thisMonTransacArray[$offset]->Name . " </b><br>(". number_format(abs($thisMonTransacArray[$offset]->Amount), 0, '.', '.') .  " Ft) " . "</div></th>";
								$offset++;
							} else {
								echo "<th><div class='emptyIntransacbut divcenter' id='$datetowrite'>Üres<br>bevétel</div></th>";
							}
						}
						
						echo "</tr>";
					}
				?>
			</table>
		</div>

		<!-- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- SIDENAV -- -->
		
		<nav id="mySidenav" class="sdnavdd" style="">
			<input type='button' id='subs' class="sub" style='width:190px' value='Bezár' onclick="closeNav()"><br>
			<div class="divcenter" style="display:inline-block">
				<form name="changemonth" id="changemonthID" method="post" action="main.php">
					<!-- Év -->
					<div class="" style="margin-left:10px;"> 
						<select name="incomingYear" id="incYrID" class="status divcenter">
							<?php
								for($i = 2016; $i <= 2025; $i++){
									if($i == $currentYear) echo "<option selected='selected' value=" . $i. ">" . $i . "</option>";
									else echo "<option value=" . $i. ">" . $i . "</option>";
								}
							?>
						</select>
					</div>

					<!-- Hónap -->
					<div class="" > 
						
							<select name="incomingMonth" id="incMonID" class="status divcenter">
								<?php
									for($i = 1; $i <= 12; $i++){
										if($i == $currentMonth) echo "<option selected='selected' value=" . $i. ">" . $monthNames[$i-1] . "</option>";
										else echo "<option value=" . $i. ">" . $monthNames[$i-1] . "</option>";
									}
								?>
							</select>
					</div>

					<!-- Ugrás -->
					<div class=""> 
						<input class = "status divcenter" type="submit" value="Ugrás" >
					</div>
				</form>

				<div class="divcenter">
					<ul class="headlist"> 
						<li class="sor" style="margin-left:10px;"><input class = " hbut status divcenter" type="submit" value="KI" name="ki"></li>
						<li class="sor"><input class = "hbut status divcenter" type="submit" value="BE" name="be"></li>
						<!--<li class="sor"><input class = "hbut status divcenter" type="submit" value="KI?" name="beun"></li>
						<li class="sor"><input class = "hbut status divcenter" type="submit" value="BE?" name="kiun"></li>-->
					</ul>
				</div>
				<div class="divcenter">
					<ul class="headlist">
						<li class="sor" style="margin-left:10px;"><div class = "set status divcenter"><img class="" style='width:20px;' src="pics/set.png" alt="Beállítások"></div></li>
						<li class="sor"><div class = "search status divcenter"><img class="" style='width:20px;' src="pics/src.png" alt="Keresés"></div></li>
						<li class="sor"><div class = "owe status divcenter"><img class="" style='width:20px;' src="pics/inout.png" alt="Tartozások"></div></li>
						<li class="sor"><div class = "stat status divcenter" id="<?php echo $currentYear; ?>"><img class="" style='width:20px;' src="pics/stat.png" alt="Statisztikák"></div></li>
					</ul>
				</div>
			</div>
		</nav>
	</div>

<!-- SCRIPT -- SCRIPT --SCRIPT --SCRIPT --SCRIPT --SCRIPT --SCRIPT --SCRIPT --SCRIPT --SCRIPT --SCRIPT --SCRIPT --SCRIPT --SCRIPT --SCRIPT --SCRIPT --SCRIPT --SCRIPT --SCRIPT --SCRIPT --SCRIPt -->

	<script>
		/// PROGNOSIS CALCULATION ///
		function calculatePrognosis(){
			var exp = <?php echo $sumOfExpenses; ?>;
			var inc = <?php echo $sumOfTakings; ?>;
			var Amount = document.getElementById('Amount').value;
			var coefficient = 0;

			if(Amount == 0 || Amount == "") {
				document.getElementById('prognosis').innerHTML = "--";
				return;
			}

			if(document.getElementById('dirk').checked){
				var dir = "KI";
			} else if(document.getElementById('dirb').checked){
				var dir = "BE";
			} else {
				document.getElementById('prognosis').innerHTML = "--";
				return;
			}

			if(dir == "KI"){
				exp += Math.abs(Amount);
			} else {
				inc += parseInt(Amount);
			}
			
			if(inc < 1) {
				document.getElementById('prognosis').innerHTML = "DB0";
				return;
			} else coefficient = (exp / inc) * 100;

			var nf = Intl.NumberFormat();
			document.getElementById('prognosis').innerHTML = nf.format(coefficient) + " %";
		}

		document.getElementById('dirk').addEventListener("click", calculatePrognosis);
		document.getElementById('dirb').addEventListener("click", calculatePrognosis);
		document.getElementById('Amount').addEventListener("input", calculatePrognosis);

		/// HEADER SCROLLING ///
		window.onscroll = function() {myFunction()};

		var header = document.getElementById("myHeader");
		var sticky = header.offsetTop;

		function myFunction() {
			if (window.pageYOffset > sticky) {
				header.classList.add("sticky");
			} else {
				header.classList.remove("sticky");
			}
		}
	</script>

	<script>
		/// AJAX NEW PAGES ///
		$(document).ready(function(){
			$('.lgout').click(function(){
				var clickBtnValue = $(this).val();
				var ajaxurl = 'actions/ses.php',
				data =  {'lgout': clickBtnValue};
				$.post(ajaxurl, data, function (response) {
					window.open("index.php", "_self");
				});
			});

			$('.refresh').click(function(){
				location.reload();
			});

			$('.hbut').click(function(){
				var clickBtnValue = $(this).val();
				var ajaxurl = 'actions/ses.php',
				data =  {'type': clickBtnValue};
				$.post(ajaxurl, data, function (response) {
					var addWindow = window.open('addTrans.php', "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,");
					addWindow.addEventListener('onunload', function (e) {
						//setTimeout('re()', 1000);
						location.reload();
					});
				});
			});

			$('.set').click(function(){
				var clickBtnValue = $(this).val();
				var ajaxurl = 'actions/ses.php',
				data =  {'set': clickBtnValue};
				$.post(ajaxurl, data, function (response) {
					var addWindow = window.open('settings.php', "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,");
					addWindow.addEventListener('beforeunload', function (e) {
						location.reload();
					});
				});
			});

			$('.search').click(function(){
				var clickBtnValue = $(this).val();
				var ajaxurl = 'actions/ses.php',
				data =  {'search': clickBtnValue};
				$.post(ajaxurl, data, function (response) {
					var addWindow = window.open('search.php', "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,");
					addWindow.addEventListener('beforeunload', function (e) {
						location.reload();
					});
				});
			});

			$('.owe').click(function(){
				var clickBtnValue = $(this).val();
				var ajaxurl = 'actions/ses.php',
				data =  {'owe': clickBtnValue};
				$.post(ajaxurl, data, function (response) {
					var addWindow = window.open('owe.php', "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,");
					addWindow.addEventListener('beforeunload', function (e) {
						location.reload();
					});
				});
			});

			$('.stat').click(function(){
				var clickBtnValue = $(this).attr('id');
				var ajaxurl = 'actions/ses.php',
				data =  {'currentYear': clickBtnValue};
				$.post(ajaxurl, data, function (response) {
					var addWindow = window.open('statistics.php', "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,");
					addWindow.addEventListener('beforeunload', function (e) {
						location.reload();
					});
				});
			});

			$('.exptransacbut').click(function(){
				var clickBtnID = $(this).attr('id');
				var ajaxurl = 'actions/ses.php',
				data =  {'transacID': clickBtnID};
				$.post(ajaxurl, data, function (response) {
					var addWindow = window.open('editTrans.php', "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,");
					addWindow.addEventListener('beforeunload', function (e) {
						location.reload();
					});
				});
			});

			$('.inctransacbut').click(function(){
				var clickBtnID = $(this).attr('id');
				var ajaxurl = 'actions/ses.php',
				data =  {'transacID': clickBtnID};
				$.post(ajaxurl, data, function (response) {
					var addWindow = window.open('editTrans.php', "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,");
					addWindow.addEventListener('beforeunload', function (e) {
						location.reload();
					});
				});
			});

			$('.emptyIntransacbut').click(function(){
				var clickBtnID = $(this).attr('id') + '_IN';;
				var ajaxurl = 'actions/ses.php',
				data =  {'transacDate': clickBtnID};
				$.post(ajaxurl, data, function (response) {
					var addWindow = window.open('addTrans.php', "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,");
					addWindow.addEventListener('beforeunload', function (e) {
						location.reload();
					});
				});
			});

			$('.emptyOuttransacbut').click(function(){
				var clickBtnID = $(this).attr('id') + '_OUT';
				var ajaxurl = 'actions/ses.php',
				data =  {'transacDate': clickBtnID};
				$.post(ajaxurl, data, function (response) {
					var addWindow = window.open('addTrans.php', "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,");
					addWindow.addEventListener('beforeunload', function (e) {
						location.reload();
					});
				});
			});

		});
	</script>
</body>
</html>