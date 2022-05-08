<?php
session_start(); // get information about the session
require_once("../constants.php"); // constants.php is here mandatory to gain information about the path of the other pages
if(!isset($_SESSION["username"]) || !isset($_SESSION["id"])){ // if the user has not been authenticated, then it cannot access this page and it will be redirected to Login page
    header("location: ".FIDO2SERVICE_LOGIN_PATH);
    exit;
}

//test
file_log("interface", __FILE__, $_SESSION["username"], "Transactions");

echo '<script type = "module" defer="true">import { showLoading } from "../js/constants.js"; showLoading(true)</script>'; // starting the loading screen, since the page needs data to be obtained from the database and, therefore, time

// declaration of useful variables
$res = "";
$conn = "";

//connection to mysql database
if($conn = mysqli_connect(FIDO2SERVICE_DB_HOSTNAME, FIDO2SERVICE_DB_USERNAME, FIDO2SERVICE_DB_PASSWORD, FIDO2SERVICE_DB_DATABASE)){ // if the connection succeeds, then it is possible to make queries
	if(mysqli_query($conn, "set character set 'utf8'")){ // setting the format
		$query = "SELECT * FROM transactions WHERE username = '".$_SESSION['username']."'"; // the query is about obtaining all data associated to the user who has the active session.
		if(!($res = mysqli_query($conn, $query))){ // execution of the query and check of the return value.
            // This is the error case
            mysqli_close($conn); // closing the connection
			alert("Error: database error");
		}
	}
	else{ // error case of the query related to format setting
		mysqli_close($conn); // closing the connection
		alert("Error: database format setting failed.");
	}
}
else{ // if the result of mysqli_connect is false, then the connection does not succeed and an error occurs
	alert("Error: connection to database failed.");
}
?>

