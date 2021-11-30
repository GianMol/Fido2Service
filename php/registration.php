<?php
session_start();
if(isset($_SESSION["username"]) && isset($_SESSION["id"])){
    header("location: /fido2service/Fido2Service/");
    exit;
}
?>

<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fido2 Service Registration</title>
        <!--<link rel="icon" type="image/png" href="./Images/libro-stilizzato.png" sizes="16x16" /> -->
        <script type = "module" src = "../js/registration.js" defer="true"></script>

        <link rel="stylesheet" href="../css/registration.css">
        <link rel="stylesheet" href="../css/layout.css">
        <link href="https://fonts.googleapis.com/css?family=Merriweather" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Playfair+Display" rel="stylesheet">
    </head>

    <body>
        <div id="loading" class="hidden">Loading...</div>
        <div id="nav-bar">
            <a href="./registration.php">Register</a>
            <a href="./login.php">Login</a>
            <a href="https://www.google.com">Resource</a>
            <a href="../index.php">Home</a>
        </div>
        <div id="body-layout">
            <h3>Registration</h3>
            <div id='parameters-error' class="hidden error">compilare tutti i campi</div>
            <form method="post" id="register-form">
                <input id="firstname" name="firstname" class="input-item" type="text" placeholder="First Name">
                <input id="lastname" name="lastname" class="input-item" type="text" placeholder="Last Name">
                <input id="username" name="username" class="input-item" type="text" placeholder="Username">
                <div id='username-error' class="hidden error"></div>
                <input id="displayname" name="displayname" class="input-item" type="text" placeholder="Display Name">
                <button id="submit-button" type="submit">Sign Up</button>
            </form>
        </div>
    </body>

</html>