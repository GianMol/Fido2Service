<?php
session_start();
require_once("../constants.php");
if(!isset($_SESSION["username"]) || !isset($_SESSION["id"])){
    header("location: ".FIDO2SERVICE_LOGIN_PATH);
    exit;
}
?>

<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fido2 Service Registration</title>
        <!--<link rel="icon" type="image/png" href="./Images/libro-stilizzato.png" sizes="16x16" /> -->
        <script type = "module" src = "../js/resource.js" defer="true"></script>

        <link rel="stylesheet" href="../css/resource.css">
        <link rel="stylesheet" href="../css/layout.css">
        <link href="https://fonts.googleapis.com/css?family=Merriweather" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Playfair+Display" rel="stylesheet">
    </head>

    <body>
        <div id="loading" class="hidden">Loading<span id="dots"></span></div>
        <div id="nav-bar" class="nav-bar">
            <a href=<?php echo FIDO2SERVICE_TRANSACTIONS_PATH; ?>>Transactions</a>
            <a href=<?php echo FIDO2SERVICE_LOGOUT_PATH; ?>>Logout</a>
            <a href=<?php echo FIDO2SERVICE_RESOURCE_PATH; ?>>Resource</a>
            <a href=<?php echo FIDO2SERVICE_HOME_PATH; ?>>Home</a>
        </div>
        <div id="body-layout" class="body-layout">
            <button id="deregister">Deregister</button>
            <h3>Resource</h3>
            <div id="transaction" class="transaction">
                <div>Buy your personal PC!</div>
                <div>100€</div>
                <button id="submit-button" type="submit">Buy</button>
            </div>
            <div id="resources_list" class="resources_list, hidden"></div>
        </div>
    </body>

</html>
