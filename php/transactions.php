<?php
session_start();
require_once("../constants.php");
if(!isset($_SESSION["username"]) || !isset($_SESSION["id"])){
    header("location: ".FIDO2SERVICE_LOGIN_PATH);
    exit;
}

$conn = mysqli_connect("localhost", "fido2service", "fido", "fido2service"); //connection to mysql database
mysqli_query($conn, "set character set 'utf8'");
$username = mysqli_real_escape_string($conn, $_SESSION['username']); //sanitizing information sent by the client

$query = "SELECT * FROM transactions WHERE username = '".$username."'"; //query to be executed in database
$res = mysqli_query($conn, $query); //execution of the query
echo '<script type = "module" defer="true">import { showLoading } from "../js/constants.js"; showLoading(true)</script>';
?>

<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fido2 Service Transactions</title>
        <!--<link rel="icon" type="image/png" href="./Images/libro-stilizzato.png" sizes="16x16" /> -->

        <link rel="stylesheet" href="../css/transactions.css">
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
            <h3>Transactions</h3>
            <div id="resources_list" class="resources_list">
                <?php
                    if(isset($res) && isset($conn)){
			if(mysqli_num_rows($res) === 0){
			    echo '<div class="no-transictions">No transactions executed</div>';
			}
			else{
			    while($row = mysqli_fetch_assoc($res)){
				echo '<div class="transition"><span>txid:</span> ',$row['txid'],', <span>txpayload:</span> ',$row['txpayload'],', <span>username:</span> ',$row['username'],', <span>date_time:</span> ',$row['date_time'],'</div>';
			    }
			}
                        echo '<script type = "module" defer="true">import { showLoading } from "../js/constants.js"; showLoading(false)</script>';
			mysqli_free_result($res);
			mysqli_close($conn);
                    }
                ?>
            </div>
        </div>
    </body>

</html>
