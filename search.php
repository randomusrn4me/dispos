<?php
	require "exp/phpFunc.php";

	/// DATABASE CONNECTION ///
	session_start();
	require "exp/connCommon.php";

	$pageTitle = "Keresés";
	require "exp/pageCommons.php";
?>

<body>
	<div class="pagediv ">
		<div class="loginContainer " style="height:500px; width: fit-content;">
			<p id='wolp' style="text-align: center; font-weight: bold; font-size: 20px; margin-top:-10px"><u>Tranzakció keresése</u></p>
			<form name="keres" id="keresel" method="post" action="searchInDatabase.php" autocomplete="off" onsubmit="">
				<table >
					<tr>
						<th style="text-align: left">Azonosító: </th>
						<th> <input type='number' id='ID' name='ID'> </th>
					</tr>

					<tr>
						<th style="text-align: left">Név: </th>
						<th> <input autofocus type='text' value='' name='Name' id='Name'> </th>
					</tr>

					<tr>
						<th style="text-align: left">Összeg: </th>
						<th> <input type='number' name='Amount' id='Amount'> </th>
					</tr>

					<tr>
						<th style="text-align: left">Irány: </th>
						<th> KI <input type='radio' value="KI" name="dir"> BE <input type='radio' value="BE" name="dir" id="dir"> </th>
					</tr>

					<tr>
						<th style="text-align: left">Típus: </th>
						<th> <input type='text' name='Type' id='Type'> </th>
					</tr>

					<tr>
						<th style="text-align: left">Dátum: </th>
						<th> <input type='text' value='' name='Date' id='Date' placeholder="pl. 2020-05-30"> </th>
					</tr>

					<tr >
						<th colspan="2" style="text-align: center"><input type='submit' id='subs' class="sub" value='Keres' onclick=""></th>
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