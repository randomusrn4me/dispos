<?php
    session_start();
    if(isset($_SESSION['errno'])){
        $color = 'red';
        switch($_SESSION['errno']){
            case 101: $errTxt = "Please provide your login details!"; break;
            case 1049: $errTxt = "Database not found!"; break;
			case 2002: $errTxt = "Server connection error!"; break;
			case 1045: $errTxt = "Invalid username or password!"; break;
			default: $errTxt = "An unknown error has occured!";
        }
    } else {
        $errTxt = 'Jelentkezz be a Dispositifs-be!<br>v0.6.8pres';
        $color = 'black';
    }
    session_unset();
    session_destroy();

    $pageTitle = "Login";
    require "exp/pageCommons.php";
?>

<body>
    <div class="pagediv ">
        <div class="loginContainer ">
            <form id="loginformID" style="margin-top: 0px" action = "actions/processor.php" method = "post" name="loginformNAME">
                <img class="pic" src="pics/usr.png" alt="Username">
                <input type="text" name="userN" placeholder="Felhasználó" style="margin-top: 5px;margin-bottom: 10px;"><br>
                <img class="pic" src="pics/pw.png" alt="Password">
                <input type="password" name="passW" placeholder="Jelszó" style="margin-top: 5px;"><br>
                <input type="submit" class="logbut"value="Login" style="margin-top: 10px;">
            </form>
            <p style="font-weight: bold;color:<?php echo $color;?>;"><?php echo $errTxt;?></p>
        </div>
    </div>   
</body>
</html>