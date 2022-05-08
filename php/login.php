<?php
session_start(); // get information about the session
require_once("../constants.php"); // constants.php is here mandatory to gain information about the path of the other pages and for other constants used for the communication with the FIDO2 server
if(isset($_SESSION["username"]) && isset($_SESSION["id"])){ // if the user has been authenticated, then it cannot access this page and it will be redirected to Resource page
    header("location: ".FIDO2SERVICE_RESOURCE_PATH);
    exit;
}

//test
file_log("interface", __FILE__, "Not authenticated", "Login");
?>

<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fido2 Service Login</title>
        <link rel="icon" type="image/png" href="../images/icon.png" sizes="16x16" />
        <script type = "module" src = "../js/login.js" defer="true"></script>

        <link rel="stylesheet" href="../css/login.css">
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
            <?php
                if(isset($_GET['registered']) && $_GET['registered']){ // this happens when this page is automatically loaded after a registration
                    echo '<h1>Registration complete! Please login</h1>';
                }
            ?>
            <h1>Login</h1>
            <div id='parameters-error' class="hidden error">Fulfill the form</div>
            <?php
                /*if(isset($err) && $err !== ""){ // in case of errors, the message sent by the web application server is here showed
                    console_log($err);
                    alert($err->status.": ".$err->statusText);
                    $err = "";
                }*/
            ?>
            <form method="post" id="login-form">
                <input id="username" name="username" class="input-item" type="text" placeholder="Username" autofocus>
                <div id='username-error' class="hidden error"></div>
                <button id="submit-button" type="submit">Login</button>
            </form>
        </div>
    </body>

</html>
