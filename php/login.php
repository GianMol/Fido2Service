<?php/*
session_start();
if(isset($_SESSION["username"])){
    header("location: resource.php");
    exit;
}*/
?>

<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fido2 Service Login</title>
        <!--<link rel="icon" type="image/png" href="./Images/libro-stilizzato.png" sizes="16x16" /> -->
        <script type = "module" src = "../js/constants.js" defer="true"></script>
        <script src = "../js/login.js" defer="true"></script>

        <link rel="stylesheet" href="../css/authentication.css">
        <link rel="stylesheet" href="../css/layout.css">
        <link href="https://fonts.googleapis.com/css?family=Merriweather" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Playfair+Display" rel="stylesheet">
    </head>

    <body>
    <div id="nav-bar">
            <a href="./registration.php">Register</a>
            <a href="./login.php">Login</a>
            <a href="https://www.google.com">Resource</a>
            <a href="../index.php">Home</a>
        </div>
        <div id="body-layout">
            <h3>Login</h3>
            <div id='parameters-error' class="hidden error">Fulfill the form</div>
            <form method="post" id="login-form">
                <input id="username" name="username" class="input-item" type="text" placeholder="Username">
                <div id='username-error' class="hidden error"></div>
                <button id="submit-button" type="submit">Login</button>
            </form>
        </div>
    </body>

</html>