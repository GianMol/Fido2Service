<?php
session_start();
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
        <div id="nav-bar">
            <a href="./php/registration.php">Register</a>
            <a href="./php/login.php">Login</a>
            <a href="https://www.google.com">Resource</a>
            <a href="./index.php">Home</a>
            <?php
                if(isset($_SESSION["username"]) && isset($_SESSION["id"])){
                    echo '<a href="./php/api/deregister.php">Deregister</a>';
                    echo '<a href="./php/api/logout.php">Logout</a>';
                }
            ?>
        </div>
        <div id="body-layout">
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