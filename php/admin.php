<?php
session_start(); // get information about the session
require_once("../constants.php"); // constants.php is here mandatory to gain information about the path of the other pages
if(!isset($_SESSION["username"]) || !isset($_SESSION["id"])){ // if the user has not been authenticated, then it cannot access this page and it will be redirected to Login page
    header("location: ".FIDO2SERVICE_LOGIN_PATH);
    exit;
}

if($_SESSION['username'] !== "admin"){ // if the user has been authenticated but it is not the admin, then it cannot access this page and it will be redirected to Resource page
    header("location: ".FIDO2SERVICE_RESOURCE_PATH);
    exit;
}

// Here, the web application has the assurance the user is the admin
//test
file_log("interface", __FILE__, $_SESSION['username'], "Admin");

echo '<script type = "module" defer="true">import { showLoading } from "../js/constants.js"; showLoading(true)</script>'; // starting the loading screen, since the page needs data to be obtained from the database and, therefore, time

// declaration of useful variables
$res = "";
$tx_res = "";
$tx_rows = "";
$conn = "";

//connection to mysql database
if($conn = mysqli_connect(FIDO2SERVICE_DB_HOSTNAME, FIDO2SERVICE_DB_USERNAME, FIDO2SERVICE_DB_PASSWORD, FIDO2SERVICE_DB_DATABASE)){ // if the connection succeeds, then it is possible to make queries
	if(mysqli_query($conn, "set character set 'utf8'")){ // setting the format
		$query = "SELECT * FROM users ORDER BY username"; // gaining information about all the users
		if($res = mysqli_query($conn, $query)){ // execution of the query related to users and check of the return value
			$tx_query = "SELECT * FROM transactions ORDER BY username"; // second query mandatory to gain information about all the transactions
			if($tx_res = mysqli_query($conn, $tx_query)){ // execution of the query related to transactions and check of the return value
				$tx_rows = array(); // declaration and initialization of the array which will contain all the transactions
				while($data = mysqli_fetch_assoc($tx_res)){ // population of the array
					array_push($tx_rows, $data);
				}
			}
			else{ // error case of the query related to transactions
				mysqli_free_result($res); // freeing the memory associated to the result of users query
				mysqli_close($conn); // closing the connection
				alert("Error: database error");
			}
		}
		else{ // error case of the query related to users
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
        <title>Fido2 Service Admin</title>
        <link rel="icon" type="image/png" href="../images/icon.png" sizes="16x16" />

        <script type="module" src="../js/transactions.js" defer="true"></script>
        <link rel="stylesheet" href="../css/admin.css">
        <link rel="stylesheet" href="../css/transactions.css">
        <link rel="stylesheet" href="../css/layout.css">
    </head>

    <body>
		<div id="loading" class="hidden">Loading<span id="dots"></span><div id="cicle"><span></span></div></div>
        <div id="nav-bar" class="nav-bar">
			<!-- The menu of this page will contain all the items dedicated to authenticated users + the Admin item -->
            <a href=<?php echo FIDO2SERVICE_TRANSACTIONS_PATH; ?>>Transactions</a>
            <a href=<?php echo FIDO2SERVICE_LOGOUT_PATH; ?>>Logout</a>
            <a href=<?php echo FIDO2SERVICE_RESOURCE_PATH; ?>>Resource</a>
            <a href=<?php echo FIDO2SERVICE_HOME_PATH; ?>>Home</a>
            <a href=<?php echo FIDO2SERVICE_ADMIN_PATH; ?>>Admin</a>
        </div>
        <div id="body-layout" class="body-layout">
            <h1>Hi Admin! Here is the list of users</h1>
            <div id="users_list" class="users_list">
                <?php
                    if($res !== "" && $conn !== "" && $tx_res !== ""){ // if no error has occured in queries
						if(mysqli_num_rows($res) === 0){ // if no user has been found in the database
							echo '<div id="no-users">No users found</div>';
						}
						else{
							$i = 0; // user counter: it is also used as identifier
							while($row = mysqli_fetch_assoc($res)){ // for each user
								// $transactions is the array only containing user's transactions, obtained filtering the $tx_rows array, which is containing all the transactions of all the users 
								$transactions = array_filter($tx_rows, function($r) use($row){
									return $r['username'] === $row['username']; // the filter is based on the association between the "username" field of the user and "username" field of the transaction
								});
								/**
								 * An example of structure of the html items is:
								 * 
								 * <div class="user_transactions">
								 * 		<div id="user_0" class="user">
								 * 			<span>id:</span> 0, 
								 * 			<span>username:</span> username, 
								 * 			<span>first name:</span> Firstname, 
								 * 			<span>last name:</span> Lastname, 
								 * 			<span>display name:</span> Displayname, 
								 * 		</div>
								 * 		<div id="transaction_0.0" class="transaction">
								 * 			<span>txid:</span> 0, 
								 * 			<span>txpayload:</span> txpayload, 
								 * 			<span>key type:</span> RSA, 
								 * 			<button id="view_details_0.0" class="view_details">View details</button>
								 * 			<img id="img_preview_0.0" class="hidden img_preview"/>
								 * 			<div id="result_0.0" class="hidden result">
								 * 				<span>txid:</span>
								 *				<div id="txid_0.0">0,</div>
								 * 				<span>txpayload:</span>
								 *				<div id="txpayload_0.0">txpayload,</div>
								 *				<span>Signature:</span>
								 *				<div id="signature_0.0">signature,</div>
								 *				<span>Public key:</span>
								 *				<div id="signerPublicKey_0.0">signerPublicKey,</div>
								 *				<span>Algorithm:</span>
								 *				<div id="signingKeyAlgorithm_0.0">signingKeyAlgorithm,</div>
								 *				<span>Key type:</span>
								 *				<div id="signingKeyType_0.0">RSA,</div>
								 *				<span>AuthenticatorData:</span>
								 *				<div id="authenticatorData_0.0">authenticatorData,</div>
								 *				<span>ClientDataJson:</span>
								 *				<div id="clientDataJson_0.0">clientDataJson,</div>
								 *				<button id="verify_signature_0.0" class="verify_signature">Verify signature</button>
								 *				<div id="actual_result_0.0" class="hidden"></div>
								 *				<img id="img_0.0" class="hidden"/>
								 * 			</div>
								 * 		</div>
								 * </div>
								 */
								// first, the user's data are shown 
								echo '<div class="user_transactions"><div id="user_'.$i.'" class="user"><span>id:</span> ',
									$row['id'],
									', <span>username:</span> ',
									$row['username'],
									', <span>first name:</span> ',
									$row['first_name'],
									', <span>last name:</span> ',
									$row['last_name'],
									', <span>display name:</span> ',
									$row['display_name'],
									'</div>';
								$j = 0; // transaction counter: it is also used as identifier
								foreach($transactions as &$t){ // for each transaction
									$sign_key_type = $t['signingKeyType']; // this is needed since through this variable the application understands if the algorithm is supported or not for the confirmation step
									// here are shown data about the transaction
									echo '<div id="transaction_'.$i.'.'.$j.'" class="transaction"><span>txid:</span> ',
										$t['txid'],
										', <span>txpayload:</span> ',
										$t['txpayload'],
										', <span>key type:</span> ',
										$t['signingKeyType'],
										'<button id="view_details_'.$i.'.'.$j.'" class="view_details">View details</button>', // this button is needed to show the modal view with details
										'<img id="img_preview_'.$i.'.'.$j.'" class="hidden img_preview"/>', // this image is initially empty: after the transaction confirmation step, if supported, this item will contain the icon of success or failure, depending on the result of the confirmation
										'<div id="result_'.$i.'.'.$j.'" class="hidden result">', // this is the modal containing all the information about this transaction. It is initially hidden and showed only through the button "View details"
											'<span>txid:</span>',
											'<div id="txid_'.$i.'.'.$j.'">',
												$t['txid'],
											'</div>',
											'<span>txpayload:</span>',
											'<div id="txpayload_'.$i.'.'.$j.'">',
												$t['txpayload'],
											'</div>',
											'<span>Signature:</span>',
											'<div id="signature_'.$i.'.'.$j.'">',
												$t['signature'],
											'</div>',
											'<span>Public key:</span>',
											'<div id="signerPublicKey_'.$i.'.'.$j.'">',
												$t['signerPublicKey'],
											'</div>',
											'<span>Algorithm:</span>',
											'<div id="signingKeyAlgorithm_'.$i.'.'.$j.'">',
												$t['signingKeyAlgorithm'],
											'</div>',
											'<span>Key type:</span>',
											'<div id="signingKeyType_'.$i.'.'.$j.'">',
												$sign_key_type,
											'</div>',
											'<span>AuthenticatorData:</span>',
											'<div id="authenticatorData_'.$i.'.'.$j.'">',
												$t['authenticatorData'],
											'</div>',
											'<span>ClientDataJson:</span>',
											'<div id="clientDataJson_'.$i.'.'.$j.'">',
												$t['clientDataJson'],
											'</div>';

									if($sign_key_type === "RSA"){ // this condition checks if the algorithm used to generate the signature is supported for the transaction confirmation step; for now, RSA is the only algorhm supported
										echo '<button id="verify_signature_'.$i.'.'.$j.'" class="verify_signature">Verify signature</button>', // this button, if clicked, will start the process of confirmation
											// these two items are initially hidden; once the process of transaction confirmation ends, these items are filled and showed.
											'<div id="actual_result_'.$i.'.'.$j.'" class="hidden"></div>', // This item will show the result of the confirmation through a string: "valid" or "not valid"
											'<img id="img_'.$i.'.'.$j.'" class="hidden"/>'; // This item will show the result of the confirmation through an image: an x or a tic
									}
									else{ // in case the algorithm used is not supported for the transaction confirmation step
										echo '<div class="no_verification">Verification not supported for this key type</div>';
									}

									echo '</div>',
										'</div>';

									$j = $j + 1; // the transaction counter is incremented
								}
								echo '</div>';
								$i = $i + 1; // the user counter is incremented
							}
						}
						// at the end of the process, the whole memory used is freed
						mysqli_free_result($res);
						mysqli_free_result($tx_res);
						mysqli_close($conn);
						echo '<script type = "module" defer="true">import { showLoading } from "../js/constants.js"; showLoading(false)</script>'; // once the information about all the transactions and all the users has been set, the loading screen can be removed
                    }
                ?>
            </div>
        </div>
    </body>

</html>
