<?php
session_start(); // get information about the session
require_once("../../constants.php"); // constants.php is here mandatory for constants used for the communication with the FIDO2 server
if($_SERVER['REQUEST_METHOD'] !== 'POST'){ // if POST method is not used, then the user cannot access to this endpoint
    // if it happens, the user is redirected to the homepage
    header("location: ".FIDO2SERVICE_HOME_PATH);
    exit;
}
if( empty($_POST)){ // if POST data are not correctly obtained, then decode them 
    $_POST = json_decode(file_get_contents('php://input', true));
}

// declaration of all data needed
$id = "";
$rawId = "";
$authenticatorData = "";
$signature = "";
$userHandle = "";
$clientDataJSON = "";
$reqOrigin = "";
$type = "";

$username = "";
$txid = "";
$txpayload = "";

// assignment of all data needed
if(isset($_POST->id)){
    $id = $_POST->id;
}
if(isset($_POST->rawId)){
    $rawId = $_POST->rawId;
}
if(isset($_POST->type)){
    $type = $_POST->type;
}
if(isset($_POST->authenticatorData)){
    $authenticatorData = $_POST->authenticatorData;
}
if(isset($_POST->clientDataJSON)){
    $clientDataJSON = $_POST->clientDataJSON;
}
if(isset($_POST->signature)){
    $signature = $_POST->signature;
}
if(isset($_POST->userHandle)){
    $userHandle = $_POST->userHandle;
}

$reqOrigin = $_SERVER['HTTP_HOST'];



if(isset($_SESSION['username'])){
    $username = $_SESSION['username'];
}
if(isset($_SESSION['txid'])){
    $txid = $_SESSION['txid'];
}
if(isset($_SESSION['txpayload'])){
    $txpayload = $_SESSION['txpayload'];
}


