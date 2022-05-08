<?php
session_start(); // get information about the session
include_once("./constants.php"); // constants.php is here mandatory to gain information about the path of the other pages

//test
file_log("interface", __FILE__, isset($_SESSION["username"]) ? $_SESSION["username"] : "Not authenticated", "Homepage");
?>

<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fido2 Service Home</title>
        <link rel="icon" type="image/png" href="./images/icon.png" sizes="16x16" />
        <link rel="stylesheet" href="./css/index.css">
        <link rel="stylesheet" href="./css/layout.css">
    </head>

    <body>
        <div id="nav-bar" class="nav-bar"> 
            <?php
                if(!isset($_SESSION['username']) || !isset($_SESSION['id'])){ // if the user has not been authenticated, then menu will contain register and login pointer items 
                    echo '<a href='.FIDO2SERVICE_REGISTRATION_PATH.'>Register</a>';
                    echo '<a href='.FIDO2SERVICE_LOGIN_PATH.'>Login</a>';
                }
                else{ // if the user has been authenticated, then menu will contain transactions and logout pointer items
                    echo '<a href='.FIDO2SERVICE_TRANSACTIONS_PATH.'>Transactions</a>';
                    echo '<a href='.FIDO2SERVICE_LOGOUT_PATH.'>Logout</a>';
                    echo '<a href='.FIDO2SERVICE_RESOURCE_PATH.'>Resource</a>';
                }
            ?>
            <!-- The Resource and Home menu items are always shown -->
            <a href=<?php echo FIDO2SERVICE_HOME_PATH; ?>>Home</a>

            <?php
                if(isset($_SESSION['username']) && isset($_SESSION['id']) && $_SESSION['username'] === 'admin'){ // if the user is authenticated as the admin, then the Admin menu item will be present
                    echo '<a href='.FIDO2SERVICE_ADMIN_PATH.'>Admin</a>';
                }
            ?>
        </div>

        <div id="body-layout" class="body-layout">
            <?php
                if(isset($_SESSION["username"]) && isset($_SESSION["id"]) && isset($_SESSION["displayname"])){
                    echo '<h1>Welcome ',
                    $_SESSION['displayname'],
                    '!</h1>';
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
            <h4>
                student: Gianluca Moliteo, s277261
            </h4>
            <h4>
                supervisor: professor Antonio Lioy
            </h4>
        </div>
    </body>

</html>
