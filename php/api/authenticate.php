<?php
include_once("../../constants.php");

if( empty($_POST)){
    $_POST = json_decode(file_get_contents('php://input', true));
}


$intent = "";
$username = "";
$id = "";
$rawId = "";
$authenticatorData = "";
$signature = "";
$userHandle = "";
$clientDataJSON = "";
$reqOrigin = "";
$type = "";

if(isset($_POST->intent)){
    $intent = $_POST->intent;
}
if(isset($_POST->username)){
    $username = $_POST->username;
}
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


if($username !== "" && $intent !== "" && $id !== "" && $rawId !== "" && $type !== "" &&
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
            'strongkeyMetadata' => $metadataObj,
            'publicKeyCredential' => $responseObj
        )
    );
    $post_data = json_encode($data); //encoding data to be correctly put in the body of the request

    $url = PRE_SKFS_HOSTNAME . SKFS_HOSTNAME . SKFS_AUTHENTICATE_PATH; //preparing the correct endpoint of the FIDO2 server
    
    $crl = curl_init($url);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true); //returns the transfer as a string of the return value of curl_exec() instead of outputting it directly
    curl_setopt($crl, CURLINFO_HEADER_OUT, true); //tracks the handle's request string
    curl_setopt($crl, CURLOPT_POST, true); //does a regular HTTP POST
    curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data); //sets the body of the POST request
    curl_setopt($crl, CURLOPT_PORT, SKFS_PORT); //sets the server port 
    curl_setopt($crl, CURLOPT_CAINFO, CERTIFICATE_PATH); //sets the path of server certificate
    curl_setopt($crl, CURLOPT_HTTPHEADER, array( //sets headers
        'Content-Type: application/json', //data has to be read as json
        'Content-Length: ' . strlen($post_data) //sets the lenght of data
    ));

    $result = curl_exec($crl); //executing the request
    
    if($result === false){ //this is the error case
        $msg = "Authenticate endpoint not found";
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
        $_SESSION['username'] = $username;
        
        $send = array(
            "status" => "200",
            "result" => $result
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