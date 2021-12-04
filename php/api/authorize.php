<?php
session_start();
include_once("../../constants.php");

if( empty($_POST)){
    $_POST = json_decode(file_get_contents('php://input', true));
}


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
$authenticatorData !== "" && $clientDataJSON !== "" && $signature !== "" && $reqOrigin !== ""){ //checking if all the information are correctly set


    /*
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
    $post_data = json_encode($data); //encoding data to be correctly put in the body of the request

    $url = PRE_SKFS_HOSTNAME . SKFS_HOSTNAME . SKFS_AUTHORIZE_PATH; //preparing the correct endpoint of the FIDO2 server

    $crl = curl_init($url);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true); //returns the transfer as a string of the return value of curl_exec() instead of outputting it directly
    curl_setopt($crl, CURLINFO_HEADER_OUT, true); //tracks the handle's request string
    curl_setopt($crl, CURLOPT_POST, true); //does a regular HTTP POST
    curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data); //sets the body of the POST request
    curl_setopt($crl, CURLOPT_PORT, SKFS_PORT); //sets the server port 
    curl_setopt($crl, CURLOPT_CAINFO, CERTIFICATE_PATH); //sets the path of server certificate
    curl_setopt($crl, CURLOPT_SSLCERT, CLIENT_CERTIFICATE_PATH);
    curl_setopt($crl, CURLOPT_SSLKEY, CLIENT_KEY_PATH);

    curl_setopt($crl, CURLOPT_HTTPHEADER, array( //sets headers
        'Content-Type: application/json', //data has to be read as json
        'Content-Length: ' . strlen($post_data) //sets the lenght of data
    ));

    $result = curl_exec($crl); //executing the request

    if($result === false){ //this is the error case
        $msg = "Authorize endpoint not found";
        $err = array(
            "status" => "404", //Not found
            'statusText' => $msg
        );
        echo json_encode($err);}
    else{ //this is the success case
        if(str_contains(strtolower(json_encode($result)), 'error')){
            $err = array(
                "status" => "500", //Internal server error
                'statusText' => $result
            );
            echo json_encode($err);
            exit;
        }


        $conn = mysqli_connect("localhost", "fido2service", "fido", "fido2service"); //connection to mysql database
        mysqli_query($conn, "set character set 'utf8'");

	$date = date("Y-m-d H:i:s");
        $query = "INSERT INTO transactions(txid, txpayload, username, date_time) VALUES('".$txid."', '".$txpayload."', '".$username."', '".$date."')"; //query to be executed in database
        mysqli_query($conn, $query); //execution of the query
        mysqli_close($conn);
        $_SESSION['txid'] = "";
        $_SESSION['txpayload'] = "";
        $send = array(
            "status" => "200",
            "statusText" => $result
        );
        echo json_encode($send);
    }
}
else{
    $msg = "Unprocessable Entity";
    $err = array(
        "status" => "422", //Unprocessable Entity
        "statusText" => $msg
    );
    echo json_encode($err);
}
?>
