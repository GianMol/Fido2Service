<?php
session_start(); // get information about the session
require_once("../constants.php"); // constants.php is here mandatory to gain information about the path of the other pages
if(isset($_SESSION["username"]) && isset($_SESSION["id"])){ // if the user has been authenticated, then it cannot access this page and it will be redirected to Resource page
    header("location: ".FIDO2SERVICE_RESOURCE_PATH);
    exit;
}

//test
file_log("interface", __FILE__, "Not authenticated", "Registration");
?>

<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fido2 Service Registration</title>
        <link rel="icon" type="image/png" href="../images/icon.png" sizes="16x16" />

        <script type = "module" src = "../js/registration.js" defer="true"></script>

        <link rel="stylesheet" href="../css/registration.css">
        <link rel="stylesheet" href="../css/layout.css">
    </head>

    <body>
        <div id="loading" class="hidden">Loading<span id="dots"></span><div id="cicle"><span></span></div></div>
        <div id="nav-bar" class="nav-bar">
            <!-- The menu of this page will contain all the items dedicated to non-authenticated users -->
            <a href=<?php echo FIDO2SERVICE_REGISTRATION_PATH; ?>>Register</a>
            <a href=<?php echo FIDO2SERVICE_LOGIN_PATH; ?>>Login</a>
            <a href=<?php echo FIDO2SERVICE_HOME_PATH; ?>>Home</a>
        </div>
        <div id="body-layout" class="body-layout">
            <h1>Registration</h1>
            <div id='parameters-error' class="hidden error">Compilare tutti i campi</div>
            <form method="post" id="register-form">
                <input id="firstname" name="firstname" class="input-item" type="text" placeholder="First Name" autofocus>
                <input id="lastname" name="lastname" class="input-item" type="text" placeholder="Last Name">
                <input id="username" name="username" class="input-item" type="text" placeholder="Username">
                <div id='username-error' class="hidden error"></div>
                <input id="displayname" name="displayname" class="input-item" type="text" placeholder="Display Name">
                <button id="submit-button" type="submit">Sign Up</button>
            </form>
        </div>
    </body>

</html>
