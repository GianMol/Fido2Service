<?php
session_start(); // get information about the session
require_once("../constants.php"); // constants.php is here mandatory to gain information about the path of the other pages
if(!isset($_SESSION["username"]) || !isset($_SESSION["id"])){ // if the user has not been authenticated, then it cannot access this page and it will be redirected to Login page
    header("location: ".FIDO2SERVICE_LOGIN_PATH);
    exit;
}

//test
file_log("interface", __FILE__, $_SESSION["username"], "Resource");
?>

<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fido2 Service Resource</title>
        <link rel="icon" type="image/png" href="../images/icon.png" sizes="16x16" />
        <script type = "module" src = "../js/resource.js" defer="true"></script>

        <link rel="stylesheet" href="../css/resource.css">
        <link rel="stylesheet" href="../css/layout.css">
    </head>

    <body>
        <div id="loading" class="hidden">Loading<span id="dots"></span><div id="cicle"><span></span></div></div>
        <div id="nav-bar" class="nav-bar">
            <!-- The menu of this page will contain all the items dedicated to authenticated users -->
            <a href=<?php echo FIDO2SERVICE_TRANSACTIONS_PATH; ?>>Transactions</a>
            <a href=<?php echo FIDO2SERVICE_LOGOUT_PATH; ?>>Logout</a>
            <a href=<?php echo FIDO2SERVICE_RESOURCE_PATH; ?>>Resource</a>
            <a href=<?php echo FIDO2SERVICE_HOME_PATH; ?>>Home</a>

	    <?php
		if($_SESSION['username'] === 'admin'){ // if the authenticated user is the admin, then the menu will also show the Admin item
		    echo '<a href=',FIDO2SERVICE_ADMIN_PATH,'>Admin</a>';
		}
	    ?>
        </div>
        <div id="body-layout" class="body-layout">
            <button id="deregister">Deregister</button>
            <?php
                echo '<h1>Hi ',
                $_SESSION['displayname'],
                '! Here is your resource</h1>';

            ?>
            <div id="transaction" class="transaction">
                <div>Buy your personal PC!</div>
                <div id="pc-information">Intel Core i5, RAM 8GB DDR4, Hard drive 512GB SSD</div>
                <div id="pc-price">500€</div>
                <button id="submit-button" type="submit">Buy</button>
            </div>
            <div id="resources_list" class="resources_list, hidden"></div>
        </div>
    </body>

</html>
