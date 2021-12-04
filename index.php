<?php
session_start();
include_once("./constants.php");
?>

<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fido2 Service Home</title>
        <!--<link rel="icon" type="image/png" href="./Images/libro-stilizzato.png" sizes="16x16" /> -->
        <link rel="stylesheet" href="./css/index.css">
        <link rel="stylesheet" href="./css/layout.css">
        <link href="https://fonts.googleapis.com/css?family=Merriweather" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Playfair+Display" rel="stylesheet">
    </head>

    <body>
        <div id="nav-bar" class="nav-bar">
	    <?php
		if(!isset($_SESSION['username']) || !isset($_SESSION['id'])){
        	    echo '<a href='.FIDO2SERVICE_REGISTRATION_PATH.'>Register</a>';
        	    echo '<a href='.FIDO2SERVICE_LOGIN_PATH.'>Login</a>';
		}
                else{
                    echo '<a href='.FIDO2SERVICE_TRANSACTIONS_PATH.'>Transactions</a>';
                    echo '<a href='.FIDO2SERVICE_LOGOUT_PATH.'>Logout</a>';
                }
            ?>
            <a href=<?php echo FIDO2SERVICE_RESOURCE_PATH; ?>>Resource</a>
            <a href=<?php echo FIDO2SERVICE_HOME_PATH; ?>>Home</a>
        </div>
        <div id="body-layout" class="body-layout">
            <?php
                if(isset($_SESSION["username"]) && isset($_SESSION["id"]) && isset($_SESSION["displayname"]) && $_SESSION["username"] !== ""){
                    echo '<h4>Welcome ',
                    $_SESSION['displayname'],
                    '!</h4>';
                }

            ?>
            <h1>
                HomePage Fido2 Service Home
            </h1>
            <h2>
                Fido2 Authentication For Embedded Systems
            </h2>
            <h3>
                Master Thesis Cyber Security
            </h3>
            <h3>
                Gianluca Moliteo, s277261
            </h3>
        </div>
    </body>

</html>