if($username !== "" && $id !== "" && $rawId !== "" && $type !== "" && $txid !== "" && $txpayload !== "" &&
$authenticatorData !== "" && $clientDataJSON !== "" && $signature !== "" && $reqOrigin !== ""){ // checking whether some needed data are missing


    /* An example of data structure to be sent as input for SKFS authorize endpoint is:

    data: {
        "svcinfo": {
            "did": 1,
            "protocol": "FIDO2_0",
            "authtype": "PASSWORD",
            "svcusername": "svcfidouser",
            "svcpassword": "Abcd1234!"
        },
        "payload": {
            "txid": "ABC123",
            "txpayload": "ABBBBCCCDDD",
            "strongkeyMetadata": {
                "version": "1.0",
                "last_used_location": "Sunnyvale, CA",
                "username": "johndoe",
                "origin": "https://demo4.strongkey.com"
            },
            "publicKeyCredential": {
                "id": "79U433x2hykUyf-h02qXwEkpyLN15N61MhYDTlM6AuWi-rmrO7kA0LdP3nSJNYedw6AqAh6RZiWjIyh5b1npW4oMJRS1sYMJVkRbNVlwBpSy_0OW2pRKLvVSRjxzT7LXsGV_i4r7KRE83ItVOS_cDKbYn3axDcYiUNaRXAR1DfHC5UP3hpystaKsOKvfCop2oA0rfrymTsUmF7RGKP-MNCiMP_Z5EnO8hHntAs41kTg",
                "rawId": "79U433x2hykUyf-h02qXwEkpyLN15N61MhYDTlM6AuWi-rmrO7kA0LdP3nSJNYedw6AqAh6RZiWjIyh5b1npW4oMJRS1sYMJVkRbNVlwBpSy_0OW2pRKLvVSRjxzT7LXsGV_i4r7KRE83ItVOS_cDKbYn3axDcYiUNaRXAR1DfHC5UP3hpystaKsOKvfCop2oA0rfrymTsUmF7RGKP-MNCiMP_Z5EnO8hHntAs41kTg",
                "response": {
                    "authenticatorData": "WnTBrV2dI2nYtpWAzOrzVHMkwfEC46dxHD4U1RP9KKMBAAAAFA",
                    "signature": "MEUCIEB1evFffkyk1TwLRNtPWTv3G40DqABEuU8PJIdevt-lAiEAq9EiWwPicP3Ln2rQ17C1g--OEYGxhp1Q1aHV3rUrE2c",
                    "userHandle": "",
                    "clientDataJSON": "eyJ0eXBlIjoid2ViYXV0aG4uZ2V0IiwiY2hhbGxlbmdlIjoiNWVWRGRGbVg0Y3JmNWJaVkE4WGhkZyIsIm9yaWdpbiI6Imh0dHBzOi8vZGVtbzQuc3Ryb25na2V5LmNvbSJ9"
                },
                "type": "public-key"
            }
        }
    }
    
    */

    // generating data to be sent to SKFS authorize endpoint
    $metadataObj = array(
        'version' => METADATA_VERSION,
        'last_used_location' => METADATA_LOCATION,
        'username' => $username,
        'origin' => "https://" . $reqOrigin,
        'clientUserAgent' => $_SERVER['HTTP_USER_AGENT']
    );

    $responseObj = array(
        'id' => $id,
        'rawId' => $rawId,
        'response' => array(
            'authenticatorData' => $authenticatorData,
            'signature' => $signature,
            'userHandle' => $userHandle,
            'clientDataJSON' => $clientDataJSON
        ),
        'type' => $type
    );

    $data = array(
        'svcinfo' => SVCINFO,
        'payload' => array(
            'txid' => $txid,
            'txpayload' => $txpayload,
            'strongkeyMetadata' => $metadataObj,
            'publicKeyCredential' => $responseObj
        )
    );
    $post_data = json_encode($data); // encoding data to be correctly put in the body of the request

    $url = PRE_SKFS_HOSTNAME . SKFS_HOSTNAME . SKFS_AUTHORIZE_PATH; // preparing the correct endpoint of the FIDO2 server, the authorize one

    $crl = curl_init($url); // initialization of curl
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true); // returns the transfer as a string of the return value of curl_exec() instead of outputting it directly
    curl_setopt($crl, CURLINFO_HEADER_OUT, true); // tracks the handle's request string
    curl_setopt($crl, CURLOPT_POST, true); // sets the method of HTTP as POST
    curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data); // sets the body of the POST request
    curl_setopt($crl, CURLOPT_PORT, SKFS_PORT); // sets the server port 
    curl_setopt($crl, CURLOPT_CAINFO, CERTIFICATE_PATH); // sets the path of server certificate

    // in case FIDO2 server requires client authentication, here the application sets the path of client certificate and key
    curl_setopt($crl, CURLOPT_SSLCERT, CLIENT_CERTIFICATE_PATH);
    curl_setopt($crl, CURLOPT_SSLKEY, CLIENT_KEY_PATH);

    curl_setopt($crl, CURLOPT_HTTPHEADER, array( // sets headers
        'Content-Type: application/json', // data has to be read as json
        'Content-Length: ' . strlen($post_data) // sets the lenght of data
    ));

    $result = curl_exec($crl); // executing the request

    //test
    file_log("curl", $url, $post_data, $result);

    if($result === false){ // this is the error case
        // an error object is generated and sent back to the client
        $msg = "Error in connection with the server";
        $err = array(
            "status" => "404", //Not found
            'statusText' => $msg
        );
        echo json_encode($err);
    }
    else{ // this is the success case
        if(str_contains(strtolower(json_encode($result)), 'error')){ // check if the endpoint answered with an error
            // in this case, an error object is generated and sent back to the client
            $err = array(
                "status" => "500", //Internal server error
                'statusText' => $result
            );
            echo json_encode($err);
        }
        else{ // in case the endpoint gives back a positive result

            // $ref is a support value where it will be stored the first object inside the 'FIDOAuthenticatorReferences' attribute of the response
            // this object has all the data needed by the application for the transaction confirmation step; this information will be stored in the database
            $ref = json_decode($result, true)['FIDOAuthenticatorReferences'][0];

            // initialization of the variables which will be used for the db query
            $signature = $ref['signature'];
            $signerPublicKey = $ref['signerPublicKey'];
            $signingKeyAlgorithm = $ref['signingKeyAlgorithm'];
            $signingKeyType = $ref['signingKeyType'];
            $authenticatorData = $ref['authenticatorData'];
            $clientDataJson = $ref['clientDataJSON'];

            //connection to mysql database
            if($conn = mysqli_connect(FIDO2SERVICE_DB_HOSTNAME, FIDO2SERVICE_DB_USERNAME, FIDO2SERVICE_DB_PASSWORD, FIDO2SERVICE_DB_DATABASE)){ // if the connection succeeds, then it is possible to make queries
                if(mysqli_query($conn, "set character set 'utf8'")){ // setting the format
                    $query = "INSERT INTO transactions(txid, txpayload, username, signature, signerPublicKey, signingKeyAlgorithm, signingKeyType, authenticatorData, clientDataJson) VALUES('".$txid."', '".$txpayload."', '".$username."', '".$signature."', '".$signerPublicKey."', '".$signingKeyAlgorithm."', '".$signingKeyType."', '".$authenticatorData."', '".$clientDataJson."')"; // insert of the transaction in the database
                    if($res = mysqli_query($conn, $query)){ // execution of the query and check of the return value.
                        // in case of success, a success object is generated and sent back to the client
                        $send = array(
                            "status" => "200",
                            "statusText" => $result
                        );
                        echo json_encode($send);
                    }
                    else{
                        // in case of error, an error object is generated and sent back to the client
                        $msg = "DB error";
                        $err = array(
                            "status" => "500", //Internal server error
                            'statusText' => $msg
                        );
                        echo json_encode($err);
                    }
                    mysqli_close($conn); // closing the connection
                    
                }
                else{ // error case of the query related to format setting
                    mysqli_close($conn); // closing the connection

                    // in this case, an error object is generated and sent back to the client
                    $msg = "DB error";
                    $err = array(
                        "status" => "500", //Internal server error
                        'statusText' => $msg
                    );
                    echo json_encode($err);
                }
            }
            else{ // if the result of mysqli_connect is false, then the connection does not succeed and an error occurs
                // in this case, an error object is generated and sent back to the client
                $msg = "DB error";
                $err = array(
                    "status" => "500", //Internal server error
                    'statusText' => $msg
                );
                echo json_encode($err);
            }
        }
    }
    // emptying temporary session attributes
    $_SESSION['txid'] = "";
    $_SESSION['txpayload'] = "";
}
else{ // in case some needed data are missing
    // an error object is generated and sent back to the client
    $msg = "Unprocessable Entity";
    $err = array(
        "status" => "422", //Unprocessable Entity
        "statusText" => $msg
    );
    echo json_encode($err);
}
?>
