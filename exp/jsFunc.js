/// SIDENAV
function openNav() {
	document.getElementById("mySidenav").style.width = "300px";
}

function closeNav() {
	document.getElementById("mySidenav").style.width = "0";
}

// TODO
function successNotif(){
	document.getElementById("end").innerHTML = "Tranzakció frissítve, zárd be az ablakot!";
}

function chk (){
	alert("runn");
	var name = document.getElementByID('Name').value;
	var id = document.getElementByID('ID').value;
	var amount = document.getElementByID('Amount').value;
	var rad = document.getElementByID('dir').value;
	var date = document.getElementByID('Date').value;
	var type = document.getElementByID('Type').value;
	var string = 'Name=' + name + '&' + 'ID='  + id + '&' + 'Amount='+ amount + '&' + 'dir=' + rad + '&' + 'Date=' + date + '&' + 'Type=' + type;
	$.ajax({
		url:'addToDatabase.php',
		type:'post',
		data: string,
		cache: false,
		success:function(){
			alert("worked");
		}
	});
}