<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fido2 Service Transactions</title>
        <link rel="icon" type="image/png" href="../images/icon.png" sizes="16x16" />

	    <script type="module" src="../js/transactions.js" defer="true"></script>

        <link rel="stylesheet" href="../css/transactions.css">
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
                if($_SESSION['username'] === "admin"){ // if the authenticated user is the admin, then the menu will also show the Admin item
                echo '<a href=',FIDO2SERVICE_ADMIN_PATH,'>Admin</a>';
                }
            ?>
        </div>
        <div id="body-layout" class="body-layout">
            <?php
                echo '<h1>Hi ',
                $_SESSION['displayname'],
                '! Here are your transactions</h1>';

            ?>
            <div id="resources_list" class="resources_list">
                <?php
                    /**
                     * An example of structure of the html items is:
                     * 
                     * <div id="resources_list" class="resources_list">
                     * 		<div id="transaction_0" class="transaction">
                     * 			<span>txid:</span> 0, 
                     * 			<span>txpayload:</span> txpayload, 
                     * 			<span>key type:</span> RSA, 
                     * 			<button id="view_details_0" class="view_details">View details</button>
                     * 			<img id="img_preview_0" class="hidden img_preview"/>
                     * 			<div id="result_0" class="hidden result">
                     * 				<span>txid:</span>
                     *				<div id="txid_0">0,</div>
                     * 				<span>txpayload:</span>
                     *				<div id="txpayload_0">txpayload,</div>
                     *				<span>Signature:</span>
                     *				<div id="signature_0">signature,</div>
                     *				<span>Public key:</span>
                     *				<div id="signerPublicKey_0">signerPublicKey,</div>
                     *				<span>Algorithm:</span>
                     *				<div id="signingKeyAlgorithm_0">signingKeyAlgorithm,</div>
                     *				<span>Key type:</span>
                     *				<div id="signingKeyType_0">RSA,</div>
                     *				<span>AuthenticatorData:</span>
                     *				<div id="authenticatorData_0">authenticatorData,</div>
                     *				<span>ClientDataJson:</span>
                     *				<div id="clientDataJson_0">clientDataJson,</div>
                     *				<button id="verify_signature_0" class="verify_signature">Verify signature</button>
                     *				<div id="actual_result_0" class="hidden"></div>
                     *				<img id="img_0" class="hidden"/>
                     * 			</div>
                     * 		</div>
                     * </div>
                     */
                    if($res !== "" && $conn !== ""){
                        if(mysqli_num_rows($res) === 0){
                            echo '<div id="no-transactions">No transactions executed</div>';
                        }
                        else{
                            $i = 0; // transaction counter: it is also used as identifier
                            while($row = mysqli_fetch_assoc($res)){ // for each transaction
                                $sign_key_type = $row['signingKeyType']; // this is needed since through this variable the application understands if the algorithm is supported or not for the confirmation step
                                // here are shown data about the transaction
                                echo '<div id="transaction_'.$i.'" class="transaction"><span>txid:</span> ',
                                    $row['txid'],
                                    ', <span>txpayload:</span> ',
                                    $row['txpayload'],
                                    ', <span>key type:</span> ',
                                    $row['signingKeyType'],
                                    '<button id="view_details_'.$i.'" class="view_details">View details</button>', // this button is needed to show the modal view with details
                                    '<img id="img_preview_'.$i.'" class="hidden img_preview"/>', // this image is initially empty: after the transaction confirmation step, if supported, this item will contain the icon of success or failure, depending on the result of the confirmation
                                    '<div id="result_'.$i.'" class="hidden result">', // this is the modal containing all the information about this transaction. It is initially hidden and showed only through the button "View details"
                                        '<span>txid:</span>',
                                        '<div id="txid_'.$i.'">',
                                            $row['txid'],
                                        '</div>',
                                        '<span>txpayload:</span>',
                                        '<div id="txpayload_'.$i.'">',
                                            $row['txpayload'],
                                        '</div>',
                                        '<span>Signature:</span>',
                                        '<div id="signature_'.$i.'">',
                                            $row['signature'],
                                        '</div>',
                                        '<span>Public key:</span>',
                                        '<div id="signerPublicKey_'.$i.'">',
                                            $row['signerPublicKey'],
                                        '</div>',
                                        '<span>Algorithm:</span>',
                                        '<div id="signingKeyAlgorithm_'.$i.'">',
                                            $row['signingKeyAlgorithm'],
                                        '</div>',
                                        '<span>Key type:</span>',
                                        '<div id="signingKeyType_'.$i.'">',
                                            $sign_key_type,
                                        '</div>',
                                        '<span>AuthenticatorData:</span>',
                                        '<div id="authenticatorData_'.$i.'">',
                                            $row['authenticatorData'],
                                        '</div>',
                                        '<span>ClientDataJson:</span>',
                                        '<div id="clientDataJson_'.$i.'">',
                                            $row['clientDataJson'],
                                        '</div>';

                                if($sign_key_type === "RSA"){ // this condition checks if the algorithm used to generate the signature is supported for the transaction confirmation step; for now, RSA is the only algorhm supported
                                    echo '<button id="verify_signature_'.$i.'" class="verify_signature">Verify signature</button>', // this button, if clicked, will start the process of confirmation
                                        // these two items are initially hidden; once the process of transaction confirmation ends, these items are filled and showed.
                                        '<div id="actual_result_'.$i.'" class="hidden actual_result"></div>', // This item will show the result of the confirmation through a string: "valid" or "not valid"
                                        '<img id="img_'.$i.'" class="hidden"/>'; // This item will show the result of the confirmation through an image: an x or a tic
                                }
                                else{ // in case the algorithm used is not supported for the transaction confirmation step
                                    echo '<div class="no_verification">Verification not supported for this key type</div>';
                                }

                                echo '</div>',
                                    '</div>';

                                $i = $i + 1; // the transaction counter is incremented
                            }
                        }
                        // at the end of the process, the whole memory used is freed
                        mysqli_free_result($res);
                        mysqli_close($conn);
                        echo '<script type = "module" defer="true">import { showLoading } from "../js/constants.js"; showLoading(false)</script>'; // once the information about all the transactions and all the users has been set, the loading screen can be removed
                    }
                ?>
            </div>
        </div>
    </body>

</html>
